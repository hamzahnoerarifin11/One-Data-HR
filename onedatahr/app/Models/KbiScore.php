<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KbiScore extends Model
{
    use HasFactory;

    protected $table = 'kbi_scores';
    protected $primaryKey = 'id_kbi_score';

    protected $fillable = [
        'kbi_assessment_id',
        'kbi_item_id',
        'skor', // Nilai 1-4
    ];

    // Relasi balik ke Header Assessment
    public function assessment()
    {
        return $this->belongsTo(KbiAssessment::class, 'kbi_assessment_id', 'id_kbi_assessment');
    }

    // Relasi ke Soal
    public function item()
    {
        return $this->belongsTo(KbiItem::class, 'kbi_item_id', 'id_kbi_item');
    }
}