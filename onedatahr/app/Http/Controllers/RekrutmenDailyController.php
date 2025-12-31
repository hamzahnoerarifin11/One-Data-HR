<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekrutmenDaily;
use App\Models\Posisi;
use Illuminate\Support\Facades\Auth;

class RekrutmenDailyController extends Controller
{
    // public function index(Request $request)
    // {
    //     // Jika dipanggil via browser biasa, arahkan ke view dashboard
    //     if (!$request->wantsJson()) {
    //         return view('pages.rekrutmen.calendar'); // Pastikan nama file view benar
    //     }

    //     $request->validate([
    //         'posisi_id' => 'nullable|integer|exists:posisi,id_posisi',
    //         'month'     => 'required|integer|min:1|max:12',
    //         'year'      => 'required|integer|min:2000',
    //     ]);

    //     $query = RekrutmenDaily::query();

    //     // Jika posisi_id diisi (filter spesifik), jika tidak tampilkan semua (untuk dashboard excel)
    //     if ($request->filled('posisi_id')) {
    //         $query->where('posisi_id', $request->posisi_id);
    //     }

    //     // Filter berdasarkan Bulan dan Tahun (Sangat penting untuk dashboard per bulan)
    //     $query->whereMonth('date', $request->month)
    //           ->whereYear('date', $request->year);

    //     $data = $query->orderBy('date')->get()->map(function($item) {
    //         // Mapping data agar aman dari nilai null dan casting ke integer
    //         return [
    //             'id'               => $item->id,
    //             'posisi_id'        => $item->posisi_id,
    //             // 'date'             => $item->date,
    //             'date'             => \Carbon\Carbon::parse($item->date)->format('Y-m-d'),
    //             'total_pelamar'    => (int)($item->total_pelamar ?? $item->count ?? 0),
    //             'lolos_cv'         => (int)($item->lolos_cv ?? 0),
    //             'lolos_psikotes'   => (int)($item->lolos_psikotes ?? 0),
    //             'lolos_kompetensi' => (int)($item->lolos_kompetensi ?? 0),
    //             'lolos_hr'         => (int)($item->lolos_hr ?? 0),
    //             'lolos_user'       => (int)($item->lolos_user ?? 0),
    //         ];
    //     });

