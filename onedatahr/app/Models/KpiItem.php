<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\KpiScore; 

class KpiItem extends Model
{
    protected $table = 'kpi_items';
    protected $primaryKey = 'id_kpi_item';
    
    // UBAH DISINI:
    // Kita ganti $guarded dengan $fillable agar lebih eksplisit
    // Pastikan 'target', 'polaritas', dan 'satuan' masuk disini.
    protected $fillable = [
        'kpi_assessment_id',
        'perspektif',
        'key_result_area',
        'key_performance_indicator',
        'bobot',
        'target',      // <--- Kolom Baru (Penting untuk Template)
        'realisasi',
        'skor',
        'skor_akhir',
        'polaritas',   // <--- Kolom Baru (Penting untuk Template)
        'units',      // <--- Kolom Baru (Penting untuk Template)
    ];

    public function scores()
    {
        return $this->hasMany(KpiScore::class, 'kpi_item_id', 'id_kpi_item');
    }
    
    // Helper untuk mengambil skor bulan tertentu
    public function getScoreByMonth($bulanNama)
    {
        // Menggunakan property ->scores (bukan method ->scores()) 
        // akan mengambil collection yang sudah di-load (Lazy Loading)
        return $this->scores->where('nama_periode', $bulanNama)->first();
    }
}