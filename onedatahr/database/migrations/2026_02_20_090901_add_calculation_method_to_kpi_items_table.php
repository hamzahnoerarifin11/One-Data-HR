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
        Schema::table('kpi_items', function (Blueprint $table) {
            $table->string('calculation_method')->default('positive')->after('polaritas');
            $table->decimal('previous_progress', 8, 2)->default(0)->after('target');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpi_items', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('kpi_items', 'calculation_method')) {
                $columnsToDrop[] = 'calculation_method';
            }
            if (Schema::hasColumn('kpi_items', 'previous_progress')) {
                $columnsToDrop[] = 'previous_progress';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
