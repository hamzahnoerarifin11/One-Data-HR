<?php

namespace App\Http\Controllers;

use App\Models\Pemberkasan;
use App\Models\Kandidat;
use App\Models\KandidatLanjutUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $kandidatLolos = KandidatLanjutUser::with(['kandidat.posisi'])
            ->whereHas('kandidat', function ($query) {
                $query->where('status_akhir', 'Interview User Lolos')
                      ->whereDoesntHave('pemberkasan');
            })
            ->get();

        return view('pages.rekrutmen.pemberkasan.create', compact('kandidatLolos'));
    }

    public function store(Request $request)
    {
        // Pemberkasan::create($request->all());
        DB::transaction(function () use ($request) {
            // 1. Buat data pemberkasan
            Pemberkasan::create($request->all());

            // 2. Update status kandidat menjadi 'Diterima'
            Kandidat::where('id_kandidat', $request->kandidat_id)
                ->update(['status_akhir' => 'Diterima']);
        });
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
        $kandidatLolos = KandidatLanjutUser::with(['kandidat.posisi'])
            ->whereHas('kandidat', function ($query) {
                $query->where('status_akhir', 'Interview User Lolos')
                      ->orWhere('status_akhir', 'Tahap Pemberkasan');
            })
            ->get();

        return view('pages.rekrutmen.pemberkasan.edit', compact('pemberkasan', 'kandidatLolos'));
    }

    public function update(Request $request, $id)
    {
        $pemberkasan = Pemberkasan::findOrFail($id);
        // $pemberkasan->update($request->all());
        DB::transaction(function () use ($request, $pemberkasan) {
            // Jika kandidat diganti saat edit, kembalikan status kandidat lama
            if ($pemberkasan->kandidat_id != $request->kandidat_id) {
                Kandidat::where('id_kandidat', $pemberkasan->kandidat_id)
                    ->update(['status_akhir' => 'Interview User Lolos']);
            }

            // Update data pemberkasan
            $pemberkasan->update($request->all());

            // Pastikan status kandidat baru/tetap menjadi 'Diterima'
            Kandidat::where('id_kandidat', $request->kandidat_id)
                ->update(['status_akhir' => 'Diterima']);
        });
        return redirect()->route('rekrutmen.pemberkasan.index')->with('success', 'Data diperbarui.');
    }
    // public function destroy($id)
    // {
    //     $pemberkasan = Pemberkasan::where('id_pemberkasan', $id)->firstOrFail();
        
    //     // Kembalikan status kandidat ke Lolos User jika data pemberkasan dihapus
    //     Kandidat::where('id_kandidat', $pemberkasan->kandidat_id)
    //         ->update(['status_akhir' => 'Interview User Lolos']);

    //     $pemberkasan->delete();

    //     return back()->with('success', 'Data berhasil dihapus');
    // }
    public function destroy($id)
    {
        $pemberkasan = Pemberkasan::where('id_pemberkasan', $id)->firstOrFail();

        DB::transaction(function () use ($pemberkasan) {
            // Kembalikan status kandidat ke 'Interview User Lolos' karena batal pemberkasan/dihapus
            Kandidat::where('id_kandidat', $pemberkasan->kandidat_id)
                ->update(['status_akhir' => 'Interview User Lolos']);

            $pemberkasan->delete();
        });

        return back()->with('success', 'Data berhasil dihapus dan status kandidat dikembalikan ke Interview User Lolos.');
    }
}
