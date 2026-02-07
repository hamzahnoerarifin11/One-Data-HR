<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('divisions', function (Blueprint $table) {
            if (!Schema::hasColumn('divisions', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('holding_id');
                $table->foreign('parent_id')->references('id')->on('divisions')->onDelete('set null');
            }
        });

        Schema::table('departments', function (Blueprint $table) {
            if (!Schema::hasColumn('departments', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('holding_id');
                $table->foreign('parent_id')->references('id')->on('departments')->onDelete('set null');
            }
        });

        Schema::table('units', function (Blueprint $table) {
            if (!Schema::hasColumn('units', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('holding_id');
                $table->foreign('parent_id')->references('id')->on('units')->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('divisions', function (Blueprint $table) {
            if (Schema::hasColumn('divisions', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
        });

        Schema::table('departments', function (Blueprint $table) {
            if (Schema::hasColumn('departments', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
        });

        Schema::table('units', function (Blueprint $table) {
            if (Schema::hasColumn('units', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
        });
    }
};
