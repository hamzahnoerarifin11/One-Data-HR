<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Tabel Header: Menyimpan sesi penilaian (siapa menilai siapa & kapan)
        Schema::create('kpi_assessments', function (Blueprint $table) {
            $table->id('id_kpi_assessment');
            // Relasi ke tabel karyawan yang sudah ada (menggunakan id_karyawan sesuai dbwr.sql)
            // Gunakan bigInteger saja (tanpa Unsigned) agar cocok dengan tabel karyawan
            $table->bigInteger('karyawan_id'); 
            $table->bigInteger('penilai_id')->nullable(); // ID User/Karyawan atasan
            
            $table->string('tahun', 4); // Contoh: 2025
            $table->string('periode', 50); // Contoh: "Semester 1", "Januari - Juni"
            $table->date('tanggal_penilaian')->nullable();
            
            // Kolom summary untuk hasil akhir (Excel bagian bawah)
            $table->decimal('total_skor_akhir', 8, 2)->default(0); 
            $table->string('grade_akhir', 20)->nullable(); // GREAT, GOOD, dll
            $table->enum('status', ['DRAFT', 'SUBMITTED', 'APPROVED'])->default('DRAFT');
            
            $table->timestamps();

            // Foreign Key ke tabel karyawan existing
            $table->foreign('karyawan_id')->references('id_karyawan')->on('karyawan')->onDelete('cascade');
        });

        // 2. Tabel Item KPI: Menyimpan baris-baris indikator (Perspektif, KPI, Bobot, Target)
        Schema::create('kpi_items', function (Blueprint $table) {
            $table->id('id_kpi_item');
            $table->unsignedBigInteger('kpi_assessment_id');
            
            // Kolom sesuai kolom Excel kiri
            $table->string('perspektif'); // Internal Business Process, Financial, dll
            $table->string('key_result_area')->nullable();
            $table->text('key_performance_indicator'); // Deskripsi KPI
            $table->string('units', 50); // Prosentase, Rupiah, Angka
            $table->enum('polaritas', ['Positif', 'Negatif']); // Penting untuk rumus
            $table->decimal('bobot', 5, 2); // Bobot dalam persen (ex: 30.00)
            $table->string('target_tahunan')->nullable(); // Target global baris tersebut
            
            $table->timestamps();

            $table->foreign('kpi_assessment_id')->references('id_kpi_assessment')->on('kpi_assessments')->onDelete('cascade');
        });

        // 3. Tabel Skor Periodik: Menyimpan nilai per kolom waktu (Sem 1, Juli, Agustus, dll)
        Schema::create('kpi_scores', function (Blueprint $table) {
            $table->id('id_kpi_score');
            $table->unsignedBigInteger('kpi_item_id');
            
            // Jenis periode untuk fleksibilitas (Bisa Bulanan atau Semesteran sesuai screenshot)
            $table->enum('tipe_periode', ['SEMESTER', 'BULAN']); 
            $table->string('nama_periode'); // "Semester 1", "Juli", "Agustus"
            $table->integer('bulan_urutan')->nullable(); // 1-12 untuk sorting, null jika semester
            
            // Data inputan & hasil hitung
            $table->string('target'); // Target spesifik bulan itu
            $table->string('realisasi')->nullable(); // Capaian aktual
            $table->decimal('skor', 8, 2)->default(0); // Hasil rumus
            $table->decimal('skor_akhir', 8, 2)->default(0); // (Skor x Bobot) / 100
            
            $table->timestamps();

            $table->foreign('kpi_item_id')->references('id_kpi_item')->on('kpi_items')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('kpi_scores');
        Schema::dropIfExists('kpi_items');
        Schema::dropIfExists('kpi_assessments');
    }
};