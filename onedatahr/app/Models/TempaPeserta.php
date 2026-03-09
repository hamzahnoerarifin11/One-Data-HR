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
        'mentor_id',
        'tempat',
        'keterangan_cabang'
    ];

    public function kelompok()
    {
        return $this->belongsTo(
            \App\Models\TempaKelompok::class,
            'id_kelompok',
            'id_kelompok'
        );
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function absensi()
    {
        return $this->hasMany(\App\Models\TempaAbsensi::class, 'id_peserta', 'id_peserta');
    }

    // Accessor untuk status label
    public function getStatusLabelAttribute()
    {
        return match($this->status_peserta) {
            1 => 'Aktif',
            2 => 'Pindah',
            3 => 'Keluar',
            default => 'Tidak Diketahui'
        };
    }

    // Accessor untuk persentase kehadiran
    public function getPersentaseKehadiranAttribute()
    {
        $absensis = $this->absensi;

        if ($absensis->isEmpty()) {
            return 0;
        }

        // Jika ada data total_hadir dan total_pertemuan di tabel absensi, gunakan itu
        $latestAbsensi = $absensis->first();
        if ($latestAbsensi && isset($latestAbsensi->total_hadir) && isset($latestAbsensi->total_pertemuan)) {
            $totalHadir = $latestAbsensi->total_hadir;
            $totalPertemuan = $latestAbsensi->total_pertemuan;
        } else {
            // Hitung dari status_hadir per record
            $totalHadir = $absensis->where('status_hadir', '1')->count();
            $totalPertemuan = $absensis->count();
        }

        return $totalPertemuan > 0 ? round(($totalHadir / $totalPertemuan) * 100, 1) : 0;
    }

    // Accessor untuk total hadir
    public function getTotalHadirAttribute()
    {
        $absensis = $this->absensi;

        if ($absensis->isEmpty()) {
            return 0;
        }

        // Jika ada data total_hadir di tabel absensi, gunakan itu
        $latestAbsensi = $absensis->first();
        if ($latestAbsensi && isset($latestAbsensi->total_hadir)) {
            return $latestAbsensi->total_hadir;
        } else {
            // Hitung dari status_hadir per record
            return $absensis->where('status_hadir', '1')->count();
        }
    }

    // Accessor untuk total pertemuan
    public function getTotalPertemuanAttribute()
    {
        $absensis = $this->absensi;

        if ($absensis->isEmpty()) {
            return 0;
        }

        // Jika ada data total_pertemuan di tabel absensi, gunakan itu
        $latestAbsensi = $absensis->first();
        if ($latestAbsensi && isset($latestAbsensi->total_pertemuan)) {
            return $latestAbsensi->total_pertemuan;
        } else {
            // Hitung dari jumlah record
            return $absensis->count();
        }
    }
}
