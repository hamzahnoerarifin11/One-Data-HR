<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TempaPesertaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pesertas = [
            [
                'id_tempa' => 1,
                'id_kelompok' => 1,
                'status_peserta' => 1, // Aktif
                'nama_peserta' => 'Nashrul Ihsan',
                'nik_karyawan' => 'NIK001',
                'mentor_id' => 3,
                'unit' => 'OPERASIONAL',
                'shift' => 1,
            ],
            [
                'id_tempa' => 1,
                'id_kelompok' => 1,
                'status_peserta' => 2, // Tidak aktif sementara
                'nama_peserta' => 'Nurul Fatah',
                'nik_karyawan' => 'NIK002',
                'mentor_id' => 3,
                'unit' => 'OPERASIONAL',
                'shift' => 1,
            ],
            [
                'id_tempa' => 1,
                'id_kelompok' => 1,
                'status_peserta' => 0, // Tidak aktif
                'nama_peserta' => 'Zaenal Marlis',
                'nik_karyawan' => 'NIK003',
                'mentor_id' => 3,
                'unit' => 'OPERASIONAL',
                'shift' => 1,
            ],
            [
                'id_tempa' => 1,
                'id_kelompok' => 1,
                'status_peserta' => 2, // Tidak aktif sementara
                'nama_peserta' => 'Bagus Cahyo',
                'nik_karyawan' => 'NIK004',
                'mentor_id' => 3,
                'unit' => 'OPERASIONAL',
                'shift' => 1,
            ],
            [
                'id_tempa' => 1,
                'id_kelompok' => 1,
                'status_peserta' => 1, // Aktif
                'nama_peserta' => 'M Syaiful Ulum',
                'nik_karyawan' => 'NIK005',
                'mentor_id' => 3,
                'unit' => 'OPERASIONAL',
                'shift' => 1,
            ],
            [
                'id_tempa' => 1,
                'id_kelompok' => 1,
                'status_peserta' => 1, // Aktif
                'nama_peserta' => 'Unggul Wahyu Leksono',
                'nik_karyawan' => 'NIK006',
                'mentor_id' => 3,
                'unit' => 'OPERASIONAL',
                'shift' => 1,
            ],
        ];

        foreach ($pesertas as $peserta) {
            \App\Models\TempaPeserta::create($peserta);
        }
    }
}
