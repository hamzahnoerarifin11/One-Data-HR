<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TempaAbsensi;

class TempaAbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tahun = 2024;

        // Data absensi berdasarkan pola Excel untuk beberapa peserta
        $absensiData = [
            // John Doe (ID: 1) - 51 kehadiran (85%)
            [
                'id_peserta' => 1,
                'tahun_absensi' => $tahun,
                'absensi_data' => [
                    'jan' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'feb' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'mar' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'apr' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'mei' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'jun' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'jul' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'agu' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'sep' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'okt' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'nov' => ['hadir', 'hadir', 'hadir', null, null],
                    'des' => ['hadir', 'hadir', 'hadir', null, null],
                ],
                'total_hadir' => 51,
                'total_pertemuan' => 60,
                'persentase' => 85.0,
            ],

            // Jane Smith (ID: 2) - 45 kehadiran (75%)
            [
                'id_peserta' => 2,
                'tahun_absensi' => $tahun,
                'absensi_data' => [
                    'jan' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'feb' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'mar' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'apr' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'mei' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'jun' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'jul' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'agu' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'sep' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'okt' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'nov' => ['hadir', 'hadir', null, null, null],
                    'des' => ['hadir', 'hadir', null, null, null],
                ],
                'total_hadir' => 45,
                'total_pertemuan' => 60,
                'persentase' => 75.0,
            ],

            // Bob Johnson (ID: 3) - 60 kehadiran (100%)
            [
                'id_peserta' => 3,
                'tahun_absensi' => $tahun,
                'absensi_data' => [
                    'jan' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'feb' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'mar' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'apr' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'mei' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'jun' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'jul' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'agu' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'sep' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'okt' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'nov' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                    'des' => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
                ],
                'total_hadir' => 60,
                'total_pertemuan' => 60,
                'persentase' => 100.0,
            ],

            // Nashrul Ihsan (ID: 5) - 30 kehadiran (50%)
            [
                'id_peserta' => 5,
                'tahun_absensi' => $tahun,
                'absensi_data' => [
                    'jan' => ['hadir', 'tidak_hadir', 'hadir', 'tidak_hadir', 'hadir'],
                    'feb' => ['hadir', 'tidak_hadir', 'hadir', 'tidak_hadir', 'hadir'],
                    'mar' => ['hadir', 'tidak_hadir', 'hadir', 'tidak_hadir', 'hadir'],
                    'apr' => ['hadir', 'tidak_hadir', 'hadir', 'tidak_hadir', 'hadir'],
                    'mei' => ['hadir', 'tidak_hadir', 'hadir', 'tidak_hadir', 'hadir'],
                    'jun' => ['hadir', 'tidak_hadir', 'hadir', 'tidak_hadir', 'hadir'],
                    'jul' => ['tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir'],
                    'agu' => ['tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir'],
                    'sep' => ['tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir'],
                    'okt' => ['tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir'],
                    'nov' => ['tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir'],
                    'des' => ['tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir'],
                ],
                'total_hadir' => 30,
                'total_pertemuan' => 60,
                'persentase' => 50.0,
            ],

            // Nurul Fatah (ID: 8) - 6 kehadiran (10%)
            [
                'id_peserta' => 8,
                'tahun_absensi' => $tahun,
                'absensi_data' => [
                    'jan' => ['hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir'],
                    'feb' => ['tidak_hadir', 'hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir'],
                    'mar' => ['tidak_hadir', 'tidak_hadir', 'hadir', 'tidak_hadir', 'tidak_hadir'],
                    'apr' => ['tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'hadir', 'tidak_hadir'],
                    'mei' => ['tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'hadir'],
                    'jun' => ['tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir'],
                    'jul' => ['tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir'],
                    'agu' => ['tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir'],
                    'sep' => ['tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir'],
                    'okt' => ['tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir'],
                    'nov' => ['tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir'],
                    'des' => ['tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir', 'tidak_hadir'],
                ],
                'total_hadir' => 6,
                'total_pertemuan' => 60,
                'persentase' => 10.0,
            ],
        ];

        foreach ($absensiData as $absensi) {
            TempaAbsensi::create($absensi);
        }
    }
}
