<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Karyawan;
use App\Models\KpiAssessment;
use App\Models\KpiItem;
use App\Models\KpiScore;
use Carbon\Carbon;
use Faker\Factory as Faker;

class KaryawanDummySeeder extends Seeder
{
    public function run()
    {
        $jumlahData = 50; 
        $faker = Faker::create('id_ID');

        $this->command->info("Sedang membuat {$jumlahData} karyawan dummy...");

        // 1. Buat Karyawan (Factory akan otomatis isi status_karyawan)
        $karyawanBaru = Karyawan::factory()->count($jumlahData)->create();

        foreach ($karyawanBaru as $karyawan) {
            
            // ======================================================
            // 2. ISI TABEL PEKERJAAN
            // ======================================================
            DB::table('pekerjaan')->insert([
                'id_karyawan'     => $karyawan->id_karyawan,
                'jabatan'         => 'Staff', // Jabatan masuk sini
                'divisi'          => null, // Divisi masuk sini
                
                // HAPUS BARIS status_karyawan DARI SINI
                // 'status_karyawan' => 'Tetap', <--- HAPUS INI
                
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now(),
            ]);
            // ======================================================

            // 3. Buat Header KPI
            $assessment = KpiAssessment::create([
                'karyawan_id'       => $karyawan->id_karyawan,
                'tahun'             => '2025',
                'penilai_id'        => $karyawan->id_karyawan,
                'periode'           => 'Januari - Desember',
                'tanggal_penilaian' => Carbon::now(),
                'status'            => 'DRAFT',
                'created_at'        => Carbon::now(),
            ]);

            // 4. Buat Item KPI
             $item = KpiItem::create([
                'kpi_assessment_id'         => $assessment->id_kpi_assessment,
                'perspektif'                => 'Internal Business Process',
                'key_result_area'           => 'Kualitas & Produktivitas',
                'key_performance_indicator' => 'Contoh KPI Dummy Pagination',
                'units'                     => '%',
                'polaritas'                 => 'Maximize',
                'bobot'                     => 100,
                'target'                    => 100
            ]);

            // 5. Buat Score
            KpiScore::create([
                'kpi_item_id'  => $item->id_kpi_item,
                'nama_periode' => 'Full Year 2025',
                'target'       => 100,
                'realisasi'    => rand(50, 100),
                'target_smt1' => 100, 'real_smt1' => 0,
                'target_jul' => 100, 'real_jul' => 0,
                'target_aug' => 100, 'real_aug' => 0,
                'target_sep' => 100, 'real_sep' => 0,
                'target_okt' => 100, 'real_okt' => 0,
                'target_nov' => 100, 'real_nov' => 0,
                'target_des' => 100, 'real_des' => 0,
            ]);
        }

        $this->command->info("Selesai!");
    }
}