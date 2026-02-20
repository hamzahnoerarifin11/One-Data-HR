<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Route::get('/fix-db-kpi', function() {
    $log = [];
    if (!Schema::hasTable('kpi_templates')) {
        Schema::create('kpi_templates', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        $log[] = 'Created kpi_templates table';
    } else {
        $log[] = 'kpi_templates table already exists';
    }

    Schema::table('kpi_items', function (Blueprint $table) use (&$log) {
        if (!Schema::hasColumn('kpi_items', 'calculation_method')) {
            $table->enum('calculation_method', ['positive', 'negative', 'progress'])->default('positive')->after('polaritas');
            $log[] = 'Added calculation_method to kpi_items';
        } else {
            $log[] = 'calculation_method already exists';
        }

        if (!Schema::hasColumn('kpi_items', 'previous_progress')) {
             $table->decimal('previous_progress', 8, 2)->default(0)->after('target');
             $log[] = 'Added previous_progress to kpi_items';
        } else {
            $log[] = 'previous_progress already exists';
        }
    });

    return response()->json($log);
});
