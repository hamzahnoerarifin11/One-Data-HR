<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\KbiAssessment; 
use App\Models\KpiAssessment; // Pastikan Model ini di-import

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $tahun = date('Y');

        // 1. Ambil Data Karyawan (Plus Filter Search)
        $query = Karyawan::with('pekerjaan');
        
        if ($request->has('search')) {
            $keyword = $request->search;
            $query->where(function($q) use ($keyword) {
                $q->where('Nama_Lengkap_Sesuai_Ijazah', 'LIKE', '%'.$keyword.'%')
                  ->orWhere('NIK', 'LIKE', '%'.$keyword.'%');
            });
        }

        $karyawans = $query->get();

        // 2. Olah Gabungan Nilai KPI & KBI
        $rekap = $karyawans->map(function($k) use ($tahun) {
            
            // --- A. AMBIL NILAI KBI (Soft Skill - Skala 4) ---
            $nilaiKbi = KbiAssessment::where('karyawan_id', $k->id_karyawan)
                        ->where('tahun', $tahun)
                        ->avg('rata_rata_akhir'); 
            
            // Jika belum ada nilai, set 0
            $skorKbiAsli = $nilaiKbi ? $nilaiKbi : 0;

            // KONVERSI KBI KE SKALA 100
            // Rumus: (Nilai / 4) * 100. Contoh: (3.0 / 4) * 100 = 75
            $skorKbi100 = ($skorKbiAsli / 4) * 100;


            // --- B. AMBIL NILAI KPI (Hard Skill - Skala 100) ---
            // [UPDATED] Mengambil dari database Anda (total_skor_akhir)
            $kpiRecord = KpiAssessment::where('karyawan_id', $k->id_karyawan)
                        ->where('tahun', $tahun)
                        ->latest('created_at') // Ambil yang paling baru jika ada duplikat
                        ->first();
            
            // Ambil kolom 'total_skor_akhir', jika null set 0
            $skorKpi = $kpiRecord ? $kpiRecord->total_skor_akhir : 0; 


            // --- C. HITUNG GRAND TOTAL ---
            // Bobot: KPI 70%, KBI 30% (Bisa diubah sesuai kebijakan)
            $bobotKpi = 70;
            $bobotKbi = 30;

            // Pastikan KPI sudah skala 100 (karena di DB desimal, biasanya sudah 0-100)
            $finalScore = ($skorKpi * ($bobotKpi/100)) + ($skorKbi100 * ($bobotKbi/100));


            // --- D. TENTUKAN GRADE FINAL ---
            if ($finalScore >= 90) $grade = 'A';
            elseif ($finalScore >= 80) $grade = 'B';
            elseif ($finalScore >= 70) $grade = 'C';
            elseif ($finalScore >= 60) $grade = 'D';
            else $grade = 'E';

            return (object) [
                'id' => $k->id_karyawan,
                'nik' => $k->NIK,
                'nama' => $k->Nama_Lengkap_Sesuai_Ijazah,
                'jabatan' => $k->pekerjaan->nama_jabatan ?? '-', 
                
                // Data untuk ditampilkan di Tabel
                'skor_kbi_asli' => round($skorKbiAsli, 2), // Tampil 3.50
                'skor_kbi_100'  => round($skorKbi100, 2),  // Tampil 87.50
                'skor_kpi'      => round($skorKpi, 2),     // Nilai KPI asli
                'final_score'   => round($finalScore, 2),
                'grade'         => $grade
            ];
        });

        return view('pages.performance.rekap', compact('rekap', 'tahun'));
    }
}