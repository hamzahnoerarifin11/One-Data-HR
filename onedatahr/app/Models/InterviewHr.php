<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewHr extends Model
{
    protected $table = 'interview_hr';
    protected $primaryKey = 'id_interview_hr';
    public $timestamps = true;
    protected $keyType = 'int';

    protected $fillable = [
        'kandidat_id',
        'posisi_id',
        'hari_tanggal',
        'nama_kandidat',
        'nama_interviewer',
        'posisi_dilamar',
        'model_wawancara',
        'skor_profesional',
        'catatan_profesional',
        'skor_spiritual',
        'catatan_spiritual',
        'skor_learning',
        'catatan_learning',
        'skor_initiative',
        'catatan_initiative',
        'skor_komunikasi',
        'catatan_komunikasi',
        'skor_problem_solving',
        'catatan_problem_solving',
        'skor_teamwork',
        'catatan_teamwork',
        'catatan_tambahan',
        'keputusan',
        'total',
        'hasil_akhir'
    ];

    public function kandidat()
    {
        return $this->belongsTo(Kandidat::class, 'kandidat_id', 'id_kandidat');
    }
    public function posisi()
    {
        return $this->belongsTo(Posisi::class, 'posisi_id', 'id_posisi');
    }
}
