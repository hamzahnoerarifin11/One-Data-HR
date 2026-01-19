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
        Schema::table('tempa_peserta', function (Blueprint $table) {
            $table->dropColumn(['tempat', 'keterangan_cabang']);
        });
    }

    public function down(): void
    {
        Schema::table('tempa_peserta', function (Blueprint $table) {
            $table->enum('tempat', ['pusat', 'cabang'])->default('pusat')->after('keterangan_pindah');
            $table->string('keterangan_cabang')->nullable()->after('tempat');
        });
    }
};
