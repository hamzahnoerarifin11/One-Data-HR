<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Models
use App\Models\Karyawan;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Kontrak;
use App\Models\Perusahaan;
use App\Models\KpiAssessment;
use App\Models\KbiAssessment;
use App\Models\Division;
use App\Models\Company;

class DashboardController extends Controller
{
    /**
     * MAIN FUNCTION: TRAFFIC CONTROLLER
     * Mengarahkan user berdasarkan Role di database.
     */
    /**
     * MAIN FUNCTION: TRAFFIC CONTROLLER & DATA LOADER
     * Mengarahkan user berdasarkan Role di database.
     */
    public function index(Request $request)
    {
        $user  = Auth::user();
        $tahun = $request->input('tahun', date('Y'));

        // 1. LOGIKA UNTUK ADMIN & SUPERADMIN (Melihat Global Data)
        // Admin tetap pakai dashboard terpisah (opsional, tapi request user fokus merge manager/spv/staff)
        if ($user->hasRole(['superadmin', 'admin'])) {
            return $this->adminDashboard();
        }

        // --- Cek Data Karyawan (Wajib untuk Manager, Supervisor & Staff) ---
        $karyawan = Karyawan::where('nik', $user->nik)->first();

        if (!$karyawan) {
            auth()->logout();
            return redirect()->route('signin')->with('error', 'Akun Anda tidak terhubung dengan Data Karyawan. Silakan hubungi admin.');
        }

        // =====================================================================
        // A. DATA PRIBADI (SEMUA ROLE DAPAT INI)
        // =====================================================================
        // Ambil KPI Saya
        $myKpi = KpiAssessment::where('karyawan_id', $karyawan->id_karyawan)
            ->where('tahun', $tahun)
            ->first();

        // Ambil KBI Saya (Self Assessment)
        $myKbi = KbiAssessment::where('karyawan_id', $karyawan->id_karyawan)
            ->where('tahun', $tahun)
            ->where('tipe_penilai', 'DIRI_SENDIRI')
            ->first();

        // =====================================================================
        // B. DATA TIM (KHUSUS MANAGER & SUPERVISOR)
        // =====================================================================
        $teamMonitoring = null;
        $totalTim = 0;
        $butuhApprovalKPI = 0;
        $belumDinilaiKBI = 0;
        $isManagerOrSpv = false;
        $roleTitle = 'Staff'; // Default

        // Cek Role untuk akses TIM
        // Manager / GM / Senior Manager -> Lihat Satuan Divisi
        // Supervisor -> Lihat Bawahan Langsung
        
        if ($user->hasRole(['manager', 'GM', 'senior_manager', 'direktur', 'manajer', 'Supervisor', 'supervisor'])) {
            $isManagerOrSpv = true;
            $scopeIds = [];

            // 1. Tentukan Scope Karyawan
            if ($user->hasRole(['manager', 'GM', 'senior_manager', 'direktur', 'manajer'])) {
                $roleTitle = 'Manager';
                // Logic Manager: Lihat Divisi
                $pekerjaanManager = $karyawan->pekerjaan()->orderByDesc('id_pekerjaan')->first();
                if ($pekerjaanManager && $pekerjaanManager->division_id) {
                    $divisionId = $pekerjaanManager->division_id;
                    $scopeIds = Karyawan::whereHas('pekerjaan', function ($q) use ($divisionId) {
                        $q->where('division_id', $divisionId);
                    })->pluck('id_karyawan')->toArray();
                }
            } elseif ($user->hasRole(['Supervisor', 'supervisor'])) {
                $roleTitle = 'Supervisor';
                // Logic Supervisor: Lihat Bawahan Langsung (atasan_id)
                $scopeIds = Karyawan::where('atasan_id', $karyawan->id_karyawan)
                            ->pluck('id_karyawan')->toArray();
            }

            // Exclude diri sendiri dari monitoring tim (opsional, biasanya manager tidak menilai diri sendiri di tabel tim)
            // $scopeIds = array_diff($scopeIds, [$karyawan->id_karyawan]);

            $totalTim = count($scopeIds);

            // 2. Hitung Statistik Tim
            if ($totalTim > 0) {
                // KPI Approved/Submitted
                $butuhApprovalKPI = KpiAssessment::whereIn('karyawan_id', $scopeIds)
                    ->where('tahun', $tahun)
                    ->where('status', 'SUBMITTED')
                    ->count();

                // KBI Belum Dinilai
                $sudahDinilaiKBI = KbiAssessment::whereIn('karyawan_id', $scopeIds)
                    ->where('tahun', $tahun)
                    ->where('penilai_id', Auth::id())
                    ->where('tipe_penilai', 'ATASAN')
                    ->count();
                
                $belumDinilaiKBI = max($totalTim - $sudahDinilaiKBI, 0);

                // 3. Ambil Data Tim (Pagination)
                $teamMonitoring = Karyawan::whereIn('id_karyawan', $scopeIds)
                    ->with([
                        'pekerjaan.division',
                        'kpiAssessment' => function ($q) use ($tahun) {
                            $q->where('tahun', $tahun);
                        },
                        'kbiAssessment' => function ($q) use ($tahun) {
                            $q->where('tahun', $tahun)
                              ->where('penilai_id', Auth::id())
                              ->where('tipe_penilai', 'ATASAN');
                        }
                    ])
                    ->paginate(5);
            } else {
                 // Empty Paginator
                 $teamMonitoring = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 5, 1);
            }
        }

