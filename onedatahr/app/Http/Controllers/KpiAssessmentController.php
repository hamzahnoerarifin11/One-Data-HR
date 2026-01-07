<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KpiAssessment;
use App\Models\Karyawan;
use App\Models\KpiItem;
use App\Models\KpiScore;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Tambahan wajib

class KpiAssessmentController extends Controller
{
    // --- 1. INDEX (KODE ASLI ANDA DENGAN FILTER) ---
    public function index(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $search = $request->input('search');
        $filterJabatan = $request->input('filter_jabatan');
        $filterStatus  = $request->input('filter_status');

        $query = Karyawan::with(['pekerjaan', 'kpiAssessment' => function($q) use ($tahun) {
            $q->where('tahun', $tahun);
        }]);

        // Logika Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('Nama_Lengkap_Sesuai_Ijazah', 'LIKE', "%{$search}%")
                  ->orWhereHas('pekerjaan', function($subQ) use ($search) {
                      $subQ->where('Jabatan', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Logika Filter
        if ($filterJabatan) {
            $query->whereHas('pekerjaan', function($q) use ($filterJabatan) {
                $q->where('jabatan', $filterJabatan);
            });
        }

        if ($filterStatus) {
            if ($filterStatus == 'BELUM_ADA') {
                $query->whereDoesntHave('kpiAssessment', function($q) use ($tahun) {
                    $q->where('tahun', $tahun);
                });
            } else {
                $query->whereHas('kpiAssessment', function($q) use ($filterStatus, $tahun) {
                    $q->where('tahun', $tahun)->where('status', $filterStatus);
                });
            }
        }

        // Clone untuk Statistik
        $queryForStats = $query->clone(); 
        $allKaryawanForStats = $queryForStats->get(); 

        // Pagination
        $karyawanList = $query->paginate(10)->appends($request->all());

        // Dropdown Jabatan
        $listJabatan = DB::table('pekerjaan')
                        ->select('jabatan')
                        ->distinct()
                        ->whereNotNull('jabatan')
                        ->orderBy('jabatan', 'asc')
                        ->pluck('jabatan');

        // Hitung Statistik
        $stats = [
            'total_karyawan' => $allKaryawanForStats->count(),
            'sudah_final'    => 0,
            'draft'          => 0,
            'belum_ada'      => 0,
            'rata_rata'      => 0
        ];

        $totalSkor = 0;
        $countSkor = 0;

        foreach ($allKaryawanForStats as $kry) {
            $kpi = $kry->kpiAssessment;
            if ($kpi) {
                if (in_array(strtoupper($kpi->status), ['FINAL', 'SELESAI', 'SUBMITTED', 'APPROVED', 'DONE'])) {
                    $stats['sudah_final']++;
                }
                else {
                    $stats['draft']++; 
                }

                if ($kpi->total_skor_akhir > 0) {
                    $totalSkor += $kpi->total_skor_akhir;
                    $countSkor++;
                }
            } else {
                $stats['belum_ada']++; 
            }
        }

        $stats['rata_rata'] = $countSkor > 0 ? round($totalSkor / $countSkor, 2) : 0;

        return view('pages.kpi.index', compact('karyawanList', 'tahun', 'stats', 'listJabatan'));
    }

    // --- 2. STORE (KODE ASLI ANDA - TIDAK ADA YANG DIHILANGKAN) ---
    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id_karyawan',
            'tahun'       => 'required|integer'
        ]);

        $cek = KpiAssessment::where('karyawan_id', $request->karyawan_id)
                            ->where('tahun', $request->tahun)
                            ->first();

        if ($cek) {
            return redirect()->route('kpi.show', ['karyawan_id' => $request->karyawan_id, 'tahun' => $request->tahun]);
        }

        DB::beginTransaction();
        try {
            $newKpi = KpiAssessment::create([
                'karyawan_id'       => $request->karyawan_id,
                'tahun'             => $request->tahun,
                'periode'           => 'Tahunan',
                'tanggal_penilaian' => now(),
                'status'            => 'DRAFT',
                'total_skor_akhir'  => 0,
                'penilai_id'        => Auth::id() ?? 1
            ]);

            // DATA TEMPLATE ASLI ANDA (SAYA PERTAHANKAN)
            $templateItems = [
                [
                    'perspektif' => 'Internal Business Process',
                    'kra'        => 'Kualitas produk',
                    'kpi'        => 'Presentase Barang Defect Pada Semeseter 1 Tahun 2025',
                    'bobot'      => 30,
                    'polaritas'  => 'Negatif',
                    'target'     => 100,
                    'satuan'     => '%'
                ],
                [
                    'perspektif' => 'Financial',
                    'kra'        => 'Produktivitas karyawan',
                    'kpi'        => 'Presentase Capaian Produksi Factory 1 pada semester 1 Tahun 2025',
                    'bobot'      => 40,
                    'polaritas'  => 'Positif',
                    'target'     => 4.5,
                    'satuan'     => 'Skala'
                ],
                [
                    'perspektif' => 'Learning & Growth',
                    'kra'        => 'Kepatuhan dan keselamatan kerja',
                    'kpi'        => 'Tingkat Kedisiplinan Penggunaan APD Factory 1 pada semseter 1 Tahun 2025',
                    'bobot'      => 20,
                    'polaritas'  => 'Positif',
                    'target'     => 0,
                    'satuan'     => 'Kasus'
                ],
                [
                    'perspektif' => 'Learning & Growth',
                    'kra'        => 'Pengembangan Kompetensi & Keterlibatan Karyawan',
                    'kpi'        => 'Presentease Kehadiran Kegiatan TEMPA',
                    'bobot'      => 10,
                    'polaritas'  => 'Positif',
                    'target'     => 100,
                    'satuan'     => '%'
                ]
            ];

            foreach ($templateItems as $item) {
                $kpiItem = KpiItem::create([
                    'kpi_assessment_id'         => $newKpi->id_kpi_assessment,
                    'perspektif'                => $item['perspektif'],
                    'key_result_area'           => $item['kra'],
                    'key_performance_indicator' => $item['kpi'],
                    'bobot'                     => $item['bobot'],
                    'target'                    => $item['target'],
                    'realisasi'                 => 0,
                    'skor'                      => 0,
                    'skor_akhir'                => 0,
                    'polaritas'                 => $item['polaritas'],
                    'satuan'                    => $item['satuan'],
                ]);

                // Simpan Score (Saya tambahkan kolom target_smt1 agar sinkron)
                KpiScore::create([
                    'kpi_item_id'  => $kpiItem->id_kpi_item,
                    'nama_periode' => 'Semester 1', // Tetap Semester 1 sesuai kode asli
                    'target'       => $item['target'],
                    'target_smt1'  => $item['target'], // Default Smt 1
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
            dd($e->getMessage());
        }
    }

    // --- 3. SHOW (FIX ERROR $ITEMS) ---
    public function show($karyawanId, $tahun)
    {
        $karyawan = Karyawan::findOrFail($karyawanId);

        $kpi = KpiAssessment::where('karyawan_id', $karyawanId)
                            ->where('tahun', $tahun)
                            ->first();

        if (!$kpi) {
            return redirect()->route('kpi.index')->with('error', 'Data KPI belum dibuat. Silakan klik "Buat" di dashboard.');
        }

        // [MODIFIKASI] Menambahkan variabel $items dengan pagination untuk View
        $items = KpiItem::where('kpi_assessment_id', $kpi->id_kpi_assessment)
                        ->with('scores')
                        ->paginate(10); // Wajib ada untuk table baru

        // Kirim $items ke view
        return view('pages.kpi.form', compact('karyawan', 'kpi', 'items'));
    }

    // --- 4. UPDATE (LOGIKA BARU ADJUSTMENT + SMT 1 & 2) ---
    public function update(Request $request, $id_kpi_assessment)
{
    $assessment = KpiAssessment::findOrFail($id_kpi_assessment);
    $inputs = $request->input('kpi');

    if (!$inputs) {
        return redirect()->back()->with('error', 'Tidak ada data yang dikirim.');
    }

    DB::beginTransaction();
    try {
        foreach ($inputs as $itemId => $data) {
            $item = KpiItem::find($itemId);
            if (!$item) continue;

            $scoreRecord = KpiScore::where('kpi_item_id', $itemId)->first();

            if ($scoreRecord) {
                // ==========================================
                // 1. DATA TARGET & REALISASI SMT 1
                // ==========================================
                $target1 = $this->cleanInput($data['target_smt1'] ?? $item->target); // Target Smt 1 = Target Tahunan
                $real1   = $this->cleanInput($data['real_smt1'] ?? 0);
                
                // --- SMT 1 ---
                $skorMurni1 = $this->hitungSkor($target1, $real1, $item->polaritas); // Cukup 3 parameter
                $nilaiSmt1  = ($skorMurni1 * $item->bobot) / 100; // Aman dikali karena $skorMurni1 sekarang adalah angka



                // Cek Adjustment Smt 1
                // Di JS, input adjustment berisi NILAI AKHIR (sudah dikali bobot), jadi kita ambil langsung
                $adjSmt1 = isset($data['adjustment_smt1']) ? $this->cleanInput($data['adjustment_smt1']) : null;
                
                // Jika ada adjustment, pakai itu. Jika tidak, pakai hitungan rumus.
                $finalSmt1 = ($adjSmt1 !== null && $data['adjustment_smt1'] !== '') ? $adjSmt1 : $nilaiSmt1;


                // ==========================================
                // 2. DATA TARGET & REALISASI SMT 2 (MANUAL)
                // ==========================================
                // Ambil input manual Total Smt 2
                $target2 = $this->cleanInput($data['total_target_smt2'] ?? 0);
                $real2   = $this->cleanInput($data['total_real_smt2'] ?? 0);

                // --- SMT 2 ---
                $skorMurni2 = $this->hitungSkor($target2, $real2, $item->polaritas);
                $nilaiSmt2  = ($skorMurni2 * $item->bobot) / 100;

                // Cek Adjustment Smt 2
                $adjSmt2 = isset($data['adjustment_smt2']) ? $this->cleanInput($data['adjustment_smt2']) : null;
                
                $finalSmt2 = ($adjSmt2 !== null && $data['adjustment_smt2'] !== '') ? $adjSmt2 : $nilaiSmt2;


                // ==========================================
                // 3. FINAL SCORE
                // ==========================================
                // Final = Nilai Smt 1 + Nilai Smt 2
                $grandFinal = $finalSmt1 + $finalSmt2;


                // ==========================================
                // 4. SIMPAN KE DATABASE
                // ==========================================
                $updateData = [
                    // Smt 1
                    'target_smt1'     => $target1,
                    'real_smt1'       => $real1,
                    'adjustment_smt1' => $adjSmt1, // Simpan nilai adj jika ada
                    'adjustment_real_smt1' => $this->cleanInput($data['adjustment_real_smt1'] ?? 0),

                    // Smt 2 (Simpan Input Manualnya)
                    'total_target_smt2' => $target2,
                    'total_real_smt2'   => $real2,
                    'adjustment_smt2'   => $adjSmt2,
                    'adjustment_target_smt2' => $this->cleanInput($data['adjustment_target_smt2'] ?? 0),
                    'adjustment_real_smt2'   => $this->cleanInput($data['adjustment_real_smt2'] ?? 0),

                    // Data Global
                    'target'     => $target1 + $target2, // Total Target (Opsional)
                    'realisasi'  => $real1 + $real2,     // Total Real (Opsional)
                    'skor'       => ($skorMurni1 + $skorMurni2) / 2, // Rata-rata skor murni (Opsional)
                    'skor_akhir' => $grandFinal, // INI YANG PENTING
                ];

                // Simpan juga data bulanan (Target & Real) agar form tidak kosong saat di-load ulang
                // Walaupun tidak dipakai perhitungan, tetap harus disimpan
                $months = ['jul','aug','sep','okt','nov','des'];
                foreach($months as $bln) {
                    $updateData['target_'.$bln] = $this->cleanInput($data['target_'.$bln] ?? 0);
                    $updateData['real_'.$bln]   = $this->cleanInput($data['real_'.$bln] ?? 0);
                }

                $scoreRecord->update($updateData);
            }
        }

        // Hitung Ulang Total Skor Assessment (Sum dari semua item)
        $totalSkorAkhir = KpiScore::where('kpi_assessment_id', $id_kpi_assessment) // Pastikan relasi benar atau gunakan join
            ->join('kpi_items', 'kpi_scores.kpi_item_id', '=', 'kpi_items.id_kpi_item')
            ->where('kpi_items.kpi_assessment_id', $id_kpi_assessment)
            ->sum('kpi_scores.skor_akhir');

        // Tentukan Grade
        $grade = $this->determineGrade($totalSkorAkhir);

        // Update Header
        $assessment->update([
            'total_skor_akhir' => $totalSkorAkhir,
            'grade'            => $grade,
            'status'           => 'SUBMITTED' // Atau sesuaikan logika status Anda
        ]);

        DB::commit();

        return redirect()->back()->with('success', 'Data berhasil disimpan. Skor Akhir: ' . number_format($totalSkorAkhir, 2));

    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
    }
}

    // --- 5. LOGIKA HITUNG SKOR (UPDATE: YES/NO & CLEAN INPUT) ---
    // PERBAIKAN: Hanya butuh 3 parameter & Return langsung angka (Float)
    private function hitungSkor($target, $realisasi, $polaritas)
    {
        // Bersihkan input
        $t = $this->cleanInput($target);
        $r = $this->cleanInput($realisasi);

        $pencapaian = 0;

        // Logika Perhitungan
        if ($t != 0) {
            // Normalisasi polaritas ke huruf kecil agar cocok (Positif/positif/Maximize)
            $p = strtolower($polaritas); 

            if (str_contains($p, 'max') || str_contains($p, 'pos')) { 
                // Maximize / Positif
                $pencapaian = ($r / $t) * 100;
            } 
            elseif (str_contains($p, 'min') || str_contains($p, 'neg')) { 
                // Minimize / Negatif
                $ratio = $r / $t;
                $pencapaian = (2 - $ratio) * 100;
            }
            elseif (str_contains($p, 'yes') || str_contains($p, 'no')) { 
                // Yes/No
                $pencapaian = ($r >= $t) ? 100 : 0;
            }
        } else {
             // Jika Target 0, Realisasi 0 = 100%
             $pencapaian = ($r == 0) ? 100 : 0;
        }

        // Return angka mentah (misal: 120.5), bukan Array
        return max(0, round($pencapaian, 2)); 
    }

    // [BARU] HELPER MEMBERSIHKAN INPUT (Hapus % dan ubah koma)
    private function cleanInput($value)
    {
        if (is_null($value)) return 0;
        $clean = str_replace(['%', ' ', ','], ['', '', '.'], $value);
        return floatval($clean);
    }

    // --- 6. METHOD LAINNYA (TETAP SAMA) ---
    public function destroy($id)
    {
        $kpi = KpiAssessment::findOrFail($id);
        foreach($kpi->items as $item) {
            $item->scores()->delete();
            $item->delete();
        }
        $kpi->delete();
        return redirect()->back()->with('success', 'Data KPI berhasil dihapus/reset.');
    }

    public function finalize($id)
    {
        $kpi = KpiAssessment::findOrFail($id);
        if ($kpi->total_skor_akhir == 0) {
            return redirect()->back()->with('error', 'Tidak bisa finalisasi karena skor masih 0.');
        }
        $kpi->update(['status' => 'FINAL']);
        return redirect()->back()->with('success', 'KPI berhasil difinalisasi!');
    }

    private function determineGrade($skor)
    {
        if ($skor > 89) return 'Great';
        if ($skor > 79) return 'Good';
        if ($skor > 69) return 'Average';
        return 'Need Improvement';
    }

    public function storeItem(Request $request)
    {
        $request->validate([
            'kpi_assessment_id'         => 'required|exists:kpi_assessments,id_kpi_assessment',
            'key_performance_indicator' => 'required|string',
            'bobot'                     => 'required|numeric',
            'target'                    => 'required', // Bisa string "100%"
        ]);

        // Bersihkan target
        $cleanTarget = $this->cleanInput($request->target);

        $newItem = KpiItem::create([
            'kpi_assessment_id'         => $request->kpi_assessment_id,
            'perspektif'                => $request->perspektif,
            'key_result_area'           => $request->key_result_area,
            'key_performance_indicator' => $request->key_performance_indicator,
            'units'                     => $request->units,
            'polaritas'                 => $request->polaritas,
            'bobot'                     => $request->bobot,
            'target'                    => $cleanTarget,
        ]);

        // Create Score
        KpiScore::create([
            'kpi_item_id'  => $newItem->id_kpi_item,
            'nama_periode' => 'Semester 1', // Samakan dengan store()
            'target'       => $cleanTarget,
            'target_smt1'  => $cleanTarget,
            'realisasi'    => 0,
        ]);

        return redirect()->back()->with('success', 'Indikator KPI berhasil ditambahkan!');
    }

    public function destroyItem($id)
    {
        $item = KpiItem::findOrFail($id);
        $item->delete();
        return redirect()->back()->with('success', 'Item KPI berhasil dihapus.');
    }

    public function updateItem(Request $request, $id)
    {
        // 1. Validasi (Target boleh string karena bisa %)
        $request->validate([
            'key_performance_indicator' => 'required|string',
            'bobot'                     => 'required|numeric',
            'target'                    => 'required', 
        ]);

        $item = KpiItem::findOrFail($id);
        
        // 2. Bersihkan Input Target (Hapus % dan ubah koma)
        // Pastikan method cleanInput() sudah ada di controller Anda
        $cleanTarget = $this->cleanInput($request->target);

        // 3. Update Master Item
        $item->update([
            'perspektif'                => $request->perspektif,
            'key_result_area'           => $request->key_result_area,
            'key_performance_indicator' => $request->key_performance_indicator,
            'units'                     => $request->units,
            'polaritas'                 => $request->polaritas,
            'bobot'                     => $request->bobot,
            'target'                    => $cleanTarget,
        ]);

        // 4. PENTING: Update juga Tabel Score agar Form Input berubah
        $score = KpiScore::where('kpi_item_id', $id)->first();
        
        if($score) {
            $score->update([
                'target'      => $cleanTarget,
                
                // Update Target Semester 1
                'target_smt1' => $cleanTarget,
                
                // Update Target Bulanan (Reset ke target baru)
                'target_jul'  => $cleanTarget,
                'target_aug'  => $cleanTarget,
                'target_sep'  => $cleanTarget,
                'target_okt'  => $cleanTarget,
                'target_nov'  => $cleanTarget,
                'target_des'  => $cleanTarget,
            ]);
        }

        return redirect()->back()->with('success', 'KPI berhasil diperbarui!');
    }
}