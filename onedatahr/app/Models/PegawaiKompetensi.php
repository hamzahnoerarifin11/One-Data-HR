<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PegawaiKompetensi extends Model
{
    use HasFactory;

    protected $table = 'pegawai_kompetensi';
    protected $primaryKey = 'id_peg_komp';

    protected $fillable = [
        'karyawan_id',
        'nama_kompetensi',
        'level',
        'sumber',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }
}
