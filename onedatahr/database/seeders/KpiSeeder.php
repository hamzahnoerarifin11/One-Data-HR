<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\KpiAssessment;
use App\Models\KpiItem;
use App\Models\KpiScore;
use App\Models\Karyawan;
use Carbon\Carbon;

class KpiSeeder extends Seeder
{
    public function run()
    {
        // ==============================================================
        // 0. BERSIHKAN DATA LAMA (RESET AGAR TIDAK DOUBLE)
        // ==============================================================
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        KpiScore::truncate();
        KpiItem::truncate();
        KpiAssessment::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('Data KPI lama berhasil dibersihkan.');

        // ==============================================================
        // 1. CARI DATA KARYAWAN
        // ==============================================================
        $karyawan = Karyawan::first();
        if (!$karyawan) {
            $this->command->error('Tabel karyawan kosong. Harap isi data karyawan dulu.');
            return;
        }
        $idKaryawan = $karyawan->id_karyawan;

        // ==============================================================
        // 2. BUAT HEADER PENILAIAN
        // ==============================================================
        $assessment = KpiAssessment::create([
            'karyawan_id'       => $idKaryawan,
            'tahun'             => '2025',
            'penilai_id'        => $idKaryawan, // Asumsi menilai diri sendiri/atasan
            'periode'           => 'Januari - Desember',
            'tanggal_penilaian' => Carbon::now(),
            'status'            => 'DRAFT',
            'created_at'        => 1, // Sesuaikan dengan ID User login
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
        ]);

        // ==============================================================
        // 3. BUAT ITEM KPI (SESUAI GAMBAR)
        // ==============================================================

        // --- ITEM 1: DEFECT (Internal Business Process) ---
        $item1 = KpiItem::create([
            'kpi_assessment_id'         => $assessment->id_kpi_assessment,
            'perspektif'                => 'Internal Business Process',
            'key_result_area'           => 'Kualitas produk',
            'key_performance_indicator' => 'Presentase Barang Defect Pada Semester 1 Tahun 2025',
            'units'                     => 'Prosentase',
            'polaritas'                 => 'Minimize', // Di gambar "Negatif" = Minimize
            'bobot'                     => 30.00,
            'target'                    => 0.5, // Target Defect biasanya kecil
        ]);

        // --- ITEM 2: PRODUKSI (Financial) ---
        $item2 = KpiItem::create([
            'kpi_assessment_id'         => $assessment->id_kpi_assessment,
            'perspektif'                => 'Financial',
            'key_result_area'           => 'Produktivitas karyawan',
            'key_performance_indicator' => 'Presentase Capaian Produksi Factory 1 pada semester 1 Tahun 2025',
            'units'                     => 'Prosentase',
            'polaritas'                 => 'Maximize', // Di gambar "Positif" = Maximize
            'bobot'                     => 40.00,
            'target'                    => 100,
        ]);

        // --- ITEM 3: APD (Learning & Growth) ---
        $item3 = KpiItem::create([
            'kpi_assessment_id'         => $assessment->id_kpi_assessment,
            'perspektif'                => 'Learning & Growth',
            'key_result_area'           => 'Kepatuhan dan keselamatan kerja',
            'key_performance_indicator' => 'Tingkat Kedisiplinan Penggunaan APD Factory 1 pada semester 1 Tahun 2025',
            'units'                     => 'Prosentase',
            'polaritas'                 => 'Maximize', // Positif
            'bobot'                     => 20.00,
            'target'                    => 100,
        ]);

        // --- ITEM 4: TEMPA (Learning & Growth) ---
        $item4 = KpiItem::create([
            'kpi_assessment_id'         => $assessment->id_kpi_assessment,
            'perspektif'                => 'Learning & Growth',
            'key_result_area'           => 'Pengembangan Kompetensi & Keterlibatan Karyawan',
            'key_performance_indicator' => 'Presentase Kehadiran Kegiatan TEMPA',
            'units'                     => 'Prosentase',
            'polaritas'                 => 'Maximize', // Positif
            'bobot'                     => 10.00,
            'target'                    => 100,
        ]);

        // ==============================================================
        // 4. BUAT SCORE RECORD (Agar kolom inputan muncul di form)
        // ==============================================================
        
        $items = [$item1, $item2, $item3, $item4];

        foreach ($items as $item) {
            KpiScore::create([
                'kpi_item_id'  => $item->id_kpi_item, // Pastikan primary key benar
                'nama_periode' => 'Full Year 2025',
                
                // Isi Kolom Target (Wajib)
                'target'      => $item->target, 
                'realisasi'   => 0,

                // Isi Kolom Bulanan & Semester (Semua 0 dulu kecuali target)
                'target_smt1' => $item->target, 'real_smt1' => 0,
                'target_jul' => $item->target, 'real_jul' => 0,
                'target_aug' => $item->target, 'real_aug' => 0,
                'target_sep' => $item->target, 'real_sep' => 0,
                'target_okt' => $item->target, 'real_okt' => 0,
                'target_nov' => $item->target, 'real_nov' => 0,
                'target_des' => $item->target, 'real_des' => 0,
            ]);
        }
        
        $this->command->info('Sukses! 4 Data KPI berhasil dibuat sesuai gambar.');
    }
}