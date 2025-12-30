<?php

namespace App\Observers;

use App\Models\Kandidat;
use App\Models\Posisi;

class KandidatObserver
{
    // Fungsi bantuan untuk menghitung ulang progres
    private function refreshPosisiProgress($posisiId)
    {
        $posisi = Posisi::find($posisiId);
        if (!$posisi) return;

        $kandidats = Kandidat::where('posisi_id', $posisiId)
            ->where('status_akhir', '!=', 'Tidak Lolos')
            ->get();

        $totalPelamar = Kandidat::where('posisi_id', $posisiId)->count();

        // LOGIKA UTAMA ANDA DI SINI
        if ($totalPelamar === 0) {
            $progress = 'Menerima Kandidat';
        } elseif ($kandidats->whereIn('status_akhir', ['Interview User Lolos', 'Diterima'])->count() > 0) {
            $progress = 'Pemberkasan';
        } elseif ($kandidats->where('status_akhir', 'Interview HR Lolos')->count() > 0) {
            $progress = 'Interview User';
        } elseif ($kandidats->whereIn('status_akhir', ['CV Lolos'])->count() > 0) {
            $progress = 'Interview HR';
        } else {
            $progress = 'Pre Interview';
        }

        // Simpan ke tabel fisik
        $posisi->update([
            'total_pelamar' => $totalPelamar,
            'progress_rekrutmen' => $progress
        ]);
    }

    public function created(Kandidat $kandidat) {
        $this->refreshPosisiProgress($kandidat->posisi_id);
    }

    public function updated(Kandidat $kandidat) {
        $this->refreshPosisiProgress($kandidat->posisi_id);
    }

    public function deleted(Kandidat $kandidat) {
        $this->refreshPosisiProgress($kandidat->posisi_id);
    }
}
