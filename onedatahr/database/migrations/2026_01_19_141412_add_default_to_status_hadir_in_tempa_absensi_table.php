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
        Schema::table('tempa_absensi', function (Blueprint $table) {
            $table->string('status_hadir')->default('hadir')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tempa_absensi', function (Blueprint $table) {
            $table->string('status_hadir')->nullable(false)->change();
        });
    }
};
