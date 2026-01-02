<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\KbiAssessment;
use App\Models\KbiItem;
use App\Models\KbiScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KbiController extends Controller
{
    public function index()
{
    $user = Auth::user();
    $tahun = date('Y');

    // 1. Cari Data Karyawan berdasarkan NIK (Sesuai hasil debug yang sukses)
    if (empty($user->nik)) {
        return redirect()->back()->with('error', 'Akun Login tidak memiliki NIK.');
    }

    $karyawan = Karyawan::where('nik', $user->nik)->first();

    if (!$karyawan) {
        return redirect()->back()->with('error', 'Data Karyawan tidak ditemukan untuk NIK ini.');
    }

    // 2. Cek Penilaian Diri Sendiri
    // Gunakan $karyawan->id_karyawan, BUKAN $user->id
    $selfAssessment = KbiAssessment::where('karyawan_id', $karyawan->id_karyawan)
        ->where('tipe_penilai', 'DIRI_SENDIRI')
        ->where('tahun', $tahun)
        ->first();

    // 3. Ambil List Bawahan (Tim Saya)
    // PERBAIKAN UTAMA DISINI: Gunakan $karyawan->id_karyawan
    $bawahanList = Karyawan::where('atasan_id', $karyawan->id_karyawan)
        ->get()
        ->map(function ($staff) use ($tahun, $user) {
            $staff->sudah_dinilai = KbiAssessment::where('karyawan_id', $staff->id_karyawan)
                ->where('penilai_id', $user->id) // Penilai tetap ID User
                ->where('tipe_penilai', 'ATASAN')
                ->where('tahun', $tahun)
                ->exists();
            return $staff;
        });

    // 4. Ambil Data Atasan
    $atasan = $karyawan->atasan; 
    
    $sudahMenilaiAtasan = false;
    if ($atasan) {
        $sudahMenilaiAtasan = KbiAssessment::where('karyawan_id', $atasan->id_karyawan)
            ->where('penilai_id', $user->id)
            ->where('tipe_penilai', 'BAWAHAN')
            ->where('tahun', $tahun)
            ->exists();
    }

    return view('pages.kbi.index', compact('karyawan', 'selfAssessment', 'bawahanList', 'atasan', 'sudahMenilaiAtasan'));
}
    // Menangkap parameter 'tipe' dari URL Route
    public function create($karyawan_id, $tipe = 'DIRI_SENDIRI')
    {
        // Gunakan id_karyawan untuk mencari (sesuai setting primaryKey model)
        $karyawan = Karyawan::where('id_karyawan', $karyawan_id)->firstOrFail();
        
        $kbiItems = KbiItem::all(); 
        
        // Gunakan variabel $tipe yang dikirim dari Route (index.blade.php)
        $tipe_penilai = $tipe; 

        return view('pages.kbi.form', compact('karyawan', 'kbiItems', 'tipe_penilai'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction(); // Gunakan transaction agar aman
        try {
            // 1. Buat Header Assessment
            $assessment = KbiAssessment::create([
                'karyawan_id'  => $request->karyawan_id, // ID Karyawan yang dinilai
                'penilai_id'   => Auth::id(),            // ID User yang menilai
                'tipe_penilai' => $request->tipe_penilai,
                'tahun'        => date('Y'),
                'status'       => 'SUBMITTED'
            ]);

            // 2. Simpan Detail Jawaban
            $totalScore = 0;
            $count = 0;
            
            if ($request->scores) {
                foreach ($request->scores as $itemId => $score) {
                    KbiScore::create([
                        'kbi_assessment_id' => $assessment->id_kbi_assessment,
                        'kbi_item_id'       => $itemId,
                        'skor'              => $score
                    ]);
                    $totalScore += $score;
                    $count++;
                }
            }

            // 3. Update Rata-rata Header
            $rataRata = $count > 0 ? $totalScore / $count : 0;
            $assessment->update(['rata_rata_akhir' => $rataRata]);

            DB::commit();
            return redirect()->route('kbi.index')->with('success', 'Penilaian KBI berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menyimpan penilaian: ' . $e->getMessage());
        }
    }
}