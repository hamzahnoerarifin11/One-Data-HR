<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KbiAssessment extends Model
{
    use HasFactory;

    protected $table = 'kbi_assessments';
    protected $primaryKey = 'id_kbi_assessment';

    protected $fillable = [
        'karyawan_id', // Yang dinilai
        'penilai_id',  // Yang menilai
        'tipe_penilai', // DIRI_SENDIRI, ATASAN, BAWAHAN
        'tahun',
        'periode',
        'rata_rata_akhir',
        'status', // DRAFT / SUBMITTED
    ];

    // Relasi ke User/Karyawan yang dinilai
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan'); // Sesuaikan 'id_karyawan' dengan PK tabel karyawan Anda
    }

    // Relasi ke Penilai
    public function penilai()
    {
        return $this->belongsTo(User::class, 'penilai_id', 'id'); // Atau Karyawan::class jika penilai juga karyawan
    }

    // Relasi ke Detail Skor (Jawaban)
    public function scores()
    {
        return $this->hasMany(KbiScore::class, 'kbi_assessment_id', 'id_kbi_assessment');
    }
}