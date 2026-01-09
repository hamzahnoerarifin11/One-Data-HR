<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekrutmenDaily;
use App\Models\Posisi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RekrutmenDailyController extends Controller
{
    // public function index(Request $request)
    // {
    //     // Jika bukan request JSON, tampilkan view kalender
    //     if (!$request->wantsJson()) {
    //         return view('pages.rekrutmen.calendar');
    //     }

    //     $month = $request->month;
    //     $year = $request->year;

    //     /**
    //      * CATATAN:
    //      * Bagian PROSES SYNC otomatis dihapus dari sini.
    //      * Sinkronisasi data tahapan sekarang sepenuhnya ditangani oleh
    //      * KandidatObserver saat status diupdate.
    //      */

    //     // Ambil data dari rekrutmen_daily untuk dikirim ke Kalender
    //     $query = RekrutmenDaily::whereMonth('date', $month)->whereYear('date', $year);

    //     if ($request->filled('posisi_id')) {
    //         $query->where('posisi_id', $request->posisi_id);
    //     }

    //     $data = $query->get()->map(function($item) {
    //         return [
    //             'id'               => $item->id,
    //             'posisi_id'        => $item->posisi_id,
    //             // Pastikan date dikonversi ke string Y-m-d
    //             'date'             => $item->date instanceof Carbon ? $item->date->format('Y-m-d') : $item->date,
    //             'total_pelamar'    => (int)$item->total_pelamar,
    //             'lolos_cv'         => (int)$item->lolos_cv,
    //             'lolos_psikotes'   => (int)$item->lolos_psikotes,
    //             'lolos_kompetensi' => (int)$item->lolos_kompetensi,
    //             'lolos_hr'         => (int)$item->lolos_hr,
    //             'lolos_user'       => (int)$item->lolos_user,
    //             'notes'            => $item->notes
    //         ];
    //     });

    //     return response()->json($data);
    // }
    public function index(Request $request)
        {
            if (!$request->wantsJson()) {
                return view('pages.rekrutmen.calendar');
            }

            $year = $request->year;

            // Ambil data satu tahun penuh agar bisa menghitung total tahunan di JS
            $query = RekrutmenDaily::whereYear('date', $year);

            if ($request->filled('posisi_id')) {
                $query->where('posisi_id', $request->posisi_id);
            }

            $data = $query->get()->map(function($item) {
                return [
                    'id'               => $item->id,
                    'posisi_id'        => $item->posisi_id,
                    // Normalisasi format tanggal
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
        if (!in_array(Auth::user()->role, ['admin', 'superadmin'])) {
        return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'posisi_id' => 'required|integer|exists:posisi,id_posisi',
            'date'      => 'required|date',
            'total_pelamar' => 'nullable|integer|min:0',
            'notes'     => 'nullable|string',
        ]);

        // Gunakan updateOrCreate untuk input manual admin
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
            'message' => 'Data manual (Total Pelamar/Notes) berhasil disimpan.',
            'entry'   => $entry
        ]);
    }

    public function destroy($id)
    {
        if (!in_array(Auth::user()->role, ['admin', 'superadmin'])) {
        return response()->json(['message' => 'Unauthorized'], 403);
        }

        $e = RekrutmenDaily::findOrFail($id);
        $e->delete();

        return response()->json(['success' => true]);
    }
}
