<?php

namespace App\Http\Controllers;

use App\Models\Posisi;
use Illuminate\Http\Request;

class PosisiController extends Controller
{
    // JSON list for filters
    public function index(Request $request)
    {
        $pos = Posisi::orderBy('nama_posisi')->get(['id_posisi', 'nama_posisi']);
        return response()->json($pos);
    }

    // server rendered management page
    public function manage()
    {
        $pos = Posisi::orderBy('nama_posisi')->get();
        return view('pages.rekrutmen.posisi.index', ['posisis' => $pos]);
    }

    // create via API
    public function store(Request $request)
    {
        $request->validate([
            'nama_posisi' => 'required|string|max:150|unique:posisi,nama_posisi',
        ]);

        $pos = Posisi::create(['nama_posisi' => $request->input('nama_posisi')]);

        return response()->json(['success' => true, 'posisi' => $pos]);
    }

    // update via API (admin only)
    public function update(Request $request, $id)
    {
        abort_unless(auth()->user() && auth()->user()->role === 'admin', 403);

        $request->validate([
            'nama_posisi' => 'required|string|max:150|unique:posisi,nama_posisi,'.$id.',id_posisi',
        ]);

        $pos = Posisi::findOrFail($id);
        $pos->nama_posisi = $request->input('nama_posisi');
        $pos->save();

        return response()->json(['success' => true, 'posisi' => $pos]);
    }

    // destroy via API (admin only)
    public function destroy(Request $request, $id)
    {
        abort_unless(auth()->user() && auth()->user()->role === 'admin', 403);

        $pos = Posisi::findOrFail($id);
        $pos->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Posisi dihapus');
    }
}
