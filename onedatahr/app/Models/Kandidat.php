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
        'link_cv',
        'file_pdf',
        'status_akhir',
        'tgl_lolos_cv', 'tgl_lolos_psikotes', 'tgl_lolos_kompetensi', 'tgl_lolos_hr', 'tgl_lolos_user',
    ];

    protected $casts = [
        'tanggal_melamar' => 'date',
        'tgl_lolos_cv' => 'date',
        'tgl_lolos_psikotes' => 'date',
        'tgl_lolos_kompetensi' => 'date',
        'tgl_lolos_hr' => 'date',
        'tgl_lolos_user' => 'date',
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
    // Di dalam class Kandidat
    public function training()
    {
        // Hubungkan id_kandidat di tabel kandidat dengan kandidat_id di tabel training
        return $this->hasOne(Training::class, 'kandidat_id', 'id_kandidat');
    }
    public function getExcelPathAttribute()
    {
        return $this->file_excel
            ? 'uploads/excel/' . $this->file_excel
            : null;
    }
}
