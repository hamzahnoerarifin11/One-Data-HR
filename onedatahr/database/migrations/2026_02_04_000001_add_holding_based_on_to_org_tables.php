<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('divisions', function (Blueprint $table) {
            if (!Schema::hasColumn('divisions', 'based_on')) {
                $table->string('based_on')->default('company')->after('company_id'); // 'company' or 'holding'
            }
            if (!Schema::hasColumn('divisions', 'holding_id')) {
                $table->unsignedBigInteger('holding_id')->nullable()->after('based_on');
                $table->foreign('holding_id')->references('id')->on('holdings')->onDelete('set null');
            }
            // make company_id nullable so a holding-based division can have null company_id
            if (Schema::hasColumn('divisions', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->change();
            }
        });

        Schema::table('departments', function (Blueprint $table) {
            if (!Schema::hasColumn('departments', 'based_on')) {
                $table->string('based_on')->default('company')->after('company_id');
            }
            if (!Schema::hasColumn('departments', 'holding_id')) {
                $table->unsignedBigInteger('holding_id')->nullable()->after('based_on');
                $table->foreign('holding_id')->references('id')->on('holdings')->onDelete('set null');
            }
            if (Schema::hasColumn('departments', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->change();
            }
        });

        Schema::table('units', function (Blueprint $table) {
            if (!Schema::hasColumn('units', 'based_on')) {
                $table->string('based_on')->default('company')->after('company_id');
            }
            if (!Schema::hasColumn('units', 'holding_id')) {
                $table->unsignedBigInteger('holding_id')->nullable()->after('based_on');
                $table->foreign('holding_id')->references('id')->on('holdings')->onDelete('set null');
            }
            if (Schema::hasColumn('units', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->change();
            }
        });
    }

    public function down()
    {
        Schema::table('divisions', function (Blueprint $table) {
            if (Schema::hasColumn('divisions', 'holding_id')) {
                $table->dropForeign(['holding_id']);
                $table->dropColumn('holding_id');
            }
            if (Schema::hasColumn('divisions', 'based_on')) {
                $table->dropColumn('based_on');
            }
            // we won't attempt to revert company_id nullability
        });

        Schema::table('departments', function (Blueprint $table) {
            if (Schema::hasColumn('departments', 'holding_id')) {
                $table->dropForeign(['holding_id']);
                $table->dropColumn('holding_id');
            }
            if (Schema::hasColumn('departments', 'based_on')) {
                $table->dropColumn('based_on');
            }
        });

        Schema::table('units', function (Blueprint $table) {
            if (Schema::hasColumn('units', 'holding_id')) {
                $table->dropForeign(['holding_id']);
                $table->dropColumn('holding_id');
            }
            if (Schema::hasColumn('units', 'based_on')) {
                $table->dropColumn('based_on');
            }
        });
    }
};
