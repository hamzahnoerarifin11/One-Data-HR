<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiScore extends Model
{
    use HasFactory;

    protected $table = 'kpi_scores';
    protected $primaryKey = 'id_kpi_score';

    // GANTI $guarded MENJADI $fillable
    // Pastikan 'target' ada disini agar inputan target baru bisa tersimpan
    protected $fillable = [
        'kpi_item_id',
        'nama_periode', // Contoh: 'Semester 1'
        'target',       // <--- WAJIB ADA (Agar target bisa diedit)
        'realisasi',
        'skor',
        'skor_akhir',
        'keterangan'
    ];

    // Relasi balik ke Item
    public function item()
    {
        return $this->belongsTo(KpiItem::class, 'kpi_item_id', 'id_kpi_item');
    }
}