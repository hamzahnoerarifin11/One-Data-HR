<?php

namespace App\Observers;

use App\Models\Kandidat;
use App\Models\Posisi;
use App\Models\RekrutmenDaily;
use Illuminate\Support\Facades\DB;

class KandidatObserver
{
    public function saved(Kandidat $kandidat)
    {
        $this->refreshPosisiProgress($kandidat->posisi_id);
        $this->syncStatusOnly($kandidat->posisi_id, $kandidat->tanggal_melamar);
    }

    public function deleted(Kandidat $kandidat)
    {
        $this->refreshPosisiProgress($kandidat->posisi_id);
        $this->syncStatusOnly($kandidat->posisi_id, $kandidat->tanggal_melamar);
    }

    public function refreshPosisiProgress($posisiId)
    {
        // Menggunakan find dan fresh() untuk memastikan data paling update dari DB
        $posisi = Posisi::find($posisiId);
        if (!$posisi) return;

        $posisi = $posisi->fresh();

        $totalPelamar = Kandidat::where('posisi_id', $posisiId)->count();

        // Bandingkan langsung dengan 'Nonaktif' sesuai isi database Anda
        if (trim($posisi->status) === 'Nonaktif') {
            $posisi->updateQuietly([
                'total_pelamar'      => $totalPelamar,
                'progress_rekrutmen' => 'Tidak Menerima Kandidat'
            ]);
            return; // Penting: Jangan biarkan kode di bawah menimpa status ini
        }

        // Ambil status kandidat yang aktif (Bukan 'Tidak Lolos')
        $kandidatAktif = Kandidat::where('posisi_id', $posisiId)
            ->where('status_akhir', '!=', 'Tidak Lolos')
            ->pluck('status_akhir')
            ->toArray();

        $progress = 'Menerima Kandidat';

        if ($totalPelamar > 0) {
            if (in_array('Diterima', $kandidatAktif)) {
                $progress = 'Rekrutmen Selesai';
            }
            elseif (in_array('Interview User Lolos', $kandidatAktif)) {
                $progress = 'Pemberkasan';
            }
            elseif (in_array('Interview HR Lolos', $kandidatAktif)) {
                $progress = 'Interview User';
            }
            elseif (in_array('CV Lolos', $kandidatAktif)) {
                $progress = 'Interview HR';
            }
            elseif (array_intersect(['Psikotes Lolos', 'Tes Kompetensi Lolos'], $kandidatAktif)) {
                $progress = 'Pre Interview';
            }
        }

        $posisi->updateQuietly([
            'total_pelamar'      => $totalPelamar,
            'progress_rekrutmen' => $progress
        ]);
    }
    // private function syncStatusOnly($posisiId, $date)
    // {
    //     if (!$posisiId || !$date) return;

    //     // Hitung statistik status saja
    //     $stats = Kandidat::where('posisi_id', $posisiId)
    //         ->whereDate('tanggal_melamar', $date)
    //         ->selectRaw("
    //             SUM(CASE WHEN status_akhir = 'CV Lolos' THEN 1 ELSE 0 END) as cv,
    //             SUM(CASE WHEN status_akhir = 'Psikotes Lolos' THEN 1 ELSE 0 END) as psikotes,
    //             SUM(CASE WHEN status_akhir = 'Tes Kompetensi Lolos' THEN 1 ELSE 0 END) as kompetensi,
    //             SUM(CASE WHEN status_akhir = 'Interview HR Lolos' THEN 1 ELSE 0 END) as hr,
    //             SUM(CASE WHEN status_akhir = 'Interview User Lolos' THEN 1 ELSE 0 END) as user
    //         ")
    //         ->first();

    //     // Update HANYA kolom status.
    //     // Jika data harian belum ada (misal blm diinput manual), maka tidak akan terjadi apa-apa.
    //     RekrutmenDaily::where('posisi_id', $posisiId)
    //         ->where('date', $date)
    //         ->update([
    //             'lolos_cv'         => $stats->cv ?? 0,
    //             'lolos_psikotes'   => $stats->psikotes ?? 0,
    //             'lolos_kompetensi' => $stats->kompetensi ?? 0,
    //             'lolos_hr'         => $stats->hr ?? 0,
    //             'lolos_user'       => $stats->user ?? 0,
    //         ]);
    // }
    // KandidatObserver.php

// private function syncStatusOnly($posisiId, $date)
// {
//     if (!$posisiId || !$date) return;

//     $stats = Kandidat::where('posisi_id', $posisiId)
//         ->whereDate('tanggal_melamar', $date)
//         ->selectRaw("
//             SUM(CASE WHEN status_akhir = 'CV Lolos' THEN 1 ELSE 0 END) as cv,
//             SUM(CASE WHEN status_akhir = 'Psikotes Lolos' THEN 1 ELSE 0 END) as psikotes,
//             SUM(CASE WHEN status_akhir = 'Tes Kompetensi Lolos' THEN 1 ELSE 0 END) as kompetensi,
//             SUM(CASE WHEN status_akhir = 'Interview HR Lolos' THEN 1 ELSE 0 END) as hr,
//             SUM(CASE WHEN status_akhir = 'Interview User Lolos' THEN 1 ELSE 0 END) as user
//         ")
//         ->first();

//     // Gunakan updateOrCreate:
//     // Jika admin belum pernah input 'total_pelamar' manual untuk tanggal tersebut,
//     // kita buatkan row-nya dengan total_pelamar = 0.
//     RekrutmenDaily::updateOrCreate(
//         ['posisi_id' => $posisiId, 'date' => $date],
//         [
//             'lolos_cv'         => $stats->cv ?? 0,
//             'lolos_psikotes'   => $stats->psikotes ?? 0,
//             'lolos_kompetensi' => $stats->kompetensi ?? 0,
//             'lolos_hr'         => $stats->hr ?? 0,
//             'lolos_user'       => $stats->user ?? 0,
//         ]
//     );
// }
// Di dalam KandidatObserver.php
private function syncStatusOnly($posisiId, $date)
{
    if (!$posisiId || !$date) return;

    $stats = Kandidat::where('posisi_id', $posisiId)
        ->whereDate('tanggal_melamar', $date)
        // ... (query SUM CASE sama seperti di atas) ...
        ->first();

    // GUNAKAN updateOrCreate agar data TERBUAT di database jika belum ada
    RekrutmenDaily::updateOrCreate(
        ['posisi_id' => $posisiId, 'date' => $date],
        [
            'lolos_cv'         => $stats->cv ?? 0,
            'lolos_psikotes'   => $stats->psikotes ?? 0,
            'lolos_kompetensi' => $stats->kompetensi ?? 0,
            'lolos_hr'         => $stats->hr ?? 0,
            'lolos_user'       => $stats->user ?? 0,
        ]
    );
}
}
