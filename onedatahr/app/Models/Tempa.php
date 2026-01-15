<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tempa extends Model
{
    use HasFactory;

    protected $table = 'tempa';
    protected $primaryKey = 'id_tempa';

    protected $fillable = [
        'jenis_tempa',
        'cabang',
        'nama_tempa',
        'periode',
        'jumlah_pertemuan',
        'created_by'
    ];

    /* =====================
     | RELATIONSHIP
     ===================== */

    public function kelompok()
    {
        return $this->hasMany(TempaKelompok::class, 'id_tempa');
    }

    public function peserta()
    {
        return $this->hasMany(TempaPeserta::class, 'id_tempa');
    }

    public function materi()
    {
        return $this->hasMany(TempaMateri::class, 'id_tempa');
    }
}
