<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            // 1. Buat kolomnya dulu
            // Pastikan tipe datanya SAMA dengan id_karyawan (biasanya Integer atau BigInteger)
            // Jika id_karyawan pakai Integer, ganti unsignedBigInteger jadi integer
            $table->BigInteger('atasan_id')->nullable()->after('id_karyawan');

            // 2. Definisikan Foreign Key dengan nama kolom yang BENAR
            $table->foreign('atasan_id')
                  ->references('id_karyawan') // <--- INI PERBAIKANNYA (Bukan 'id')
                  ->on('karyawan')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            // Hapus foreign key
            $table->dropForeign(['atasan_id']); 
            
            // Hapus kolom
            $table->dropColumn('atasan_id');
        });
    }
};