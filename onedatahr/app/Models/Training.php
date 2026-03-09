<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $table = 'training';
    protected $primaryKey = 'id_training';

    protected $fillable = [
        'kandidat_id',
        'posisi_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'jadwal_ttd_kontrak',
        'hasil_evaluasi',
        'keterangan_tambahan',
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
