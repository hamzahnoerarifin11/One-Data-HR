<?php

namespace App\Http\Controllers;

use App\Models\Posisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $pos = Posisi::orderBy('id_posisi', 'DESC')->get();
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
        // HAPUS pengecekan manual in_array(auth()->user()->role, ...)

        try {
            $pos = Posisi::findOrFail($id);
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
