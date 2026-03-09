<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('divisions', function (Blueprint $table) {
            // make company_id nullable so division can be based on holding
            // use raw statement to avoid requiring doctrine/dbal for ->change()
            try {
                DB::statement("ALTER TABLE `divisions` MODIFY `company_id` bigint(20) unsigned NULL");
            } catch (\Exception $e) {
                // ignore if DB driver doesn't support or column already nullable
            }

            // add based_on column to indicate company or holding
            if (!Schema::hasColumn('divisions', 'based_on')) {
                $table->string('based_on')->default('company')->after('company_id');
            }

            // add holding_id and parent_id
            if (!Schema::hasColumn('divisions', 'holding_id')) {
                $table->unsignedBigInteger('holding_id')->nullable()->after('based_on');
            }

            if (!Schema::hasColumn('divisions', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('holding_id');
            }

        });

        // add foreign keys using raw statements wrapped in try/catch to avoid duplicate-key errors
        if (Schema::hasTable('holdings')) {
            try {
                DB::statement("ALTER TABLE `divisions` ADD CONSTRAINT `divisions_holding_id_foreign` FOREIGN KEY (`holding_id`) REFERENCES `holdings` (`id`) ON DELETE SET NULL");
            } catch (\Exception $e) {
                // ignore duplicate foreign key or other errors
            }
        }

        try {
            DB::statement("ALTER TABLE `divisions` ADD CONSTRAINT `divisions_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `divisions` (`id`) ON DELETE SET NULL");
        } catch (\Exception $e) {
            // ignore duplicate foreign key or other errors
        }
    }

    public function down()
    {
        Schema::table('divisions', function (Blueprint $table) {
            if (Schema::hasColumn('divisions', 'parent_id')) {
                // drop foreign by column name
                try { $table->dropForeign(['parent_id']); } catch (\Exception $e) {}
                $table->dropColumn('parent_id');
            }
            if (Schema::hasColumn('divisions', 'holding_id')) {
                try { $table->dropForeign(['holding_id']); } catch (\Exception $e) {}
                $table->dropColumn('holding_id');
            }
            if (Schema::hasColumn('divisions', 'based_on')) {
                $table->dropColumn('based_on');
            }

            // make company_id not nullable again (use raw statement)
            try {
                DB::statement("ALTER TABLE `divisions` MODIFY `company_id` bigint(20) unsigned NOT NULL");
            } catch (\Exception $e) {
                // ignore
            }
        });
    }
};