        // View Unified: pages/dashboard/index.blade.php
        return view('pages.dashboard.index', compact(
            'karyawan',
            'tahun',
            'myKpi',
            'myKbi',
            'isManagerOrSpv',
            'roleTitle',
            'teamMonitoring',
            'totalTim',
            'butuhApprovalKPI',
            'belumDinilaiKBI'
        ));
    }

    // =========================================================================
    // 1. DASHBOARD ADMIN / SUPERADMIN (Global HR Stats)
    // =========================================================================
    private function adminDashboard()
    {
        // --- Statistik Karyawan ---
        $totalKaryawan   = Karyawan::count();
        $karyawanAktif   = Karyawan::where('Kode', 'Aktif')->count();
        $totalKontrak    = Kontrak::count();
        $totaldepartment_id = Pekerjaan::distinct('department_id')->count('department_id');

        // --- Statistik Demografi (Gender, Jabatan, Divisi, dll) ---
        // (Kode query sama persis seperti sebelumnya)
        $genderData = Karyawan::select(DB::raw("CASE WHEN Jenis_Kelamin_Karyawan = 'L' THEN 'Laki-laki' WHEN Jenis_Kelamin_Karyawan = 'P' THEN 'Perempuan' ELSE 'Tidak Diketahui' END as gender"), DB::raw('count(*) as total'))->groupBy('gender')->pluck('total', 'gender')->toArray();

        $jabatanData = Pekerjaan::with('position')->whereHas('position')->groupBy('position_id')->select('position_id', DB::raw('count(*) as total'))->get()->pluck('total', 'position.name')->toArray();

        $divisiData = Division::whereNotNull('name')->groupBy('name')->select('name', DB::raw('count(*) as total'))->pluck('total', 'name')->toArray();

        $pendidikanData = Pendidikan::whereNotNull('Pendidikan_Terakhir')->groupBy('Pendidikan_Terakhir')->select('Pendidikan_Terakhir', DB::raw('count(*) as total'))->pluck('total', 'Pendidikan_Terakhir')->toArray();

        // --- Masa Kerja & Umur ---
        $tenureCounts = ['< 1 Tahun' => 0, '1 - 3 Tahun' => 0, '4 - 8 Tahun' => 0, '> 8 Tahun' => 0];
        foreach (Kontrak::whereNotNull('Tanggal_Mulai_Tugas')->get() as $k) {
            $years = Carbon::parse($k->Tanggal_Mulai_Tugas)->diffInYears(now());
            if ($years < 1) $tenureCounts['< 1 Tahun']++;
            elseif ($years <= 3) $tenureCounts['1 - 3 Tahun']++;
            elseif ($years <= 8) $tenureCounts['4 - 8 Tahun']++;
            else $tenureCounts['> 8 Tahun']++;
        }

        $ageCounts = ['< 25' => 0, '25 - 27' => 0, '28 - 30' => 0, '30 - 40' => 0, '40 - 50' => 0, '> 50' => 0];
        foreach (Karyawan::whereNotNull('Tanggal_Lahir_Karyawan')->get() as $k) {
            $age = Carbon::parse($k->Tanggal_Lahir_Karyawan)->age;
            if ($age < 25) $ageCounts['< 25']++;
            elseif ($age <= 27) $ageCounts['25 - 27']++;
            elseif ($age <= 30) $ageCounts['28 - 30']++;
            elseif ($age <= 40) $ageCounts['30 - 40']++;
            elseif ($age <= 50) $ageCounts['40 - 50']++;
            else $ageCounts['> 50']++;
        }

        $perusahaanData = Company::whereNotNull('name')->groupBy('name')->select('name', DB::raw('count(*) as total'))->pluck('total', 'name')->toArray();

        // --- Data Turnover (Keluar Masuk per Bulan per Perusahaan) ---
        $turnoverData = [];
        $companies = Company::all();
        $currentYear = date('Y');
        for ($month = 1; $month <= 12; $month++) {
            $turnoverData[$month] = [];
            foreach ($companies as $company) {
                // Masuk: Tanggal_Mulai_Tugas dari kontrak
                $masuk = Kontrak::join('pekerjaan', 'kontrak.id_karyawan', '=', 'pekerjaan.id_karyawan')
                    ->where('pekerjaan.company_id', $company->id)
                    ->whereYear('kontrak.Tanggal_Mulai_Tugas', $currentYear)
                    ->whereMonth('kontrak.Tanggal_Mulai_Tugas', $month)
                    ->count();

                // Keluar: Karyawan dengan Kode = 'Non Aktif', asumsikan updated_at sebagai tanggal keluar
                $keluar = Karyawan::join('pekerjaan', 'karyawan.id_karyawan', '=', 'pekerjaan.id_karyawan')
                    ->where('pekerjaan.company_id', $company->id)
                    ->where('karyawan.Kode', 'Non Aktif')
                    ->whereYear('karyawan.updated_at', $currentYear)
                    ->whereMonth('karyawan.updated_at', $month)
                    ->count();

                $turnoverData[$month][$company->name] = ['masuk' => $masuk, 'keluar' => $keluar];
            }
        }

        // View: pages/dashboard/admin.blade.php (atau dashboard.blade.php yang lama)
        return view('pages.dashboard', compact(
            'totalKaryawan',
            'karyawanAktif',
            'totalKontrak',
            'totaldepartment_id',
            'genderData',
            'jabatanData',
            'divisiData',
            'pendidikanData',
            'tenureCounts',
            'ageCounts',
            'perusahaanData',
            'turnoverData'
        ));
    }
}
