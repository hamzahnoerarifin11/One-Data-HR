<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KpiAssessment;
use App\Models\Karyawan;
use App\Models\KpiItem;
use App\Models\KpiScore;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KpiAssessmentController extends Controller
{
    // --- 1. DASHBOARD MONITORING (All Employees) ---
    public function index(Request $request)
    {
        // Default tahun sekarang, atau dari filter user
        $tahun = $request->input('tahun', date('Y')); 

        // Ambil SEMUA Karyawan + Data KPI mereka di tahun terpilih (Eager Loading)
        $karyawanList = Karyawan::with(['pekerjaan', 'kpiAssessment' => function($q) use ($tahun) {
            $q->where('tahun', $tahun);
        }])->get();

        // --- Hitung Statistik untuk Cards di Atas ---
        $stats = [
            'total_karyawan' => $karyawanList->count(),
            'sudah_final'    => KpiAssessment::where('tahun', $tahun)->where('status', 'APPROVED')->count(),
            'draft'          => KpiAssessment::where('tahun', $tahun)->whereIn('status', ['DRAFT', 'SUBMITTED'])->count(),
            'rata_rata'      => KpiAssessment::where('tahun', $tahun)->avg('total_skor_akhir') ?? 0
        ];

        return view('kpi.index', compact('karyawanList', 'tahun', 'stats'));
    }


// --- 2. GENERATE KPI BARU DENGAN TEMPLATE ISI ---
    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id_karyawan',
            'tahun'       => 'required|integer'
        ]);

        // 1. Cek Duplikasi
        $cek = KpiAssessment::where('karyawan_id', $request->karyawan_id)
                            ->where('tahun', $request->tahun)
                            ->first();

        if ($cek) {
            return redirect()->route('kpi.show', ['karyawan_id' => $request->karyawan_id, 'tahun' => $request->tahun]);
        }

        DB::beginTransaction();
        try {
            // 2. Buat Header Assessment
            $newKpi = KpiAssessment::create([
                'karyawan_id'       => $request->karyawan_id,
                'tahun'             => $request->tahun,
                'periode'           => 'Tahunan',
                'tanggal_penilaian' => now(),
                'status'            => 'DRAFT',
                'total_skor_akhir'  => 0,
                'penilai_id'        => Auth::id() ?? 1
            ]);

            // 3. DATA TEMPLATE (Isi sesuai Spreadsheet KPI Umum)
            // Total Bobot harus 100%
            $templateItems = [
                [
                    'perspektif' => 'Financial',
                    'kra'        => 'Efisiensi Anggaran',
                    'kpi'        => 'Persentase penggunaan anggaran operasional sesuai budget',
                    'bobot'      => 10,
                    'polaritas'  => 'Minimize', // Makin hemat makin baik (atau Maximize jika targetnya serapan)
                    'target'     => 100, // 100%
                    'satuan'     => '%'
                ],
                [
                    'perspektif' => 'Customer',
                    'kra'        => 'Kepuasan Pelanggan (Internal/Eksternal)',
                    'kpi'        => 'Nilai rata-rata kepuasan user/klien (Survey)',
                    'bobot'      => 20,
                    'polaritas'  => 'Maximize',
                    'target'     => 4.5, // Skala 1-5
                    'satuan'     => 'Skala'
                ],
                [
                    'perspektif' => 'Customer',
                    'kra'        => 'Penanganan Komplain',
                    'kpi'        => 'Jumlah komplain yang tidak terselesaikan (Unresolved)',
                    'bobot'      => 10,
                    'polaritas'  => 'Minimize', // Makin sedikit makin baik
                    'target'     => 0,
                    'satuan'     => 'Kasus'
                ],
                [
                    'perspektif' => 'Internal Process',
                    'kra'        => 'Penyelesaian Tugas Utama',
                    'kpi'        => 'Persentase penyelesaian project/tugas tepat waktu (On-time)',
                    'bobot'      => 30, // Bobot paling besar
                    'polaritas'  => 'Maximize',
                    'target'     => 100,
                    'satuan'     => '%'
                ],
                [
                    'perspektif' => 'Internal Process',
                    'kra'        => 'Kualitas Kerja',
                    'kpi'        => 'Jumlah kesalahan (error/rework) major dalam pekerjaan',
                    'bobot'      => 15,
                    'polaritas'  => 'Minimize',
                    'target'     => 0,
                    'satuan'     => 'Kasus'
                ],
                [
                    'perspektif' => 'Learning & Growth',
                    'kra'        => 'Pengembangan Diri',
                    'kpi'        => 'Jumlah jam pelatihan / training yang diikuti',
                    'bobot'      => 10,
                    'polaritas'  => 'Maximize',
                    'target'     => 20, // 20 Jam setahun
                    'satuan'     => 'Jam'
                ],
                [
                    'perspektif' => 'Learning & Growth',
                    'kra'        => 'Kedisiplinan',
                    'kpi'        => 'Persentase kehadiran kerja (Absensi)',
                    'bobot'      => 5,
                    'polaritas'  => 'Maximize',
                    'target'     => 98, // Minimal 98% hadir
                    'satuan'     => '%'
                ],
            ];

            // 4. Loop dan Masukkan ke Database
            foreach ($templateItems as $item) {
                // Simpan Item KPI
                $kpiItem = KpiItem::create([
                    'kpi_assessment_id'         => $newKpi->id_kpi_assessment,
                    'perspektif'                => $item['perspektif'],
                    'key_result_area'           => $item['kra'],
                    'key_performance_indicator' => $item['kpi'],
                    'bobot'                     => $item['bobot'],
                    'target'                    => $item['target'], // Target default master
                    'realisasi'                 => 0,
                    'skor'                      => 0,
                    'skor_akhir'                => 0,
                    'polaritas'                 => $item['polaritas'],
                    'satuan'                    => $item['satuan'],
                ]);

                // Simpan Score (Semester 1) agar Form Input Target muncul
                KpiScore::create([
                    'kpi_item_id'  => $kpiItem->id_kpi_item,
                    'nama_periode' => 'Semester 1',
                    'target'       => $item['target'], // Target turunan
                    'realisasi'    => 0,
                    'skor'         => 0,
                    'skor_akhir'   => 0
                ]);
            }

            DB::commit();

            return redirect()->route('kpi.show', ['karyawan_id' => $request->karyawan_id, 'tahun' => $request->tahun])
                             ->with('success', 'KPI berhasil dibuat dengan Template Standar.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal membuat KPI: ' . $e->getMessage());
        }
    }

    // --- 3. SHOW FORM DETAIL (PENTING: Disini variabel $karyawan dikirim) ---
    public function show($karyawanId, $tahun)
    {
        // 1. Ambil Data Karyawan (Ini yang menyebabkan error jika hilang)
        $karyawan = Karyawan::findOrFail($karyawanId);

        // 2. Ambil Data KPI
        $kpi = KpiAssessment::where('karyawan_id', $karyawanId)
                            ->where('tahun', $tahun)
                            ->with(['items.scores']) 
                            ->first();

        // Jika belum ada, redirect kembali ke dashboard
        if (!$kpi) {
            return redirect()->route('kpi.index')->with('error', 'Data KPI belum dibuat. Silakan klik "Buat" di dashboard.');
        }

        // 3. Kirim variabel $karyawan dan $kpi ke View
        return view('kpi.form', compact('karyawan', 'kpi'));
    }

    public function update(Request $request, $id_kpi_assessment)
    {
        $assessment = KpiAssessment::findOrFail($id_kpi_assessment);
        
        // Data input bentuknya: name="kpi[ITEM_ID][target]" dan "kpi[ITEM_ID][realisasi]"
        $inputs = $request->input('kpi'); 

        if (!$inputs) {
            return redirect()->back()->with('warning', 'Tidak ada data yang dikirim.');
        }

        DB::beginTransaction();
        try {
            foreach ($inputs as $itemId => $data) {
                
                // Ambil Target & Realisasi dari input
                $targetBaru = $data['target'] ?? 0;
                $realisasiBaru = $data['realisasi'] ?? 0;

                // Ambil Item KPI (untuk tau Bobot & Polaritas)
                $item = KpiItem::find($itemId);
                if (!$item) continue;

                // Cari Score Record (Asumsi Semester 1 dulu)
                $scoreRecord = KpiScore::where('kpi_item_id', $itemId)
                                        ->where('nama_periode', 'Semester 1')
                                        ->first();

                if ($scoreRecord) {
                    // Hitung Skor di Backend (Agar aman & tersimpan di DB)
                    $hasilHitung = $this->hitungSkor(
                        $targetBaru,        // Pakai target baru dari input
                        $realisasiBaru, 
                        $item->polaritas, 
                        $item->bobot
                    );

                    // Update Database (Target + Realisasi + Skor)
                    $scoreRecord->update([
                        'target'     => $targetBaru,      // Simpan Target Baru
                        'realisasi'  => $realisasiBaru,
                        'skor'       => $hasilHitung['skor_mentah'],
                        'skor_akhir' => $hasilHitung['skor_akhir_bobot']
                    ]);
                }
            }

            // Hitung Ulang Total Skor Assessment
            $totalSkor = KpiScore::whereHas('item', function($q) use ($id_kpi_assessment) {
                $q->where('kpi_assessment_id', $id_kpi_assessment);
            })->sum('skor_akhir');

            // Update Status jadi SUBMITTED jika sebelumnya DRAFT
            $status = ($assessment->status == 'DRAFT') ? 'SUBMITTED' : $assessment->status;

            $assessment->update([
                'total_skor_akhir' => $totalSkor,
                'status' => $status
            ]);

            DB::commit(); 

            return redirect()->back()->with('success', 'Data KPI berhasil disimpan. Total Skor: ' . number_format($totalSkor, 2));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // --- 5. PRIVATE HELPER (Rumus Hitung) ---
    private function hitungSkor($target, $realisasi, $polaritas, $bobot)
    {
        // Bersihkan format input
        $t = floatval(str_replace(['%', ','], ['', '.'], $target));
        $r = floatval(str_replace(['%', ','], ['', '.'], $realisasi));
        $b = floatval(str_replace(['%', ','], ['', '.'], $bobot));

        // Jika target 0, skor 0 untuk menghindari error division by zero
        if ($t == 0) return ['skor_mentah' => 0, 'skor_akhir_bobot' => 0];

        $skor = 0;

        // Logika Polaritas
        if ($polaritas == 'Positif' || $polaritas == 'Maximize') {
            // Makin tinggi makin bagus
            $skor = ($r / $t) * 100;
        } elseif ($polaritas == 'Negatif' || $polaritas == 'Minimize') {
            // Makin rendah makin bagus
            // Rumus umum (Target / Realisasi * 100)
            if ($r > 0) {
                $skor = ($t / $r) * 100;
            } else {
                $skor = 100; // Jika realisasi 0 (misal complain 0), anggap sempurna
            }
        }

        // Hitung skor akhir dikali bobot
        $skorAkhir = $skor * ($b / 100);

        return [
            'skor_mentah'      => round($skor, 2),
            'skor_akhir_bobot' => round($skorAkhir, 2)
        ];
    }

    // ... method update ...

    // --- 6. HAPUS KPI (Untuk Reset) ---
    public function destroy($id)
    {
        $kpi = KpiAssessment::findOrFail($id);
        
        // Hapus (Items dan Scores akan terhapus otomatis jika sudah set cascade on delete di migration)
        // Jika belum, kita hapus manual items-nya agar bersih
        foreach($kpi->items as $item) {
            $item->scores()->delete(); // Hapus skor
            $item->delete(); // Hapus item
        }
        
        $kpi->delete(); // Hapus header

        return redirect()->back()->with('success', 'Data KPI berhasil dihapus/reset.');
    }

}