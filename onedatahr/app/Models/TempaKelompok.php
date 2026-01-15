<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TempaKelompok extends Model
{
    use HasFactory;

    protected $table = 'tempa_kelompok';
    protected $primaryKey = 'id_kelompok';

    protected $fillable = [
        'id_tempa',
        'nama_kelompok',
        'nama_mentor'
    ];

    /* =====================
     | RELATIONSHIP
     ===================== */

    public function tempa()
    {
        return $this->belongsTo(Tempa::class, 'id_tempa');
    }

    // public function peserta()
    // {
    //     return $this->hasMany(TempaPeserta::class, 'id_kelompok');
    // }
    public function pesertas(): HasMany
    {
        // Adjust 'id_kelompok' to match your actual foreign key in the tempa_peserta table
        return $this->hasMany(TempaPeserta::class, 'id_kelompok', 'id');
    }
}
