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
        Schema::table('tempa_materi', function (Blueprint $table) {
            if (Schema::hasColumn('tempa_materi', 'id_kelompok')) {
                $table->dropForeign(['id_kelompok']);
                $table->dropColumn('id_kelompok');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tempa_materi', function (Blueprint $table) {
            //
        });
    }
};
