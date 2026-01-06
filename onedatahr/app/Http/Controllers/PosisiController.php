<?php

namespace App\Http\Controllers;

use App\Models\Posisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosisiController extends Controller
{
    // Menampilkan halaman manajemen (Server Rendered)
    public function manage()
    {
        // Mengambil semua data posisi untuk ditampilkan di table
        // $pos = Posisi::orderBy('created_at', 'DESC')->get();
        // return view('pages.rekrutmen.posisi.index', ['posisis' => $pos]);
        // Mengambil semua data dari VIEW agar kolom progress_rekrutmen & total_pelamar muncul
        // $pos = DB::table('view_rekrutmen_dashboard')
        //         ->orderBy('id_posisi', 'DESC') // Urutkan berdasarkan ID posisi terbaru
        //         ->get();

        // return view('pages.rekrutmen.posisi.index', ['posisis' => $pos]);
        // Kembali menggunakan Eloquent Model karena kolom sudah ada di tabel fisik
        $pos = Posisi::orderBy('id_posisi', 'DESC')->get();
        return view('pages.rekrutmen.posisi.index', ['posisis' => $pos]);
    }

    // JSON list untuk keperluan API/Filter
    public function index()
    {
        $pos = Posisi::aktif()->orderBy('nama_posisi')->get(['id_posisi', 'nama_posisi', 'status']);
        return response()->json($pos);
    }


    // Create via AJAX
    public function store(Request $request)
    {
        // Validasi akses (Pastikan user login & admin)
        if (!auth()->user() || !in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            return response()->json(['message' => 'Unauthorized. Hanya admin yang diperbolehkan.'], 403);
        }

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

    // Update via AJAX
    public function update(Request $request, $id)
    {
        if (!auth()->user() || !in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

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

    // Destroy via AJAX
    public function destroy(Request $request, $id)
    {
        if (!auth()->user() || !in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $pos = Posisi::findOrFail($id);

            // Opsional: Cek jika posisi sedang digunakan di tabel lain sebelum hapus
            // if ($pos->kandidat()->count() > 0) {
            //     return response()->json(['message' => 'Posisi tidak bisa dihapus karena memiliki data kandidat.'], 422);
            // }

            $pos->delete();

            return response()->json([
                'success' => true,
                'message' => 'Posisi berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data'], 500);
        }
    }
}
