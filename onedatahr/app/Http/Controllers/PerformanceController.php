<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\KbiAssessment;
use App\Models\KpiAssessment;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class PerformanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|superadmin|manager');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $tahun = request()->get('tahun', date('Y'));

        // ======================================================
        // 0. PENGAMAN DATA DIRI (PENTING!)
        // ======================================================
        // Kita cari dulu data karyawan milik user yang login.
        // Asumsi relasi di model User: public function karyawan() { return $this->hasOne(Karyawan::class, 'nik', 'nik'); }
        $me = Karyawan::where('nik', $user->nik)->first();

        // Jika user BUKAN admin, tapi data karyawannya tidak ada -> TENDANG KELUAR
        if (!$me && !$user->hasRole(['superadmin', 'admin'])) {
            return redirect()->back()->with('error', 'Data profil karyawan Anda belum terhubung. Silakan hubungi HRD.');
        }

        // ======================================================
        // 1. QUERY UTAMA
        // ======================================================
        $query = Karyawan::with('pekerjaan'); // Eager load pekerjaan biar cepat

        // A. Filter Search (Nama/NIK)
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('Nama_Lengkap_Sesuai_Ijazah', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('NIK', 'LIKE', '%' . $keyword . '%');
            });
        }

        // B. Filter Role (Manager hanya lihat bawahan)
        if ($user->hasRole('manager')) {
            // PERBAIKAN: Gunakan $me->id_karyawan (Aman karena sudah dicek diatas)
            // PERBAIKAN: Typo 'atasa_id' jadi 'atasan_id'
            $query->where('atasan_id', $me->id_karyawan);
        } elseif ($user->hasRole('staff')) {
            // Staff hanya lihat diri sendiri
            $query->where('id_karyawan', $me->id_karyawan);
        }

        // Eksekusi Query
        $karyawans = $query->get();

        // ======================================================
        // 2. HITUNG NILAI & MAPPING
        // ======================================================
        $rekapCollection = $karyawans->map(function ($k) use ($tahun) {

            // --- A. Hitung KBI ---
            // Tips Optimasi: Gunakan Eager Loading 'kbiAssessment' di query atas agar lebih cepat
            $nilaiKbi = KbiAssessment::where('karyawan_id', $k->id_karyawan)
                ->where('tahun', $tahun)
                ->avg('rata_rata_akhir'); // Ambil rata-rata jika ada banyak penilai

            $skorKbiAsli = $nilaiKbi ? $nilaiKbi : 0;

            // --- B. Hitung KPI ---
            $kpiRecord = KpiAssessment::where('karyawan_id', $k->id_karyawan)
                ->where('tahun', $tahun)
                ->latest('created_at') // Ambil yang paling baru
                ->first();

            $skorKpi = $kpiRecord ? $kpiRecord->total_skor_akhir : 0;

            // --- C. Hitung Final Score (Bobot 70:30) ---
            $finalScore = ($skorKpi * 0.7) + ($skorKbiAsli * 0.3);

            // --- D. Tentukan Grade ---
            if ($finalScore >= 90) $grade = 'A';
            elseif ($finalScore >= 80) $grade = 'B';
            elseif ($finalScore >= 70) $grade = 'C';
            elseif ($finalScore >= 60) $grade = 'D';
            else $grade = 'E';

            // Return Object Bersih
            return (object) [
                'id_karyawan' => $k->id_karyawan, // Penting untuk link detail
                'nik'         => $k->NIK,
                'nama'        => $k->Nama_Lengkap_Sesuai_Ijazah,
                // PERBAIKAN: Gunakan safe navigation operator
                'jabatan'     => $k->pekerjaan->first()?->Jabatan ?? '-',
                'skor_kbi_asli' => $skorKbiAsli,
                'skor_kbi'    => number_format($skorKbiAsli, 2),
                'skor_kpi'    => number_format($skorKpi, 2),
                'final_score' => number_format($finalScore, 2),
                'grade'       => $grade
            ];
        });

        // ======================================================
        // 3. FILTER GRADE (Dropdown)
        // ======================================================
        if ($request->has('grade') && $request->grade != '') {
            $rekapCollection = $rekapCollection->where('grade', $request->grade);
        }

        // ======================================================
        // 4. PAGINASI MANUAL (Karena Data dari Collection)
        // ======================================================
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;

        // Potong data sesuai halaman
        $currentItems = $rekapCollection->slice(($page - 1) * $perPage, $perPage)->all();

        // Buat Object Paginator
        $rekap = new LengthAwarePaginator(
            $currentItems,
            count($rekapCollection),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        // Tambahkan query string agar filter tidak hilang saat ganti halaman
        $rekap->appends($request->all());

        return view('pages.performance.rekap', compact('rekap', 'tahun'));
    }
}
