<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Posisi;
use App\Models\Kandidat;

class SyncPosisiProgress extends Command
{
    // Nama perintah yang akan dijalankan di terminal
    protected $signature = 'rekrutmen:sync-posisi';
    protected $description = 'Sinkronisasi ulang total pelamar aktif dan progress rekrutmen pada tabel posisi';

    public function handle()
    {
        $this->info('Memulai sinkronisasi data posisi...');
        $posisis = Posisi::all();

        $bar = $this->output->createProgressBar(count($posisis));
        $bar->start();

        foreach ($posisis as $posisi) {
            // 1. Hitung pelamar yang statusnya BUKAN 'Tidak Lolos'
            $totalPelamarAktif = Kandidat::where('posisi_id', $posisi->id_posisi)
                ->where('status_akhir', '!=', 'Tidak Lolos')
                ->count();

            // 2. Ambil list status kandidat aktif untuk menentukan progress
            $kandidatAktif = Kandidat::where('posisi_id', $posisi->id_posisi)
                ->where('status_akhir', '!=', 'Tidak Lolos')
                ->pluck('status_akhir')
                ->toArray();

            $progress = 'Menerima Kandidat';

            if (trim($posisi->status) === 'Nonaktif') {
                $progress = 'Tidak Menerima Kandidat';
            } elseif ($totalPelamarAktif > 0) {
                if (in_array('Diterima', $kandidatAktif)) {
                    $progress = 'Rekrutmen Selesai';
                } elseif (in_array('Interview User Lolos', $kandidatAktif)) {
                    $progress = 'Pemberkasan';
                } elseif (in_array('Interview HR Lolos', $kandidatAktif)) {
                    $progress = 'Interview User';
                } elseif (in_array('CV Lolos', $kandidatAktif)) {
                    $progress = 'Interview HR';
                } elseif (array_intersect(['Psikotes Lolos', 'Tes Kompetensi Lolos'], $kandidatAktif)) {
                    $progress = 'Pre Interview';
                }
            }

            // 3. Update database secara langsung
            $posisi->updateQuietly([
                'total_pelamar' => $totalPelamarAktif,
                'progress_rekrutmen' => $progress
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Sinkronisasi selesai! Semua data posisi telah diperbarui.');
    }
}
