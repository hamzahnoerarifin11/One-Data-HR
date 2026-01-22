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
        'nama_kelompok',
        'nama_mentor',
        'ketua_tempa_id',
        'mentor_id',
        'tempat',
        'keterangan_cabang',
        'created_by_tempa'
    ];

    /* =====================
     | RELATIONSHIP
     ===================== */

    /**
     * Ketua TEMPA yang mengelola kelompok ini
     */
    public function ketuaTempa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ketua_tempa_id');
    }

    /**
     * Mentor
     */
    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    /**
     * Peserta dalam kelompok ini
     */
    public function pesertas(): HasMany
    {
        return $this->hasMany(TempaPeserta::class, 'id_kelompok', 'id_kelompok');
    }
}
