<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kandidat;
use App\Models\Posisi;
use Illuminate\Support\Facades\Storage;



class KandidatController extends Controller
{
    public function index(Request $request)
    {
        $query = Kandidat::with('posisi')
            ->orderBy('created_at', 'desc');

        if ($request->filled('posisi_id')) {
            $query->where('posisi_id', $request->posisi_id);
        }

        $kandidats = $query->get();
        $posisis   = Posisi::where('status', 'Aktif')->get();

        return view(
            'pages.rekrutmen.kandidat.index',
            compact('kandidats', 'posisis')
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'             => 'required|string|max:150',
            'posisi_id'        => 'required|exists:posisi,id_posisi',
            'tanggal_melamar'  => 'nullable|date',
            'sumber'           => 'nullable|string|max:100',
            'link_cv'          => 'nullable|url',
            'file_pdf'        => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($request->file('file_pdf')) {
            $file = $request->file('file_pdf');
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->storeAs('uploads/pdf', $filename, 'public');
            $data['file_pdf'] = $filename;
        }

        $kandidat = Kandidat::create($data);

        // 🔥 FETCH-FRIENDLY RESPONSE
        return response()->json([
            'success' => true,
            'message' => 'Kandidat berhasil ditambahkan',
            'data'    => $kandidat
        ]);
    }

    public function update(Request $request, $id)
    {
        $kandidat = Kandidat::findOrFail($id);

        $data = $request->validate([
            'nama'             => 'required|string|max:150',
            'posisi_id'        => 'required|exists:posisi,id_posisi',
            'tanggal_melamar'  => 'nullable|date',
            'sumber'           => 'nullable|string|max:100',
            'status_akhir'     => 'required|string',
            'link_cv'          => 'nullable|url',
            'file_pdf'        => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($request->file('file_pdf')) {
            if ($kandidat->file_pdf) {
                Storage::disk('public')->delete('uploads/pdf/' . $kandidat->file_pdf);
            }

            $file = $request->file('file_pdf');
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->storeAs('uploads/pdf', $filename, 'public');
            $data['file_pdf'] = $filename;
        }

        $kandidat->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data kandidat berhasil diperbarui'
        ]);
    }

    public function destroy($id)
    {
        $kandidat = Kandidat::findOrFail($id);

        if ($kandidat->file_pdf) {
            Storage::disk('public')->delete('uploads/pdf/' . $kandidat->file_pdf);
        }

        $kandidat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kandidat berhasil dihapus'
        ]);
    }

    public function list(Request $request)
    {
        $query = Kandidat::orderBy('created_at', 'desc');

        if ($request->filled('posisi_id')) {
            $query->where('posisi_id', $request->posisi_id);
        }

        if ($request->filled('q')) {
            $query->where('nama', 'like', '%' . $request->q . '%');
        }

        return response()->json(
            $query->limit(50)->get(['id_kandidat', 'nama'])
        );
    }
}
