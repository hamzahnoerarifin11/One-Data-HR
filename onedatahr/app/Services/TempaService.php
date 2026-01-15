<?php

namespace App\Services;

use App\Models\TempaPeserta;
use App\Models\TempaAbsensi;

class TempaService
{
    public function hitungKehadiranPeserta($pesertaId)
    {
        $absensis = TempaAbsensi::where('id_peserta', $pesertaId)->get();
        $totalKehadiran = $absensis->where('status_hadir', 1)->count();
        $totalPertemuan = $absensis->count();
        $persentase = $totalPertemuan > 0 ? ($totalKehadiran / $totalPertemuan) * 100 : 0;

        return [
            'total_kehadiran' => $totalKehadiran,
            'total_pertemuan' => $totalPertemuan,
            'persentase' => round($persentase, 2),
        ];
    }

    /**
     * Hitung persentase kehadiran kelompok berdasarkan mentor_id
     */
    public function hitungPersentaseKelompok($mentorId)
    {
        $pesertas = TempaPeserta::where('mentor_id', $mentorId)
            ->where('status_peserta', 1) // Hanya yang aktif
            ->with('absensi')
            ->get();

        if ($pesertas->isEmpty()) return 0;

        $totalPersentase = 0;
        foreach ($pesertas as $peserta) {
            $totalPersentase += $peserta->persentase_kehadiran;
        }

        return round($totalPersentase / $pesertas->count(), 2);
    }

    /**
     * Hitung persentase kehadiran nasional (untuk admin/superadmin)
     */
    public function hitungPersentaseNasional()
    {
        $pesertas = TempaPeserta::where('status_peserta', 1)
            ->with('absensi')
            ->get();

        if ($pesertas->isEmpty()) return 0;

        $totalPersentase = 0;
        foreach ($pesertas as $peserta) {
            $totalPersentase += $peserta->persentase_kehadiran;
        }

        return round($totalPersentase / $pesertas->count(), 2);
    }

    /**
     * Hitung total pertemuan dalam setahun (12 bulan x 5 pertemuan = 60)
     */
    public function getTotalPertemuanTahunan()
    {
        return 12 * 5; // 60 pertemuan
    }

    /**
     * Hitung statistik kelompok untuk monitoring
     */
    public function getStatistikKelompok()
    {
        $statistikKelompok = [];
        $kelompoks = \App\Models\TempaKelompok::with('pesertas')->get();

        foreach ($kelompoks as $kelompok) {
            $mentor = $kelompok->pesertas->first()?->mentor;
            if ($mentor) {
                $pesertaAktif = $kelompok->pesertas->where('status_peserta', 1);
                $statistikKelompok[] = [
                    'nama_kelompok' => $kelompok->nama_kelompok,
                    'nama_mentor' => $mentor->name,
                    'jumlah_peserta_aktif' => $pesertaAktif->count(),
                    'total_peserta' => $kelompok->pesertas->count(),
                    'persentase' => $this->hitungPersentaseKelompok($mentor->id),
                ];
            }
        }

        return $statistikKelompok;
    }
}
