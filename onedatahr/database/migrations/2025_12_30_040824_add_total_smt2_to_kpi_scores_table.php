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
        Schema::table('kpi_scores', function (Blueprint $table) {
            // Menambahkan kolom setelah bulan desember agar rapi
            $table->double('total_target_smt2')->nullable()->default(0)->after('real_des');
            $table->double('total_real_smt2')->nullable()->default(0)->after('total_target_smt2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpi_scores', function (Blueprint $table) {
            $table->dropColumn(['total_target_smt2', 'total_real_smt2']);
        });
    }
};