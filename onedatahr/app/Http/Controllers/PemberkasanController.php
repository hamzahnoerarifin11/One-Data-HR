<?php

namespace App\Http\Controllers;

use App\Models\Pemberkasan;
use App\Models\Kandidat;
use App\Models\KandidatLanjutUser;
use Illuminate\Http\Request;

class PemberkasanController extends Controller
{
    public function index()
    {
        // Load relasi kandidat untuk menampilkan Nama dan Posisi di tabel index
        $pemberkasan = Pemberkasan::with('kandidat','posisi')->latest()->get();
        return view('pages.rekrutmen.pemberkasan.index', compact('pemberkasan'));
    }

    public function create()
    {
        // Ambil kandidat yang Lolos ASS dan Lolos ASM
        // Kita join ke tabel kandidat untuk mendapatkan Nama dan Posisi
        $kandidatLolos = KandidatLanjutUser::where('hasil_ass', 'Lolos')
            ->where('hasil_asm', 'Lolos')
            ->with('kandidat','posisi') // Pastikan di model KandidatLanjutUser ada relasi 'kandidat'
            ->whereDoesntHave('kandidat.pemberkasan')
            ->get();

        return view('pages.rekrutmen.pemberkasan.create', compact('kandidatLolos'));
    }

    public function store(Request $request)
    {
        Pemberkasan::create($request->all());
        return redirect()->route('rekrutmen.pemberkasan.index')->with('success', 'Data pemberkasan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $pemberkasan = Pemberkasan::with('kandidat','posisi')->findOrFail($id);
        return view('pages.rekrutmen.pemberkasan.show', compact('pemberkasan'));
    }

    public function edit($id)
    {
        $pemberkasan = Pemberkasan::findOrFail($id);
        // Tetap tampilkan kandidat yang memenuhi syarat lolos untuk pilihan edit
        $kandidatLolos = KandidatLanjutUser::where('hasil_ass', 'Lolos')
            ->where('hasil_asm', 'Lolos')
            ->with('kandidat','posisi')
            ->get();

        return view('pages.rekrutmen.pemberkasan.edit', compact('pemberkasan', 'kandidatLolos'));
    }

    public function update(Request $request, $id)
    {
        $pemberkasan = Pemberkasan::findOrFail($id);
        $pemberkasan->update($request->all());
        return redirect()->route('rekrutmen.pemberkasan.index')->with('success', 'Data diperbarui.');
    }
    public function destroy($id)
    {
        Pemberkasan::where('id_pemberkasan', $id)->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }
}
