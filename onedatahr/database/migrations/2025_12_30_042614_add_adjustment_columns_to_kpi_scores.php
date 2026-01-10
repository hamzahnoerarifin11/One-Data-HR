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
            
            // 1. Tambah Adjustment Smt 1 (jika belum ada)
            if (!Schema::hasColumn('kpi_scores', 'adjustment_smt1')) {
                // Taruh setelah real_smt1 agar rapi
                $table->double('adjustment_smt1')->nullable()->default(null)->after('real_smt1');
            }

            // 2. Tambah Total Target/Real Smt 2 (Jaga-jaga jika langkah sebelumnya gagal)
            if (!Schema::hasColumn('kpi_scores', 'total_target_smt2')) {
                $table->double('total_target_smt2')->nullable()->default(0)->after('real_des');
            }
            if (!Schema::hasColumn('kpi_scores', 'total_real_smt2')) {
                $table->double('total_real_smt2')->nullable()->default(0)->after('total_target_smt2');
            }

            // 3. Tambah Adjustment Smt 2 (jika belum ada)
            if (!Schema::hasColumn('kpi_scores', 'adjustment_smt2')) {
                // Taruh paling belakang atau setelah total_real_smt2
                $table->double('adjustment_smt2')->nullable()->default(null)->after('total_real_smt2');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpi_scores', function (Blueprint $table) {
            $table->dropColumn([
                'adjustment_smt1', 
                'total_target_smt2', 
                'total_real_smt2', 
                'adjustment_smt2'
            ]);
        });
    }
};