<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pekerjaan', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id_karyawan');
            $table->unsignedBigInteger('division_id')->nullable()->after('company_id');
            $table->unsignedBigInteger('department_id')->nullable()->after('division_id');
            $table->unsignedBigInteger('unit_id')->nullable()->after('department_id');
            $table->unsignedBigInteger('position_id')->nullable()->after('unit_id');

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('division_id')->references('id')->on('divisions');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('unit_id')->references('id')->on('units');
            $table->foreign('position_id')->references('id')->on('positions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pekerjaan', function (Blueprint $table) {
            $table->dropForeign(['position_id']);
            $table->dropForeign(['unit_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['division_id']);
            $table->dropForeign(['company_id']);

            $table->dropColumn(['position_id', 'unit_id', 'department_id', 'division_id', 'company_id']);
        });
    }
};
