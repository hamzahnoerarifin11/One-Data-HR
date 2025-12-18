<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KpiAssessment;
use App\Models\KpiItem;
use App\Models\KpiScore;
use Carbon\Carbon;

class KpiSeeder extends Seeder
{
    public function run()
    {
        // 1. Buat Header Penilaian untuk Karyawan ID 3 (Marcell Bas)
        $assessment = KpiAssessment::create([
            'karyawan_id'       => 3, // Pastikan ID ini ada di tabel karyawan
            'penilai_id'        => 3, // Diri sendiri atau atasan (sementara dummy)
            'tahun'             => '2025',
            'periode'           => 'Januari - Juni',
            'tanggal_penilaian' => Carbon::now(),
            'status'            => 'DRAFT'
        ]);

        // 2. Buat Item KPI Baris Pertama (Defect)
        $item1 = KpiItem::create([
            'kpi_assessment_id'         => $assessment->id_kpi_assessment,
            'perspektif'                => 'Internal Business Process',
            'key_result_area'           => 'Kualitas produk',
            'key_performance_indicator' => 'Presentase Barang Defect Pada Semester 1 Tahun 2025',
            'units'                     => 'Prosentase',
            'polaritas'                 => 'Negatif',
            'bobot'                     => 30.00,
            'target_tahunan'            => '0,5%'
        ]);

        // 3. Buat Item KPI Baris Kedua (Produktivitas)
        $item2 = KpiItem::create([
            'kpi_assessment_id'         => $assessment->id_kpi_assessment,
            'perspektif'                => 'Financial',
            'key_result_area'           => 'Produktivitas karyawan',
            'key_performance_indicator' => 'Presentase Capaian Produksi Factory 1',
            'units'                     => 'Prosentase',
            'polaritas'                 => 'Positif',
            'bobot'                     => 40.00,
            'target_tahunan'            => '100%'
        ]);

        // 4. Buat Slot Penilaian untuk Semester 1 (Agar kolom input muncul)
        
        // Slot untuk Item 1
        KpiScore::create([
            'kpi_item_id'  => $item1->id_kpi_item,
            'tipe_periode' => 'SEMESTER',
            'nama_periode' => 'Semester 1',
            'target'       => '0,5%',
            'realisasi'    => null, // Kosong karena user belum input
            'skor'         => 0
        ]);

        // Slot untuk Item 2
        KpiScore::create([
            'kpi_item_id'  => $item2->id_kpi_item,
            'tipe_periode' => 'SEMESTER',
            'nama_periode' => 'Semester 1',
            'target'       => '100%',
            'realisasi'    => null, 
            'skor'         => 0
        ]);
    }
}