<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KpiAssessment;
use App\Models\Karyawan;
use App\Models\KpiItem;
use App\Models\KpiScore;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SingleKpiExport;
use Barryvdh\DomPDF\Facade\Pdf;

class KpiAssessmentController extends Controller
{
    // =================================================================
    // 1. INDEX: PENGATUR LALU LINTAS (TRAFFIC CONTROL)
    // =================================================================
    public function index(Request $request)
    {
        $user = Auth::user();
        $tahun = $request->input('tahun', date('Y'));

        // --- SKENARIO 1: ADMIN & SUPERADMIN (Lihat Semua Data) ---
        if (in_array($user->role, ['superadmin', 'admin'])) {

            $query = Karyawan::with(['pekerjaan', 'kpiAssessment' => function ($q) use ($tahun) {
                $q->where('tahun', $tahun);
            }]);

            // Filter Search
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where('Nama_Lengkap_Sesuai_Ijazah', 'LIKE', "%{$search}%")
                    ->orWhere('NIK', 'LIKE', "%{$search}%");
            }

            // Filter Jabatan
            if ($request->has('filter_jabatan') && $request->filter_jabatan != '') {
                $query->whereHas('pekerjaan', function ($q) use ($request) {
                    $q->where('jabatan', $request->filter_jabatan);
                });
            }

            // Filter Status
            if ($request->has('filter_status') && $request->filter_status != '') {
                if ($request->filter_status == 'BELUM_ADA') {
                    $query->whereDoesntHave('kpiAssessment', fn($q) => $q->where('tahun', $tahun));
                } else {
                    $query->whereHas('kpiAssessment', fn($q) => $q->where('tahun', $tahun)->where('status', $request->filter_status));
                }
            }

            // Statistik Sederhana
            $allKaryawan = $query->get(); // Clone query untuk statistik berat, disini pakai simple count saja
            $stats = [
                'total_karyawan' => $allKaryawan->count(),
                'sudah_final' => $allKaryawan->filter(fn($k) => $k->kpiAssessment && $k->kpiAssessment->status == 'FINAL')->count(),
                'draft' => $allKaryawan->filter(fn($k) => $k->kpiAssessment && $k->kpiAssessment->status != 'FINAL')->count(),
                'belum_ada'  => $allKaryawan->filter(fn($k) => !$k->kpiAssessment)->count(),
                'rata_rata' => $allKaryawan->filter(fn($k) => $k->kpiAssessment)->avg(fn($k) => $k->kpiAssessment->total_skor_akhir),
            ];

            // List Jabatan Dropdown
            $listJabatan = DB::table('pekerjaan')->select('jabatan')->distinct()->whereNotNull('jabatan')->orderBy('jabatan')->pluck('jabatan');

            $karyawanList = $query->paginate(10)->appends($request->all());

            return view('pages.kpi.index', compact('karyawanList', 'tahun', 'stats', 'listJabatan'));
        }

        // --- SKENARIO 2: STAFF & MANAGER (Redirect ke Punya Sendiri) ---

        $me = Karyawan::where('nik', $user->nik)->first();

        if (!$me) {
            return redirect()->back()->with('error', 'Profil karyawan tidak ditemukan. Hubungi HRD.');
        }

        // Cek apakah KPI tahun ini sudah ada?
        $existingKpi = KpiAssessment::where('karyawan_id', $me->id_karyawan)
            ->where('tahun', $tahun)
            ->first();

