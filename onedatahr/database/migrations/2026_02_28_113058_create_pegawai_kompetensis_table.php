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
        Schema::create('pegawai_kompetensi', function (Blueprint $table) {
            $table->id('id_peg_komp');
            $table->unsignedBigInteger('karyawan_id')->nullable();
            $table->string('nama_kompetensi')->nullable();
            $table->string('level')->nullable();
            $table->string('sumber')->default('LMS Wadja')->nullable();
            $table->timestamps();

            // Opsional: Foreign key constraint jika karyawan_id direferensikan ke tabel karyawans
            // (Sesuaikan dengan struktur OneDataHR jika primary key tabel karyawan adalah id_karyawan)
            // $table->foreign('karyawan_id')->references('id_karyawan')->on('karyawans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai_kompetensi');
    }
};
