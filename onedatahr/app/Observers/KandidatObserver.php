<?php

namespace App\Observers;

use App\Models\Kandidat;
use App\Models\Posisi;
use App\Models\RekrutmenDaily;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KandidatObserver
{
    public function saving(Kandidat $kandidat)
    {
        // Jika status_akhir berubah, isi kolom tanggal yang sesuai
        if ($kandidat->isDirty('status_akhir')) {
            $status = $kandidat->status_akhir;
            $today = Carbon::today()->format('Y-m-d');

            $mapStatusTanggal = [
                'CV Lolos'               => 'tgl_lolos_cv',
                'Psikotes Lolos'         => 'tgl_lolos_psikotes',
                'Tes Kompetensi Lolos'   => 'tgl_lolos_kompetensi',
                'Interview HR Lolos'     => 'tgl_lolos_hr',
                'Interview User Lolos'   => 'tgl_lolos_user',
            ];

            if (isset($mapStatusTanggal[$status])) {
                $kolomTgl = $mapStatusTanggal[$status];
                // Isi tanggal otomatis hanya jika belum ada isinya (mencegah overwrite tanggal lama)
                if (empty($kandidat->$kolomTgl)) {
                    $kandidat->$kolomTgl = $today;
                }
            }
        }
    }
    public function saved(Kandidat $kandidat)
    {
        $this->refreshPosisiProgress($kandidat->posisi_id);
        $this->syncDailyAllStages($kandidat);
    }

    public function deleted(Kandidat $kandidat)
    {
        $this->refreshPosisiProgress($kandidat->posisi_id);
        $this->syncDailyAllStages($kandidat);
    }

    public function refreshPosisiProgress($posisiId)
    {
        $posisi = Posisi::find($posisiId);
        if (!$posisi) return;

        $posisi = $posisi->fresh();

        // PERBAIKAN: Tambahkan filter agar 'Tidak Lolos' tidak terhitung sebagai pelamar aktif
        $totalPelamarAktif = Kandidat::where('posisi_id', $posisiId)
            ->where('status_akhir', '!=', 'Tidak Lolos')
            ->count();

        // Bandingkan status posisi
        if (trim($posisi->status) === 'Nonaktif') {
            $posisi->updateQuietly([
                'total_pelamar'      => $totalPelamarAktif, // Update dengan jumlah yang aktif
                'progress_rekrutmen' => 'Tidak Menerima Kandidat'
            ]);
            return;
        }

        // Ambil status kandidat yang aktif saja
        $kandidatAktif = Kandidat::where('posisi_id', $posisiId)
            ->where('status_akhir', '!=', 'Tidak Lolos')
            ->pluck('status_akhir')
            ->toArray();

        $progress = 'Menerima Kandidat';

        // Logika penentuan label progress
        if ($totalPelamarAktif > 0) {
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
        } else {
            // Jika tidak ada pelamar aktif sama sekali (semua tidak lolos atau belum ada pelamar)
            $progress = 'Menerima Kandidat';
        }

        $posisi->updateQuietly([
            'total_pelamar'      => $totalPelamarAktif,
            'progress_rekrutmen' => $progress
        ]);
    }

    private function syncDailyAllStages(Kandidat $kandidat)
    {
        $posisiId = $kandidat->posisi_id;

        // Daftar kolom tanggal dan pasangannya di tabel daily
        $tahapan = [
            'tgl_lolos_cv'         => 'lolos_cv',
            'tgl_lolos_psikotes'   => 'lolos_psikotes',
            'tgl_lolos_kompetensi' => 'lolos_kompetensi',
            'tgl_lolos_hr'         => 'lolos_hr',
            'tgl_lolos_user'       => 'lolos_user',
        ];

        foreach ($tahapan as $kolomTglKandidat => $kolomDaily) {
            $tanggalStatus = $kandidat->$kolomTglKandidat;

            if ($tanggalStatus) {
                // Hitung total kandidat untuk posisi ini, di tanggal tersebut, untuk tahap tersebut
                $count = Kandidat::where('posisi_id', $posisiId)
                    ->where($kolomTglKandidat, $tanggalStatus)
                    ->count();

                RekrutmenDaily::updateOrCreate(
                    ['posisi_id' => $posisiId, 'date' => $tanggalStatus],
                    [$kolomDaily => $count]
                );
            }
        }
    }
}
