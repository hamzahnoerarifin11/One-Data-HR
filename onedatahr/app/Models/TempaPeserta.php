<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class TempaPeserta extends Model {
    protected $table = 'tempa_peserta';
    protected $primaryKey = 'id_peserta';
    protected $guarded = [];

    protected $fillable = [
        'id_tempa',
        'id_kelompok',
        'status_peserta',
        'nama_peserta',
        'nik_karyawan',
        'mentor_id',
        'unit',
        'shift'
    ];

    protected static function booted()
    {
        static::addGlobalScope('ketuaTempaScope', function (Builder $builder) {
            $user = auth()->user();
            if ($user && $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin'])) {
                $builder->where('mentor_id', $user->id);
            }
        });
    }

    public function kelompok() {
        return $this->belongsTo(TempaKelompok::class, 'id_kelompok');
    }

    public function mentor() {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function absensi() {
        return $this->hasMany(TempaAbsensi::class, 'id_peserta', 'id_peserta');
    }

    /* =====================
     | ACCESSOR
     ===================== */

    public function getTotalHadirAttribute()
    {
        return $this->absensi->where('status_hadir', 1)->count();
    }

    public function getTotalPertemuanAttribute()
    {
        return $this->absensi->count();
    }

    public function getPersentaseKehadiranAttribute()
    {
        $totalPertemuan = $this->total_pertemuan;
        if ($totalPertemuan == 0) return 0;

        return round(($this->total_hadir / $totalPertemuan) * 100, 2);
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status_peserta) {
            0 => 'Tidak Aktif',
            1 => 'Aktif',
            2 => 'Tidak Aktif Sementara',
            default => 'Unknown'
        };
    }
}
