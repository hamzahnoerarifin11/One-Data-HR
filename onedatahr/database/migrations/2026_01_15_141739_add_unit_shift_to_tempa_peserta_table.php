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
            // Ubah status_peserta menjadi integer (0=tidak aktif, 1=aktif, 2=tidak aktif sementara)
            $table->integer('status_peserta')->default(1)->change();

            // Tambah kolom unit dan shift
            $table->string('unit')->nullable()->after('mentor_id');
            $table->integer('shift')->nullable()->after('unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tempa_peserta', function (Blueprint $table) {
            $table->dropColumn(['unit', 'shift']);
            $table->boolean('status_peserta')->default(true)->change();
        });
    }
};
