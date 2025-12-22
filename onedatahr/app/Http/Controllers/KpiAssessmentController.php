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
        // 1. Ambil Tahun (Default tahun ini)
        $tahun = $request->input('tahun', date('Y'));
        
        // 2. Ambil Kata Kunci Search
        $search = $request->input('search');

        // 3. Query Karyawan
        $query = Karyawan::with(['pekerjaan', 'kpiAssessment' => function($q) use ($tahun) {
            $q->where('tahun', $tahun);
        }]);

        // --- LOGIKA SEARCH (Modifikasi Disini) ---
        if ($search) {
            $query->where(function($q) use ($search) {
                // Cari berdasarkan Nama
                $q->where('Nama_Lengkap_Sesuai_Ijazah', 'LIKE', "%{$search}%")
                  // ATAU Cari berdasarkan Jabatan (Relasi Pekerjaan)
                  ->orWhereHas('pekerjaan', function($subQ) use ($search) {
                      $subQ->where('Jabatan', 'LIKE', "%{$search}%");
                  });
            });
        }
        // ------------------------------------------

        $karyawanList = $query->get();

        // 4. Hitung Statistik (Sama seperti sebelumnya)
        $stats = [
            'total_karyawan' => $karyawanList->count(),
            'sudah_final'    => 0,
            'draft'          => 0,
            'rata_rata'      => 0
        ];

        $totalSkor = 0;
        $countSkor = 0;

        foreach ($karyawanList as $kry) {
            $kpi = $kry->kpiAssessment; // Sudah difilter tahun di query atas
            if ($kpi) {
                if ($kpi->status == 'FINAL') $stats['sudah_final']++;
                else $stats['draft']++;

                if ($kpi->total_skor_akhir > 0) {
                    $totalSkor += $kpi->total_skor_akhir;
                    $countSkor++;
                }
            }
        }

        $stats['rata_rata'] = $countSkor > 0 ? ($totalSkor / $countSkor) : 0;

        // Return ke View (Jangan lupa sesuaikan path views.pages.kpi.index)
        return view('pages.kpi.index', compact('karyawanList', 'tahun', 'stats'));
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
                    'perspektif' => 'Internal Business Process',
                    'kra'        => 'Kualitas produk',
                    'kpi'        => 'Presentase Barang Defect Pada Semeseter 1 Tahun 2025',
                    'bobot'      => 30,
                    'polaritas'  => 'Negatif', // Makin hemat makin baik (atau Maximize jika targetnya serapan)
                    'target'     => 100, // 100%
                    'satuan'     => '%'
                ],
                [
                    'perspektif' => 'Financial',
                    'kra'        => 'Produktivitas karyawan',
                    'kpi'        => 'Presentase Capaian Produksi Factory 1 pada semester 1 Tahun 2025',
                    'bobot'      => 40,
                    'polaritas'  => 'Positif', // Makin tinggi makin baik
                    'target'     => 4.5, // Skala 1-5
                    'satuan'     => 'Skala'
                ],
                [
                    'perspektif' => 'Learning & Growth',
                    'kra'        => 'Kepatuhan dan keselamatan kerja',
                    'kpi'        => 'Tingkat Kedisiplinan Penggunaan APD Factory 1 pada semseter 1 Tahun 2025',
                    'bobot'      => 20,
                    'polaritas'  => 'Positif', // Makin tinggi makin baik
                    'target'     => 0,
                    'satuan'     => 'Kasus'
                ],
                [
                    'perspektif' => 'Learning & Growth',
                    'kra'        => 'Pengembangan Kompetensi & Keterlibatan Karyawan',
                    'kpi'        => 'Presentease Kehadiran Kegiatan TEMPA',
                    'bobot'      => 10, // Bobot paling besar
                    'polaritas'  => 'Positif',
                    'target'     => 100,
                    'satuan'     => '%'
                ]
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
        return view('pages.kpi.form', compact('karyawan', 'kpi'));
    }

    public function update(Request $request, $id_kpi_assessment)
    {
        $assessment = KpiAssessment::findOrFail($id_kpi_assessment);
        $inputs = $request->input('kpi');

        if (!$inputs) {
            return redirect()->back()->with('warning', 'Tidak ada data input yang dikirim.');
        }

        DB::beginTransaction();
        try {
            // 1. Loop Simpan Per-Item (Target & Realisasi)
            foreach ($inputs as $itemId => $data) {
                $targetBaru = $data['target'] ?? 0;
                $realisasiBaru = $data['realisasi'] ?? 0;

                $item = KpiItem::find($itemId);
                if (!$item) continue;

                $scoreRecord = KpiScore::where('kpi_item_id', $itemId)
                                       ->where('nama_periode', 'Semester 1') // Sesuaikan jika ada periode lain
                                       ->first();

                if ($scoreRecord) {
                    // Panggil helper hitungSkor
                    $hasilHitung = $this->hitungSkor(
                        $targetBaru, 
                        $realisasiBaru, 
                        $item->polaritas, 
                        $item->bobot
                    );

                    $scoreRecord->update([
                        'target'     => $targetBaru,
                        'realisasi'  => $realisasiBaru,
                        'skor'       => $hasilHitung['skor_mentah'],
                        'skor_akhir' => $hasilHitung['skor_akhir_bobot']
                    ]);
                }
            }

            // 2. HITUNG ULANG TOTAL SKOR
            $totalSkor = KpiScore::whereHas('item', function($q) use ($id_kpi_assessment) {
                $q->where('kpi_assessment_id', $id_kpi_assessment);
            })->sum('skor_akhir');

            // 3. TENTUKAN GRADE (INI YANG MUNGKIN HILANG DI KODINGAN ANDA)
            $grade = $this->determineGrade($totalSkor);

            // 4. UPDATE HEADER ASSESSMENT
            $assessment->update([
                'total_skor_akhir' => $totalSkor,
                'grade'            => $grade,     // <--- PASTIKAN BARIS INI ADA!
                'status'           => ($assessment->status == 'DRAFT') ? 'SUBMITTED' : $assessment->status
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Data berhasil disimpan. Skor: ' . number_format($totalSkor, 2) . ' (Grade: ' . $grade . ')');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function hitungSkor($target, $realisasi, $polaritas, $bobot)
    {
        // 1. Bersihkan input: Hapus %, ganti Koma jadi Titik
        // Contoh: "0,5%" jadi 0.5
        $t = floatval(str_replace(['%', ','], ['', '.'], $target));
        $r = floatval(str_replace(['%', ','], ['', '.'], $realisasi));
        $b = floatval(str_replace(['%', ','], ['', '.'], $bobot));

        $pencapaian = 0;

        if ($t == 0) {
             // Hindari error bagi 0
            $pencapaian = ($r == 0) ? 100 : 0; 
        } else {
            // --- LOGIKA PERHITUNGAN ---
            
            if ($polaritas == 'Positif' || $polaritas == 'Maximize') {
                // Rumus: (Realisasi / Target) * 100
                $pencapaian = ($r / $t) * 100;
            } 
            elseif ($polaritas == 'Negatif' || $polaritas == 'Minimize') {
                // Rumus Spreadsheet: 200% - (Realisasi / Target)
                // Contoh: 2 - (0.01 / 0.5) = 1.98 -> 198%
                $ratio = $r / $t;
                $pencapaian = (2 - $ratio) * 100;
                
                // Opsional: Skor tidak boleh minus
                if ($pencapaian < 0) $pencapaian = 0;
            } 
            elseif ($polaritas == 'Yes/No') {
                // Yes/No logic
                $pencapaian = ($r >= $t) ? 100 : 0;
            }
        }

        // Hitung Skor Akhir (Pencapaian * Bobot / 100)
        // Contoh: 198 * 30 / 100 = 59.4
        $skorAkhir = ($pencapaian * $b) / 100;

        return [
            'skor_mentah'      => round($pencapaian, 2),
            'skor_akhir_bobot' => round($skorAkhir, 2)
        ];
    }

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

    // --- 7. FINALISASI KPI (Mengunci Data) ---
    public function finalize($id)
    {
        $kpi = KpiAssessment::findOrFail($id);
        
        // Cek apakah skor sudah terisi (Opsional)
        if ($kpi->total_skor_akhir == 0) {
            return redirect()->back()->with('error', 'Tidak bisa finalisasi karena skor masih 0.');
        }

        // Ubah Status jadi FINAL
        $kpi->update([
            'status' => 'FINAL',
            // 'tanggal_finalisasi' => now() // Jika punya kolom ini
        ]);

        return redirect()->back()->with('success', 'KPI berhasil difinalisasi! Data sekarang terkunci.');
    }

    // --- HELPER: MENENTUKAN GRADE ---
    private function determineGrade($skor)
    {
        // Sesuaikan rentang nilai ini dengan kebijakan perusahaan Anda
        if ($skor >= 100) return 'Outstanding'; // Luar Biasa
        if ($skor >= 90)  return 'Great';       // Sangat Baik
        if ($skor >= 75)  return 'Good';        // Baik
        if ($skor >= 60)  return 'Enough';      // Cukup
        return 'Poor';                          // Kurang
    }

}