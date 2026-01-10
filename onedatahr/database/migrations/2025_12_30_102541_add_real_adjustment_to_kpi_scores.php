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
        // Tambah kolom Real Smt 1
        $table->double('adjustment_real_smt1')->nullable()->after('real_smt1');
        
        // Tambah kolom Target & Real Smt 2 (Untuk Adjustment Smt 2)
        $table->double('adjustment_target_smt2')->nullable()->after('total_real_smt2');
        $table->double('adjustment_real_smt2')->nullable()->after('adjustment_target_smt2');
    });
}

public function down()
{
    Schema::table('kpi_scores', function (Blueprint $table) {
        $table->dropColumn(['adjustment_real_smt1', 'adjustment_target_smt2', 'adjustment_real_smt2']);
    });
}
};