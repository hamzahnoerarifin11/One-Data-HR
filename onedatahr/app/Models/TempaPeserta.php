<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempaPeserta extends Model
{
    use HasFactory;

    protected $table = 'tempa_peserta';
    protected $primaryKey = 'id_peserta';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_peserta',
        'nik_karyawan',
        'status_peserta',
        'keterangan_pindah',
        'id_kelompok',
        'id_tempa'
    ];

    public function kelompok()
    {
        return $this->belongsTo(
            \App\Models\TempaKelompok::class,
            'id_kelompok',
            'id_kelompok'
        );
    }


    // public function mentor()
    // {
    //     return $this->belongsTo(User::class, 'mentor_id');
    // }

    public function tempa()
    {
        return $this->belongsTo(Tempa::class, 'id_tempa', 'id_tempa');
    }
}
