<?php

namespace App\Http\Controllers;

use App\Models\TempaPeserta;
use App\Models\TempaKelompok;
use App\Models\Tempa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TempaPesertaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('viewTempaPeserta');

        $user = Auth::user();
        if ($user instanceof User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        } else {
            $isKetuaTempa = false;
        }

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

    public function show($peserta)
    {
        $this->authorize('viewTempaPeserta');

        $user = Auth::user();
        if ($user instanceof User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        } else {
            $isKetuaTempa = false;
        }

        $pesertaModel = TempaPeserta::with(['kelompok.ketuaTempa', 'tempa'])->findOrFail($peserta);

        // Cek akses ketua_tempa
        if ($isKetuaTempa && $pesertaModel->kelompok->ketua_tempa_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        return view('pages.tempa.peserta.show', compact('pesertaModel'));
    }

    public function create()
    {
        $this->authorize('createTempaPeserta');

        $user = Auth::user();
        if ($user instanceof User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        } else {
            $isKetuaTempa = false;
        }

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
        $user = Auth::user();
        if ($user instanceof User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        } else {
            $isKetuaTempa = false;
        }

        $validated = $request->validated();

        if ($isKetuaTempa) {
            // Ketua TEMPA gunakan kelompok yang dipilih
            $kelompok = TempaKelompok::where('id_kelompok', $validated['kelompok_id'])
                ->where('ketua_tempa_id', $user->id)
                ->first();

            if (!$kelompok) {
                return back()->withErrors(['kelompok_id' => 'Kelompok tidak ditemukan atau tidak milik Anda'])->withInput();
            }

            $validated['id_kelompok'] = $kelompok->id_kelompok;
            $validated['id_tempa'] = $kelompok->id_tempa;
            unset($validated['kelompok_id']);
        } else {
            // Admin/Superadmin gunakan kelompok yang dipilih
            $kelompok = TempaKelompok::find($validated['kelompok_id']);
            if (!$kelompok) {
                return back()->withErrors(['kelompok_id' => 'Kelompok tidak ditemukan'])->withInput();
            }

            $validated['id_kelompok'] = $validated['kelompok_id'];
            $validated['id_tempa'] = $kelompok->id_tempa;
            unset($validated['nama_mentor'], $validated['kelompok_id']);
        }

        TempaPeserta::create($validated);

        return redirect()
            ->route('tempa.peserta.index')
            ->with('success', 'Peserta berhasil ditambahkan');
    }



    public function edit($peserta)
    {
        $this->authorize('editTempaPeserta');

        $user = Auth::user();
        if ($user instanceof User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        } else {
            $isKetuaTempa = false;
        }
        $peserta = TempaPeserta::findOrFail($peserta);

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
        $user = Auth::user();
        if ($user instanceof User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        } else {
            $isKetuaTempa = false;
        }
        $pesertaModel = TempaPeserta::findOrFail($peserta);

        // Cek akses ketua_tempa
        if ($isKetuaTempa && $pesertaModel->kelompok->ketua_tempa_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validated();

        if ($isKetuaTempa) {
            // Ketua TEMPA gunakan kelompok yang dipilih
            $kelompok = TempaKelompok::where('id_kelompok', $validated['kelompok_id'])
                ->where('ketua_tempa_id', $user->id)
                ->first();

            if (!$kelompok) {
                return back()->withErrors(['kelompok_id' => 'Kelompok tidak ditemukan atau tidak milik Anda'])->withInput();
            }

            $validated['id_kelompok'] = $kelompok->id_kelompok;
            $validated['id_tempa'] = $kelompok->id_tempa;
            unset($validated['kelompok_id']);
        } else {
            // Admin/Superadmin gunakan kelompok yang dipilih
            $validated['id_kelompok'] = $validated['kelompok_id'];
            unset($validated['nama_mentor'], $validated['kelompok_id']);
        }

        $pesertaModel->update($validated);

        return redirect()
            ->route('tempa.peserta.index')
            ->with('success', 'Peserta berhasil diperbarui');
    }

    public function destroy($peserta)
    {
        $this->authorize('deleteTempaPeserta');

        $user = Auth::user();
        if ($user instanceof User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        } else {
            $isKetuaTempa = false;
        }
        $pesertaModel = TempaPeserta::findOrFail($peserta);

        // Cek akses ketua_tempa
        if ($isKetuaTempa && $pesertaModel->kelompok->ketua_tempa_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        $pesertaModel->delete();

        return redirect()
            ->route('tempa.peserta.index')
            ->with('success', 'Peserta berhasil dihapus');
    }
}
