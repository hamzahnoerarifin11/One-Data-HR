<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempaAbsensi extends Model
{
    use HasFactory;

    protected $table = 'tempa_absensi';
    protected $primaryKey = 'id_absensi';

    protected $fillable = [
        'id_peserta',
        'tahun_absensi',
        'absensi_data',
        // Kolom kalkulasi
        'total_hadir',
        'total_pertemuan',
        'persentase',
        'bulan',
        'tahun',
        'pertemuan_ke',
        'tanggal',
        'status_hadir',
        'bukti_foto',
        'created_by'
    ];

    protected $casts = [
        'absensi_data' => 'array',
    ];

    /**
     * Get absensi data for a specific month
     */
    public function getAbsensiBulan($bulan)
    {
        $bulanMap = [
            1 => 'jan', 2 => 'feb', 3 => 'mar', 4 => 'apr', 5 => 'mei', 6 => 'jun',
            7 => 'jul', 8 => 'agu', 9 => 'sep', 10 => 'okt', 11 => 'nov', 12 => 'des'
        ];

        $key = $bulanMap[$bulan] ?? null;
        return $this->absensi_data[$key] ?? [null, null, null, null, null];
    }

    /**
     * Set absensi data for a specific month
     */
    public function setAbsensiBulan($bulan, $data)
    {
        $bulanMap = [
            1 => 'jan', 2 => 'feb', 3 => 'mar', 4 => 'apr', 5 => 'mei', 6 => 'jun',
            7 => 'jul', 8 => 'agu', 9 => 'sep', 10 => 'okt', 11 => 'nov', 12 => 'des'
        ];

        $key = $bulanMap[$bulan] ?? null;
        if ($key) {
            $absensiData = $this->absensi_data ?? [];
            $absensiData[$key] = $data;
            $this->absensi_data = $absensiData;
        }
    }

    /**
     * Calculate total hadir and total pertemuan
     */
    public function calculateTotals()
    {
        $totalHadir = 0;
        $totalPertemuan = 0;

        if ($this->absensi_data) {
            foreach ($this->absensi_data as $bulan => $minggu) {
                foreach ($minggu as $status) {
                    if ($status === 'hadir') {
                        $totalHadir++;
                        $totalPertemuan++;
                    } elseif ($status === 'tidak_hadir') {
                        $totalPertemuan++;
                    }
                    // null = tidak ada pertemuan, skip
                }
            }
        }

        $this->total_hadir = $totalHadir;
        $this->total_pertemuan = $totalPertemuan;
        $this->persentase = $totalPertemuan > 0 ? round(($totalHadir / $totalPertemuan) * 100, 2) : 0;
    }

    public function peserta()
    {
        return $this->belongsTo(
            TempaPeserta::class,
            'id_peserta',
            'id_peserta'
        );
    }
}
