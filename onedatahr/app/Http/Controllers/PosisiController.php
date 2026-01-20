<?php

namespace App\Http\Controllers;

use App\Models\Posisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class PosisiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // Gunakan middleware role yang sudah kita perbaiki logikanya.
        // Method manage, store, update, dan destroy hanya untuk admin & superadmin.
        // Method index (untuk API filter) mungkin bisa diakses semua user yang login.
        $this->middleware('role:admin|superadmin')->except(['index']);
    }

    public function manage()
    {
    // Tambahkan withCount untuk menghitung jumlah kandidat per posisi
    // Pastikan di model Posisi ada function kandidat() { return $this->hasMany(Kandidat::class, ...); }
    $pos = Posisi::withCount('kandidat as total_pelamar')
        ->orderBy('id_posisi', 'DESC')
        ->get();
        
    // Jika Anda butuh 'progress_rekrutmen', kita bisa manipulasi collection
    // Atau sementara kita samakan saja dengan status
    $pos->transform(function($item) {
        $item->progress_rekrutmen = $item->status == 'Aktif' ? 'Menerima Kandidat' : 'Tidak Menerima Kandidat';
        return $item;
    });

    return view('pages.rekrutmen.posisi.index', ['posisis' => $pos]);
    }

    public function index()
    {
        $pos = Posisi::aktif()->orderBy('nama_posisi')->get(['id_posisi', 'nama_posisi', 'status']);
        return response()->json($pos);
    }

    public function store(Request $request)
    {
        // HAPUS pengecekan manual in_array(auth()->user()->role, ...)
        // karena sudah ditangani oleh middleware di __construct

        $request->validate([
            'nama_posisi' => 'required|string|max:150|unique:posisi,nama_posisi',
            'status'      => 'required|in:Aktif,Nonaktif',
        ]);

        try {
            $pos = Posisi::create([
                'nama_posisi' => $request->nama_posisi,
                'status'      => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Posisi berhasil ditambahkan',
                'data'    => $pos
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // HAPUS pengecekan manual in_array(auth()->user()->role, ...)

        $request->validate([
            'nama_posisi' => 'required|string|max:150|unique:posisi,nama_posisi,' . $id . ',id_posisi',
            'status'      => 'required|in:Aktif,Nonaktif',
        ]);

        try {
            $pos = Posisi::findOrFail($id);
            $pos->update([
                'nama_posisi' => $request->nama_posisi,
                'status'      => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Posisi berhasil diperbarui',
                'data'    => $pos
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui data'], 500);
        }
    }

    public function destroy(Request $request, $id)
{
    try {
        $pos = Posisi::findOrFail($id);
        
        // Opsi 1: Cek Manual (Lebih User Friendly)
        // Asumsi relasi di model Posisi bernama 'kandidats'
        // atau cek manual ke tabel kandidat
        $terpakai = DB::table('kandidat')->where('posisi_id', $id)->exists();
        
        if ($terpakai) {
            return response()->json([
                'success' => false, 
                'message' => 'Posisi tidak bisa dihapus karena sudah ada kandidat yang melamar di posisi ini. Silakan nonaktifkan saja statusnya.'
            ], 422);
        }

        $pos->delete();

        return response()->json([
            'success' => true,
            'message' => 'Posisi berhasil dihapus'
        ]);

    } catch (QueryException $e) {
        // Tangkap error spesifik database (FK Constraint)
        if ($e->errorInfo[1] == 1451) { // Kode error MySQL untuk FK constraint
            return response()->json(['success' => false, 'message' => 'Data tidak bisa dihapus karena berelasi dengan data lain.'], 409);
        }
        return response()->json(['success' => false, 'message' => 'Database error.'], 500);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Gagal menghapus data'], 500);
    }
}
}
