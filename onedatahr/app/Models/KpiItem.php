<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\KpiScore; 

class KpiItem extends Model
{
    protected $table = 'kpi_items';
    protected $primaryKey = 'id_kpi_item';
    
    protected $fillable = [
        'kpi_assessment_id',
        'key_result_area',
        'key_performance_indicator',
        'perspektif',
        'polaritas',
        'calculation_method',
        'units',   
        'bobot',
        'target',      
        'previous_progress',
        'realisasi',
        'skor',
        'skor_akhir',
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