        if ($existingKpi) {
            // Jika sudah ada, langsung BUKA (Show)
            return redirect()->route('kpi.show', [
                'karyawan_id' => $me->id_karyawan,
                'tahun' => $tahun
            ]);
        } else {
            // Jika belum ada, BUAT BARU OTOMATIS (Store)
            // Kita panggil method store manual atau redirect ke route store dengan hidden input
            // Cara paling aman: Tampilkan view konfirmasi "Buat KPI Baru" atau auto-create di sini.

            // Auto Create Header KPI
            $newKpi = KpiAssessment::create([
                'karyawan_id'       => $me->id_karyawan,
                'tahun'             => $tahun,
                'periode'           => 'Tahunan',
                'tanggal_penilaian' => now(),
                'status'            => 'DRAFT',
                'total_skor_akhir'  => 0,
                'penilai_id'        => $user->id
            ]);

            return redirect()->route('kpi.show', [
                'karyawan_id' => $me->id_karyawan,
                'tahun' => $tahun
            ])->with('success', 'Dokumen KPI Tahun ' . $tahun . ' berhasil dibuat. Silakan isi indikator.');
        }
    }

    // =================================================================
    // 2. SHOW: HALAMAN UTAMA FORM KPI (DETAIL & EDIT SCORE)
    // =================================================================
    public function show($karyawanId, $tahun)
    {
        $karyawan = Karyawan::findOrFail($karyawanId);

        // Validasi Akses (Cegah Staff A mengintip Staff B)
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'superadmin'])) {
            // Jika bukan admin, pastikan dia melihat punya sendiri atau punya bawahannya
            $me = Karyawan::where('nik', $user->nik)->first();
            if ($me->id_karyawan != $karyawanId && $karyawan->atasan_id != $me->id_karyawan) {
                return abort(403, 'Anda tidak berhak melihat dokumen ini.');
            }
        }

        $kpi = KpiAssessment::where('karyawan_id', $karyawanId)
            ->where('tahun', $tahun)
            ->first();

        // Jika KPI belum ada, buat baru dengan status DRAFT
        if (!$kpi) {
            $kpi = KpiAssessment::create([
                'karyawan_id' => $karyawanId,
                'tahun' => $tahun,
                'periode' => 'Tahunan',
                'status' => 'DRAFT',
                'total_skor_akhir' => 0,
                'nama_periode' => "KPI Tahun {$tahun}",
            ]);
        }

        $items = KpiItem::where('kpi_assessment_id', $kpi->id_kpi_assessment)
            ->with('scores')
            ->paginate(10); // Pagination untuk item

        return view('pages.kpi.form', compact('karyawan', 'kpi', 'items', 'tahun'));
    }
    

    // =================================================================
    // 3. STORE HEADER (Opsional, karena sudah dihandle di Index)
    // =================================================================
    public function store(Request $request)
    {
        // Method ini dipakai jika Admin membuatkan KPI untuk orang lain
        $request->validate([
            'karyawan_id' => 'required',
            'tahun'       => 'required'
        ]);

        // Cek duplikasi
        $cek = KpiAssessment::where('karyawan_id', $request->karyawan_id)->where('tahun', $request->tahun)->first();
        if ($cek) {
            return redirect()->route('kpi.show', ['karyawan_id' => $request->karyawan_id, 'tahun' => $request->tahun]);
        }

        KpiAssessment::create([
            'karyawan_id' => $request->karyawan_id,
            'tahun' => $request->tahun,
            'periode' => 'Tahunan',
            'status' => 'DRAFT',
            'total_skor_akhir' => 0,
            'nama_periode' => 'Tahunan',
        ]);

        return redirect()->route('kpi.show', ['karyawan_id' => $request->karyawan_id, 'tahun' => $request->tahun]);
    }

    // =================================================================
    // 4. CRUD ITEM & SCORE (Sangat Penting)
    // =================================================================

    // Tambah Indikator Baru
    public function storeItem(Request $request)
    {
        $request->validate([
            'kpi_assessment_id'         => 'required',
            'key_performance_indicator' => 'required',
            'bobot'                     => 'required|numeric',
            'target'                    => 'required',
        ]);

        $cleanTarget = $this->cleanInput($request->target);

        // 1. Simpan Item KPI
        $item = KpiItem::create([
            'kpi_assessment_id'         => $request->kpi_assessment_id,
            'perspektif'                => $request->perspektif,
            'key_result_area'           => $request->key_result_area,
            'key_performance_indicator' => $request->key_performance_indicator,
            'units'                     => $request->units,
            'polaritas'                 => $request->polaritas,
            'bobot'                     => $request->bobot,
            'target'                    => $cleanTarget,
        ]);

        // 2. Simpan Score (Disini Error 1364 Muncul)
        KpiScore::create([
            'kpi_item_id'  => $item->id_kpi_item,
            'target'       => $cleanTarget,
            'target_smt1'  => $cleanTarget,

            // PERBAIKAN DISINI: Jangan pakai $request->nama_periode
            'nama_periode' => 'Semester 1', // <--- ISI MANUAL AGAR TIDAK EROR

            'realisasi'    => 0
        ]);

        return redirect()->back()->with('success', 'Indikator berhasil ditambahkan');
    }

    // Update Nilai / Realisasi (Dipanggil saat tombol Simpan di form ditekan)
    public function update(Request $request, $id_kpi_assessment)
    {
        $assessment = KpiAssessment::with('karyawan')->findOrFail($id_kpi_assessment);
        $inputs = $request->input('kpi'); // Array dari form

        if (!$inputs) return redirect()->back()->with('error', 'Tidak ada data dikirim.');

        DB::beginTransaction();
        try {
            foreach ($inputs as $itemId => $data) {

                $item = KpiItem::find($itemId);
                if (!$item) continue;

                $score = KpiScore::where('kpi_item_id', $itemId)->first();

                if ($score) {
                    // ====================================================
                    // 1. AMBIL SEMUA INPUT (BERSIHKAN DARI KOMA/PERSEN)
                    // ====================================================

                    // --- Semester 1 ---
                    $target1 = $this->cleanInput($data['target_smt1'] ?? $item->target);
                    $real1   = $this->cleanInput($data['real_smt1'] ?? 0);
                    // Tangkap Adjustment Smt 1
                    $adjReal1 = isset($data['adjustment_real_smt1']) ? $this->cleanInput($data['adjustment_real_smt1']) : null;

                    // --- Bulanan (Juli - Desember) ---
                    // WAJIB DITANGKAP AGAR TIDAK HILANG
                    $t_jul = $this->cleanInput($data['target_jul'] ?? 0);
                    $r_jul = $this->cleanInput($data['real_jul'] ?? 0);
                    $t_aug = $this->cleanInput($data['target_aug'] ?? 0);
                    $r_aug = $this->cleanInput($data['real_aug'] ?? 0);
                    $t_sep = $this->cleanInput($data['target_sep'] ?? 0);
                    $r_sep = $this->cleanInput($data['real_sep'] ?? 0);
                    $t_okt = $this->cleanInput($data['target_okt'] ?? 0);
                    $r_okt = $this->cleanInput($data['real_okt'] ?? 0);
                    $t_nov = $this->cleanInput($data['target_nov'] ?? 0);
                    $r_nov = $this->cleanInput($data['real_nov'] ?? 0);
                    $t_des = $this->cleanInput($data['target_des'] ?? 0);
                    $r_des = $this->cleanInput($data['real_des'] ?? 0);

                    // --- Semester 2 (Manual) ---
                    $target2 = $this->cleanInput($data['total_target_smt2'] ?? 0);
                    $real2   = $this->cleanInput($data['total_real_smt2'] ?? 0);
                    // Tangkap Adjustment Smt 2
                    $adjReal2   = isset($data['adjustment_real_smt2']) ? $this->cleanInput($data['adjustment_real_smt2']) : null;
                    $adjTarget2 = isset($data['adjustment_target_smt2']) ? $this->cleanInput($data['adjustment_target_smt2']) : null;

                    // ====================================================
                    // 2. HITUNG SKOR DI BACKEND (LOGIKA PENILAIAN)
                    // ====================================================

                    // --- Hitung SMT 1 ---
                    // Gunakan Adjustment Real jika ada, jika tidak pakai Real biasa
                    $real1Final = ($adjReal1 !== null && $data['adjustment_real_smt1'] !== "") ? $adjReal1 : $real1;
                    $skor1      = $this->hitungSkor($target1, $real1Final, $item->polaritas);

                    // --- Hitung SMT 2 ---
                    // Gunakan Adjustment Target/Real jika ada
                    $target2Final = ($adjTarget2 !== null && $data['adjustment_target_smt2'] !== "") ? $adjTarget2 : $target2;
                    $real2Final   = ($adjReal2 !== null && $data['adjustment_real_smt2'] !== "") ? $adjReal2 : $real2;
                    $skor2        = $this->hitungSkor($target2Final, $real2Final, $item->polaritas);

                    // --- Final Score Item ---
                    $pencapaianTotal = ($skor1 + $skor2) / 2;
                    $finalSkorItem   = ($pencapaianTotal * $item->bobot) / 100;

                    // ====================================================
                    // 3. SIMPAN KE DATABASE (UPDATE LENGKAP)
                    // ====================================================
                    $score->update([
                        // Data Semester 1
                        'target_smt1' => $target1,
                        'real_smt1'   => $real1,
                        'adjustment_real_smt1' => $adjReal1, // <--- Jangan Lupa Disimpan

                        // Data Bulanan (AGAR TIDAK HILANG)
                        'target_jul' => $t_jul,
                        'real_jul' => $r_jul,
                        'target_aug' => $t_aug,
                        'real_aug' => $r_aug,
                        'target_sep' => $t_sep,
                        'real_sep' => $r_sep,
                        'target_okt' => $t_okt,
                        'real_okt' => $r_okt,
                        'target_nov' => $t_nov,
                        'real_nov' => $r_nov,
                        'target_des' => $t_des,
                        'real_des' => $r_des,

                        // Data Semester 2
                        'total_target_smt2' => $target2,
                        'total_real_smt2'   => $real2,
                        'adjustment_target_smt2' => $adjTarget2, // <--- Jangan Lupa Disimpan
                        'adjustment_real_smt2'   => $adjReal2,   // <--- Jangan Lupa Disimpan

                        // Skor Akhir
                        'skor_akhir' => $finalSkorItem
                    ]);
                }
            }

            // Hitung Total Header
            $grandTotal = KpiScore::join('kpi_items', 'kpi_scores.kpi_item_id', '=', 'kpi_items.id_kpi_item')
                ->where('kpi_items.kpi_assessment_id', $id_kpi_assessment)
                ->sum('kpi_scores.skor_akhir');

            $user = Auth::user();
            $statusSekarang = $assessment->status;
            $statusBaru = $statusSekarang; // Default tidak berubah

            // SKENARIO 1: STAFF (Pemilik KPI) KLIK SIMPAN
            // Jika yang login adalah Staff, otomatis jadi "SUBMITTED" (Menunggu Approval)
            if ($user->role == 'staff') {
                $statusBaru = 'SUBMITTED';
            }

            // SKENARIO 2: MANAGER / ADMIN KLIK SIMPAN
            // Jika Manager/Admin yang simpan, otomatis jadi "FINAL" (Approved)
            elseif (in_array($user->role, ['manager', 'admin', 'superadmin'])) {
                $statusBaru = 'FINAL';
            }

            // Update Header KPI
            $assessment->update([
                'total_skor_akhir' => $grandTotal,
                'grade'            => $this->determineGrade($grandTotal),
                'status'           => $statusBaru, // <--- PENTING: UPDATE STATUS DISINI
            ]);

            DB::commit();

            // Pesan Feedback Disesuaikan
            $pesan = ($statusBaru == 'FINAL') ? 'Data disetujui & difinalisasi.' : 'Data berhasil dikirim ke Atasan.';

            return redirect()->back()->with('success', $pesan . ' Skor Akhir: ' . number_format($grandTotal, 2));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    // =================================================================
    // 5. HELPER FUNCTION
    // =================================================================

    private function cleanInput($value)
    {
        if (is_null($value)) return 0;
        return floatval(str_replace(['%', ','], ['', '.'], $value));
    }

    private function hitungSkor($target, $realisasi, $polaritas)
    {
        $t = $this->cleanInput($target);
        $r = $this->cleanInput($realisasi);

        if ($t == 0) return 0;

        $p = strtolower($polaritas);
        if (str_contains($p, 'min')) {
            // Minimize: Makin kecil makin bagus
            return ($t / ($r == 0 ? 1 : $r)) * 100; // Rumus sederhana minimize
        } else {
            // Maximize: Makin besar makin bagus
            return ($r / $t) * 100;
        }
    }

    private function determineGrade($skor)
    {
        if ($skor > 90) return 'Great';
        if ($skor > 80) return 'Good';
        if ($skor > 70) return 'Standard';
        return 'Low';
    }

    // =================================================================
    // 6. UPDATE ITEM & DELETE ITEM (INI YANG HILANG)
    // =================================================================

    // Hapus Item KPI
    public function destroyItem($id)
    {
        $item = KpiItem::findOrFail($id);

        // Hapus skor terkait dulu agar bersih
        KpiScore::where('kpi_item_id', $id)->delete();

        // Hapus itemnya
        $item->delete();

        return redirect()->back()->with('success', 'Indikator KPI berhasil dihapus.');
    }

    // Update Item KPI (Edit via Modal)
    public function updateItem(Request $request, $id)
    {
        // 1. Validasi
        $request->validate([
            'key_performance_indicator' => 'required|string',
            'bobot'                     => 'required|numeric',
            'target'                    => 'required',
        ]);

        $item = KpiItem::findOrFail($id);

        // 2. Bersihkan Input Target
        $cleanTarget = $this->cleanInput($request->target);

        // 3. Update Master Item
        $item->update([
            'perspektif'                => $request->perspektif,
            'key_result_area'           => $request->key_result_area, // atau 'kra' sesuaikan database
            'key_performance_indicator' => $request->key_performance_indicator, // atau 'indikator'
            'units'                     => $request->units,
            'polaritas'                 => $request->polaritas,
            'bobot'                     => $request->bobot,
            'target'                    => $cleanTarget,
        ]);

        // 4. Update Tabel Score juga (agar Target di tabel berubah)
        $score = KpiScore::where('kpi_item_id', $id)->first();

        if ($score) {
            $score->update([
                'target'      => $cleanTarget,
                'target_smt1' => $cleanTarget,
                // Reset target bulanan ke target baru
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
