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
            // --- BAGIAN INI YANG BERUBAH DRASTIS ---
            
            foreach ($inputs as $itemId => $data) {
                // 1. Ambil Data Item dari DB
                $item = KpiItem::find($itemId);
                if (!$item) continue;

                $scoreRecord = KpiScore::where('kpi_item_id', $itemId)
                                       ->where('nama_periode', 'Semester 1') 
                                       ->first();

                if ($scoreRecord) {
                    // 2. Siapkan Array untuk Update Data Bulanan
                    // Kita akan menampung data update di variable $updateData
                    $updateData = [
                        'adjustment' => $data['adjustment'] ?? null, // Ambil input Adjustment
                    ];

                    // 3. Loop Bulan (Juli - Desember) untuk Hitung Total Semester
                    $totalTargetSemester = 0;
                    $totalRealSemester = 0;
                    $listBulan = ['jul', 'aug', 'sep', 'okt', 'nov', 'des'];

                    foreach ($listBulan as $bln) {
                        // Ambil input per bulan (default 0 jika kosong)
                        $t = $data['target_' . $bln] ?? 0;
                        $r = $data['real_' . $bln] ?? 0;

                        // Masukkan ke array untuk disimpan ke DB
                        $updateData['target_' . $bln] = $t;
                        $updateData['real_' . $bln] = $r;

                        // Akumulasi Total Semester
                        $totalTargetSemester += $t;
                        $totalRealSemester += $r;
                    }

                    // 4. Hitung Skor Murni (Berdasarkan Total Semester)
                    $hasilHitung = $this->hitungSkor(
                        $totalTargetSemester,
                        $totalRealSemester,
                        $item->polaritas,
                        $item->bobot
                    );

                    // 5. Logika Adjustment (PENTING!)
                    // Jika ada input adjustment, pakai itu. Jika tidak, pakai skor murni sistem.
                    $adj = $updateData['adjustment'];
                    $skorFinalItem = ($adj !== null && $adj !== '') ? $adj : $hasilHitung['skor_mentah'];
                    
                    // Hitung Skor Akhir Terbobot
                    $skorAkhirBobot = ($skorFinalItem * $item->bobot) / 100;

                    // 6. Masukkan sisa data ke array update
                    $updateData['target']     = $totalTargetSemester; // Total target disimpan di kolom utama
                    $updateData['realisasi']  = $totalRealSemester;   // Total realisasi disimpan di kolom utama
                    $updateData['skor']       = $skorFinalItem;
                    $updateData['skor_akhir'] = $skorAkhirBobot;

                    // 7. Eksekusi Update ke Database
                    $scoreRecord->update($updateData);
                }
            }
            // --- AKHIR BAGIAN YANG DIUBAH ---


            // 2. HITUNG ULANG TOTAL SKOR (Tetap Sama)
            $totalSkor = KpiScore::whereHas('item', function($q) use ($id_kpi_assessment) {
                $q->where('kpi_assessment_id', $id_kpi_assessment);
            })->sum('skor_akhir');

            // 3. TENTUKAN GRADE (Tetap Sama)
            $grade = $this->determineGrade($totalSkor);

            // 4. UPDATE HEADER ASSESSMENT (Tetap Sama)
            $assessment->update([
                'total_skor_akhir' => $totalSkor,
                'grade'            => $grade,
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
        // Logika sesuai gambar Spreadsheet atasan
        if ($skor > 89)  return 'Great';             // Hijau Tua
        if ($skor > 79)  return 'Good';              // Hijau/Biru
        if ($skor > 69)  return 'Average';           // Kuning
        return 'Need Improvement';                   // Merah (<= 69)
    }

 

        public function storeItem(Request $request)
        {
            // 1. Validasi Input
            $request->validate([
                'kpi_assessment_id'         => 'required|exists:kpi_assessments,id_kpi_assessment',
                'key_performance_indicator' => 'required|string',
                'bobot'                     => 'required|numeric',
                'target'                    => 'required|numeric',
            ]);

            // 2. Simpan Item KPI (Pertanyaannya)
            $newItem = KpiItem::create([
                'kpi_assessment_id'         => $request->kpi_assessment_id,
                'perspektif'                => $request->perspektif,
                'key_result_area'           => $request->key_result_area,
                'key_performance_indicator' => $request->key_performance_indicator,
                'units'                     => $request->units,
                'polaritas'                 => $request->polaritas,
                'bobot'                     => $request->bobot,
                'target'                    => $request->target,
            ]);

            // 3. PENTING: Buat Slot Score Kosong (Agar kolom input bulanan muncul)
            // Kita isi default target bulanan dengan target tahunan (opsional, atau 0)
            KpiScore::create([
                'kpi_item_id'  => $newItem->id_kpi_item, 
                'nama_periode' => 'Full Year 2025',
                
                // Kolom Wajib
                'target'      => $newItem->target, 
                'realisasi'   => 0,

                // Inisialisasi Kolom Bulanan dengan 0
                'target_smt1' => $newItem->target, 'real_smt1' => 0,
                
                'target_jul' => $newItem->target, 'real_jul' => 0,
                'target_aug' => $newItem->target, 'real_aug' => 0,
                'target_sep' => $newItem->target, 'real_sep' => 0,
                'target_okt' => $newItem->target, 'real_okt' => 0,
                'target_nov' => $newItem->target, 'real_nov' => 0,
                'target_des' => $newItem->target, 'real_des' => 0,
            ]);

            return redirect()->back()->with('success', 'Indikator KPI berhasil ditambahkan!');
        }


        public function destroyItem($id)
        {
            // 1. Cari Item berdasarkan ID
            $item = KpiItem::findOrFail($id);

            // 2. Hapus Item
            // Note: Karena setting database biasanya 'ON DELETE CASCADE', 
            // maka Score (Nilai) terkait item ini otomatis ikut terhapus.
            $item->delete();

            // 3. Kembali ke halaman sebelumnya
            return redirect()->back()->with('success', 'Item KPI berhasil dihapus.');
        }

        public function updateItem(Request $request, $id)
        {
            // 1. Validasi
            $request->validate([
                'key_performance_indicator' => 'required|string',
                'bobot'                     => 'required|numeric',
                'target'                    => 'required|numeric',
            ]);

            // 2. Ambil Item & Update
            $item = KpiItem::findOrFail($id);
            
            $item->update([
                'perspektif'                => $request->perspektif,
                'key_result_area'           => $request->key_result_area,
                'key_performance_indicator' => $request->key_performance_indicator,
                'units'                     => $request->units,
                'polaritas'                 => $request->polaritas,
                'bobot'                     => $request->bobot,
                'target'                    => $request->target,
            ]);

            // 3. (Opsional) Update Target di Score jika berubah
            // Agar nilai referensi di tabel input ikut berubah
            $score = KpiScore::where('kpi_item_id', $id)->first();
            if($score) {
                $score->update(['target' => $request->target]);
            }

            return redirect()->back()->with('success', 'KPI berhasil diperbarui!');
        }

}