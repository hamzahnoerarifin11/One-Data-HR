<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\KpiScore; // <--- Pastikan baris ini ada jika IDE masih error, atau untuk memastikan

class KpiItem extends Model
{
    protected $table = 'kpi_items';
    protected $primaryKey = 'id_kpi_item';
    protected $guarded = [];

    public function scores()
    {
        // Pastikan nama class 'KpiScore' sesuai dengan nama file KpiScore.php
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