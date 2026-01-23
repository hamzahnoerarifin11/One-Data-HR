<?php

namespace App\Http\Controllers;

use App\Http\Requests\TempaKelompokRequest;
use App\Models\TempaKelompok;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TempaKelompokController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('viewTempaKelompok');

            $user = Auth::user();
            if ($user instanceof \App\Models\User) {
                $user->loadMissing('roles');
                $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
            } else {
                $isKetuaTempa = false;
            }

        if ($isKetuaTempa) {
            // Ketua TEMPA hanya melihat kelompoknya sendiri
            $kelompoks = TempaKelompok::with(['ketuaTempa'])
                ->where('ketua_tempa_id', $user->id)
                ->get();
        } else {
            // Admin/Superadmin melihat semua kelompok
            $kelompoks = TempaKelompok::with(['ketuaTempa'])->get();
        }

        return view('pages.tempa.kelompok.index', compact('kelompoks'));
    }

    public function create()
    {
        $this->authorize('createTempaKelompok');

        $user = Auth::user();
        if ($user instanceof \App\Models\User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        } else {
            $isKetuaTempa = false;
        }

        if ($isKetuaTempa) {
            // Ketua TEMPA hanya bisa membuat kelompok untuk dirinya sendiri
            return view('pages.tempa.kelompok.create');
        } else {
            // Admin/Superadmin bisa membuat kelompok untuk siapa saja
            return view('pages.tempa.kelompok.create');
        }
    }

    public function store(TempaKelompokRequest $request)
    {
        $user = Auth::user();
        if ($user instanceof \App\Models\User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        } else {
            $isKetuaTempa = false;
        }

        $validated = $request->validated();

        if ($isKetuaTempa) {
            $validated['ketua_tempa_id'] = $user->id;
        } else {
            // Untuk admin, bisa set ketua_tempa_id jika diperlukan, tapi untuk sekarang biarkan null atau set default
            $validated['ketua_tempa_id'] = $request->input('ketua_tempa_id', $user->id); // atau sesuaikan
        }

        TempaKelompok::create($validated);

        return redirect()
            ->route('tempa.kelompok.index')
            ->with('success', 'Kelompok berhasil ditambahkan');
    }

    public function edit($kelompok)
    {
        $this->authorize('editTempaKelompok');

        $user = Auth::user();
        $kelompok = TempaKelompok::findOrFail($kelompok);
        if ($user instanceof \App\Models\User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        } else {
            $isKetuaTempa = false;
        }

        // Cek akses ketua_tempa
        if ($isKetuaTempa && $kelompok->ketua_tempa_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        return view('pages.tempa.kelompok.edit', compact('kelompok'));
    }

    public function show($kelompok)
    {
        $this->authorize('viewTempaKelompok');

        $user = Auth::user();
        if ($user instanceof \App\Models\User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        } else {
            $isKetuaTempa = false;
        }

        $kelompok = TempaKelompok::with(['ketuaTempa', 'pesertas'])->findOrFail($kelompok);

        // Cek akses ketua_tempa
        if ($isKetuaTempa && $kelompok->ketua_tempa_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        return view('pages.tempa.kelompok.show', compact('kelompok'));
    }

    public function update(TempaKelompokRequest $request, $kelompok)
    {
        $user = Auth::user();
        $kelompokModel = TempaKelompok::findOrFail($kelompok);
        if ($user instanceof \App\Models\User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        } else {
            $isKetuaTempa = false;
        }

        // Cek akses ketua_tempa
        if ($isKetuaTempa && $kelompokModel->ketua_tempa_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validated();

        $kelompokModel->update($validated);

        return redirect()
            ->route('tempa.kelompok.index')
            ->with('success', 'Kelompok berhasil diperbarui');
    }

    public function destroy($kelompok)
    {
        $this->authorize('deleteTempaKelompok');

        $user = Auth::user();
        $kelompokModel = TempaKelompok::findOrFail($kelompok);
        if ($user instanceof \App\Models\User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        } else {
            $isKetuaTempa = false;
        }

        // Cek akses ketua_tempa
        if ($isKetuaTempa && $kelompokModel->ketua_tempa_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        // Cek apakah ada peserta di kelompok ini
        if ($kelompokModel->pesertas()->count() > 0) {
            return back()->withErrors(['kelompok' => 'Tidak dapat menghapus kelompok yang masih memiliki peserta']);
        }

        $kelompokModel->delete();

        return redirect()
            ->route('tempa.kelompok.index')
            ->with('success', 'Kelompok berhasil dihapus');
    }
}
