<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProsesRekrutmen extends Model
{
    use HasFactory;

    protected $table = 'proses_rekrutmen';
    protected $primaryKey = 'id_proses_rekrutmen';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'kandidat_id',
        'cv_lolos',
        'tanggal_cv',
        'psikotes_lolos',
        'tanggal_psikotes',
        'tes_kompetensi_lolos',
        'tanggal_tes_kompetensi',
        'interview_hr_lolos',
        'tanggal_interview_hr',
        'interview_user_lolos',
        'tanggal_interview_user',
        'tahap_terakhir',
    ];

    protected $casts = [
        'cv_lolos' => 'boolean',
        'psikotes_lolos' => 'boolean',
        'tes_kompetensi_lolos' => 'boolean',
        'interview_hr_lolos' => 'boolean',
        'interview_user_lolos' => 'boolean',
        'tanggal_cv' => 'date',
        'tanggal_psikotes' => 'date',
        'tanggal_tes_kompetensi' => 'date',
        'tanggal_interview_hr' => 'date',
        'tanggal_interview_user' => 'date',
    ];

    public function kandidat()
    {
        return $this->belongsTo(Kandidat::class, 'kandidat_id', 'id_kandidat');
    }
}
