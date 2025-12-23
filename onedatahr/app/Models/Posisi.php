<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posisi extends Model
{
    use HasFactory;

    protected $table = 'posisi';
    protected $primaryKey = 'id_posisi'; // Sesuai database
    public $incrementing = true;
    protected $keyType = 'int';

    // Kolom yang boleh diisi secara mass-assignment
    protected $fillable = [
        'nama_posisi',
        'status'
    ];

    /* ===================== RELATIONSHIPS ===================== */

    public function kandidat()
    {
        return $this->hasMany(Kandidat::class, 'posisi_id', 'id_posisi');
    }

    public function wigRekrutmen()
    {
        return $this->hasOne(WigRekrutmen::class, 'posisi_id', 'id_posisi');
    }

    public function rekrutmenDaily()
    {
        return $this->hasMany(RekrutmenDaily::class, 'posisi_id', 'id_posisi');
    }

    /* ===================== SCOPES ===================== */

    public function scopeAktif($query)
    {
        return $query->where('status', 'Aktif');
    }
}
