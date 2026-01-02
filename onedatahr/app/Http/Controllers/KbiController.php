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
    public function index(Request $request)
{
    $user = Auth::user();
    $tahun = date('Y');

    // 1. Validasi NIK
    if (empty($user->nik)) {
        return redirect()->back()->with('error', 'Akun Login tidak memiliki NIK.');
    }

    // 2. Cari Data Karyawan (Diri Sendiri)
    $karyawan = Karyawan::where('nik', $user->nik)->first();
    if (!$karyawan) {
        return redirect()->back()->with('error', 'Data Karyawan tidak ditemukan.');
    }

    // 3. Cek Penilaian Diri Sendiri
    $selfAssessment = KbiAssessment::where('karyawan_id', $karyawan->id_karyawan)
        ->where('tipe_penilai', 'DIRI_SENDIRI')
        ->where('tahun', $tahun)
        ->first();

    // ==========================================
    // 4. LOGIC DAFTAR KARYAWAN (ALL ACCESS)
    // ==========================================
    
    $query = Karyawan::query();

    // Filter A: Jangan tampilkan diri sendiri di tabel kanan
    // (Karena diri sendiri sudah ada di kartu kiri)
    $query->where('id_karyawan', '!=', $karyawan->id_karyawan);

    // Filter B: Jika ada pencarian
    if ($request->has('search') && $request->search != '') {
        $keyword = $request->search;
        $query->where(function($q) use ($keyword) {
            $q->where('Nama_Lengkap_Sesuai_Ijazah', 'LIKE', '%' . $keyword . '%')
              ->orWhere('Nama_Sesuai_KTP', 'LIKE', '%' . $keyword . '%')
              ->orWhere('NIK', 'LIKE', '%' . $keyword . '%');
        });
    }
    
    // CATATAN: Saya MENGHAPUS bagian 'else { where atasan_id }'
    // Jadi sekarang otomatis mengambil SEMUA data.

    // Eksekusi Query
    // Kita gunakan 'map' untuk mengecek status penilaian satu per satu
    $bawahanList = $query->paginate(10); 

    // 2. Gunakan 'through' (pengganti map) untuk menyuntikkan status penilaian
    $bawahanList->through(function ($staff) use ($tahun, $user) {
        $staff->sudah_dinilai = KbiAssessment::where('karyawan_id', $staff->id_karyawan)
            ->where('penilai_id', $user->id)
            ->where('tipe_penilai', 'ATASAN')
            ->where('tahun', $tahun)
            ->exists();
        return $staff;
    });

    // ==========================================

    // 5. Ambil Data Atasan (Tetap sama, opsional jika ingin menilai bos)
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