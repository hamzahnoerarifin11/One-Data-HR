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
            $table->decimal('subtotal_smt1', 5, 2)->default(0);
            $table->decimal('subtotal_smt2', 5, 2)->default(0);
            $table->json('justification')->nullable();
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
