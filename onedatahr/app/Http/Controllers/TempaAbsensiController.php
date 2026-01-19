<?php

namespace App\Http\Controllers;

use App\Models\TempaAbsensi;
use App\Models\TempaPeserta;
use App\Models\TempaKelompok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TempaAbsensiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('viewTempaAbsensi');

        $user = Auth::user();
        if ($user instanceof \App\Models\User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        } else {
            $isKetuaTempa = false;
        }

        $tahun = request('tahun', date('Y'));
        $bulan = request('bulan', null); // null = tampilkan semua bulan

        if ($isKetuaTempa) {
            // Ketua TEMPA hanya melihat absensi peserta dari kelompoknya
            $absensis = TempaAbsensi::with(['peserta.kelompok.ketuaTempa', 'peserta.tempa'])
                ->whereHas('peserta.kelompok', function($q) use ($user) {
                    $q->where('ketua_tempa_id', $user->id);
                })
                ->where('tahun_absensi', $tahun)
                ->get();
        } else {
            // Admin/Superadmin melihat semua absensi
            $absensis = TempaAbsensi::with(['peserta.kelompok.ketuaTempa', 'peserta.tempa'])
                ->where('tahun_absensi', $tahun)
                ->get();
        }

        return view('pages.tempa.absensi.index', compact('absensis', 'tahun', 'bulan'));
    }

    public function create()
    {
        $this->authorize('createTempaAbsensi');

        $user = Auth::user();
        if ($user instanceof \App\Models\User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        } else {
            $isKetuaTempa = false;
        }

        if ($isKetuaTempa) {
            // Ketua TEMPA hanya bisa membuat absensi untuk peserta kelompoknya
            $pesertas = TempaPeserta::with('kelompok')
                ->whereHas('kelompok', function($q) use ($user) {
                    $q->where('ketua_tempa_id', $user->id);
                })
                ->where('status_peserta', 1) // Hanya peserta aktif
                ->get();
        } else {
            // Admin/Superadmin bisa membuat absensi untuk semua peserta
            $pesertas = TempaPeserta::with('kelompok')
                ->where('status_peserta', 1) // Hanya peserta aktif
                ->get();
        }

        return view('pages.tempa.absensi.create', compact('pesertas'));
    }

    public function store(Request $request)
    {
        $this->authorize('createTempaAbsensi');

        $request->validate([
            'id_peserta' => 'required|exists:tempa_peserta,id_peserta',
            'tahun_absensi' => 'required|numeric|min:2020|max:' . (date('Y') + 1),
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'absensi' => 'nullable|array',
            'absensi.*' => 'array',
            'absensi.*.*' => 'nullable|in:hadir,tidak_hadir',
        ]);

        $user = Auth::user();
        if ($user instanceof \App\Models\User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        } else {
            $isKetuaTempa = false;
        }

        $peserta = TempaPeserta::findOrFail($request->id_peserta);

        // Cek akses ketua_tempa
        if ($isKetuaTempa && $peserta->kelompok->ketua_tempa_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        // Prepare absensi data
        $absensiData = $request->input('absensi', []);

        // Handle file upload
        $buktiFotoPath = null;
        if ($request->hasFile('bukti_foto')) {
            $buktiFotoPath = $request->file('bukti_foto')->store('absensi_tempa', 'public');
        }

        $absensi = TempaAbsensi::updateOrCreate(
            [
                'id_peserta' => $request->id_peserta,
                'tahun_absensi' => $request->tahun_absensi,
            ],
            [
                'absensi_data' => $absensiData,
                'pertemuan_ke' => 1, // Set default pertemuan_ke to 1
                'bukti_foto' => $buktiFotoPath,
                'created_by' => $user->id,
            ]
        );

        // Calculate totals
        $absensi->calculateTotals();
        $absensi->save();

        return redirect()
            ->route('tempa.absensi.index')
            ->with('success', 'Data absensi berhasil disimpan');
    }

    public function show($absensi)
    {
        $this->authorize('viewTempaAbsensi');

        $user = Auth::user();
        if ($user instanceof \App\Models\User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        } else {
            $isKetuaTempa = false;
        }

        $absensiModel = TempaAbsensi::with(['peserta.kelompok.ketuaTempa', 'peserta.tempa'])->findOrFail($absensi);

        // Cek akses ketua_tempa
        if ($isKetuaTempa && $absensiModel->peserta->kelompok->ketua_tempa_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        return view('pages.tempa.absensi.show', compact('absensiModel'));
    }

    public function edit($absensi)
    {
        $this->authorize('editTempaAbsensi');

        $user = Auth::user();
        if ($user instanceof \App\Models\User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        } else {
            $isKetuaTempa = false;
        }

        $absensiModel = TempaAbsensi::with(['peserta.kelompok.ketuaTempa', 'peserta.tempa'])->findOrFail($absensi);

        // Cek akses ketua_tempa
        if ($isKetuaTempa && $absensiModel->peserta->kelompok->ketua_tempa_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        if ($isKetuaTempa) {
            // Ketua TEMPA hanya bisa edit absensi untuk peserta kelompoknya
            $pesertas = TempaPeserta::with('kelompok')
                ->whereHas('kelompok', function($q) use ($user) {
                    $q->where('ketua_tempa_id', $user->id);
                })
                ->where('status_peserta', 1) // Hanya peserta aktif
                ->get();
        } else {
            // Admin/Superadmin bisa edit absensi untuk semua peserta
            $pesertas = TempaPeserta::with('kelompok')
                ->where('status_peserta', 1) // Hanya peserta aktif
                ->get();
        }

        return view('pages.tempa.absensi.edit', compact('absensiModel', 'pesertas'));
    }

    public function update(Request $request, $absensi)
    {
        $this->authorize('editTempaAbsensi');

        $request->validate([
            'id_peserta' => 'required|exists:tempa_peserta,id_peserta',
            'tahun_absensi' => 'required|numeric|min:2020|max:' . (date('Y') + 1),
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'absensi' => 'nullable|array',
            'absensi.*' => 'array',
            'absensi.*.*' => 'nullable|in:hadir,tidak_hadir',
        ]);

        $user = Auth::user();
        if ($user instanceof \App\Models\User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        } else {
            $isKetuaTempa = false;
        }

        $absensiModel = TempaAbsensi::findOrFail($absensi);
        $peserta = TempaPeserta::findOrFail($request->id_peserta);

        // Cek akses ketua_tempa
        if ($isKetuaTempa && $absensiModel->peserta->kelompok->ketua_tempa_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        // Prepare absensi data
        $absensiData = $request->input('absensi', []);

        // Handle file upload
        $buktiFotoPath = $absensiModel->bukti_foto;
        if ($request->hasFile('bukti_foto')) {
            // Delete old file if exists
            if ($buktiFotoPath && Storage::disk('public')->exists($buktiFotoPath)) {
                Storage::disk('public')->delete($buktiFotoPath);
            }
            $buktiFotoPath = $request->file('bukti_foto')->store('absensi_tempa', 'public');
        }

        $absensiModel->update([
            'id_peserta' => $request->id_peserta,
            'tahun_absensi' => $request->tahun_absensi,
            'absensi_data' => $absensiData,
            'bukti_foto' => $buktiFotoPath,
        ]);

        // Calculate totals
        $absensiModel->calculateTotals();
        $absensiModel->save();

        return redirect()
            ->route('tempa.absensi.index')
            ->with('success', 'Data absensi berhasil diperbarui');
    }

    public function destroy($absensi)
    {
        $this->authorize('deleteTempaAbsensi');

        $user = Auth::user();
        if ($user instanceof \App\Models\User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        } else {
            $isKetuaTempa = false;
        }

        $absensiModel = TempaAbsensi::findOrFail($absensi);

        // Cek akses ketua_tempa
        if ($isKetuaTempa && $absensiModel->peserta->kelompok->ketua_tempa_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        // Delete file if exists
        if ($absensiModel->bukti_foto && Storage::disk('public')->exists($absensiModel->bukti_foto)) {
            Storage::disk('public')->delete($absensiModel->bukti_foto);
        }

        $absensiModel->delete();

        return redirect()
            ->route('tempa.absensi.index')
            ->with('success', 'Data absensi berhasil dihapus');
    }
}
