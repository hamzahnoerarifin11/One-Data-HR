<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TempaAbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tahun = 2026;

        // Data absensi berdasarkan pola Excel
        $absensiData = [
            // Nashrul Ihsan (ID: 1) - 51 kehadiran
            ['id_peserta' => 1, 'bulan' => 1, 'tahun' => $tahun, 'pertemuan_ke' => 1, 'status_hadir' => 1, 'tanggal' => '2026-01-05'],
            ['id_peserta' => 1, 'bulan' => 1, 'tahun' => $tahun, 'pertemuan_ke' => 2, 'status_hadir' => 1, 'tanggal' => '2026-01-12'],
            ['id_peserta' => 1, 'bulan' => 1, 'tahun' => $tahun, 'pertemuan_ke' => 3, 'status_hadir' => 1, 'tanggal' => '2026-01-19'],
            ['id_peserta' => 1, 'bulan' => 1, 'tahun' => $tahun, 'pertemuan_ke' => 4, 'status_hadir' => 1, 'tanggal' => '2026-01-26'],
            ['id_peserta' => 1, 'bulan' => 1, 'tahun' => $tahun, 'pertemuan_ke' => 5, 'status_hadir' => 1, 'tanggal' => '2026-01-30'],

            // Februari - lengkap
            ['id_peserta' => 1, 'bulan' => 2, 'tahun' => $tahun, 'pertemuan_ke' => 1, 'status_hadir' => 1, 'tanggal' => '2026-02-05'],
            ['id_peserta' => 1, 'bulan' => 2, 'tahun' => $tahun, 'pertemuan_ke' => 2, 'status_hadir' => 1, 'tanggal' => '2026-02-12'],
            ['id_peserta' => 1, 'bulan' => 2, 'tahun' => $tahun, 'pertemuan_ke' => 3, 'status_hadir' => 1, 'tanggal' => '2026-02-19'],
            ['id_peserta' => 1, 'bulan' => 2, 'tahun' => $tahun, 'pertemuan_ke' => 4, 'status_hadir' => 1, 'tanggal' => '2026-02-26'],
            ['id_peserta' => 1, 'bulan' => 2, 'tahun' => $tahun, 'pertemuan_ke' => 5, 'status_hadir' => 1, 'tanggal' => '2026-02-28'],

            // Maret - lengkap
            ['id_peserta' => 1, 'bulan' => 3, 'tahun' => $tahun, 'pertemuan_ke' => 1, 'status_hadir' => 1, 'tanggal' => '2026-03-05'],
            ['id_peserta' => 1, 'bulan' => 3, 'tahun' => $tahun, 'pertemuan_ke' => 2, 'status_hadir' => 1, 'tanggal' => '2026-03-12'],
            ['id_peserta' => 1, 'bulan' => 3, 'tahun' => $tahun, 'pertemuan_ke' => 3, 'status_hadir' => 1, 'tanggal' => '2026-03-19'],
            ['id_peserta' => 1, 'bulan' => 3, 'tahun' => $tahun, 'pertemuan_ke' => 4, 'status_hadir' => 1, 'tanggal' => '2026-03-26'],
            ['id_peserta' => 1, 'bulan' => 3, 'tahun' => $tahun, 'pertemuan_ke' => 5, 'status_hadir' => 1, 'tanggal' => '2026-03-31'],

            // April - lengkap
            ['id_peserta' => 1, 'bulan' => 4, 'tahun' => $tahun, 'pertemuan_ke' => 1, 'status_hadir' => 1, 'tanggal' => '2026-04-05'],
            ['id_peserta' => 1, 'bulan' => 4, 'tahun' => $tahun, 'pertemuan_ke' => 2, 'status_hadir' => 1, 'tanggal' => '2026-04-12'],
            ['id_peserta' => 1, 'bulan' => 4, 'tahun' => $tahun, 'pertemuan_ke' => 3, 'status_hadir' => 1, 'tanggal' => '2026-04-19'],
            ['id_peserta' => 1, 'bulan' => 4, 'tahun' => $tahun, 'pertemuan_ke' => 4, 'status_hadir' => 1, 'tanggal' => '2026-04-26'],
            ['id_peserta' => 1, 'bulan' => 4, 'tahun' => $tahun, 'pertemuan_ke' => 5, 'status_hadir' => 1, 'tanggal' => '2026-04-30'],

            // Mei - lengkap
            ['id_peserta' => 1, 'bulan' => 5, 'tahun' => $tahun, 'pertemuan_ke' => 1, 'status_hadir' => 1, 'tanggal' => '2026-05-05'],
            ['id_peserta' => 1, 'bulan' => 5, 'tahun' => $tahun, 'pertemuan_ke' => 2, 'status_hadir' => 1, 'tanggal' => '2026-05-12'],
            ['id_peserta' => 1, 'bulan' => 5, 'tahun' => $tahun, 'pertemuan_ke' => 3, 'status_hadir' => 1, 'tanggal' => '2026-05-19'],
            ['id_peserta' => 1, 'bulan' => 5, 'tahun' => $tahun, 'pertemuan_ke' => 4, 'status_hadir' => 1, 'tanggal' => '2026-05-26'],
            ['id_peserta' => 1, 'bulan' => 5, 'tahun' => $tahun, 'pertemuan_ke' => 5, 'status_hadir' => 1, 'tanggal' => '2026-05-31'],

            // Juni - lengkap
            ['id_peserta' => 1, 'bulan' => 6, 'tahun' => $tahun, 'pertemuan_ke' => 1, 'status_hadir' => 1, 'tanggal' => '2026-06-05'],
            ['id_peserta' => 1, 'bulan' => 6, 'tahun' => $tahun, 'pertemuan_ke' => 2, 'status_hadir' => 1, 'tanggal' => '2026-06-12'],
            ['id_peserta' => 1, 'bulan' => 6, 'tahun' => $tahun, 'pertemuan_ke' => 3, 'status_hadir' => 1, 'tanggal' => '2026-06-19'],
            ['id_peserta' => 1, 'bulan' => 6, 'tahun' => $tahun, 'pertemuan_ke' => 4, 'status_hadir' => 1, 'tanggal' => '2026-06-26'],
            ['id_peserta' => 1, 'bulan' => 6, 'tahun' => $tahun, 'pertemuan_ke' => 5, 'status_hadir' => 1, 'tanggal' => '2026-06-30'],

            // Juli - lengkap
            ['id_peserta' => 1, 'bulan' => 7, 'tahun' => $tahun, 'pertemuan_ke' => 1, 'status_hadir' => 1, 'tanggal' => '2026-07-05'],
            ['id_peserta' => 1, 'bulan' => 7, 'tahun' => $tahun, 'pertemuan_ke' => 2, 'status_hadir' => 1, 'tanggal' => '2026-07-12'],
            ['id_peserta' => 1, 'bulan' => 7, 'tahun' => $tahun, 'pertemuan_ke' => 3, 'status_hadir' => 1, 'tanggal' => '2026-07-19'],
            ['id_peserta' => 1, 'bulan' => 7, 'tahun' => $tahun, 'pertemuan_ke' => 4, 'status_hadir' => 1, 'tanggal' => '2026-07-26'],
            ['id_peserta' => 1, 'bulan' => 7, 'tahun' => $tahun, 'pertemuan_ke' => 5, 'status_hadir' => 1, 'tanggal' => '2026-07-31'],

            // Agustus - lengkap
            ['id_peserta' => 1, 'bulan' => 8, 'tahun' => $tahun, 'pertemuan_ke' => 1, 'status_hadir' => 1, 'tanggal' => '2026-08-05'],
            ['id_peserta' => 1, 'bulan' => 8, 'tahun' => $tahun, 'pertemuan_ke' => 2, 'status_hadir' => 1, 'tanggal' => '2026-08-12'],
            ['id_peserta' => 1, 'bulan' => 8, 'tahun' => $tahun, 'pertemuan_ke' => 3, 'status_hadir' => 1, 'tanggal' => '2026-08-19'],
            ['id_peserta' => 1, 'bulan' => 8, 'tahun' => $tahun, 'pertemuan_ke' => 4, 'status_hadir' => 1, 'tanggal' => '2026-08-26'],
            ['id_peserta' => 1, 'bulan' => 8, 'tahun' => $tahun, 'pertemuan_ke' => 5, 'status_hadir' => 1, 'tanggal' => '2026-08-31'],

            // September - lengkap
            ['id_peserta' => 1, 'bulan' => 9, 'tahun' => $tahun, 'pertemuan_ke' => 1, 'status_hadir' => 1, 'tanggal' => '2026-09-05'],
            ['id_peserta' => 1, 'bulan' => 9, 'tahun' => $tahun, 'pertemuan_ke' => 2, 'status_hadir' => 1, 'tanggal' => '2026-09-12'],
            ['id_peserta' => 1, 'bulan' => 9, 'tahun' => $tahun, 'pertemuan_ke' => 3, 'status_hadir' => 1, 'tanggal' => '2026-09-19'],
            ['id_peserta' => 1, 'bulan' => 9, 'tahun' => $tahun, 'pertemuan_ke' => 4, 'status_hadir' => 1, 'tanggal' => '2026-09-26'],
            ['id_peserta' => 1, 'bulan' => 9, 'tahun' => $tahun, 'pertemuan_ke' => 5, 'status_hadir' => 1, 'tanggal' => '2026-09-30'],

            // Oktober - lengkap
            ['id_peserta' => 1, 'bulan' => 10, 'tahun' => $tahun, 'pertemuan_ke' => 1, 'status_hadir' => 1, 'tanggal' => '2026-10-05'],
            ['id_peserta' => 1, 'bulan' => 10, 'tahun' => $tahun, 'pertemuan_ke' => 2, 'status_hadir' => 1, 'tanggal' => '2026-10-12'],
            ['id_peserta' => 1, 'bulan' => 10, 'tahun' => $tahun, 'pertemuan_ke' => 3, 'status_hadir' => 1, 'tanggal' => '2026-10-19'],
            ['id_peserta' => 1, 'bulan' => 10, 'tahun' => $tahun, 'pertemuan_ke' => 4, 'status_hadir' => 1, 'tanggal' => '2026-10-26'],
            ['id_peserta' => 1, 'bulan' => 10, 'tahun' => $tahun, 'pertemuan_ke' => 5, 'status_hadir' => 1, 'tanggal' => '2026-10-31'],

            // November - lengkap
            ['id_peserta' => 1, 'bulan' => 11, 'tahun' => $tahun, 'pertemuan_ke' => 1, 'status_hadir' => 1, 'tanggal' => '2026-11-05'],
            ['id_peserta' => 1, 'bulan' => 11, 'tahun' => $tahun, 'pertemuan_ke' => 2, 'status_hadir' => 1, 'tanggal' => '2026-11-12'],
            ['id_peserta' => 1, 'bulan' => 11, 'tahun' => $tahun, 'pertemuan_ke' => 3, 'status_hadir' => 1, 'tanggal' => '2026-11-19'],
            ['id_peserta' => 1, 'bulan' => 11, 'tahun' => $tahun, 'pertemuan_ke' => 4, 'status_hadir' => 1, 'tanggal' => '2026-11-26'],
            ['id_peserta' => 1, 'bulan' => 11, 'tahun' => $tahun, 'pertemuan_ke' => 5, 'status_hadir' => 1, 'tanggal' => '2026-11-30'],

            // Desember - 4 kehadiran (tidak lengkap)
            ['id_peserta' => 1, 'bulan' => 12, 'tahun' => $tahun, 'pertemuan_ke' => 1, 'status_hadir' => 1, 'tanggal' => '2026-12-05'],
            ['id_peserta' => 1, 'bulan' => 12, 'tahun' => $tahun, 'pertemuan_ke' => 2, 'status_hadir' => 1, 'tanggal' => '2026-12-12'],
            ['id_peserta' => 1, 'bulan' => 12, 'tahun' => $tahun, 'pertemuan_ke' => 3, 'status_hadir' => 1, 'tanggal' => '2026-12-19'],
            ['id_peserta' => 1, 'bulan' => 12, 'tahun' => $tahun, 'pertemuan_ke' => 4, 'status_hadir' => 1, 'tanggal' => '2026-12-26'],
            // Pertemuan 5 Desember tidak hadir

            // Zaenal Marlis (ID: 3) - 22 kehadiran
            ['id_peserta' => 3, 'bulan' => 1, 'tahun' => $tahun, 'pertemuan_ke' => 1, 'status_hadir' => 1, 'tanggal' => '2026-01-05'],
            ['id_peserta' => 3, 'bulan' => 1, 'tahun' => $tahun, 'pertemuan_ke' => 2, 'status_hadir' => 1, 'tanggal' => '2026-01-12'],
            ['id_peserta' => 3, 'bulan' => 1, 'tahun' => $tahun, 'pertemuan_ke' => 3, 'status_hadir' => 1, 'tanggal' => '2026-01-19'],
            ['id_peserta' => 3, 'bulan' => 1, 'tahun' => $tahun, 'pertemuan_ke' => 4, 'status_hadir' => 1, 'tanggal' => '2026-01-26'],
            ['id_peserta' => 3, 'bulan' => 1, 'tahun' => $tahun, 'pertemuan_ke' => 5, 'status_hadir' => 1, 'tanggal' => '2026-01-30'],

            // Februari - lengkap
            ['id_peserta' => 3, 'bulan' => 2, 'tahun' => $tahun, 'pertemuan_ke' => 1, 'status_hadir' => 1, 'tanggal' => '2026-02-05'],
            ['id_peserta' => 3, 'bulan' => 2, 'tahun' => $tahun, 'pertemuan_ke' => 2, 'status_hadir' => 1, 'tanggal' => '2026-02-12'],
            ['id_peserta' => 3, 'bulan' => 2, 'tahun' => $tahun, 'pertemuan_ke' => 3, 'status_hadir' => 1, 'tanggal' => '2026-02-19'],
            ['id_peserta' => 3, 'bulan' => 2, 'tahun' => $tahun, 'pertemuan_ke' => 4, 'status_hadir' => 1, 'tanggal' => '2026-02-26'],
            ['id_peserta' => 3, 'bulan' => 2, 'tahun' => $tahun, 'pertemuan_ke' => 5, 'status_hadir' => 1, 'tanggal' => '2026-02-28'],

            // Maret - lengkap
            ['id_peserta' => 3, 'bulan' => 3, 'tahun' => $tahun, 'pertemuan_ke' => 1, 'status_hadir' => 1, 'tanggal' => '2026-03-05'],
            ['id_peserta' => 3, 'bulan' => 3, 'tahun' => $tahun, 'pertemuan_ke' => 2, 'status_hadir' => 1, 'tanggal' => '2026-03-12'],
            ['id_peserta' => 3, 'bulan' => 3, 'tahun' => $tahun, 'pertemuan_ke' => 3, 'status_hadir' => 1, 'tanggal' => '2026-03-19'],
            ['id_peserta' => 3, 'bulan' => 3, 'tahun' => $tahun, 'pertemuan_ke' => 4, 'status_hadir' => 1, 'tanggal' => '2026-03-26'],
            ['id_peserta' => 3, 'bulan' => 3, 'tahun' => $tahun, 'pertemuan_ke' => 5, 'status_hadir' => 1, 'tanggal' => '2026-03-31'],

            // April - lengkap
            ['id_peserta' => 3, 'bulan' => 4, 'tahun' => $tahun, 'pertemuan_ke' => 1, 'status_hadir' => 1, 'tanggal' => '2026-04-05'],
            ['id_peserta' => 3, 'bulan' => 4, 'tahun' => $tahun, 'pertemuan_ke' => 2, 'status_hadir' => 1, 'tanggal' => '2026-04-12'],
            ['id_peserta' => 3, 'bulan' => 4, 'tahun' => $tahun, 'pertemuan_ke' => 3, 'status_hadir' => 1, 'tanggal' => '2026-04-19'],
            ['id_peserta' => 3, 'bulan' => 4, 'tahun' => $tahun, 'pertemuan_ke' => 4, 'status_hadir' => 1, 'tanggal' => '2026-04-26'],
            ['id_peserta' => 3, 'bulan' => 4, 'tahun' => $tahun, 'pertemuan_ke' => 5, 'status_hadir' => 1, 'tanggal' => '2026-04-30'],

            // Bagus Cahyo (ID: 5) - 20 kehadiran (tidak lengkap)
            ['id_peserta' => 5, 'bulan' => 6, 'tahun' => $tahun, 'pertemuan_ke' => 1, 'status_hadir' => 1, 'tanggal' => '2026-06-05'],
            ['id_peserta' => 5, 'bulan' => 6, 'tahun' => $tahun, 'pertemuan_ke' => 2, 'status_hadir' => 1, 'tanggal' => '2026-06-12'],
            ['id_peserta' => 5, 'bulan' => 6, 'tahun' => $tahun, 'pertemuan_ke' => 3, 'status_hadir' => 1, 'tanggal' => '2026-06-19'],
            ['id_peserta' => 5, 'bulan' => 6, 'tahun' => $tahun, 'pertemuan_ke' => 4, 'status_hadir' => 1, 'tanggal' => '2026-06-26'],
            ['id_peserta' => 5, 'bulan' => 6, 'tahun' => $tahun, 'pertemuan_ke' => 5, 'status_hadir' => 1, 'tanggal' => '2026-06-30'],

            // M Syaiful Ulum (ID: 5) - 51 kehadiran (sama dengan Nashrul)
            // Data lengkap untuk semua bulan seperti Nashrul
        ];

        foreach ($absensiData as $absensi) {
            \App\Models\TempaAbsensi::create($absensi);
        }
    }
}