    //     return response()->json($data);
    // }
    // RekrutmenDailyController.php

// public function index(Request $request)
// {
//     if (!$request->wantsJson()) {
//         return view('pages.rekrutmen.calendar');
//     }

//     $request->validate([
//         'month' => 'required|integer|min:1|max:12',
//         'year'  => 'required|integer|min:2000',
//     ]);

//     $month = $request->month;
//     $year = $request->year;

//     // --- LOGIKA AUTO-SYNC SAAT VIEW DIBUKA ---
//     // Mengambil rekap kandidat berdasarkan bulan dan tahun yang dipilih
//     $kandidatStats = \App\Models\Kandidat::select('posisi_id', 'tanggal_melamar')
//         ->whereMonth('tanggal_melamar', $month)
//         ->whereYear('tanggal_melamar', $year)
//         ->selectRaw("
//             SUM(CASE WHEN status_akhir = 'CV Lolos' THEN 1 ELSE 0 END) as cv,
//             SUM(CASE WHEN status_akhir = 'Psikotes Lolos' THEN 1 ELSE 0 END) as psikotes,
//             SUM(CASE WHEN status_akhir = 'Tes Kompetensi Lolos' THEN 1 ELSE 0 END) as kompetensi,
//             SUM(CASE WHEN status_akhir = 'Interview HR Lolos' THEN 1 ELSE 0 END) as hr,
//             SUM(CASE WHEN status_akhir = 'Interview User Lolos' THEN 1 ELSE 0 END) as user
//         ")
//         ->groupBy('posisi_id', 'tanggal_melamar')
//         ->get();

//     foreach ($kandidatStats as $stat) {
//         // Update atau Buat record baru jika belum ada
//         // Menggunakan updateOrCreate agar total_pelamar tetap aman (jika record baru, default 0)
//         RekrutmenDaily::updateOrCreate(
//             ['posisi_id' => $stat->posisi_id, 'date' => $stat->tanggal_melamar],
//             [
//                 'lolos_cv'         => $stat->cv,
//                 'lolos_psikotes'   => $stat->psikotes,
//                 'lolos_kompetensi' => $stat->kompetensi,
//                 'lolos_hr'         => $stat->hr,
//                 'lolos_user'       => $stat->user,
//                 // 'total_pelamar' TIDAK DISENTUH di sini agar input manual tidak hilang
//             ]
//         );
//     }
//     // --- END AUTO-SYNC ---

//     $query = RekrutmenDaily::whereMonth('date', $month)->whereYear('date', $year);

//     if ($request->filled('posisi_id')) {
//         $query->where('posisi_id', $request->posisi_id);
//     }

//     $data = $query->get()->map(function($item) {
//         return [
//             'id'               => $item->id,
//             'posisi_id'        => $item->posisi_id,
//             'date'             => \Carbon\Carbon::parse($item->date)->format('Y-m-d'),
//             'total_pelamar'    => (int)$item->total_pelamar,
//             'lolos_cv'         => (int)$item->lolos_cv,
//             'lolos_psikotes'   => (int)$item->lolos_psikotes,
//             'lolos_kompetensi' => (int)$item->lolos_kompetensi,
//             'lolos_hr'         => (int)$item->lolos_hr,
//             'lolos_user'       => (int)$item->lolos_user,
//         ];
//     });

//     return response()->json($data);
// }
// public function index(Request $request)
// {
//     if (!$request->wantsJson()) {
//         return view('pages.rekrutmen.calendar');
//     }

//     $request->validate([
//         'month' => 'required|integer|min:1|max:12',
//         'year'  => 'required|integer|min:2000',
//     ]);

//     $month = $request->month;
//     $year = $request->year;

//     // --- PROSES PERHITUNGAN DAN PENYIMPANAN KE DATABASE ---
//     // 1. Ambil statistik dari tabel kandidat
//     $statsKandidat = \App\Models\Kandidat::select('posisi_id', 'tanggal_melamar')
//         ->whereMonth('tanggal_melamar', $month)
//         ->whereYear('tanggal_melamar', $year)
//         ->selectRaw("
//             SUM(CASE WHEN status_akhir = 'CV Lolos' THEN 1 ELSE 0 END) as cv,
//             SUM(CASE WHEN status_akhir = 'Psikotes Lolos' THEN 1 ELSE 0 END) as psikotes,
//             SUM(CASE WHEN status_akhir = 'Tes Kompetensi Lolos' THEN 1 ELSE 0 END) as kompetensi,
//             SUM(CASE WHEN status_akhir = 'Interview HR Lolos' THEN 1 ELSE 0 END) as hr,
//             SUM(CASE WHEN status_akhir = 'Interview User Lolos' THEN 1 ELSE 0 END) as user
//         ")
//         ->groupBy('posisi_id', 'tanggal_melamar')
//         ->get();

//     // 2. SIMPAN/UPDATE ke tabel rekrutmen_daily
//     foreach ($statsKandidat as $stat) {
//         RekrutmenDaily::updateOrCreate(
//             [
//                 'posisi_id' => $stat->posisi_id,
//                 'date'      => $stat->tanggal_melamar // Pastikan formatnya Y-m-d
//             ],
//             [
//                 'lolos_cv'         => $stat->cv,
//                 'lolos_psikotes'   => $stat->psikotes,
//                 'lolos_kompetensi' => $stat->kompetensi,
//                 'lolos_hr'         => $stat->hr,
//                 'lolos_user'       => $stat->user,
//                 // total_pelamar tidak diupdate di sini agar input manual admin aman
//             ]
//         );
//     }

//     // 3. Ambil data yang sudah tersimpan di database untuk ditampilkan ke view
//     $query = RekrutmenDaily::whereMonth('date', $month)->whereYear('date', $year);

//     if ($request->filled('posisi_id')) {
//         $query->where('posisi_id', $request->posisi_id);
//     }

//     $data = $query->get()->map(function($item) {
//         return [
//             'id'               => $item->id,
//             'posisi_id'        => $item->posisi_id,
//             'date'             => \Carbon\Carbon::parse($item->date)->format('Y-m-d'),
//             'total_pelamar'    => (int)$item->total_pelamar,
//             'lolos_cv'         => (int)$item->lolos_cv,
//             'lolos_psikotes'   => (int)$item->lolos_psikotes,
//             'lolos_kompetensi' => (int)$item->lolos_kompetensi,
//             'lolos_hr'         => (int)$item->lolos_hr,
//             'lolos_user'       => (int)$item->lolos_user,
//         ];
//     });

//     return response()->json($data);
// }
public function index(Request $request)
{
    if (!$request->wantsJson()) {
        return view('pages.rekrutmen.calendar');
    }

    $month = $request->month;
    $year = $request->year;

    // 1. Ambil Statistik dari tabel Kandidat
    // Kita hitung berdasarkan posisi_id dan tanggal_melamar
    $statsKandidat = \App\Models\Kandidat::select('posisi_id', 'tanggal_melamar')
        ->whereMonth('tanggal_melamar', $month)
        ->whereYear('tanggal_melamar', $year)
        ->selectRaw("
            COUNT(id_kandidat) as total_riil,
            SUM(CASE WHEN status_akhir = 'CV Lolos' THEN 1 ELSE 0 END) as cv,
            SUM(CASE WHEN status_akhir = 'Psikotes Lolos' THEN 1 ELSE 0 END) as psikotes,
            SUM(CASE WHEN status_akhir = 'Tes Kompetensi Lolos' THEN 1 ELSE 0 END) as kompetensi,
            SUM(CASE WHEN status_akhir = 'Interview HR Lolos' THEN 1 ELSE 0 END) as hr,
            SUM(CASE WHEN status_akhir = 'Interview User Lolos' THEN 1 ELSE 0 END) as user
        ")
        ->groupBy('posisi_id', 'tanggal_melamar')
        ->get();

    // 2. PROSES SYNC: Paksa simpan ke rekrutmen_daily
    foreach ($statsKandidat as $stat) {
        // Pastikan tanggal dalam format string Y-m-d
        $tgl = \Carbon\Carbon::parse($stat->tanggal_melamar)->format('Y-m-d');

        \App\Models\RekrutmenDaily::updateOrCreate(
            [
                'posisi_id' => $stat->posisi_id,
                'date'      => $tgl
            ],
            [
                'lolos_cv'         => (int)$stat->cv,
                'lolos_psikotes'   => (int)$stat->psikotes,
                'lolos_kompetensi' => (int)$stat->kompetensi,
                'lolos_hr'         => (int)$stat->hr,
                'lolos_user'       => (int)$stat->user,
                // Kita tidak menimpa total_pelamar agar input manual admin tidak hilang
                // kecuali jika baris itu baru dibuat, kita bisa isi dengan total_riil
            ]
        );
    }

    // 3. Ambil data dari rekrutmen_daily untuk dikirim ke Kalender
    $query = RekrutmenDaily::whereMonth('date', $month)->whereYear('date', $year);

    if ($request->filled('posisi_id')) {
        $query->where('posisi_id', $request->posisi_id);
    }

    $data = $query->get()->map(function($item) {
        return [
            'id'               => $item->id,
            'posisi_id'        => $item->posisi_id,
            'date'             => $item->date->format('Y-m-d'),
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

    // public function store(Request $request)
    // {
    //     // Guard: Hanya admin atau role yang diizinkan
    //     if (Auth::user()->role !== 'admin') {
    //         return response()->json(['message' => 'Hanya admin yang dapat mengubah data.'], 403);
    //     }

    //     $validated = $request->validate([
    //         'posisi_id'        => 'required|integer|exists:posisi,id_posisi',
    //         'date'             => 'required|date',
    //         'total_pelamar'    => 'nullable|integer|min:0',
    //         'lolos_cv'         => 'nullable|integer|min:0',
    //         'lolos_psikotes'   => 'nullable|integer|min:0',
    //         'lolos_kompetensi' => 'nullable|integer|min:0',
    //         'lolos_hr'         => 'nullable|integer|min:0',
    //         'lolos_user'       => 'nullable|integer|min:0',
    //         'notes'            => 'nullable|string',
    //     ]);

    //     // Support legacy field 'count'
    //     $total = $request->filled('total_pelamar') ? (int)$request->total_pelamar : 0;

    //     // Gunakan updateOrCreate untuk mencegah duplikasi posisi + tanggal yang sama
    //     $entry = RekrutmenDaily::updateOrCreate(
    //         [
    //             'posisi_id' => $validated['posisi_id'],
    //             'date'      => $validated['date']
    //         ],
    //         [
    //             'total_pelamar'    => $total,
    //             'lolos_cv'         => $validated['lolos_cv'] ?? 0,
    //             'lolos_psikotes'   => $validated['lolos_psikotes'] ?? 0,
    //             'lolos_kompetensi' => $validated['lolos_kompetensi'] ?? 0,
    //             'lolos_hr'         => $validated['lolos_hr'] ?? 0,
    //             'lolos_user'       => $validated['lolos_user'] ?? 0,
    //             'notes'            => $validated['notes'] ?? null,
    //             'created_by'       => Auth::id(),
    //         ]
    //     );

    //     return response()->json(['success' => true, 'entry' => $entry]);
    // }
//    public function store(Request $request)
// {
//     if (Auth::user()->role !== 'admin') {
//         return response()->json(['message' => 'Hanya admin yang dapat mengubah data.'], 403);
//     }

//     // Tetap validasi untuk keamanan
//     $validated = $request->validate([
//         'posisi_id'        => 'required|integer|exists:posisi,id_posisi',
//         'date'             => 'required|date',
//         'total_pelamar'    => 'nullable|integer|min:0',
//         'lolos_cv'         => 'nullable|integer|min:0',
//         'lolos_psikotes'   => 'nullable|integer|min:0',
//         'lolos_kompetensi' => 'nullable|integer|min:0',
//         'lolos_hr'         => 'nullable|integer|min:0',
//         'lolos_user'       => 'nullable|integer|min:0',
//         'notes'            => 'nullable|string',
//     ]);

//     // Ambil atau buat baru
//     $entry = RekrutmenDaily::firstOrNew([
//         'posisi_id' => $request->posisi_id,
//         'date'      => $request->date
//     ]);

//     // DAFTAR KOLOM YANG BOLEH DIUPDATE
//     $allowedFields = [
//         'total_pelamar', 'lolos_cv', 'lolos_psikotes',
//         'lolos_kompetensi', 'lolos_hr', 'lolos_user', 'notes'
//     ];

//     // HANYA update kolom yang benar-benar ada di dalam request payload
//     foreach ($allowedFields as $field) {
//         if ($request->has($field)) {
//             $entry->$field = $request->input($field);
//         }
//     }

//     if (!$entry->exists) {
//         $entry->created_by = Auth::id();
//     }

//     $entry->save();

//     return response()->json(['success' => true, 'entry' => $entry]);
// }
    public function store(Request $request)
        {
            if (Auth::user()->role !== 'admin') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'posisi_id' => 'required|integer|exists:posisi,id_posisi',
                'date'      => 'required|date',
                'total_pelamar' => 'nullable|integer|min:0',
                'notes'     => 'nullable|string',
            ]);

            // Ambil data yang sudah ada
            $entry = RekrutmenDaily::firstOrNew([
                'posisi_id' => $request->posisi_id,
                'date'      => $request->date
            ]);

            // HANYA Izinkan update total_pelamar dan notes secara manual
            if ($request->has('total_pelamar')) {
                $entry->total_pelamar = $request->total_pelamar;
            }

            if ($request->has('notes')) {
                $entry->notes = $request->notes;
            }

            // Kolom lolos_cv, lolos_psikotes, dll TIDAK dimasukkan di sini
            // karena mereka dihandle oleh Observer dan Sync otomatis di index().

            if (!$entry->exists) {
                $entry->created_by = Auth::id();
            }

            $entry->save();

            return response()->json([
                'success' => true,
                'message' => 'Data manual berhasil disimpan. Data tahapan tetap tersinkron dengan kandidat.',
                'entry' => $entry
            ]);
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
