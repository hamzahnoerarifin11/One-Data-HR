<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiAssessment extends Model
{
    protected $table = 'kpi_assessments';
    protected $primaryKey = 'id_kpi_assessment';
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(KpiItem::class, 'kpi_assessment_id', 'id_kpi_assessment');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }
}