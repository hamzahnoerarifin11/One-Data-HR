<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WigRekrutmen extends Model
{
    protected $table = 'wig_rekrutmen';
    protected $primaryKey = 'id_wig_rekrutmen';

    protected $fillable = [
        'posisi_id',
        'fpk_user',
        'fpk_hrd',
        'fpk_finance',
        'fpk_direktur',
        'tanggal_publish_loker',
        'total_pelamar',
        'total_lead',
        'total_lolos_psikotes',
        'tanggal_tes_kompetensi',
        'dipanggil_tes_kompetensi',
        'hadir_tes_kompetensi',
        'lolos_tes_kompetensi',
        'tanggal_interview_hr',
        'dipanggil_interview_hr',
        'hadir_interview_hr',
        'lolos_interview_hr',
        'tanggal_interview_user',
        'dipanggil_interview_user',
        'hadir_interview_user',
        'lolos_interview_user',
        'tanggal_bg_checking',
        'tanggal_mulai_training',
        'tanggal_selesai_training',
        'keterangan'
    ];

    public function posisi()
    {
        return $this->belongsTo(Posisi::class, 'posisi_id', 'id_posisi');
    }
}
