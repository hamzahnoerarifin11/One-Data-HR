<?php

namespace App\Http\Controllers;

use App\Models\TempaPeserta;
use App\Models\TempaKelompok;
use App\Models\Tempa;
use Illuminate\Http\Request;

class TempaPesertaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('viewTempaPeserta');

        $user = auth()->user();
        $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);

        if ($isKetuaTempa) {
            // Ketua TEMPA hanya melihat peserta dari kelompoknya saja
            $pesertas = TempaPeserta::with(['kelompok'])
                ->whereHas('kelompok', function($q) use ($user) {
                    $q->where('ketua_tempa_id', $user->id);
                })->get();
        } else {
            // Admin/Superadmin melihat semua peserta
            $pesertas = TempaPeserta::with(['kelompok'])->get();
        }

        return view('pages.tempa.peserta.index', compact('pesertas'));
    }



    public function create()
    {
        $this->authorize('createTempaPeserta');

        $user = auth()->user();
        $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);

        if ($isKetuaTempa) {
            // Ketua TEMPA hanya bisa membuat peserta untuk kelompoknya
            $kelompoks = TempaKelompok::where('ketua_tempa_id', $user->id)->get();
        } else {
            // Admin/Superadmin bisa membuat peserta di mana saja
            $kelompoks = TempaKelompok::with('ketuaTempa')->get();
        }

        return view('pages.tempa.peserta.create', compact('kelompoks', 'isKetuaTempa'));
    }

    public function store(\App\Http\Requests\TempaPesertaRequest $request)
    {
        $user = auth()->user();
        $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);

        $validated = $request->validated();

        if ($isKetuaTempa) {
            // Ketua TEMPA buat kelompok baru atau gunakan yang ada
            $activeTempa = Tempa::latest()->first();
            if (!$activeTempa) {
                return back()->withErrors(['tempa' => 'Tidak ada TEMPA aktif'])->withInput();
            }

            // Buat atau cari kelompok dengan kombinasi unik: nama_kelompok + ketua_tempa_id
            $kelompok = TempaKelompok::firstOrCreate(
                [
                    'nama_kelompok' => $validated['nama_kelompok'],
                    'ketua_tempa_id' => $user->id,
                    'id_tempa' => $activeTempa->id_tempa
                ],
                [
                    'nama_mentor' => $validated['nama_mentor']
                ]
            );

            $validated['id_kelompok'] = $kelompok->id_kelompok;
            $validated['id_tempa'] = $activeTempa->id_tempa;
            unset($validated['nama_kelompok'], $validated['nama_mentor']);
        } else {
            // Admin/Superadmin gunakan kelompok yang dipilih
            unset($validated['nama_mentor']);
        }

        TempaPeserta::create($validated);

        return redirect()
            ->route('tempa.peserta.index')
            ->with('success', 'Peserta berhasil ditambahkan');
    }



    public function edit($peserta)
    {
        $this->authorize('editTempaPeserta');

        $user = auth()->user();
        $peserta = TempaPeserta::findOrFail($peserta);
        $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);

        // Cek akses ketua_tempa
        if ($isKetuaTempa && $peserta->kelompok->ketua_tempa_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        if ($isKetuaTempa) {
            // Ketua TEMPA hanya bisa edit kelompoknya sendiri
            $kelompoks = TempaKelompok::where('ketua_tempa_id', $user->id)->get();
        } else {
            // Admin/Superadmin bisa edit di kelompok mana saja
            $kelompoks = TempaKelompok::with('ketuaTempa')->get();
        }

        return view('pages.tempa.peserta.edit', compact('peserta', 'kelompoks', 'isKetuaTempa'));
    }

    public function update(\App\Http\Requests\TempaPesertaRequest $request, $peserta)
    {
        $user = auth()->user();
        $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);

        $pesertaModel = TempaPeserta::findOrFail($peserta);

        // Cek akses ketua_tempa
        if ($isKetuaTempa && $pesertaModel->kelompok->ketua_tempa_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validated();

        if ($isKetuaTempa) {
            // Ketua TEMPA buat atau gunakan kelompok
            $activeTempa = Tempa::latest()->first();
            if (!$activeTempa) {
                return back()->withErrors(['tempa' => 'Tidak ada TEMPA aktif'])->withInput();
            }

            $kelompok = TempaKelompok::firstOrCreate(
                [
                    'nama_kelompok' => $validated['nama_kelompok'],
                    'ketua_tempa_id' => $user->id,
                    'id_tempa' => $activeTempa->id_tempa
                ],
                [
                    'nama_mentor' => $validated['nama_mentor']
                ]
            );

            $validated['id_kelompok'] = $kelompok->id_kelompok;
            $validated['id_tempa'] = $activeTempa->id_tempa;
            unset($validated['nama_kelompok'], $validated['nama_mentor']);
        } else {
            // Admin/Superadmin gunakan kelompok yang dipilih
            unset($validated['nama_mentor']);
        }

        $pesertaModel->update($validated);

        return redirect()
            ->route('tempa.peserta.index')
            ->with('success', 'Peserta berhasil diperbarui');
    }

    public function destroy($peserta)
    {
        $this->authorize('deleteTempaPeserta');

        $user = auth()->user();
        $pesertaModel = TempaPeserta::findOrFail($peserta);

        // Cek akses ketua_tempa
        $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        if ($isKetuaTempa && $pesertaModel->kelompok->ketua_tempa_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        $pesertaModel->delete();

        return redirect()
            ->route('tempa.peserta.index')
            ->with('success', 'Peserta berhasil dihapus');
    }
}
