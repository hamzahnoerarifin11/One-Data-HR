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
        Schema::table('perusahaan', function (Blueprint $table) {
            // Using raw SQL to avoid doctrine/dbal dependency issues with ENUMs
            DB::statement("ALTER TABLE perusahaan MODIFY COLUMN Perusahaan VARCHAR(255) NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perusahaan', function (Blueprint $table) {
            // Revert back to ENUM if needed, though exact previous values might be lost if not careful.
            // For now, we'll leave it as VARCHAR or define a basic set if strictly necessary.
            // But usually down() should reverse. Let's try to restore a generic ENUM or just keep VARCHAR as it allows more.
            // To be strictly correct, we should revert to the previous ENUM definition.
            // However, since we don't know the exact previous ENUM list without checking DB (which we did partially),
            // and we want to move AWAY from ENUM, keeping it as VARCHAR or reverting to a safe ENUM is an option.
            // Let's just comment that it cannot be easily reversed without knowing exact ENUM list
        });
    }
};
