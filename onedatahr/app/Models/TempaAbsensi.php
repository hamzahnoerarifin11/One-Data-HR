<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempaAbsensi extends Model
{
    use HasFactory;

    protected $table = 'tempa_absensi';
    protected $primaryKey = 'id_absensi';

    protected $fillable = [
        'id_peserta',
        'bulan',
        'tahun',
        'pertemuan_ke',
        'tanggal',
        'status_hadir',
        'bukti_foto',
        'created_by'
    ];

    public function peserta()
    {
        return $this->belongsTo(
            TempaPeserta::class,
            'id_peserta',
            'id_peserta'
        );
    }
}
