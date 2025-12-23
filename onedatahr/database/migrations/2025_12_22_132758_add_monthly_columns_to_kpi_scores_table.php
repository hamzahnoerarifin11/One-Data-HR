<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('kpi_scores', function (Blueprint $table) {
        // Tambahkan kolom untuk Semester 1
        $table->decimal('target_smt1', 10, 2)->default(0)->nullable();
        $table->decimal('real_smt1', 10, 2)->default(0)->nullable();

        // Tambahkan Loop Kolom Juli - Desember
        $months = ['jul', 'aug', 'sep', 'okt', 'nov', 'des'];
        foreach ($months as $month) {
            $table->decimal('target_' . $month, 10, 2)->default(0)->nullable();
            $table->decimal('real_' . $month, 10, 2)->default(0)->nullable();
        }
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpi_scores', function (Blueprint $table) {
            //
        });
    }
};
