<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


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
    'status',
    'progress_rekrutmen',
    'total_pelamar',
    'activated_at'
    ];

    // Kolom yang selalu disertakan saat serialize
    protected $appends = ['hari_aktif'];

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

    /* ===================== ACCESSORS ===================== */

    public function getHariAktifAttribute()
    {
        if (!$this->activated_at) {
            return 0;
        }

        return Carbon::parse($this->activated_at)
            ->startOfDay()
            ->diffInDays(now()->startOfDay());
    }
}
