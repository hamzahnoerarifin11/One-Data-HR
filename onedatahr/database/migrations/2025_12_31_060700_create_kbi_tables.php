<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Master Kategori & Pertanyaan (Berdasarkan Image)
        Schema::create('kbi_items', function (Blueprint $table) {
            $table->id('id_kbi_item');
            $table->string('kategori'); // Komunikatif, Unggul, dll
            $table->text('perilaku');   // Isi butir perilaku
            $table->timestamps();
        });

        // 2. Tabel Header Penilaian (Sesi Penilaian)
        Schema::create('kbi_assessments', function (Blueprint $table) {
            $table->id('id_kbi_assessment');
            $table->foreignId('karyawan_id'); // Siapa yang dinilai
            $table->foreignId('penilai_id');  // Siapa yang menilai
            $table->enum('tipe_penilai', ['DIRI_SENDIRI', 'ATASAN', 'BAWAHAN']);
            $table->year('tahun');
            $table->string('periode')->default('Semester 1'); // Atau Tahunan
            $table->double('rata_rata_akhir')->default(0);
            $table->string('status')->default('DRAFT'); // DRAFT, SUBMITTED
            $table->timestamps();
        });

        // 3. Tabel Detail Skor (Jawaban 1-4)
        Schema::create('kbi_scores', function (Blueprint $table) {
            $table->id('id_kbi_score');
            $table->foreignId('kbi_assessment_id')->constrained('kbi_assessments', 'id_kbi_assessment')->onDelete('cascade');
            $table->foreignId('kbi_item_id')->constrained('kbi_items', 'id_kbi_item');
            $table->integer('skor'); // 1, 2, 3, 4
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kbi_scores');
        Schema::dropIfExists('kbi_assessments');
        Schema::dropIfExists('kbi_items');
    }
};