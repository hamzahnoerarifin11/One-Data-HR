<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiScore extends Model
{
    use HasFactory;

    protected $table = 'kpi_scores';
    protected $primaryKey = 'id_kpi_score';

    protected $fillable = [
        // --- 1. DATA UTAMA ---
        'kpi_item_id',
        'nama_periode',
        'target',       // Target Tahunan
        'realisasi',    // Realisasi Tahunan (Total)
        'skor',         // Rata-rata Skor
        'skor_akhir',   // Final Score setelah bobot
        'keterangan',

        // --- 2. SEMESTER 1 ---
        'target_smt1',
        'real_smt1',
        'adjustment_smt1', // Nilai Adjustment Smt 1
        'adjustment_real_smt1', // Nilai Adjustment Real Smt 1

        // --- 3. DATA BULANAN (SEMESTER 2) ---
        // Wajib didaftarkan agar loop di controller tersimpan
        'target_jul', 'real_jul',
        'target_aug', 'real_aug',
        'target_sep', 'real_sep',
        'target_okt', 'real_okt',
        'target_nov', 'real_nov',
        'target_des', 'real_des',

        // --- 4. TOTAL SEMESTER 2 (INPUT MANUAL BARU) ---
        'total_target_smt2',
        'total_real_smt2',
        'adjustment_smt2', // Nilai Adjustment Smt 2
        'adjustment_target_smt2', // <--- TAMBAHKAN INI (Input User)
        'adjustment_real_smt2',
    ];

    // Relasi balik ke Item
    public function item()
    {
        return $this->belongsTo(KpiItem::class, 'kpi_item_id', 'id_kpi_item');
    }
}