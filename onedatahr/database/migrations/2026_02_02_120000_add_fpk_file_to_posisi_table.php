<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posisi', function (Blueprint $table) {
            if (!Schema::hasColumn('posisi', 'fpk_file')) {
                $table->string('fpk_file', 255)->nullable()->after('activated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posisi', function (Blueprint $table) {
            if (Schema::hasColumn('posisi', 'fpk_file')) {
                $table->dropColumn('fpk_file');
            }
        });
    }
};
