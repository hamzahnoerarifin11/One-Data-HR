<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\KbiAssessment;
use App\Models\KpiAssessment;
use Illuminate\Pagination\LengthAwarePaginator;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $tahun = request()->get('tahun', date('Y'));

        // 1. Query Data Karyawan (Filter Pencarian Nama/NIK tetap pakai SQL)
        $query = Karyawan::with('pekerjaan');
        
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->where(function($q) use ($keyword) {
                $q->where('Nama_Lengkap_Sesuai_Ijazah', 'LIKE', '%'.$keyword.'%')
                  ->orWhere('NIK', 'LIKE', '%'.$keyword.'%');
            });
        }

        $karyawans = $query->get();

        // PAGINA

        // 2. HITUNG NILAI (Mapping)
        // Kita hitung dulu semua karyawan, baru nanti difilter Grade-nya
        $rekapCollection = $karyawans->map(function($k) use ($tahun) {
            
            // --- A. Hitung KBI ---
            $nilaiKbi = KbiAssessment::where('karyawan_id', $k->id_karyawan)
                        ->where('tahun', $tahun)
                        ->avg('rata_rata_akhir'); 
            
            $skorKbiAsli = $nilaiKbi ? $nilaiKbi : 0;
            $skorKbi100 = ($skorKbiAsli / 4) * 100;

            // --- B. Hitung KPI ---
            $kpiRecord = KpiAssessment::where('karyawan_id', $k->id_karyawan)
                        ->where('tahun', $tahun)
                        ->latest('created_at')
                        ->first();
            $skorKpi = $kpiRecord ? $kpiRecord->total_skor_akhir : 0; 

            // --- C. Hitung Final Score ---
            $finalScore = ($skorKpi * 0.7) + ($skorKbi100 * 0.3);

            // --- D. Tentukan Grade ---
            if ($finalScore >= 90) $grade = 'A';
            elseif ($finalScore >= 80) $grade = 'B';
            elseif ($finalScore >= 70) $grade = 'C';
            elseif ($finalScore >= 60) $grade = 'D';
            else $grade = 'E';

            return (object) [
                'nik' => $k->NIK,
                'nama' => $k->Nama_Lengkap_Sesuai_Ijazah,
                'jabatan' => $k->pekerjaan->Jabatan ?? '-', 
                'skor_kbi_asli' => $skorKbiAsli,
                'skor_kbi_100'  => $skorKbi100,
                'skor_kpi'      => $skorKpi,
                'final_score'   => $finalScore,
                'grade'         => $grade
            ];
        });

        // ============================================================
        // 3. BAGIAN INI YANG KEMUNGKINAN ANDA LEWATKAN
        // Filter Collection berdasarkan Grade yang dipilih di Dropdown
        // ============================================================
        if ($request->has('grade') && $request->grade != '') {
            $rekapCollection = $rekapCollection->where('grade', $request->grade);
        }

        // A. Tentukan halaman saat ini
        $page = LengthAwarePaginator::resolveCurrentPage();
        
        // B. Tentukan jumlah data per halaman
        $perPage = 10; 

        // C. Potong data Collection sesuai halaman (Slice)
        // Logika: Ambil data mulai dari (halaman_skrg - 1) * 10, sebanyak 10 item
        $currentItems = $rekapCollection->slice(($page - 1) * $perPage, $perPage)->all();

        // D. Buat Object Paginator Baru
        $rekap = new LengthAwarePaginator(
            $currentItems,              // Data potongannya
            count($rekapCollection),    // Total data keseluruhan (sebelum dipotong)
            $perPage,                   // Limit per halaman
            $page,                      // Halaman aktif
            ['path' => LengthAwarePaginator::resolveCurrentPath()] // URL dasar
        );

        // E. Tambahkan query string (search/grade) agar tidak hilang saat klik next page
        $rekap->appends($request->all());
        
        // F. (Opsional) Batasi jumlah link pagination biar tidak panjang (1 ... 40)
        $rekap->onEachSide(1);

        return view('pages.performance.rekap', compact('rekap', 'tahun'));
    }
}