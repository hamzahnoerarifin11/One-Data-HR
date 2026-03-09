<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KpiAssessment;
use App\Models\Karyawan;
use App\Models\KpiItem;
use App\Models\KpiScore;
use App\Models\KpiPerspective;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\KpiCalculationService;

class KpiAssessmentController extends Controller
{
    protected $calculationService;

    public function __construct(KpiCalculationService $calculationService)
    {
        $this->calculationService = $calculationService;
    }
    public function getPerspektifAktif(){
        return KpiPerspective::where('is_active', true)
            ->orderBy('name')
            ->pluck('name');
    }
    // =================================================================
    // 1. INDEX: PENGATUR LALU LINTAS (TRAFFIC CONTROL)
    // =================================================================
    public function index(Request $request)
    {
        $user = Auth::user();
        $tahun = $request->input('tahun', date('Y'));


        // --- SKENARIO 1: ADMIN & SUPERADMIN (Lihat Semua Data) ---
        if ($this->roleMatches($user, ['superadmin', 'admin'])) {

            $query = Karyawan::with(['pekerjaan.company', 'pekerjaan.position', 'pekerjaan.division', 'pekerjaan.department', 'kpiAssessment' => function ($q) use ($tahun) {
                $q->where('tahun', $tahun);
            }]);

            $this->applyIndexFilters($query, $request, $tahun);

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
            $listJabatan = \App\Models\Level::distinct()->orderBy('name')->pluck('name');

            // List Divisi Dropdown
            $listDivisi = \App\Models\Division::distinct()->orderBy('name')->pluck('name');

            // List Companies Dropdown
            $listCompanies = \App\Models\Company::distinct()->orderBy('name')->pluck('name');

            $karyawanList = $query->orderBy('Nama_Lengkap_Sesuai_Ijazah', 'ASC')->paginate(10)->appends($request->all());

            return view('pages.kpi.index', compact('karyawanList', 'tahun', 'stats', 'listJabatan', 'listCompanies', 'listDivisi'));
        }

        // --- SKENARIO 1.B: DIREKTUR (Lihat Semua GM & Manager) ---
        if ($this->roleMatches($user, 'direktur')) {
             $me = Karyawan::where('nik', $user->nik)->first();
             $query = Karyawan::with(['pekerjaan.company', 'pekerjaan.position', 'pekerjaan.division', 'pekerjaan.department', 'pekerjaan.level', 'kpiAssessment' => function ($q) use ($tahun) {
                $q->where('tahun', $tahun);
            }])->whereHas('pekerjaan.level', function($q) {
                $q->where('name', 'LIKE', '%Manager%')
                  ->orWhere('name', 'LIKE', '%General Manager%');
            })->orWhere('atasan_id', $me->id_karyawan ?? 0); // Tetap masukkan direct subordinate jika ada

            $this->applyIndexFilters($query, $request, $tahun);

            $allKaryawan = $query->get();
            $stats = [
                'total_karyawan' => $allKaryawan->count(),
                'sudah_final' => $allKaryawan->filter(fn($k) => $k->kpiAssessment && $k->kpiAssessment->status == 'FINAL')->count(),
                'draft' => $allKaryawan->filter(fn($k) => $k->kpiAssessment && $k->kpiAssessment->status != 'FINAL')->count(),
                'belum_ada'  => $allKaryawan->filter(fn($k) => !$k->kpiAssessment)->count(),
                'rata_rata' => $allKaryawan->filter(fn($k) => $k->kpiAssessment)->avg(fn($k) => $k->kpiAssessment->total_skor_akhir),
            ];

            $listJabatan = \App\Models\Level::distinct()->orderBy('name')->pluck('name');
            $listCompanies = \App\Models\Company::distinct()->orderBy('name')->pluck('name');
            $listDivisi = \App\Models\Division::distinct()->orderBy('name')->pluck('name');

            $karyawanList = $query->orderBy('Nama_Lengkap_Sesuai_Ijazah', 'ASC')->paginate(10)->appends($request->all());

            return view('pages.kpi.index', compact('karyawanList', 'tahun', 'stats', 'listJabatan', 'listCompanies', 'listDivisi', 'me'));
        }

        // --- SKENARIO 2: MANAGER (Dashboard Bawahan) ---

        $me = Karyawan::where('nik', $user->nik)->first();

        if (!$me) {
            return redirect()->back()->with('error', 'Profil karyawan tidak ditemukan. Hubungi HRD.');
        }

        // Jika yang login adalah Manager / GM / Senior Manager: tampilkan dashboard bawahan
        if ($this->roleMatches($user, ['manager', 'GM', 'senior_manager'])) {
            
            // LOGIC FIX: SELALU TAMPILKAN SATU DIVISI
            // Tidak peduli apakah mereka sudah punya atasan atau belum.
            // Manager harus bisa melihat semua staff di divisinya.

            $pekerjaanManager = $me->pekerjaan()
                ->orderByDesc('id_pekerjaan')
                ->first();

            if (!$pekerjaanManager || !$pekerjaanManager->division_id) {
                // Jika Manager tidak punya divisi, tampilkan pesan error atau kosong
                // Option: abort(403, 'Manager tidak memiliki divisi.');
                // Better: Return empty list to avoid crash
                 $karyawanList = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
                 $stats = [
                    'total_karyawan' => 0,
                    'sudah_final' => 0,
                    'draft' => 0,
                    'belum_ada' => 0,
                    'rata_rata' => 0,
                ];
                $listJabatan = [];
                $listCompanies = [];
                $listDivisi = [];

                return view('pages.kpi.index', compact('karyawanList', 'tahun', 'stats', 'listJabatan', 'listCompanies', 'listDivisi', 'me'))->with('error', 'Akun Anda tidak terdaftar dalam divisi manapun.');
            }

            $divisionId = $pekerjaanManager->division_id;
            $listJabatan = \App\Models\Level::distinct()->orderBy('name')->pluck('name');
            $listCompanies = \App\Models\Company::distinct()->orderBy('name')->pluck('name');
            $listDivisi = \App\Models\Division::distinct()->orderBy('name')->pluck('name');

            $query = Karyawan::with([
                'pekerjaan.company',
                'pekerjaan.position',
                'pekerjaan.division',
                'pekerjaan.department',
                'kpiAssessment' => function ($q) use ($tahun) {
                    $q->where('tahun', $tahun);
                }
            ])->whereHas('pekerjaan', function ($q) use ($divisionId) {
                $q->where('division_id', $divisionId);
            });

            $this->applyIndexFilters($query, $request, $tahun);

            // Bangun statistik berdasarkan query yang sudah dibuat
            $allKaryawan = $query->orderBy('Nama_Lengkap_Sesuai_Ijazah', 'ASC')->get();
            $stats = [
                'total_karyawan' => $allKaryawan->count(),
                'sudah_final' => $allKaryawan->filter(fn($k) => $k->kpiAssessment && $k->kpiAssessment->status == 'FINAL')->count(),
                'draft' => $allKaryawan->filter(fn($k) => $k->kpiAssessment && $k->kpiAssessment->status != 'FINAL')->count(),
                'belum_ada'  => $allKaryawan->filter(fn($k) => !$k->kpiAssessment)->count(),
                'rata_rata' => $allKaryawan->filter(fn($k) => $k->kpiAssessment)->avg(fn($k) => $k->kpiAssessment->total_skor_akhir),
            ];

            // Paginasi untuk daftar karyawan (bisa dipakai di view ->links())
            $karyawanList = $query->orderBy('Nama_Lengkap_Sesuai_Ijazah', 'ASC')->paginate(10)->appends($request->all());

            return view('pages.kpi.index', compact('karyawanList', 'tahun', 'stats', 'listJabatan', 'listCompanies', 'listDivisi', 'me'));
        }

        // =====================================================================
        // SUPERVISOR: hanya bisa lihat karyawan di DIVISI yang sama dan level di bawah
        // =====================================================================
        if ($this->roleMatches($user, 'supervisor')) {
            $pekerjaanSup = $me->pekerjaan()->orderByDesc('id_pekerjaan')->first();
            if (!$pekerjaanSup || !$pekerjaanSup->division_id) {
                abort(403, 'Supervisor tidak memiliki divisi.');
            }

            $divisionId = $pekerjaanSup->division_id;
            $supLevelOrder = $pekerjaanSup->level->level_order ?? null;

            $listJabatan = \App\Models\Level::distinct()->orderBy('name')->pluck('name');
            $listCompanies = \App\Models\Company::distinct()->orderBy('name')->pluck('name');
            $listDivisi = \App\Models\Division::distinct()->orderBy('name')->pluck('name');

            $query = Karyawan::with([
                'pekerjaan.company',
                'pekerjaan.position',
                'pekerjaan.division',
                'pekerjaan.department',
                'kpiAssessment' => function ($q) use ($tahun) {
                    $q->where('tahun', $tahun);
                }
            ])->whereHas('pekerjaan', function ($q) use ($divisionId, $supLevelOrder) {
                $q->where('division_id', $divisionId);
                if ($supLevelOrder !== null) {
                    // hanya yang level_order lebih besar (lebih rendah posisinya)
                    $q->whereHas('level', function ($l) use ($supLevelOrder) {
                        $l->where('level_order', '>', $supLevelOrder);
                    });
                }
            });

            $this->applyIndexFilters($query, $request, $tahun);

            // Statistik & pagination
            $allKaryawan = $query->get();
            $stats = [
                'total_karyawan' => $allKaryawan->count(),
                'sudah_final' => $allKaryawan->filter(fn($k) => $k->kpiAssessment && $k->kpiAssessment->status == 'FINAL')->count(),
                'draft' => $allKaryawan->filter(fn($k) => $k->kpiAssessment && $k->kpiAssessment->status != 'FINAL')->count(),
                'belum_ada'  => $allKaryawan->filter(fn($k) => !$k->kpiAssessment)->count(),
                'rata_rata' => $allKaryawan->filter(fn($k) => $k->kpiAssessment)->avg(fn($k) => $k->kpiAssessment->total_skor_akhir),
            ];

            $karyawanList = $query->orderBy('Nama_Lengkap_Sesuai_Ijazah', 'ASC')->paginate(10)->appends($request->all());

            return view('pages.kpi.index', compact('karyawanList', 'tahun', 'stats', 'listJabatan', 'listCompanies', 'listDivisi', 'me'));
        }

        // --- SKENARIO 3: STAFF (Redirect ke Punya Sendiri) ---

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

        // Validasi Akses
        $user = Auth::user();

        // 1) Admin / Superadmin => akses penuh
        if ($this->roleMatches($user, ['admin', 'superadmin'])) {
            // nothing to check
        }
        // 2) Manager / GM / Senior Manager => boleh lihat milik sendiri, bawahan langsung, atau bawahan level-2
        elseif ($this->roleMatches($user, ['manager', 'GM', 'senior_manager','direktur'])) {
            $me = Karyawan::where('nik', $user->nik)->first();
            $allowed = false;
            if ($me && $me->id_karyawan == $karyawanId) $allowed = true; // melihat punya sendiri
            if ($karyawan->atasan_id == ($me->id_karyawan ?? null)) $allowed = true; // direct subordinate
            if ($karyawan->atasan && $karyawan->atasan->atasan_id == ($me->id_karyawan ?? null)) $allowed = true; // level-2

            // Jika belum diizinkan oleh aturan direct/level-2, cek fallback DIVISI (skenario index: manager dengan no direct bawahan)
            if (!$allowed && $me) {
                $pekerjaanManager = $me->pekerjaan()->orderByDesc('id_pekerjaan')->first();
                if ($pekerjaanManager && $pekerjaanManager->division_id) {
                    $kryP = $karyawan->pekerjaan()->orderByDesc('id_pekerjaan')->first();
                    if ($kryP && $kryP->division_id == $pekerjaanManager->division_id) {
                        $allowed = true;
                    }
                }
            }

            if (!$allowed) return abort(403, 'Anda tidak berhak melihat dokumen ini.');
        }
        // 3) Supervisor => hanya yang ada di DIVISI yang sama dan memiliki level di bawah supervisor (atau milik sendiri)
        elseif ($this->roleMatches($user, 'supervisor')) {
            $me = Karyawan::where('nik', $user->nik)->first();
            $allowed = false;
            if ($me && $me->id_karyawan == $karyawanId) $allowed = true; // melihat punya sendiri

            $pekerjaanSup = $me?->pekerjaan()->orderByDesc('id_pekerjaan')->first();
            if ($pekerjaanSup && $pekerjaanSup->division_id) {
                $divisionId = $pekerjaanSup->division_id;
                $supLevelOrder = $pekerjaanSup->level->level_order ?? null;

                $kryP = $karyawan->pekerjaan()->orderByDesc('id_pekerjaan')->first();
                if ($kryP && $kryP->division_id == $divisionId) {
                    if ($supLevelOrder !== null && ($kryP->level->level_order ?? 9999) > $supLevelOrder) {
                        $allowed = true;
                    } else {
                        // jika level supervisor tidak terdefinisi, berikan akses hanya untuk direct subordinate
                        if ($karyawan->atasan_id == ($me->id_karyawan ?? null)) $allowed = true;
                    }
                }
            }

            if (!$allowed) return abort(403, 'Anda tidak berhak melihat dokumen ini.');
        }
        // 4) Lainnya (staff) => hanya milik sendiri atau fallback bawahan direct / level-2 jika diperlukan
        else {
            $me = Karyawan::where('nik', $user->nik)->first();
            $allowed = false;
            if ($me && $me->id_karyawan == $karyawanId) $allowed = true;
            if ($karyawan->atasan_id == ($me->id_karyawan ?? null)) $allowed = true;
            if ($karyawan->atasan && $karyawan->atasan->atasan_id == ($me->id_karyawan ?? null)) $allowed = true;
            if (!$allowed) return abort(403, 'Anda tidak berhak melihat dokumen ini.');
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
            ]);
        }

        $items = KpiItem::where('kpi_assessment_id', $kpi->id_kpi_assessment)
            ->with('scores')
            ->paginate(10); // Pagination untuk item

        $perspektifList = $this->getPerspektifAktif();
        
        // Cek Hak Akses Adjustment
        $canAdjust = $this->canAdjust($user, $karyawan);

        return view('pages.kpi.form', compact('karyawan', 'kpi', 'items', 'tahun', 'perspektifList', 'canAdjust'));
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
            'key_result_area'           => 'required|string',
            'key_performance_indicator' => 'required|string',
            'units'                     => 'required|string',
            'bobot'                     => 'required|numeric',
            'target'                    => 'required|numeric',
            'polaritas'                 => 'required|string',
            'calculation_method'        => 'required|in:positive,negative,progress', // Added
            'perspektif'                => 'nullable|string',
        ]);

        // Bersihkan input target
        $target = $this->cleanInput($request->target);

        // 1. Simpan Item KPI
        $item = KpiItem::create([
            'kpi_assessment_id'         => $request->kpi_assessment_id,
            'perspektif'                => $request->perspektif,
            'key_result_area'           => $request->key_result_area,
            'key_performance_indicator' => $request->key_performance_indicator,
            'units'                     => $request->units,
            'polaritas'                 => $request->polaritas,
            'calculation_method'        => $request->calculation_method, // Added
            'previous_progress'         => $request->previous_progress ?? 0, // Added
            'bobot'                     => $request->bobot,
            'target'                    => $target,
        ]);

        // 2. Simpan Score
        // Target bulanan di-set sama dengan target tahunan/global sebagai default
        KpiScore::create([
            'kpi_item_id'  => $item->id_kpi_item,
            'target'       => $target,
            'target_smt1'  => $target,
            'nama_periode' => 'Semester 1',
            'realisasi'    => 0,
            // Init monthly targets
            'target_jan' => $target, 'target_feb' => $target, 'target_mar' => $target,
            'target_apr' => $target, 'target_mei' => $target, 'target_jun' => $target,
            'target_jul' => $target, 'target_aug' => $target, 'target_sep' => $target,
            'target_okt' => $target, 'target_nov' => $target, 'target_des' => $target,
        ]);

        return redirect()->back()->with('success', 'Indikator berhasil ditambahkan');
    }

    // Update Nilai / Realisasi (Dipanggil saat tombol Simpan di form ditekan)
    public function update(Request $request, $id_kpi_assessment)
    {
        $assessment = KpiAssessment::with('karyawan')->findOrFail($id_kpi_assessment);
        $inputs = $request->input('kpi'); // Array dari form

        if (!$inputs) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada data dikirim.']);
            }
            return redirect()->back()->with('error', 'Tidak ada data dikirim.');
        }

        // Define canAdjust for this request
        $user = auth()->user();
        $karyawan = $assessment->karyawan;
        $canAdjust = $this->canAdjust($user, $karyawan);

        // Cek Total Bobot (Server Side Validation)
        $totalBobot = KpiItem::where('kpi_assessment_id', $id_kpi_assessment)->sum('bobot');
        if ($totalBobot > 100.05) { // Sedikit toleransi float
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Total Bobot melebihi 100% (' . $totalBobot . '%). Mohon sesuaikan bobot item.']);
            }
            return redirect()->back()->with('error', 'Gagal: Total Bobot melebihi 100% (' . $totalBobot . '%).');
        }

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

                    // --- Semester 1 (Januari - Juni) ---
                    $t_jan = $this->cleanInput($data['target_jan']);
                    $r_jan = $this->cleanInput($data['real_jan']);
                    $t_feb = $this->cleanInput($data['target_feb']);
                    $r_feb = $this->cleanInput($data['real_feb']);
                    $t_mar = $this->cleanInput($data['target_mar']);
                    $r_mar = $this->cleanInput($data['real_mar']);
                    $t_apr = $this->cleanInput($data['target_apr']);
                    $r_apr = $this->cleanInput($data['real_apr']);
                    $t_mei = $this->cleanInput($data['target_mei']);
                    $r_mei = $this->cleanInput($data['real_mei']);
                    $t_jun = $this->cleanInput($data['target_jun']);
                    $r_jun = $this->cleanInput($data['real_jun']);

                    // --- Semester 2 (Juli - Desember) ---
                    $t_jul = $this->cleanInput($data['target_jul']);
                    $r_jul = $this->cleanInput($data['real_jul']);
                    $t_aug = $this->cleanInput($data['target_aug']);
                    $r_aug = $this->cleanInput($data['real_aug']);
                    $t_sep = $this->cleanInput($data['target_sep']);
                    $r_sep = $this->cleanInput($data['real_sep']);
                    $t_okt = $this->cleanInput($data['target_okt']);
                    $r_okt = $this->cleanInput($data['real_okt']);
                    $t_nov = $this->cleanInput($data['target_nov']);
                    $r_nov = $this->cleanInput($data['real_nov']);
                    $t_des = $this->cleanInput($data['target_des']);
                    $r_des = $this->cleanInput($data['real_des']);

                    // Jumlahkan untuk menjadi Semester 1
                    $target1 = $t_jan + $t_feb + $t_mar + $t_apr + $t_mei + $t_jun;
                    $real1   = $r_jan + $r_feb + $r_mar + $r_apr + $r_mei + $r_jun;
                    // Fallback bila bulan-bulan tidak diisi (compatibility)
                    if ($target1 == 0) {
                        $target1 = $this->cleanInput($data['target_smt1'] ?? $item->target);
                    }
                    if ($real1 == 0) {
                        $real1 = $this->cleanInput($data['real_smt1'] ?? 0);
                    }

                    // Tangkap Adjustment Smt 1
                    $adjReal1 = null;
                    if ($canAdjust) {
                        $adjReal1 = isset($data['adjustment_real_smt1']) ? $this->cleanInput($data['adjustment_real_smt1']) : null;
                    } else {
                        // Keep existing value if user cannot adjust
                        $adjReal1 = $score->adjustment_real_smt1;
                    }

                    // ... intermediate logic ...

                    // Tangkap Adjustment Smt 2
                    $adjReal2 = null;
                    // $adjTarget2 = ... (if used)
                    
                    if ($canAdjust) {
                        $adjReal2 = isset($data['adjustment_real_smt2']) ? $this->cleanInput($data['adjustment_real_smt2']) : null;
                        $adjTarget2 = isset($data['adjustment_target_smt2']) ? $this->cleanInput($data['adjustment_target_smt2']) : null;
                    } else {
                        $adjReal2 = $score->adjustment_real_smt2;
                        $adjTarget2 = $score->adjustment_target_smt2;
                    }
                    // Re-calculate for safety: Average of Monthly Scores
                    // Note: If using complex polaritas patterns, better to trust the Service.
                    // Let's use the Frontend logic principle: Subtotal = Avg(Monthly Scores)
                    // But we need to calculate Monthly Scores first.
                    
                    // --- 2. HITUNG SKOR DI BACKEND (LOGIKA PENILAIAN BARU) ---
                    
                    // Calculate Monthly Scores for SMT 1
                    $sumSkor1 = 0; $count1 = 0;
                    $months1 = ['jan','feb','mar','apr','mei','jun'];
                    $justification = []; // Collect justification

                    foreach ($months1 as $m) {
                        $t_val = ${'t_'.$m};
                        $r_val = ${'r_'.$m};
                        
                        // Collect Justification (Staff and Manager merged securely)
                        if(isset($data['justification'][$m])) {
                            $existing = $score->justification[$m] ?? ['staff' => '', 'manager' => ''];
                            
                            // Handling legacy string justification
                            if (is_string($existing)) {
                                $existing = ['staff' => $existing, 'manager' => ''];
                            }

                            $newStaff = $data['justification'][$m]['staff'] ?? ($existing['staff'] ?? '');
                            $newManager = $data['justification'][$m]['manager'] ?? ($existing['manager'] ?? '');

                            // If staff, only update staff part. If manager/admin, they can update feedback.
                            if ($this->roleMatches($user, 'staff')) {
                                $justification[$m] = [
                                    'staff'   => $newStaff,
                                    'manager' => $existing['manager'] ?? ''
                                ];
                            } else {
                                $justification[$m] = [
                                    'staff'   => $newStaff,
                                    'manager' => $newManager
                                ];
                            }
                        }

                        if ($t_val != 0) {
                            $skorMonth = $this->hitungSkor($t_val, $r_val, $item->polaritas, $item->calculation_method, $item->previous_progress);
                            $sumSkor1 += $skorMonth;
                            $count1++;
                        }
                    }
                    
                    // Subtotal Smt 1
                    $subtotal1 = ($count1 > 0) ? ($sumSkor1 / $count1) : 0;
                    
                    // Final Score Smt 1 -> (Subtotal + Adjustment) * Bobot% in Frontend
                    // Here we save the components.
                    // The 'skor1' variable in previous logic was "Total Sum of Weighted Values".
                    // Now we need "Final Score" (0-100 scale) to multiply by weight later?
                    // Wait, previous logic: $skor1 = calculateScore(Target, Real).
                    // New logic: $skor1 = Subtotal + Adjustment.
                    
                    $real1Final = $subtotal1 + ($adjReal1 ?? 0);
                    $real1Final = max(0, $real1Final); // Clamp min 0
                    
                    // Effective Score for Smt 1 (Weighted)
                    $nilaiSmt1 = ($real1Final * $item->bobot) / 100;
                    
                    
                    // --- Semester 2 ---
                    // Capture Adjustment Smt 2
                    $adjReal2   = isset($data['adjustment_real_smt2']) ? $this->cleanInput($data['adjustment_real_smt2']) : null;
                    // $adjTarget2 = isset($data['adjustment_target_smt2']) ? $this->cleanInput($data['adjustment_target_smt2']) : null; // Unused?

                    // Calculate Monthly Scores for SMT 2
                    $sumSkor2 = 0; $count2 = 0;
                    $months2 = ['jul','aug','sep','okt','nov','des'];
                    
                    foreach ($months2 as $m) {
                        $t_val = ${'t_'.$m};
                        $r_val = ${'r_'.$m};
                        
                        // Collect Justification
                        if(isset($data['justification'][$m])) {
                            $justification[$m] = $data['justification'][$m];
                        }

                        if ($t_val != 0) {
                            $skorMonth = $this->hitungSkor($t_val, $r_val, $item->polaritas, $item->calculation_method, $item->previous_progress);
                            $sumSkor2 += $skorMonth;
                            $count2++;
                        }
                    }

                    // Calculate Semester 2 Totals (Target & Real)
                    $target2 = $t_jul + $t_aug + $t_sep + $t_okt + $t_nov + $t_des;
                    $real2   = $r_jul + $r_aug + $r_sep + $r_okt + $r_nov + $r_des;
                    // Fallback for compatibility
                    if ($target2 == 0) {
                        $target2 = $this->cleanInput($data['target_smt2'] ?? 0); // Smt 2 usually sum of months
                    }
                    if ($real2 == 0) {
                        $real2 = $this->cleanInput($data['real_smt2'] ?? 0);
                    }

                    // Subtotal Smt 2
                    $subtotal2 = ($count2 > 0) ? ($sumSkor2 / $count2) : 0;
                    
                    $real2Final = $subtotal2 + ($adjReal2 ?? 0);
                    $real2Final = max(0, $real2Final);

                    // Effective Score for Smt 2 (Weighted)
                    $nilaiSmt2 = ($real2Final * $item->bobot) / 100;

                    // --- Final Score Item ---
                    // Average of Weighted Values
                    $pencapaianTotal = ($nilaiSmt1 + $nilaiSmt2) / 2; // This is actually "Average Value"
                    // Wait. In previous code:
                    // $pencapaianTotal = ($skor1 + $skor2) / 2; (where skor1/2 were raw scores)
                    // $finalSkorItem   = ($pencapaianTotal * $item->bobot) / 100;
                    // IF we change Logic, we must be careful.
                    // New Logic: 
                    // Smt 1 Value = (Subtotal1 + Adj1) * Weight%
                    // Smt 2 Value = (Subtotal2 + Adj2) * Weight%
                    // Final Value = (Value1 + Value2) / 2 ???
                    // Example: 
                    // Smt 1: Subtotal 100 + 0 Adj -> 100 * 10% = 10.
                    // Smt 2: Subtotal 100 + 0 Adj -> 100 * 10% = 10.
                    // Final = (10+10)/2 = 10. Correct.
                    
                    $finalSkorItem = ($nilaiSmt1 + $nilaiSmt2) / 2;

                    // ====================================================
                    // 3. SIMPAN KE DATABASE (UPDATE LENGKAP)
                    // ====================================================
                    $score->update([
                        // Data Semester 1
                        'target_smt1' => $target1,
                        'real_smt1'   => $real1,
                        'subtotal_smt1' => $subtotal1, // NEW
                        'adjustment_real_smt1' => $adjReal1, // Saved as Adjustment Value

                        // Data Bulanan (AGAR TIDAK HILANG) - JAN-JUN & JUL-DEC
                        'target_jan' => $t_jan, 'real_jan' => $r_jan,
                        'target_feb' => $t_feb, 'real_feb' => $r_feb,
                        'target_mar' => $t_mar, 'real_mar' => $r_mar,
                        'target_apr' => $t_apr, 'real_apr' => $r_apr,
                        'target_mei' => $t_mei, 'real_mei' => $r_mei,
                        'target_jun' => $t_jun, 'real_jun' => $r_jun,

                        'target_jul' => $t_jul, 'real_jul' => $r_jul,
                        'target_aug' => $t_aug, 'real_aug' => $r_aug,
                        'target_sep' => $t_sep, 'real_sep' => $r_sep,
                        'target_okt' => $t_okt, 'real_okt' => $r_okt,
                        'target_nov' => $t_nov, 'real_nov' => $r_nov,
                        'target_des' => $t_des, 'real_des' => $r_des,

                        // Justification
                        'justification' => $justification, // NEW JSON

                        // Data Semester 2
                        'total_target_smt2' => $target2,
                        'total_real_smt2'   => $real2,
                        'subtotal_smt2'     => $subtotal2, // NEW
                        'adjustment_target_smt2' => $adjTarget2,
                        'adjustment_real_smt2'   => $adjReal2, // Saved as Adjustment Value

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

            // SKENARIO 1: STAFF atau SUPERVISOR (Pemilik KPI / Supervisor) KLIK SIMPAN
            // Jika yang login adalah Staff atau Supervisor, otomatis jadi "SUBMITTED" (Menunggu Approval dari Manager)
            if ($this->roleMatches($user, 'staff') || $this->roleMatches($user, 'supervisor')) {
                $statusBaru = 'SUBMITTED';
            }

            // SKENARIO 2: MANAGER / ADMIN / SUPERADMIN KLIK SIMPAN
            // Jika Manager/Admin yang simpan, otomatis jadi "FINAL" (Approved)
            elseif ($this->roleMatches($user, ['manager', 'Manajer', 'admin', 'superadmin'])) {
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

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => $pesan . ' Skor Akhir: ' . number_format($grandTotal, 2)]);
            }

            return redirect()->back()->with('success', $pesan . ' Skor Akhir: ' . number_format($grandTotal, 2));
        } catch (\Exception $e) {
            DB::rollback();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()]);
            }
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    // =================================================================
    // 5. HELPER FUNCTION
    // =================================================================
    private function applyIndexFilters($query, Request $request, $tahun)
    {
        // Filter Search (grouped to avoid breaking other filters)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('Nama_Lengkap_Sesuai_Ijazah', 'LIKE', "%{$search}%")
                    ->orWhere('NIK', 'LIKE', "%{$search}%");
            });
        }

        // Filter Jabatan
        if ($request->filled('filter_jabatan')) {
            $query->whereHas('pekerjaan.level', function ($q) use ($request) {
                $q->where('name', $request->filter_jabatan);
            });
        }

        // Filter Status
        if ($request->filled('filter_status')) {
            if ($request->filter_status === 'BELUM_ADA') {
                $query->whereDoesntHave('kpiAssessment', fn($q) => $q->where('tahun', $tahun));
            } else {
                $query->whereHas('kpiAssessment', fn($q) => $q->where('tahun', $tahun)->where('status', $request->filter_status));
            }
        }

        // Filter Company
        if ($request->filled('filter_company')) {
            $query->whereHas('pekerjaan', function ($q) use ($request) {
                $q->whereHas('company', function ($companyQ) use ($request) {
                    $companyQ->where('name', $request->filter_company);
                });
            });
        }

        // Filter Divisi
        if ($request->filled('filter_divisi')) {
            $query->whereHas('pekerjaan.division', function ($q) use ($request) {
                $q->where('name', $request->filter_divisi);
            });
        }
    }

    private function cleanInput($value)
    {
        if (is_null($value)) return 0;
        return floatval(str_replace(['%', ','], ['', '.'], $value));
    }

    private function hitungSkor($target, $realisasi, $polaritas, $method = 'positive', $previousProgress = 0)
    {
        $t = $this->cleanInput($target);
        $r = $this->cleanInput($realisasi);

        // Delegasikan ke Service
        return $this->calculationService->calculateScore($method, $t, $r, $previousProgress);
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
            'calculation_method'        => $request->calculation_method ?? $item->calculation_method, 
            'previous_progress'         => $request->previous_progress ?? $item->previous_progress,
            'bobot'                     => $request->bobot,
            'target'                    => $cleanTarget,
        ]);

        // 4. Update Tabel Score juga (agar Target di tabel berubah)
        $score = KpiScore::where('kpi_item_id', $id)->first();

        if ($score) {
            $score->update([
                'target'      => $cleanTarget,
                'target_smt1' => $cleanTarget,
                // Reset target bulanan ke target baru (setiap bulan ke nilai target master)
                'target_jan'  => $cleanTarget,
                'target_feb'  => $cleanTarget,
                'target_mar'  => $cleanTarget,
                'target_apr'  => $cleanTarget,
                'target_mei'  => $cleanTarget,
                'target_jun'  => $cleanTarget,
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

    // =================================================================
    // 6. BULK ACTIONS (MANAGER)
    // =================================================================

    /**
     * Bulk create KPI header for all karyawan in manager scope (direct & level-2)
     */
    public function bulkCreateForManager(Request $request)
    {
        // Backward-compatible simple action (header-only) kept for API/legacy use
        $request->validate(['tahun' => 'required']);
        $user = Auth::user();

        if (!$this->roleMatches($user, ['manager', 'GM', 'senior_manager', 'admin', 'superadmin'])) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $scopeIds = Karyawan::pluck('id_karyawan')->toArray();
        
        if (empty($scopeIds)) {
            return redirect()->back()->with('error', 'Tidak ada karyawan untuk ditetapkan KPI.');
            }
            
        $tahun = $request->tahun;
        $created = 0;

        DB::beginTransaction();
        try {
            foreach ($scopeIds as $karyawanId) {
                $exists = KpiAssessment::where('karyawan_id', $karyawanId)->where('tahun', $tahun)->first();
                if ($exists) continue;

                KpiAssessment::create([
                    'karyawan_id' => $karyawanId,
                    'tahun' => $tahun,
                    'periode' => 'Tahunan',
                    'status' => 'DRAFT',
                    'total_skor_akhir' => 0,
                    'penilai_id' => $user->id,
                ]);

                $created++;
            }

            DB::commit();
            return redirect()->back()->with('success', "Berhasil membuat KPI untuk {$created} karyawan.");
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal membuat KPI: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan form untuk manager mengisi template KPI yang akan diterapkan ke semua karyawan
     */
public function bulkCreateForm(Request $request)
    {
        $user = Auth::user();
        if (!$this->roleMatches($user, ['manager', 'GM', 'senior_manager', 'admin', 'superadmin', 'direktur'])) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $tahun = $request->input('tahun', date('Y'));
        $employees = collect();

        // 1. Admin / Superadmin: Semua Karyawan
        if ($this->roleMatches($user, ['admin', 'superadmin'])) {
            $employees = Karyawan::with('pekerjaan')->where('status_karyawan', 'ACTIVE')
                ->orderBy('Nama_Lengkap_Sesuai_Ijazah')
                ->get();
        } 
        // 1.B Direktur: GM & Manager
        elseif ($this->roleMatches($user, 'direktur')) {
            $me = Karyawan::where('nik', $user->nik)->first();
            $employees = Karyawan::with('pekerjaan')
                ->whereHas('pekerjaan.level', function($q) {
                    $q->where('name', 'LIKE', '%Manager%')
                      ->orWhere('name', 'LIKE', '%General Manager%');
                })
                ->orWhere('atasan_id', $me->id_karyawan ?? 0)
                ->orderBy('Nama_Lengkap_Sesuai_Ijazah')
                ->get();
        } 
        // 2. Manager / GM / Senior Manager
        elseif ($this->roleMatches($user, ['manager', 'GM', 'senior_manager'])) {
            $me = Karyawan::where('nik', $user->nik)->first();
            if ($me) {
                // LOGIC FIX: SELALU TAMPILKAN SATU DIVISI
                // Sama seperti perbaikan di Dashboard, Manager berhak melihat semua karyawan di divisi mereka.
                // Tidak peduli apakah ada atasan_id atau tidak.

                $pekerjaanManager = $me->pekerjaan()->orderByDesc('id_pekerjaan')->first();
                
                if ($pekerjaanManager && $pekerjaanManager->division_id) {
                    $employees = Karyawan::with('pekerjaan')
                        ->whereHas('pekerjaan', function ($q) use ($pekerjaanManager) {
                            $q->where('division_id', $pekerjaanManager->division_id);
                        })
                        ->orderBy('Nama_Lengkap_Sesuai_Ijazah')
                        ->get();
                } else {
                    // Jika tidak punya divisi, kosongkan list
                    $employees = collect();
                }
            }
        }
        // 3. Supervisor
        elseif ($this->roleMatches($user, 'supervisor')) {
             $me = Karyawan::where('nik', $user->nik)->first();
             if ($me) {
                $pekerjaanSup = $me->pekerjaan()->orderByDesc('id_pekerjaan')->first();
                if ($pekerjaanSup && $pekerjaanSup->division_id) {
                    $supLevelOrder = $pekerjaanSup->level->level_order ?? null;
                    $employees = Karyawan::with('pekerjaan')->whereHas('pekerjaan', function ($q) use ($pekerjaanSup, $supLevelOrder) {
                        $q->where('division_id', $pekerjaanSup->division_id);
                        if ($supLevelOrder !== null) {
                             $q->whereHas('level', function ($l) use ($supLevelOrder) {
                                $l->where('level_order', '>', $supLevelOrder);
                            });
                        }
                    })->orderBy('Nama_Lengkap_Sesuai_Ijazah')->get();
                }
             }
        }

        $perspektifList = $this->getPerspektifAktif();
        return view('pages.kpi.bulk_create', compact('tahun', 'perspektifList', 'employees'));
    }

    /**
     * Simpan template KPI dan buatkan item untuk semua karyawan
     */
public function bulkStoreWithItems(Request $request)
    {
        $request->validate([
            'tahun' => 'required',
            'employee_ids' => 'required|array|min:1',
            'items' => 'required|array|min:1',
            'items.*.key_result_area' => 'required|string',
            'items.*.key_performance_indicator' => 'required|string',
            'items.*.units' => 'required|string',
            'items.*.bobot' => 'required|numeric',
            'items.*.target' => 'required|numeric',
            'items.*.perspektif' => 'required|string',
            'items.*.polaritas' => 'required|string',
        ], [
            'employee_ids.required' => 'Pilih minimal satu karyawan.',
            'items.required' => 'Minimal satu indikator KPI harus diisi.'
        ]);

        $user = Auth::user();
        if (!$this->roleMatches($user, ['manager', 'GM', 'senior_manager', 'admin', 'superadmin', 'direktur'])) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $tahun = $request->tahun;
        $items = $request->items;
        $targetEmployeeIds = $request->employee_ids;

        // $perspektifList = $this->getPerspektifAktif();
        $createdHeaders = 0;
        $createdItems = 0;

        DB::beginTransaction();
        try {
            // $scopeIds = Karyawan::pluck('id_karyawan')->toArray(); // OLD DANGEROUS CODE

            foreach ($targetEmployeeIds as $karyawanId) {
                // Optional: Verify this ID is allowed for this user? 
                // For now trusting the form input as it filtered the view, and user is manager/admin.
                
                $kpi = KpiAssessment::firstOrCreate(
                    ['karyawan_id' => $karyawanId, 'tahun' => $tahun],
                    ['periode' => 'Tahunan', 'status' => 'DRAFT', 'total_skor_akhir' => 0, 'penilai_id' => $user->id]
                );

                if ($kpi->wasRecentlyCreated) $createdHeaders++;

                // Hapus logic pengecekan duplicate item untuk memungkinkan penambahan item baru ke KPI yang sudah ada
                // $existsItem = \App\Models\KpiItem::where('kpi_assessment_id', $kpi->id_kpi_assessment)->exists();
                // if ($existsItem) continue;

                foreach ($items as $it) {
                    $item = \App\Models\KpiItem::create([
                        'kpi_assessment_id' => $kpi->id_kpi_assessment,
                        'perspektif' => $it['perspektif'],
                        'key_result_area' => $it['key_result_area'] ?? null,
                        'key_performance_indicator' => $it['key_performance_indicator'],
                        'units' => $it['units'],
                        'polaritas' => $it['polaritas'] ?? 'MAX',
                        'bobot' => $it['bobot'],
                        'target' => $it['target'],
                    ]);

                    \App\Models\KpiScore::create([
                        'kpi_item_id' => $item->id_kpi_item,
                        'target' => 0,
                        'target_smt1' => 0,
                        'nama_periode' => 'Semester 1',
                        'realisasi' => 0,
                    ]);

                    $createdItems++;
                }
            }

            DB::commit();
            return redirect()->route('kpi.index')->with('success', "Template berhasil diterapkan. Header dibuat: {$createdHeaders}, item ditambahkan: {$createdItems}.");
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menyimpan template: ' . $e->getMessage());
        }
    }

    /**
     * Finalize / Approve KPI by manager/admin
     */
public function finalize(Request $request, $id)
    {
        $user = Auth::user();
        if (!$this->roleMatches($user, ['direktur', 'manager', 'GM', 'senior_manager', 'admin', 'superadmin'])) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $kpi = KpiAssessment::find($id);
        if (!$kpi) return redirect()->back()->with('error', 'KPI tidak ditemukan.');

        // Additional scope check for managers (same rules as show())
        if ($this->roleMatches($user, ['direktur', 'manager', 'GM', 'senior_manager'])) {
            $me = Karyawan::where('nik', $user->nik)->first();
            $allowed = false;
            if ($me && $me->id_karyawan == $kpi->karyawan_id) $allowed = true;

            $karyawan = Karyawan::find($kpi->karyawan_id);
            if ($karyawan) {
                if ($karyawan->atasan_id == ($me->id_karyawan ?? null)) $allowed = true;
                if ($karyawan->atasan && $karyawan->atasan->atasan_id == ($me->id_karyawan ?? null)) $allowed = true;

                if (!$allowed && $me) {
                    $pekerjaanManager = $me->pekerjaan()->orderByDesc('id_pekerjaan')->first();
                    if ($pekerjaanManager && $pekerjaanManager->division_id) {
                        $kryP = $karyawan->pekerjaan()->orderByDesc('id_pekerjaan')->first();
                        if ($kryP && $kryP->division_id == $pekerjaanManager->division_id) {
                            $allowed = true;
                        }
                    }
                }
            }

            if (!$allowed) return redirect()->back()->with('error', 'Anda tidak berhak melakukan approval ini.');
        }

        try {
            // Recompute grand total from scores
            $grandTotal = KpiScore::join('kpi_items', 'kpi_scores.kpi_item_id', '=', 'kpi_items.id_kpi_item')
                ->where('kpi_items.kpi_assessment_id', $kpi->id_kpi_assessment)
                ->sum('kpi_scores.skor_akhir');

            $kpi->update([
                'total_skor_akhir' => $grandTotal,
                'grade' => $this->determineGrade($grandTotal),
                'status' => 'FINAL'
            ]);

            return redirect()->back()->with('success', 'KPI berhasil di-approve dan difinalisasi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal melakukan approval: ' . $e->getMessage());
        }
    }

    // =================================================================
    // 7. EXPORT FUNCTIONS & IMPORT EXCEL FEATURES
    // =================================================================

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = [
            'NIK',
            'Nama Karyawan (Info)',
            'Tahun',
            'Key Result Area',
            'Key Performance Indicator',
            'Perspektif',
            'Polaritas',
            'Units',
            'Bobot',
            'Target'
        ];

        $sheet->fromArray($headers, NULL, 'A1');

        // Style Header
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        $sheet->getStyle('A1:J1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');

        // Auto Size Columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Example Data
        $sheet->fromArray([
            '123456',
            'John Doe',
            date('Y'),
            'Financial',
            'Revenue Growth',
            $this->getPerspektifAktif()->first() ?? 'Financial', // Default value from DB
            'MAX',
            'Percent',
            20,
            100
        ], null, 'A2');

        // ==========================================
        // ADD DROPDOWN VALIDATION FOR PERSPEKTIF (COL F)
        // ==========================================
        $perspektifs = $this->getPerspektifAktif()->toArray();
        if (!empty($perspektifs)) {
            $options = implode(',', $perspektifs);
            
            // Define validation for Perspektif (Col F)
            $validation = $sheet->getCell('F2')->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Input Error');
            $validation->setError('Value is not in list.');
            $validation->setPromptTitle('Pick from list');
            $validation->setPrompt('Please pick a value from the drop-down list.');
            $validation->setFormula1('"' . $options . '"');

            // Clone validation to a range of rows (e.g., 2 to 100)
            for ($i = 3; $i <= 100; $i++) {
                $sheet->getCell("F$i")->setDataValidation(clone $validation);
            }
        }

        // ==========================================
        // ADD DROPDOWN VALIDATION FOR POLARITAS (COL G)
        // ==========================================
        $polaritasOptions = "Min,Max";
        $validationPol = $sheet->getCell('G2')->getDataValidation();
        $validationPol->setType(DataValidation::TYPE_LIST);
        $validationPol->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $validationPol->setAllowBlank(false);
        $validationPol->setShowInputMessage(true);
        $validationPol->setShowErrorMessage(true);
        $validationPol->setShowDropDown(true);
        $validationPol->setErrorTitle('Input Error');
        $validationPol->setError('Value is not in list.');
        $validationPol->setPromptTitle('Pick from list');
        $validationPol->setPrompt('Please select Min or Max.');
        $validationPol->setFormula1('"' . $polaritasOptions . '"');

        for ($i = 3; $i <= 100; $i++) {
            $sheet->getCell("G$i")->setDataValidation(clone $validationPol);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Template_Import_KPI.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
        exit;
    }

   public function importExcel(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv|max:2048',
    ]);

    try {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(
            $request->file('file')->getPathname()
        );

        $sheet = $spreadsheet->getActiveSheet();
        $rows  = $sheet->toArray();

        // Hapus header
        array_shift($rows);

        DB::beginTransaction();
        $count = 0;

        foreach ($rows as $row) {

            // =========================
            // Mapping kolom Excel
            // =========================
            $nik         = $row[0] ?? null;
            $tahun       = $row[2] ?? date('Y');
            $kra         = $row[3] ?? null;
            $kpiName     = $row[4] ?? null;
            $perspektif  = $row[5] ?? null;
            $polaritas   = $row[6] ?? null;
            $units       = $row[7] ?? null;
            $bobot       = $row[8] ?? null;
            $target      = $row[9] ?? null;

            if (empty($nik) || empty($kpiName)) {
                continue;
            }

            // =========================
            // Cari karyawan
            // =========================
            $karyawan = Karyawan::where('NIK', $nik)->first();
            if (!$karyawan) {
                continue;
            }

            // =========================
            // KPI Assessment (Header)
            // =========================
            $kpi = KpiAssessment::firstOrCreate(
                [
                    'karyawan_id' => $karyawan->id_karyawan,
                    'tahun'       => $tahun,
                ],
                [
                    'status'            => 'DRAFT',
                    'periode'           => 'Tahunan',
                    'grade'             => null,
                    'total_skor_akhir'  => null,
                    'penilai_id'        => auth()->id(),
                ]
            );

            // =========================
            // KPI Item
            // =========================
            $item = KpiItem::updateOrCreate(
                [
                    'kpi_assessment_id'       => $kpi->id_kpi_assessment,
                    'key_performance_indicator' => $kpiName,
                ],
                [
                    'perspektif'      => $perspektif,
                    'key_result_area' => $kra,
                    'units'           => $units,
                    'polaritas'       => $polaritas, // Save Polaritas
                    'bobot'           => is_numeric($bobot) ? $bobot : null,
                    'target'          => is_numeric($target) ? $target : null,
                ]
            );

            $count++;
        }

        if ($count === 0) {
            DB::rollBack();
            return back()->with('error', 'Tidak ada data valid yang berhasil diimport. Kesalahan Nik bisa menjadi penyebabnya');
        }

        DB::commit();
        return back()->with('success', "Berhasil mengimport {$count} data KPI.");

    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal import file: ' . $e->getMessage());
    }
}


public function exportExcel(Request $request)
    {
        $karyawanId = $request->get('karyawan_id');
        $tahun = $request->get('tahun');

        if (!$karyawanId || !$tahun) {
            return redirect()->back()->with('error', 'Parameter karyawan_id dan tahun diperlukan.');
        }

        $karyawan = Karyawan::findOrFail($karyawanId);
        $kpi = KpiAssessment::where('karyawan_id', $karyawanId)
            ->where('tahun', $tahun)
            ->first();

        if (!$kpi) {
            return redirect()->back()->with('error', 'Data KPI tidak ditemukan.');
        }

        return \App\Exports\SingleKpiExport::download($kpi->id_kpi_assessment);
    }

public function exportPdf(Request $request)
    {
        $karyawanId = $request->get('karyawan_id');
        $tahun = $request->get('tahun');

        if (!$karyawanId || !$tahun) {
            return redirect()->back()->with('error', 'Parameter karyawan_id dan tahun diperlukan.');
        }

        $karyawan = Karyawan::findOrFail($karyawanId);
        $kpi = KpiAssessment::where('karyawan_id', $karyawanId)
            ->where('tahun', $tahun)
            ->first();

        if (!$kpi) {
            return redirect()->back()->with('error', 'Data KPI tidak ditemukan.');
        }

        // Fetch items explicitly to ensure consistency with show() method
        $items = KpiItem::where('kpi_assessment_id', $kpi->id_kpi_assessment)
            ->with('scores')
            ->get();

        $filename = "KPI_{$karyawan->NIK}_{$tahun}.pdf";

        $pdf = Pdf::loadView('pages.kpi.pdf', compact('karyawan', 'kpi', 'items', 'tahun'))->setPaper('legal', 'landscape');

        return $pdf->download($filename);
    }

    // Helper: gabungkan role dari manajemen user dan turunan pekerjaan (level/position/Jabatan)
private function roleMatches($user, $roles)
    {
        if (!$user) return false;

        // Ambil role eksplisit dari tabel roles
        $userRoleNames = [];
        try {
            $userRoleNames = $user->roles()->pluck('name')->map(function ($r) {
                return strtolower($r);
            })->toArray();
        } catch (\Throwable $e) {
            $userRoleNames = [];
        }

        // Turunkan role dari pekerjaan (level.name, position.name, Jabatan)
        $derivedRoles = [];
        $karyawan = Karyawan::where('user_id', $user->id)->first();
        if (!$karyawan && !empty($user->nik)) {
            $karyawan = Karyawan::where('nik', $user->nik)->first();
        }
        if ($karyawan) {
            $pekerjaan = $karyawan->pekerjaanTerkini()->first() ?? $karyawan->pekerjaan()->first();
            if ($pekerjaan) {
                if (!empty($pekerjaan->level) && !empty($pekerjaan->level->name)) $derivedRoles[] = strtolower($pekerjaan->level->name);
                if (!empty($pekerjaan->position) && !empty($pekerjaan->position->name)) $derivedRoles[] = strtolower($pekerjaan->position->name);
                if (!empty($pekerjaan->Jabatan)) $derivedRoles[] = strtolower($pekerjaan->Jabatan);
            }
        }

        if (is_string($roles)) $roles = [$roles];
        $roles = array_map('strtolower', $roles);

        foreach ($roles as $r) {
            if (in_array($r, $userRoleNames)) return true;
            if (in_array($r, $derivedRoles)) return true;
        }
        return false;
    }

    // =================================================================
    // DESTROY: Hapus KPI Assessment
    // =================================================================
    public function destroy($id)
    {
        try {
            $kpi = KpiAssessment::findOrFail($id);

            // Hapus scores terkait
            KpiScore::whereHas('item', function ($query) use ($kpi) {
                $query->where('kpi_assessment_id', $kpi->id_kpi_assessment);
            })->delete();

            // Hapus items terkait
            KpiItem::where('kpi_assessment_id', $kpi->id_kpi_assessment)->delete();

            // Hapus assessment utama
            $kpi->delete();

            return redirect()->back()->with('success', 'Data KPI berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data KPI: ' . $e->getMessage());
        }
    }
    public function bulkDestroyItems(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:kpi_items,id_kpi_item',
        ]);

        DB::beginTransaction();
        try {
            // Hapus skor terkait dulu
            KpiScore::whereIn('kpi_item_id', $request->ids)->delete();
            
            // Hapus itemnya
            KpiItem::whereIn('id_kpi_item', $request->ids)->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Item KPI terpilih berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Gagal menghapus item: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Bulk Delete KPI Assessments
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:kpi_assessments,id_kpi_assessment'
        ]);

        $user = Auth::user();

        // Security check: Only Manager, Admin, Superadmin can bulk delete
        if (!$this->roleMatches($user, ['manager', 'GM', 'senior_manager', 'admin', 'superadmin'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        DB::beginTransaction();
        try {
            $deletedCount = KpiAssessment::whereIn('id_kpi_assessment', $request->ids)->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true, 
                'message' => "Berhasil menghapus {$deletedCount} data KPI."
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false, 
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
    // =================================================================
    // HELPER: CEK HAK AKSES ADJUSTMENT
    // =================================================================
    private function canAdjust($user, $karyawan)
    {
        // 1. Superadmin / Senior Manager (Grand Super)
        if ($this->roleMatches($user, ['superadmin', 'senior_manager'])) {
            return true;
        }

        $me = Karyawan::where('nik', $user->nik)->first();
        if (!$me) return false;

        // 2. Atasan Langsung (Direct Supervisor)
        if ($karyawan->atasan_id == $me->id_karyawan) {
            return true;
        }

        // 3. Manager dalam Satu Divisi (Hierarchy check)
        // Jika User adalah Manager, dan Karyawan berada di Divisi yang sama
        if ($this->roleMatches($user, 'manager')) {
            $myJob = $me->pekerjaan()->orderByDesc('id_pekerjaan')->first();
            $targetJob = $karyawan->pekerjaan()->orderByDesc('id_pekerjaan')->first();

            if ($myJob && $targetJob && $myJob->division_id == $targetJob->division_id) {
                return true;
            }
        }

        return false;
    }
}