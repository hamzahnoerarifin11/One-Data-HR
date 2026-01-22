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
        $search = request('search', null);
        $kelompok = request('kelompok', null);
        $status = request('status', null);
        $lokasi = request('lokasi', null);

        $query = TempaAbsensi::with(['peserta.kelompok.ketuaTempa', 'peserta'])
            ->leftJoin('tempa_peserta', 'tempa_absensi.id_peserta', '=', 'tempa_peserta.id_peserta')
            ->leftJoin('tempa_kelompok', 'tempa_peserta.id_kelompok', '=', 'tempa_kelompok.id_kelompok')
            ->where('tempa_absensi.tahun_absensi', $tahun);

        if ($isKetuaTempa) {
            // Ketua TEMPA hanya melihat absensi peserta dari kelompoknya
            $query->where('tempa_kelompok.ketua_tempa_id', $user->id);
        }

        // Filter berdasarkan search (nama peserta atau NIK)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('tempa_peserta.nama_peserta', 'like', '%' . $search . '%')
                  ->orWhere('tempa_peserta.nik_karyawan', 'like', '%' . $search . '%');
            });
        }

        // Filter berdasarkan kelompok
        if ($kelompok) {
            $query->where('tempa_peserta.id_kelompok', $kelompok);
        }

        // Filter berdasarkan status peserta
        if ($status) {
            $query->where('tempa_peserta.status_peserta', $status);
        }

        // Filter berdasarkan lokasi (pusat/cabang)
        if ($lokasi) {
            $query->where('tempa_kelompok.tempat', $lokasi);
        }

        $absensis = $query->select('tempa_absensi.*')->get();

        // Load kelompoks untuk filter dropdown
        if ($isKetuaTempa) {
            $kelompoks = TempaKelompok::where('ketua_tempa_id', $user->id)->get();
        } else {
            $kelompoks = TempaKelompok::all();
        }

        return view('pages.tempa.absensi.index', compact('absensis', 'tahun', 'bulan', 'search', 'kelompok', 'status', 'lokasi', 'kelompoks'));
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

        $absensiModel = TempaAbsensi::with(['peserta.kelompok.ketuaTempa', 'peserta'])->findOrFail($absensi);

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

        $absensiModel = TempaAbsensi::with(['peserta.kelompok.ketuaTempa', 'peserta'])->findOrFail($absensi);

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
