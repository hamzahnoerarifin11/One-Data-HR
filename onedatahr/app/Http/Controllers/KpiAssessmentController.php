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
            
            $query = Karyawan::with(['pekerjaan', 'kpiAssessment' => function($q) use ($tahun) {
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
                $query->whereHas('pekerjaan', function($q) use ($request) {
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
                'total' => $allKaryawan->count(),
                'final' => $allKaryawan->filter(fn($k) => $k->kpiAssessment && $k->kpiAssessment->status == 'FINAL')->count(),
                'draft' => $allKaryawan->filter(fn($k) => $k->kpiAssessment && $k->kpiAssessment->status != 'FINAL')->count(),
                'void'  => $allKaryawan->filter(fn($k) => !$k->kpiAssessment)->count(),
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
            ])->with('success', 'Dokumen KPI Tahun '.$tahun.' berhasil dibuat. Silakan isi indikator.');
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

        if (!$kpi) {
            return redirect()->route('kpi.index')->with('error', 'Data KPI tidak ditemukan.');
        }

        $items = KpiItem::where('kpi_assessment_id', $kpi->id_kpi_assessment)
                        ->with('scores')
                        ->paginate(10); // Pagination untuk item

        return view('pages.kpi.form', compact('karyawan', 'kpi', 'items'));
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
            'status' => 'DRAFT',
            'total_skor_akhir' => 0
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

        $item = KpiItem::create([
            'kpi_assessment_id'         => $request->kpi_assessment_id,
            'perspektif'                => $request->perspektif,
            'key_result_area'           => $request->key_result_area,
            'key_performance_indicator' => $request->key_performance_indicator,
            'units'                     => $request->units, // Pastikan kolom di DB 'units' atau 'satuan'
            'polaritas'                 => $request->polaritas,
            'bobot'                     => $request->bobot,
            'target'                    => $cleanTarget
        ]);

        // Otomatis Buat Wadah Nilainya
        KpiScore::create([
            'kpi_item_id' => $item->id_kpi_item, // Gunakan ID yang baru dibuat
            'target'      => $cleanTarget,
            'target_smt1' => $cleanTarget, // Default target smt 1
            'realisasi'   => 0
        ]);

        return redirect()->back()->with('success', 'Indikator berhasil ditambahkan');
    }

    // Update Nilai / Realisasi (Dipanggil saat tombol Simpan di form ditekan)
    public function update(Request $request, $id_kpi_assessment)
    {
        $assessment = KpiAssessment::with('karyawan')->findOrFail($id_kpi_assessment);
        $inputs = $request->input('kpi'); // Array dari form

        if (!$inputs) return redirect()->back();

        DB::beginTransaction();
        try {
            foreach ($inputs as $itemId => $data) {
                $item = KpiItem::find($itemId);
                if (!$item) continue;

                $score = KpiScore::where('kpi_item_id', $itemId)->first();
                if ($score) {
                    // Logic Hitung SMT 1
                    $target1 = $this->cleanInput($data['target_smt1'] ?? $item->target);
                    $real1   = $this->cleanInput($data['real_smt1'] ?? 0);
                    $skor1   = $this->hitungSkor($target1, $real1, $item->polaritas);
                    // Adjustment logic... (Sederhanakan jika perlu)
                    
                    // Logic Hitung SMT 2
                    $target2 = $this->cleanInput($data['total_target_smt2'] ?? 0);
                    $real2   = $this->cleanInput($data['total_real_smt2'] ?? 0);
                    $skor2   = $this->hitungSkor($target2, $real2, $item->polaritas);

                    // Final Calculation
                    // Asumsi: Skor Item = (Capaian Smt 1 + Capaian Smt 2) / 2 * Bobot
                    // Atau sesuaikan dengan rumus perusahaan Anda
                    $pencapaianTotal = ($skor1 + $skor2) / 2; 
                    $finalSkorItem   = ($pencapaianTotal * $item->bobot) / 100;

                    $score->update([
                        'target_smt1' => $target1,
                        'real_smt1'   => $real1,
                        'total_target_smt2' => $target2,
                        'total_real_smt2'   => $real2,
                        'skor_akhir'        => $finalSkorItem
                    ]);
                }
            }

            // Hitung Ulang Total Header
            $grandTotal = KpiScore::join('kpi_items', 'kpi_scores.kpi_item_id', '=', 'kpi_items.id_kpi_item')
                ->where('kpi_items.kpi_assessment_id', $id_kpi_assessment)
                ->sum('kpi_scores.skor_akhir');

            $assessment->update([
                'total_skor_akhir' => $grandTotal,
                'grade' => $this->determineGrade($grandTotal),
                // Logika ganti status jadi FINAL jika admin/manager
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil disimpan. Skor: ' . number_format($grandTotal, 2));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
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

    // --- Method DestroyItem & UpdateItem ---
    // (Bisa copy paste dari kode Anda sebelumnya, sudah benar)
}