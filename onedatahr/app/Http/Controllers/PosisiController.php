<?php

namespace App\Http\Controllers;

use App\Models\Posisi;
use App\Models\Pekerjaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;

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
        // Ambil semua posisi dengan total pelamar, sudah tersetting dengan progress_rekrutmen otomatis dari KandidatObserver
        $pos = Posisi::withCount('kandidat as total_pelamar_view')
            ->orderBy('id_posisi', 'DESC')
            ->get();

        // Get unique job titles based on positions used in pekerjaan
        $usedPositionIds = Pekerjaan::whereNotNull('position_id')->pluck('position_id');
        $jobTitles = \App\Models\Position::whereIn('id', $usedPositionIds)
            ->distinct()
            ->orderBy('name')
            ->pluck('name')
            ->toArray();

        return view('pages.rekrutmen.posisi.index', [
            'posisis' => $pos,
            'jobTitles' => $jobTitles
        ]);
    }

    public function index()
    {
        $pos = Posisi::aktif()->orderBy('nama_posisi')->get(['id_posisi', 'nama_posisi', 'status']);
        return response()->json($pos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_posisi' => 'required|string|max:150|unique:posisi,nama_posisi',
            'status'      => 'required|in:Aktif,Nonaktif',
            'fpk_file'    => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $fpkFilePath = null;

        if ($request->hasFile('fpk_file')) {
            $fpkFilePath = $request->file('fpk_file')
                ->store('fpk', 'public');
        }

        $pos = Posisi::create([
            'nama_posisi' => $request->nama_posisi,
            'status'      => $request->status,
            'activated_at'=> $request->status === 'Aktif' ? now() : null,
            'fpk_file'    => $fpkFilePath ? basename($fpkFilePath) : null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Posisi berhasil ditambahkan',
            'data' => $pos
        ]);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_posisi' => 'required|string|max:150|unique:posisi,nama_posisi,' . $id . ',id_posisi',
            'status'      => 'required|in:Aktif,Nonaktif',
            'fpk_file'    => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $pos = Posisi::findOrFail($id);
        $oldStatus = $pos->status;

        $data = [
            'nama_posisi' => $request->nama_posisi,
            'status'      => $request->status
        ];

        if ($request->hasFile('fpk_file')) {
            // HAPUS FILE LAMA
            if ($pos->fpk_file && Storage::disk('public')->exists('fpk/'.$pos->fpk_file)) {
                Storage::disk('public')->delete('fpk/'.$pos->fpk_file);
            }

            $path = $request->file('fpk_file')->store('fpk', 'public');
            $data['fpk_file'] = basename($path);
        }

        if ($request->status === 'Aktif' && $oldStatus !== 'Aktif') {
            $data['activated_at'] = now();
        }

        if ($request->status === 'Nonaktif') {
            $data['activated_at'] = null;
        }

        $pos->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Posisi berhasil diperbarui',
            'data' => $pos
        ]);
    }

     /**
     * Download Surat FPK file for a given posisi
     */
    public function downloadFpk($id)
    {
        $posisi = Posisi::findOrFail($id);
        if (!$posisi->fpk_file || !\Storage::disk('public')->exists('fpk/' . $posisi->fpk_file)) {
            abort(404, 'File tidak ditemukan');
        }
        $filePath = 'fpk/' . $posisi->fpk_file;
        $fileName = $posisi->fpk_file;
        return response()->download(storage_path('app/public/' . $filePath), $fileName);
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
