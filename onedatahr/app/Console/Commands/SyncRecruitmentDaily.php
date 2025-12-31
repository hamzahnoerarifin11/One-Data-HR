<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kandidat;
use App\Models\RekrutmenDaily;
use Illuminate\Support\Facades\DB;

class SyncRecruitmentDaily extends Command
{
    // Nama command yang akan dipanggil di terminal
    protected $signature = 'rekrutmen:sync-daily';
    protected $description = 'Sinkronisasi data rekrutmen_daily dari data kandidat yang sudah ada';

//    public function handle()
//     {
//         $this->info('Sinkronisasi kolom status (Total Pelamar tidak akan berubah)...');

//         $groups = Kandidat::select('posisi_id', 'tanggal_melamar')
//             ->whereNotNull('tanggal_melamar')
//             ->groupBy('posisi_id', 'tanggal_melamar')
//             ->get();

//         foreach ($groups as $group) {
//             $stats = Kandidat::where('posisi_id', $group->posisi_id)
//                 ->whereDate('tanggal_melamar', $group->tanggal_melamar)
//                 ->selectRaw("
//                     SUM(CASE WHEN status_akhir = 'CV Lolos' THEN 1 ELSE 0 END) as cv,
//                     SUM(CASE WHEN status_akhir = 'Psikotes Lolos' THEN 1 ELSE 0 END) as psikotes,
//                     SUM(CASE WHEN status_akhir = 'Tes Kompetensi Lolos' THEN 1 ELSE 0 END) as kompetensi,
//                     SUM(CASE WHEN status_akhir = 'Interview HR Lolos' THEN 1 ELSE 0 END) as hr,
//                     SUM(CASE WHEN status_akhir = 'Interview User Lolos' THEN 1 ELSE 0 END) as user
//                 ")
//                 ->first();

//             // Menggunakan update() agar total_pelamar yang sudah diinput manual aman
//             RekrutmenDaily::where('posisi_id', $group->posisi_id)
//                 ->where('date', $group->tanggal_melamar)
//                 ->update([
//                     'lolos_cv'         => $stats->cv ?? 0,
//                     'lolos_psikotes'   => $stats->psikotes ?? 0,
//                     'lolos_kompetensi' => $stats->kompetensi ?? 0,
//                     'lolos_hr'         => $stats->hr ?? 0,
//                     'lolos_user'       => $stats->user ?? 0,
//                 ]);
//         }
//         $this->info("Selesai!");
//     }
// SyncRecruitmentDaily.php

public function handle()
{
    $this->info('Memulai sinkronisasi status kandidat ke data harian...');

    // Ambil semua kombinasi posisi dan tanggal yang ada di tabel kandidat
    $groups = Kandidat::select('posisi_id', 'tanggal_melamar')
        ->whereNotNull('tanggal_melamar')
        ->groupBy('posisi_id', 'tanggal_melamar')
        ->get();

    foreach ($groups as $group) {
        $stats = Kandidat::where('posisi_id', $group->posisi_id)
            ->whereDate('tanggal_melamar', $group->tanggal_melamar)
            ->selectRaw("
                SUM(CASE WHEN status_akhir = 'CV Lolos' THEN 1 ELSE 0 END) as cv,
                SUM(CASE WHEN status_akhir = 'Psikotes Lolos' THEN 1 ELSE 0 END) as psikotes,
                SUM(CASE WHEN status_akhir = 'Tes Kompetensi Lolos' THEN 1 ELSE 0 END) as kompetensi,
                SUM(CASE WHEN status_akhir = 'Interview HR Lolos' THEN 1 ELSE 0 END) as hr,
                SUM(CASE WHEN status_akhir = 'Interview User Lolos' THEN 1 ELSE 0 END) as user
            ")
            ->first();

        RekrutmenDaily::updateOrCreate(
            ['posisi_id' => $group->posisi_id, 'date' => $group->tanggal_melamar],
            [
                'lolos_cv'         => $stats->cv ?? 0,
                'lolos_psikotes'   => $stats->psikotes ?? 0,
                'lolos_kompetensi' => $stats->kompetensi ?? 0,
                'lolos_hr'         => $stats->hr ?? 0,
                'lolos_user'       => $stats->user ?? 0,
            ]
        );
    }
    $this->info("Sinkronisasi Selesai!");
}
}
