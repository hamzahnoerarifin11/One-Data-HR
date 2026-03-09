<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekrutmenDaily;
use App\Models\Posisi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RekrutmenDailyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // Membatasi akses tulis (store & destroy) hanya untuk admin/superadmin
        // Method 'index' dibiarkan agar semua staff bisa melihat kalender
        $this->middleware('role:admin|superadmin')->only(['store', 'destroy']);
    }

    public function index(Request $request)
    {
        if (!$request->wantsJson()) {
            return view('pages.rekrutmen.calendar');
        }

        $year = $request->year;

        $query = RekrutmenDaily::whereYear('date', $year);

        if ($request->filled('posisi_id')) {
            $query->where('posisi_id', $request->posisi_id);
        }

        $data = $query->get()->map(function($item) {
            return [
                'id'               => $item->id,
                'posisi_id'        => $item->posisi_id,
                'date'             => $item->date instanceof \Carbon\Carbon ? $item->date->format('Y-m-d') : substr($item->date, 0, 10),
                'total_pelamar'    => (int)$item->total_pelamar,
                'lolos_cv'         => (int)$item->lolos_cv,
                'lolos_psikotes'   => (int)$item->lolos_psikotes,
                'lolos_kompetensi' => (int)$item->lolos_kompetensi,
                'lolos_hr'         => (int)$item->lolos_hr,
                'lolos_user'       => (int)$item->lolos_user,
            ];
        });

        return response()->json($data);
    }

    public function store(Request $request)
    {
        // BARIS INI DIHAPUS karena sudah ditangani Middleware di __construct
        // if (!in_array(Auth::user()->role, ['admin', 'superadmin'])) { ... }

        $validated = $request->validate([
            'posisi_id' => 'required|integer|exists:posisi,id_posisi',
            'date'      => 'required|date',
            'total_pelamar' => 'nullable|integer|min:0',
            'notes'     => 'nullable|string',
        ]);

        $entry = RekrutmenDaily::updateOrCreate(
            [
                'posisi_id' => $request->posisi_id,
                'date'      => $request->date
            ],
            [
                'total_pelamar' => $request->total_pelamar ?? 0,
                'notes'         => $request->notes,
                'created_by'    => Auth::id()
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Data manual berhasil disimpan.',
            'entry'   => $entry
        ]);
    }

    public function destroy($id)
    {
        // BARIS INI DIHAPUS karena sudah ditangani Middleware di __construct

        $e = RekrutmenDaily::findOrFail($id);
        $e->delete();

        return response()->json(['success' => true]);
    }
}
