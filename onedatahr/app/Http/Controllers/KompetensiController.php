<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PegawaiKompetensi;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Auth;

class KompetensiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Security Check: Hanya admin, superadmin, manager, direktur yang bisa melihat
        if (!$this->roleMatches($user, ['admin', 'superadmin', 'manager', 'GM', 'senior_manager', 'direktur'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $query = Karyawan::with(['pegawaiKompetensi', 'pekerjaan.company', 'pekerjaan.division', 'pekerjaan.department'])
            ->whereHas('pegawaiKompetensi');

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('Nama_Lengkap_Sesuai_Ijazah', 'LIKE', "%{$search}%")
                  ->orWhere('NIK', 'LIKE', "%{$search}%")
                  ->orWhereHas('pegawaiKompetensi', function($kompQuery) use ($search) {
                      $kompQuery->where('nama_kompetensi', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter Level (Opsional)
        if ($request->filled('level')) {
            $query->whereHas('pegawaiKompetensi', function ($q) use ($request) {
                $q->where('level', $request->level);
            });
        }

        // Urutkan data berdasarkan nama
        $kompetensiList = $query->orderBy('Nama_Lengkap_Sesuai_Ijazah', 'ASC')->paginate(10)->appends($request->all());

        // Untuk Dropdown Filter (Level Kompetensi unik dari database)
        $listLevel = PegawaiKompetensi::select('level')->whereNotNull('level')->distinct()->pluck('level');

        return view('pages.kompetensi.index', compact('kompetensiList', 'listLevel'));
    }

    // Helper: Role Checker
    private function roleMatches($user, $roles)
    {
        if (!$user) return false;

        $userRoleNames = [];
        try {
            $userRoleNames = $user->roles()->pluck('name')->map(function ($r) {
                return strtolower($r);
            })->toArray();
        } catch (\Throwable $e) {}

        $derivedRoles = [];
        $karyawan = Karyawan::where('user_id', $user->id)->first();
        if (!$karyawan && !empty($user->nik)) {
            $karyawan = Karyawan::where('nik', $user->nik)->first();
        }
        if ($karyawan) {
            $pekerjaan = $karyawan->pekerjaanTerkini()->first() ?? $karyawan->pekerjaan()->first();
            if ($pekerjaan) {
                if (!empty($pekerjaan->level) && !empty($pekerjaan->level->name)) $derivedRoles[] = strtolower($pekerjaan->level->name);
                if (!empty($pekerjaan->position) && !empty($pekerjaan->position->name)) $derivedRoles[] = strtolower($pekerjaan->position->name);
            }
        }

        if (is_string($roles)) $roles = [$roles];
        $roles = array_map('strtolower', $roles);

        foreach ($roles as $r) {
            if (in_array($r, $userRoleNames)) return true;
            if (in_array($r, $derivedRoles)) return true;
        }
        return false;
    }
}
