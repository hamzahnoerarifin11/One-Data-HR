<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiScore extends Model
{
    protected $table = 'kpi_scores';
    protected $primaryKey = 'id_kpi_score';
    protected $guarded = [];

    // Relasi balik ke Item
    public function item()
    {
        return $this->belongsTo(KpiItem::class, 'kpi_item_id', 'id_kpi_item');
    }
}