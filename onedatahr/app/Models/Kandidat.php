<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kandidat extends Model
{
    use HasFactory;

    protected $table = 'kandidat';
    protected $primaryKey = 'id_kandidat';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'nama',
        'posisi_id',
        'tanggal_melamar',
        'sumber',
        'status_akhir',
    ];

    protected $casts = [
        'tanggal_melamar' => 'date',
    ];

    public function posisi()
    {
        return $this->belongsTo(Posisi::class, 'posisi_id', 'id_posisi');
    }

    public function proses()
    {
        return $this->hasOne(ProsesRekrutmen::class, 'kandidat_id', 'id_kandidat');
    }

    public function pemberkasan()
    {
        return $this->hasOne(Pemberkasan::class, 'kandidat_id', 'id_kandidat');
    }

    public function interviewHr()
    {
        return $this->hasOne(InterviewHr::class, 'kandidat_id', 'id_kandidat');
    }
     public function kandidatLanjutUser()
    {
        return $this->hasOne(
            KandidatLanjutUser::class,
            'kandidat_id',     // FK di tabel kandidat_lanjut_user
            'id_kandidat'      // PK di tabel kandidat
        );
    }
}
