<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiAssessment extends Model
{
    use HasFactory; // Tambahkan ini (standard Laravel)

    protected $table = 'kpi_assessments';
    protected $primaryKey = 'id_kpi_assessment';

    // HAPUS $guarded, GANTI DENGAN $fillable
    // Pastikan semua nama kolom tabel kamu ada di sini
    protected $fillable = [
        'karyawan_id',
        'tahun',
        'periode',
        'status',
        'total_skor_akhir',
        'grade',         // <--- INI YANG PALING PENTING
        'created_by',    // Tambahkan jika ada kolom created_by/updated_by custom
        // Tambahkan kolom lain jika ada
    ];

    public function items()
    {
        return $this->hasMany(KpiItem::class, 'kpi_assessment_id', 'id_kpi_assessment');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }
}