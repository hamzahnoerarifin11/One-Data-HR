<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekrutmenDaily;
use App\Models\Posisi;
use Illuminate\Support\Facades\Auth;

class RekrutmenDailyController extends Controller
{
    public function index(Request $request)
    {
        // Jika dipanggil via browser biasa, arahkan ke view dashboard
        if (!$request->wantsJson()) {
            return view('pages.rekrutmen.calendar'); // Pastikan nama file view benar
        }

        $request->validate([
            'posisi_id' => 'nullable|integer|exists:posisi,id_posisi',
            'month'     => 'required|integer|min:1|max:12',
            'year'      => 'required|integer|min:2000',
        ]);

        $query = RekrutmenDaily::query();

        // Jika posisi_id diisi (filter spesifik), jika tidak tampilkan semua (untuk dashboard excel)
        if ($request->filled('posisi_id')) {
            $query->where('posisi_id', $request->posisi_id);
        }

        // Filter berdasarkan Bulan dan Tahun (Sangat penting untuk dashboard per bulan)
        $query->whereMonth('date', $request->month)
              ->whereYear('date', $request->year);

        $data = $query->orderBy('date')->get()->map(function($item) {
            // Mapping data agar aman dari nilai null dan casting ke integer
            return [
                'id'               => $item->id,
                'posisi_id'        => $item->posisi_id,
                'date'             => $item->date,
                'total_pelamar'    => (int)($item->total_pelamar ?? $item->count ?? 0),
                'lolos_cv'         => (int)($item->lolos_cv ?? 0),
                'lolos_psikotes'   => (int)($item->lolos_psikotes ?? 0),
                'lolos_kompetensi' => (int)($item->lolos_kompetensi ?? 0),
                'lolos_hr'         => (int)($item->lolos_hr ?? 0),
                'lolos_user'       => (int)($item->lolos_user ?? 0),
            ];
        });

        return response()->json($data);
    }

    public function store(Request $request)
    {
        // Guard: Hanya admin atau role yang diizinkan
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Hanya admin yang dapat mengubah data.'], 403);
        }

        $validated = $request->validate([
            'posisi_id'        => 'required|integer|exists:posisi,id_posisi',
            'date'             => 'required|date',
            'total_pelamar'    => 'nullable|integer|min:0',
            'lolos_cv'         => 'nullable|integer|min:0',
            'lolos_psikotes'   => 'nullable|integer|min:0',
            'lolos_kompetensi' => 'nullable|integer|min:0',
            'lolos_hr'         => 'nullable|integer|min:0',
            'lolos_user'       => 'nullable|integer|min:0',
            'notes'            => 'nullable|string',
        ]);

        // Support legacy field 'count'
        $total = $request->filled('total_pelamar') ? (int)$request->total_pelamar : 0;

        // Gunakan updateOrCreate untuk mencegah duplikasi posisi + tanggal yang sama
        $entry = RekrutmenDaily::updateOrCreate(
            [
                'posisi_id' => $validated['posisi_id'],
                'date'      => $validated['date']
            ],
            [
                'total_pelamar'    => $total,
                'lolos_cv'         => $validated['lolos_cv'] ?? 0,
                'lolos_psikotes'   => $validated['lolos_psikotes'] ?? 0,
                'lolos_kompetensi' => $validated['lolos_kompetensi'] ?? 0,
                'lolos_hr'         => $validated['lolos_hr'] ?? 0,
                'lolos_user'       => $validated['lolos_user'] ?? 0,
                'notes'            => $validated['notes'] ?? null,
                'created_by'       => Auth::id(),
            ]
        );

        return response()->json(['success' => true, 'entry' => $entry]);
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $e = RekrutmenDaily::findOrFail($id);
        $e->delete();

        return response()->json(['success' => true]);
    }
}
