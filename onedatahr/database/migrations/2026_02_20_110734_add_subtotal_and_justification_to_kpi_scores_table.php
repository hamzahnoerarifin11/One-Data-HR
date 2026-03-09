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
            $table->decimal('subtotal_smt1', 8, 2)->nullable()->after('real_smt1')->comment('Rata-rata skor sebelum adjustment');
            $table->decimal('subtotal_smt2', 8, 2)->nullable()->after('total_real_smt2');
            $table->json('justification')->nullable()->after('keterangan')->comment('Catatan per bulan/semester');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpi_scores', function (Blueprint $table) {
            $table->dropColumn(['subtotal_smt1', 'subtotal_smt2', 'justification']);
        });
    }
};
