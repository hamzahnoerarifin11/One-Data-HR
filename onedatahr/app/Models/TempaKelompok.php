<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempaKelompok extends Model
{
    use HasFactory;

    protected $table = 'tempa_kelompok';
    protected $primaryKey = 'id_kelompok';

    protected $fillable = [
        'id_tempa',
        'nama_kelompok',
        'nama_mentor',
        'ketua_tempa_id'
    ];

    /* =====================
     | RELATIONSHIP
     ===================== */

    public function tempa()
    {
        return $this->belongsTo(Tempa::class, 'id_tempa');
    }

    /**
     * Ketua TEMPA yang mengelola kelompok ini
     */
    public function ketuaTempa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ketua_tempa_id');
    }

    /**
     * Peserta dalam kelompok ini
     */
    public function pesertas(): HasMany
    {
        return $this->hasMany(TempaPeserta::class, 'id_kelompok', 'id_kelompok');
    }
}
