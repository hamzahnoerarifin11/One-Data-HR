<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TempaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create Kelompok with TEMPA data
        $kelompok1 = \App\Models\TempaKelompok::create([
            'nama_kelompok' => 'Kelompok Alpha',
            'nama_mentor' => 'Manager Alpha',
        ]);

        $kelompok2 = \App\Models\TempaKelompok::create([
            'nama_kelompok' => 'Kelompok Beta',
            'nama_mentor' => 'Manager Beta',
        ]);

        // Create Peserta
        \App\Models\TempaPeserta::create([
            'id_kelompok' => $kelompok1->id_kelompok,
            'status_peserta' => 1,
            'nama_peserta' => 'John Doe',
            'nik_karyawan' => '1234567890',
        ]);

        \App\Models\TempaPeserta::create([
            'id_kelompok' => $kelompok1->id_kelompok,
            'status_peserta' => 1,
            'nama_peserta' => 'Jane Smith',
            'nik_karyawan' => '1234567891',
        ]);

        \App\Models\TempaPeserta::create([
            'id_kelompok' => $kelompok2->id_kelompok,
            'status_peserta' => 1,
            'nama_peserta' => 'Bob Johnson',
            'nik_karyawan' => '1234567892',
        ]);

        // Create some absensi records - commented out to avoid duplicates
        // $pesertas = \App\Models\TempaPeserta::all();
        // $pertemuan = 1;
        // foreach ($pesertas as $peserta) {
        //     \App\Models\TempaAbsensi::create([
        //         'id_peserta' => $peserta->id_peserta,
        //         'tahun_absensi' => 2026,
        //         'bulan' => 1,
        //         'tahun' => 2026,
        //         'pertemuan_ke' => $pertemuan++,
        //         'status_hadir' => rand(0, 1),
        //         'bukti_foto' => null,
        //         'created_by' => 3
        //     ]);
        // }

        // Create materi
        \App\Models\TempaMateri::create([
            'judul_materi' => 'Materi Pengenalan TEMPA',
            'file_materi' => 'tempa/materi/sample.pdf',
            'uploaded_by' => 3
        ]);
    }
}
