<?php

namespace App\Services;

use App\Models\TempaPeserta;
use App\Models\TempaAbsensi;

class TempaAttendanceService
{
    /**
     * Menghitung kehadiran per peserta
     */
    public function calculateIndividual($pesertaId)
    {
        $totalHadir = TempaAbsensi::where('id_peserta', $pesertaId)->where('status_hadir', 1)->count();
        $totalPertemuan = TempaAbsensi::where('id_peserta', $pesertaId)->count();

        $persen = $totalPertemuan > 0 ? ($totalHadir / $totalPertemuan) * 100 : 0;

        return [
            'total_hadir' => $totalHadir,
            'persentase' => round($persen, 2)
        ];
    }

    /**
     * Menghitung rata-rata kehadiran kelompok
     */
    public function calculateGroupAverage($idKelompok)
    {
        $pesertaIds = TempaPeserta::where('id_kelompok', $idKelompok)->pluck('id_peserta');

        $totalHadir = TempaAbsensi::whereIn('id_peserta', $pesertaIds)->where('status_hadir', 1)->count();
        $totalData = TempaAbsensi::whereIn('id_peserta', $pesertaIds)->count();

        return $totalData > 0 ? round(($totalHadir / $totalData) * 100, 2) : 0;
    }
}
