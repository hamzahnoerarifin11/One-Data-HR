<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kandidat;
use App\Models\Posisi;
use App\Models\ProsesRekrutmen;
use App\Models\Pemberkasan;

class RecruitmentDashboardController extends Controller
{
    public function index(Request $request)
{
    // 1. Filter Posisi
    $posisiId = $request->input('posisi_id');
    $year     = $request->input('year', date('Y'));
    $posisi = \App\Models\Posisi::all();

    // Ambil tahun-tahun yang ada di database kandidat untuk opsi filter
    $availableYears = DB::table('kandidat')
        ->select(DB::raw('YEAR(tanggal_melamar) as year'))
        ->distinct()
        ->orderBy('year', 'desc')
        ->pluck('year');
        
    // Jika database kosong, sediakan minimal tahun sekarang
    if ($availableYears->isEmpty()) {
        $availableYears = [date('Y')];
    }

    // 2. Base Query
    $query = DB::table('kandidat');
    if ($posisiId) {
        $query->where('posisi_id', $posisiId);
    }
    // Terapkan Filter TAHUN (Berdasarkan tanggal melamar)
    if (!empty($year)) {
        $query->whereYear('tanggal_melamar', $year);
    }

    // --- HITUNGAN FUNNEL KUMULATIF ---
    // Konsep: Jika lolos tahap tinggi, otomatis lolos tahap bawahnya.

    // A. Total Pelamar
    $totalPelamar = (clone $query)->count();
    // dd([
    //     'Total Pelamar' => $totalPelamar,
    //     'Posisi ID' => $posisiId,
    //     'Contoh Data Kandidat' => DB::table('kandidat')->first()
    // ]);

    // B. Lolos User (Tahap Paling Tinggi di Data Anda)
    // Cek: Apakah tgl_lolos_user terisi?
    $userLolos = (clone $query)->whereNotNull('tgl_lolos_user')->count();

    // C. Lolos HR
    // Cek: tgl_lolos_hr terisi ATAU tgl_lolos_user terisi
    $hrLolos = (clone $query)->where(function($q) {
        $q->whereNotNull('tgl_lolos_hr')
          ->orWhereNotNull('tgl_lolos_user');
    })->count();

    // D. Lolos Kompetensi (Asumsi tahap sebelum HR)
    // Cek: tgl_lolos_kompetensi terisi ATAU sudah sampai HR/User
    $kompetensiLolos = (clone $query)->where(function($q) {
        $q->whereNotNull('tgl_lolos_kompetensi')
          ->orWhereNotNull('tgl_lolos_hr')
          ->orWhereNotNull('tgl_lolos_user');
    })->count();

    // E. Lolos Psikotes
    // Cek: tgl_lolos_psikotes terisi ATAU sudah sampai Kompetensi/HR/User
    $psikotesLolos = (clone $query)->where(function($q) {
        $q->whereNotNull('tgl_lolos_psikotes')
          ->orWhereNotNull('tgl_lolos_kompetensi')
          ->orWhereNotNull('tgl_lolos_hr')
          ->orWhereNotNull('tgl_lolos_user');
    })->count();

    // F. Lolos CV
    // Cek: tgl_lolos_cv terisi ATAU sudah sampai tahap apapun di atasnya
    $cvLolos = (clone $query)->where(function($q) {
        $q->whereNotNull('tgl_lolos_cv')
          ->orWhereNotNull('tgl_lolos_psikotes')
          ->orWhereNotNull('tgl_lolos_kompetensi')
          ->orWhereNotNull('tgl_lolos_hr')
          ->orWhereNotNull('tgl_lolos_user');
    })->count();

    // G. Hired / Selesai
    // Kita cek berdasarkan status_akhir yang mengandung kata "Hired" atau "Diterima"
    // ATAU cek relasi ke tabel pemberkasan (jika ada)
    $hired = DB::table('pemberkasan')
        ->join('kandidat', 'pemberkasan.kandidat_id', 'kandidat.id_kandidat')
        ->whereNotNull('selesai_recruitment');
    if ($posisiId) $hired->where('kandidat.posisi_id', $posisiId);
    $totalHired = $hired->count();

    // 3. Susun Data
    $data = [
        'Total Kandidat'    => $totalPelamar,
        'Lolos CV'          => $cvLolos,
        'Lolos Psikotes'    => $psikotesLolos,
        'Lolos Kompetensi'  => $kompetensiLolos,
        'Lolos Interview HR'=> $hrLolos,
        'Lolos User'        => $userLolos,
        'Hired (Selesai)'   => $totalHired
    ];

    // 4. Kirim ke View (Gunakan variabel $pelamar untuk perhitungan persen di blade)
    return view('pages.rekrutmen.dashboard', compact(
        'posisi', 
        'posisiId', 
        'data',
        'availableYears',
        'year'
    ))->with('pelamar', $totalPelamar);
}

    public function candidatesByPositionMonth(Request $request)
    {
        $this->validateFilters($request);
        $query = DB::table('kandidat')
            ->join('posisi', 'kandidat.posisi_id', 'posisi.id_posisi')
            ->select('posisi.id_posisi', 'posisi.nama_posisi', DB::raw('YEAR(kandidat.tanggal_melamar) as year'), DB::raw('MONTH(kandidat.tanggal_melamar) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('posisi.id_posisi', 'posisi.nama_posisi', 'year', 'month')
            ->orderBy('year', 'desc')->orderBy('month', 'desc');

        if ($request->filled('posisi_id')) {
            $query->where('posisi.id_posisi', $request->posisi_id);
        }
        // Support open-ended ranges and exact between
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('kandidat.tanggal_melamar', [$request->from, $request->to]);
        } else {
            if ($request->filled('from')) {
                $query->whereDate('kandidat.tanggal_melamar', '>=', $request->from);
            }
            if ($request->filled('to')) {
                $query->whereDate('kandidat.tanggal_melamar', '<=', $request->to);
            }
        }

        return response()->json($query->get());
    }

    public function exportCandidatesCsv(Request $request)
    {
        $this->validateFilters($request);
        $rows = DB::table('kandidat')
            ->join('posisi', 'kandidat.posisi_id', 'posisi.id_posisi')
            ->select('posisi.id_posisi', 'posisi.nama_posisi', DB::raw('YEAR(kandidat.tanggal_melamar) as year'), DB::raw('MONTH(kandidat.tanggal_melamar) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('posisi.id_posisi', 'posisi.nama_posisi', 'year', 'month')
            ->orderBy('year', 'desc')->orderBy('month', 'desc');

        if ($request->filled('posisi_id')) {
            $rows->where('posisi.id_posisi', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $rows->whereBetween('kandidat.tanggal_melamar', [$request->from, $request->to]);
        } else {
            if ($request->filled('from')) $rows->whereDate('kandidat.tanggal_melamar', '>=', $request->from);
            if ($request->filled('to')) $rows->whereDate('kandidat.tanggal_melamar', '<=', $request->to);
        }

        $data = $rows->get();

        $filename = 'candidates_' . now()->format('Ymd_His') . '.csv';
        $callback = function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['id_posisi', 'nama_posisi', 'year', 'month', 'total']);
            foreach ($data as $row) {
                fputcsv($handle, [(int)$row->id_posisi, $row->nama_posisi, $row->year, $row->month, $row->total]);
            }
            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportCvCsv(Request $request)
    {
        $rows = DB::table('proses_rekrutmen')
            ->join('kandidat', 'proses_rekrutmen.kandidat_id', 'kandidat.id_kandidat')
            ->join('posisi', 'kandidat.posisi_id', 'posisi.id_posisi')
            ->where('proses_rekrutmen.cv_lolos', 1)
            ->select('posisi.id_posisi', 'posisi.nama_posisi', DB::raw('YEAR(proses_rekrutmen.tanggal_cv) as year'), DB::raw('MONTH(proses_rekrutmen.tanggal_cv) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('posisi.id_posisi', 'posisi.nama_posisi', 'year', 'month')
            ->orderBy('year', 'desc')->orderBy('month', 'desc');

        if ($request->filled('posisi_id')) {
            $rows->where('posisi.id_posisi', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $rows->whereBetween('proses_rekrutmen.tanggal_cv', [$request->from, $request->to]);
        }

        $data = $rows->get();

        $filename = 'cv_passed_' . now()->format('Ymd_His') . '.csv';
        $callback = function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['id_posisi', 'nama_posisi', 'year', 'month', 'total']);
            foreach ($data as $row) {
                fputcsv($handle, [(int)$row->id_posisi, $row->nama_posisi, $row->year, $row->month, $row->total]);
            }
            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportPsikotesCsv(Request $request)
    {
        $rows = DB::table('proses_rekrutmen')
            ->join('kandidat', 'proses_rekrutmen.kandidat_id', 'kandidat.id_kandidat')
            ->join('posisi', 'kandidat.posisi_id', 'posisi.id_posisi')
            ->where('proses_rekrutmen.psikotes_lolos', 1)
            ->select('posisi.id_posisi', 'posisi.nama_posisi', DB::raw('COUNT(*) as total'))
            ->groupBy('posisi.id_posisi', 'posisi.nama_posisi');

        if ($request->filled('posisi_id')) {
            $rows->where('posisi.id_posisi', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $rows->whereBetween('proses_rekrutmen.tanggal_psikotes', [$request->from, $request->to]);
        }

        $data = $rows->get();

        $filename = 'psikotes_passed_' . now()->format('Ymd_His') . '.csv';
        $callback = function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['id_posisi', 'nama_posisi', 'total']);
            foreach ($data as $row) {
                fputcsv($handle, [(int)$row->id_posisi, $row->nama_posisi, $row->total]);
            }
            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportProgressCsv(Request $request)
    {
        $query = DB::table('kandidat')
            ->leftJoin('proses_rekrutmen', 'kandidat.id_kandidat', 'proses_rekrutmen.kandidat_id')
            ->join('posisi', 'kandidat.posisi_id', 'posisi.id_posisi')
            ->select(
                'posisi.id_posisi',
                'posisi.nama_posisi',
                DB::raw('COUNT(kandidat.id_kandidat) as total'),
                DB::raw('SUM(COALESCE(proses_rekrutmen.cv_lolos,0)) as cv_lolos'),
                DB::raw('SUM(COALESCE(proses_rekrutmen.psikotes_lolos,0)) as psikotes'),
                DB::raw('SUM(COALESCE(proses_rekrutmen.tes_kompetensi_lolos,0)) as kompetensi'),
                DB::raw('SUM(COALESCE(proses_rekrutmen.interview_hr_lolos,0)) as hr'),
                DB::raw('SUM(COALESCE(proses_rekrutmen.interview_user_lolos,0)) as user')
            )
            ->groupBy('posisi.id_posisi', 'posisi.nama_posisi');

        if ($request->filled('posisi_id')) {
            $query->where('posisi.id_posisi', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            // filter by proses_rekrutmen.tanggal_cv because grouping uses that column
            $query->whereBetween('proses_rekrutmen.tanggal_cv', [$request->from, $request->to]);
        } else {
            if ($request->filled('from')) $query->whereDate('proses_rekrutmen.tanggal_cv', '>=', $request->from);
            if ($request->filled('to')) $query->whereDate('proses_rekrutmen.tanggal_cv', '<=', $request->to);
        }

        $data = $query->get()->map(function ($row) {
            $row->percent_cv = $row->total ? round(($row->cv_lolos / $row->total) * 100, 2) : 0;
            $row->percent_psikotes = $row->total ? round(($row->psikotes / $row->total) * 100, 2) : 0;
            $row->percent_kompetensi = $row->total ? round(($row->kompetensi / $row->total) * 100, 2) : 0;
            $row->percent_hr = $row->total ? round(($row->hr / $row->total) * 100, 2) : 0;
            $row->percent_user = $row->total ? round(($row->user / $row->total) * 100, 2) : 0;
            return $row;
        });

        $filename = 'progress_' . now()->format('Ymd_His') . '.csv';
        $callback = function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['id_posisi', 'nama_posisi', 'total', 'cv_lolos', 'psikotes', 'kompetensi', 'hr', 'user', 'percent_cv', 'percent_psikotes', 'percent_kompetensi', 'percent_hr', 'percent_user']);
            foreach ($data as $row) {
                fputcsv($handle, [
                    (int)$row->id_posisi,
                    $row->nama_posisi,
                    $row->total,
                    $row->cv_lolos,
                    $row->psikotes,
                    $row->kompetensi,
                    $row->hr,
                    $row->user,
                    $row->percent_cv,
                    $row->percent_psikotes,
                    $row->percent_kompetensi,
                    $row->percent_hr,
                    $row->percent_user,
                ]);
            }
            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
    }

    public function calendarPage()
    {
        $posisis = Posisi::all();
        return view('pages.rekrutmen.calendar', compact('posisis'));
    }

    public function cvPassedByPositionMonth(Request $request)
    {
        $this->validateFilters($request);
        $query = DB::table('proses_rekrutmen')
            ->join('kandidat', 'proses_rekrutmen.kandidat_id', 'kandidat.id_kandidat')
            ->join('posisi', 'kandidat.posisi_id', 'posisi.id_posisi')
            ->where('proses_rekrutmen.cv_lolos', 1)
            ->select('posisi.id_posisi', 'posisi.nama_posisi', DB::raw('YEAR(proses_rekrutmen.tanggal_cv) as year'), DB::raw('MONTH(proses_rekrutmen.tanggal_cv) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('posisi.id_posisi', 'posisi.nama_posisi', 'year', 'month')
            ->orderBy('year', 'desc')->orderBy('month', 'desc');

        if ($request->filled('posisi_id')) {
            $query->where('posisi.id_posisi', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('proses_rekrutmen.tanggal_cv', [$request->from, $request->to]);
        } else {
            if ($request->filled('from')) $query->whereDate('proses_rekrutmen.tanggal_cv', '>=', $request->from);
            if ($request->filled('to')) $query->whereDate('proses_rekrutmen.tanggal_cv', '<=', $request->to);
        }

        return response()->json($query->get());
    }

    public function psikotesPassedByPosition(Request $request)
    {
        $this->validateFilters($request);
        $query = DB::table('proses_rekrutmen')
            ->join('kandidat', 'proses_rekrutmen.kandidat_id', 'kandidat.id_kandidat')
            ->join('posisi', 'kandidat.posisi_id', 'posisi.id_posisi')
            ->where('proses_rekrutmen.psikotes_lolos', 1)
            ->select('posisi.id_posisi', 'posisi.nama_posisi', DB::raw('COUNT(*) as total'))
            ->groupBy('posisi.id_posisi', 'posisi.nama_posisi');

        if ($request->filled('posisi_id')) {
            $query->where('posisi.id_posisi', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('proses_rekrutmen.tanggal_psikotes', [$request->from, $request->to]);
        } else {
            if ($request->filled('from')) $query->whereDate('proses_rekrutmen.tanggal_psikotes', '>=', $request->from);
            if ($request->filled('to')) $query->whereDate('proses_rekrutmen.tanggal_psikotes', '<=', $request->to);
        }

        return response()->json($query->get());
    }

    public function kompetensiPassedByPosition(Request $request)
    {
        $this->validateFilters($request);
        $query = DB::table('proses_rekrutmen')
            ->join('kandidat', 'proses_rekrutmen.kandidat_id', 'kandidat.id_kandidat')
            ->join('posisi', 'kandidat.posisi_id', 'posisi.id_posisi')
            ->where('proses_rekrutmen.tes_kompetensi_lolos', 1)
            ->select('posisi.id_posisi', 'posisi.nama_posisi', DB::raw('COUNT(*) as total'))
            ->groupBy('posisi.id_posisi', 'posisi.nama_posisi');

        if ($request->filled('posisi_id')) {
            $query->where('posisi.id_posisi', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('proses_rekrutmen.tanggal_tes_kompetensi', [$request->from, $request->to]);
        } else {
            if ($request->filled('from')) $query->whereDate('proses_rekrutmen.tanggal_tes_kompetensi', '>=', $request->from);
            if ($request->filled('to')) $query->whereDate('proses_rekrutmen.tanggal_tes_kompetensi', '<=', $request->to);
        }

        return response()->json($query->get());
    }

    public function interviewHrPassedByPositionMonth(Request $request)
    {
        $this->validateFilters($request);
        $query = DB::table('proses_rekrutmen')
            ->join('kandidat', 'proses_rekrutmen.kandidat_id', 'kandidat.id_kandidat')
            ->join('posisi', 'kandidat.posisi_id', 'posisi.id_posisi')
            ->where('proses_rekrutmen.interview_hr_lolos', 1)
            ->select('posisi.id_posisi', 'posisi.nama_posisi', DB::raw('YEAR(proses_rekrutmen.tanggal_interview_hr) as year'), DB::raw('MONTH(proses_rekrutmen.tanggal_interview_hr) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('posisi.id_posisi', 'posisi.nama_posisi', 'year', 'month')
            ->orderBy('year', 'desc')->orderBy('month', 'desc');

        if ($request->filled('posisi_id')) {
            $query->where('posisi.id_posisi', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('proses_rekrutmen.tanggal_interview_hr', [$request->from, $request->to]);
        } else {
            if ($request->filled('from')) $query->whereDate('proses_rekrutmen.tanggal_interview_hr', '>=', $request->from);
            if ($request->filled('to')) $query->whereDate('proses_rekrutmen.tanggal_interview_hr', '<=', $request->to);
        }

        return response()->json($query->get());
    }

    public function interviewUserPassedByPositionMonth(Request $request)
    {
        $this->validateFilters($request);
        $query = DB::table('proses_rekrutmen')
            ->join('kandidat', 'proses_rekrutmen.kandidat_id', 'kandidat.id_kandidat')
            ->join('posisi', 'kandidat.posisi_id', 'posisi.id_posisi')
            ->where('proses_rekrutmen.interview_user_lolos', 1)
            ->select('posisi.id_posisi', 'posisi.nama_posisi', DB::raw('YEAR(proses_rekrutmen.tanggal_interview_user) as year'), DB::raw('MONTH(proses_rekrutmen.tanggal_interview_user) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('posisi.id_posisi', 'posisi.nama_posisi', 'year', 'month')
            ->orderBy('year', 'desc')->orderBy('month', 'desc');

        if ($request->filled('posisi_id')) {
            $query->where('posisi.id_posisi', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('proses_rekrutmen.tanggal_interview_user', [$request->from, $request->to]);
        } else {
            if ($request->filled('from')) $query->whereDate('proses_rekrutmen.tanggal_interview_user', '>=', $request->from);
            if ($request->filled('to')) $query->whereDate('proses_rekrutmen.tanggal_interview_user', '<=', $request->to);
        }

        return response()->json($query->get());
    }

    public function recruitmentProgressByPosition(Request $request)
    {
        $this->validateFilters($request);
        $query = DB::table('kandidat')
            ->leftJoin('proses_rekrutmen', 'kandidat.id_kandidat', 'proses_rekrutmen.kandidat_id')
            ->join('posisi', 'kandidat.posisi_id', 'posisi.id_posisi')
            ->select(
                'posisi.id_posisi',
                'posisi.nama_posisi',
                DB::raw('COUNT(kandidat.id_kandidat) as total'),
                DB::raw('SUM(COALESCE(proses_rekrutmen.cv_lolos,0)) as cv_lolos'),
                DB::raw('SUM(COALESCE(proses_rekrutmen.psikotes_lolos,0)) as psikotes'),
                DB::raw('SUM(COALESCE(proses_rekrutmen.tes_kompetensi_lolos,0)) as kompetensi'),
                DB::raw('SUM(COALESCE(proses_rekrutmen.interview_hr_lolos,0)) as hr'),
                DB::raw('SUM(COALESCE(proses_rekrutmen.interview_user_lolos,0)) as user')
            )
            ->groupBy('posisi.id_posisi', 'posisi.nama_posisi');

        if ($request->filled('posisi_id')) {
            $query->where('posisi.id_posisi', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('kandidat.tanggal_melamar', [$request->from, $request->to]);
        } else {
            if ($request->filled('from')) $query->whereDate('kandidat.tanggal_melamar', '>=', $request->from);
            if ($request->filled('to')) $query->whereDate('kandidat.tanggal_melamar', '<=', $request->to);
        }

        $data = $query->get()->map(function ($row) {
            $row->percent_cv = $row->total ? round(($row->cv_lolos / $row->total) * 100, 2) : 0;
            $row->percent_psikotes = $row->total ? round(($row->psikotes / $row->total) * 100, 2) : 0;
            $row->percent_kompetensi = $row->total ? round(($row->kompetensi / $row->total) * 100, 2) : 0;
            $row->percent_hr = $row->total ? round(($row->hr / $row->total) * 100, 2) : 0;
            $row->percent_user = $row->total ? round(($row->user / $row->total) * 100, 2) : 0;
            return $row;
        });

        return response()->json($data);
    }

    // simple page views for per-stage metrics
    public function cvPage()
    {
        $posisis = Posisi::orderBy('nama_posisi')->get();
        return view('pages.rekrutmen.metrics.cv', compact('posisi'));
    }

    public function psikotesPage()
    {
        $posisis = Posisi::orderBy('nama_posisi')->get();
        return view('pages.rekrutmen.metrics.psikotes', compact('posisi'));
    }

    public function kompetensiPage()
    {
        $posisis = Posisi::orderBy('nama_posisi')->get();
        return view('pages.rekrutmen.metrics.kompetensi', compact('posisi'));
    }

    public function interviewHrPage()
    {
        $posisis = Posisi::orderBy('nama_posisi')->get();
        return view('pages.rekrutmen.metrics.interview_hr', compact('posisi'));
    }

    public function interviewUserPage()
    {
        $posisis = Posisi::orderBy('nama_posisi')->get();
        return view('pages.rekrutmen.metrics.interview_user', compact('posisi'));
    }

    public function pemberkasanProgress(Request $request)
    {
        $this->validateFilters($request);
        $query = DB::table('pemberkasan')
            ->join('kandidat', 'pemberkasan.kandidat_id', 'kandidat.id_kandidat')
            ->join('posisi', 'kandidat.posisi_id', 'posisi.id_posisi')
            ->select(
                'posisi.id_posisi',
                'posisi.nama_posisi',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN pemberkasan.selesai_recruitment IS NOT NULL THEN 1 ELSE 0 END) as done_recruitment'),
                DB::raw('SUM(CASE WHEN pemberkasan.selesai_skgk_finance IS NOT NULL THEN 1 ELSE 0 END) as done_skgk_finance'),
                DB::raw('SUM(CASE WHEN pemberkasan.selesai_ttd_manager_hrd IS NOT NULL THEN 1 ELSE 0 END) as done_ttd_manager_hrd'),
                DB::raw('SUM(CASE WHEN pemberkasan.selesai_ttd_user IS NOT NULL THEN 1 ELSE 0 END) as done_ttd_user'),
                DB::raw('SUM(CASE WHEN pemberkasan.selesai_ttd_direktur IS NOT NULL THEN 1 ELSE 0 END) as done_ttd_direktur')
            )
            ->groupBy('posisi.id_posisi', 'posisi.nama_posisi');

        if ($request->filled('posisi_id')) {
            $query->where('posisi.id_posisi', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('kandidat.tanggal_melamar', [$request->from, $request->to]);
        } else {
            if ($request->filled('from')) $query->whereDate('kandidat.tanggal_melamar', '>=', $request->from);
            if ($request->filled('to')) $query->whereDate('kandidat.tanggal_melamar', '<=', $request->to);
        }

        $data = $query->get()->map(function ($row) {
            $row->percent_done_recruitment = $row->total ? round(($row->done_recruitment / $row->total) * 100, 2) : 0;
            return $row;
        });

        return response()->json($data);
    }

    public function pemberkasanPage()
    {
        return view('pages.rekrutmen.pemberkasan.monitor');
    }

    public function exportKompetensiCsv(Request $request)
    {
        $rows = DB::table('proses_rekrutmen')
            ->join('kandidat', 'proses_rekrutmen.kandidat_id', 'kandidat.id_kandidat')
            ->join('posisi', 'kandidat.posisi_id', 'posisi.id_posisi')
            ->where('proses_rekrutmen.tes_kompetensi_lolos', 1)
            ->select('posisi.id_posisi', 'posisi.nama_posisi', DB::raw('COUNT(*) as total'))
            ->groupBy('posisi.id_posisi', 'posisi.nama_posisi');

        if ($request->filled('posisi_id')) {
            $rows->where('posisi.id_posisi', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $rows->whereBetween('proses_rekrutmen.tanggal_tes_kompetensi', [$request->from, $request->to]);
        }

        $data = $rows->get();

        $filename = 'kompetensi_passed_' . now()->format('Ymd_His') . '.csv';
        $callback = function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['id_posisi', 'nama_posisi', 'total']);
            foreach ($data as $row) {
                fputcsv($handle, [(int)$row->id_posisi, $row->nama_posisi, $row->total]);
            }
            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportInterviewHrCsv(Request $request)
    {
        $rows = DB::table('proses_rekrutmen')
            ->join('kandidat', 'proses_rekrutmen.kandidat_id', 'kandidat.id_kandidat')
            ->join('posisi', 'kandidat.posisi_id', 'posisi.id_posisi')
            ->where('proses_rekrutmen.interview_hr_lolos', 1)
            ->select('posisi.id_posisi', 'posisi.nama_posisi', DB::raw('YEAR(proses_rekrutmen.tanggal_interview_hr) as year'), DB::raw('MONTH(proses_rekrutmen.tanggal_interview_hr) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('posisi.id_posisi', 'posisi.nama_posisi', 'year', 'month')
            ->orderBy('year', 'desc')->orderBy('month', 'desc');

        if ($request->filled('posisi_id')) {
            $rows->where('posisi.id_posisi', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $rows->whereBetween('proses_rekrutmen.tanggal_interview_hr', [$request->from, $request->to]);
        }

        $data = $rows->get();

        $filename = 'interview_hr_passed_' . now()->format('Ymd_His') . '.csv';
        $callback = function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['id_posisi', 'nama_posisi', 'year', 'month', 'total']);
            foreach ($data as $row) {
                fputcsv($handle, [(int)$row->id_posisi, $row->nama_posisi, $row->year, $row->month, $row->total]);
            }
            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportInterviewUserCsv(Request $request)
    {
        $rows = DB::table('proses_rekrutmen')
            ->join('kandidat', 'proses_rekrutmen.kandidat_id', 'kandidat.id_kandidat')
            ->join('posisi', 'kandidat.posisi_id', 'posisi.id_posisi')
            ->where('proses_rekrutmen.interview_user_lolos', 1)
            ->select('posisi.id_posisi', 'posisi.nama_posisi', DB::raw('YEAR(proses_rekrutmen.tanggal_interview_user) as year'), DB::raw('MONTH(proses_rekrutmen.tanggal_interview_user) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('posisi.id_posisi', 'posisi.nama_posisi', 'year', 'month')
            ->orderBy('year', 'desc')->orderBy('month', 'desc');

        if ($request->filled('posisi_id')) {
            $rows->where('posisi.id_posisi', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $rows->whereBetween('proses_rekrutmen.tanggal_interview_user', [$request->from, $request->to]);
        }

        $data = $rows->get();

        $filename = 'interview_user_passed_' . now()->format('Ymd_His') . '.csv';
        $callback = function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['id_posisi', 'nama_posisi', 'year', 'month', 'total']);
            foreach ($data as $row) {
                fputcsv($handle, [(int)$row->id_posisi, $row->nama_posisi, $row->year, $row->month, $row->total]);
            }
            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
    }


    /**
     * Validate common filters used across metrics
     */
    private function validateFilters(Request $request)
    {
        $request->validate([
            'posisi_id' => 'nullable|integer|exists:posisi,id_posisi',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);

        if ($request->filled('from') && $request->filled('to')) {
            if (strtotime($request->from) > strtotime($request->to)) {
                abort(response()->json(['message' => 'Invalid date range: from must be before to'], 422));
            }
        }
    }

    public function dashboardStats(Request $request)
    {
        // 1. Validasi Input (Reuse fungsi private yang sudah ada)
        $this->validateFilters($request);

        // 2. Helper Closure untuk Filter Posisi (agar tidak berulang)
        // Kita gunakan join ke tabel kandidat karena posisi_id ada di sana
        $applyPosisiFilter = function ($query) use ($request) {
            if ($request->filled('posisi_id')) {
                $query->where('kandidat.posisi_id', $request->posisi_id);
            }
        };

        // --- A. TOTAL KANDIDAT ---
        // Logic: Hitung tabel kandidat, filter by tanggal_melamar
        $qKandidat = DB::table('kandidat');
        if ($request->filled('posisi_id')) {
            $qKandidat->where('posisi_id', $request->posisi_id);
        }

        // HANYA filter tanggal jika user MEMANG memilih tanggal
        if ($request->filled('from')) {
            $qKandidat->whereDate('tanggal_melamar', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $qKandidat->whereDate('tanggal_melamar', '<=', $request->to);
        }

        $totalKandidat = $qKandidat->count();


        // --- B. DATA DARI TABEL PROSES REKRUTMEN ---
        // Helper untuk query proses rekrutmen dasar
        $baseProses = DB::table('proses_rekrutmen')
            ->join('kandidat', 'proses_rekrutmen.kandidat_id', 'kandidat.id_kandidat');

        // 1. CV Lolos
        // Logic: copy dari cvPassedByPositionMonth
        $qCv = clone $baseProses;
        $qCv->where('proses_rekrutmen.cv_lolos', 1);
        $applyPosisiFilter($qCv);
        if ($request->filled('from')) $qCv->whereDate('proses_rekrutmen.tanggal_cv', '>=', $request->from);
        if ($request->filled('to')) $qCv->whereDate('proses_rekrutmen.tanggal_cv', '<=', $request->to);
        $totalCv = $qCv->count();

        // 2. Psikotes Lolos
        // Logic: copy dari psikotesPassedByPosition
        $qPsi = clone $baseProses;
        $qPsi->where('proses_rekrutmen.psikotes_lolos', 1);
        $applyPosisiFilter($qPsi);
        if ($request->filled('from')) $qPsi->whereDate('proses_rekrutmen.tanggal_psikotes', '>=', $request->from);
        if ($request->filled('to')) $qPsi->whereDate('proses_rekrutmen.tanggal_psikotes', '<=', $request->to);
        $totalPsi = $qPsi->count();

        // 3. Kompetensi Lolos
        // Logic: copy dari kompetensiPassedByPosition
        $qKomp = clone $baseProses;
        $qKomp->where('proses_rekrutmen.tes_kompetensi_lolos', 1);
        $applyPosisiFilter($qKomp);
        if ($request->filled('from')) $qKomp->whereDate('proses_rekrutmen.tanggal_tes_kompetensi', '>=', $request->from);
        if ($request->filled('to')) $qKomp->whereDate('proses_rekrutmen.tanggal_tes_kompetensi', '<=', $request->to);
        $totalKomp = $qKomp->count();

        // 4. Interview HR Lolos
        // Logic: copy dari interviewHrPassedByPositionMonth
        $qHr = clone $baseProses;
        $qHr->where('proses_rekrutmen.interview_hr_lolos', 1);
        $applyPosisiFilter($qHr);
        if ($request->filled('from')) $qHr->whereDate('proses_rekrutmen.tanggal_interview_hr', '>=', $request->from);
        if ($request->filled('to')) $qHr->whereDate('proses_rekrutmen.tanggal_interview_hr', '<=', $request->to);
        $totalHr = $qHr->count();

        // 5. Interview User Lolos
        // Logic: copy dari interviewUserPassedByPositionMonth
        $qUser = clone $baseProses;
        $qUser->where('proses_rekrutmen.interview_user_lolos', 1);
        $applyPosisiFilter($qUser);
        if ($request->filled('from')) $qUser->whereDate('proses_rekrutmen.tanggal_interview_user', '>=', $request->from);
        if ($request->filled('to')) $qUser->whereDate('proses_rekrutmen.tanggal_interview_user', '<=', $request->to);
        $totalUser = $qUser->count();

        // 6. TOTAL POSISI (optional, jika diperlukan di dashboard)
        $qPosisi = DB::table('posisi');
    
        if ($request->filled('posisi_id')) {
            $qPosisi->where('id_posisi', $request->posisi_id);
        }
        
        // Opsional: Jika ingin menghitung hanya yang statusnya 'Aktif'
        // $qPosisi->where('status', 'Aktif'); 

        $totalPosisi = $qPosisi->count();



        // --- C. PEMBERKASAN (HIRED) ---
        // Logic: copy dari pemberkasanProgress (menggunakan filter tanggal_melamar, bukan tanggal selesai)
        $qPemberkasan = DB::table('pemberkasan')
            ->join('kandidat', 'pemberkasan.kandidat_id', 'kandidat.id_kandidat');
        
        $applyPosisiFilter($qPemberkasan);
        
        // Filter tanggal mengikuti logic pemberkasanProgress Anda (filter tanggal_melamar)
        if ($request->filled('from')) $qPemberkasan->whereDate('kandidat.tanggal_melamar', '>=', $request->from);
        if ($request->filled('to')) $qPemberkasan->whereDate('kandidat.tanggal_melamar', '<=', $request->to);
        
        // Hitung yang selesai_recruitment-nya tidak NULL
        $totalPemberkasan = $qPemberkasan->whereNotNull('pemberkasan.selesai_recruitment')->count();


        // --- D. RETURN JSON ---
        return response()->json([
            'success' => true,
            'data' => [
                'total_posisi' => $totalPosisi,
                'total_kandidat' => $totalKandidat,
                'cv_lolos' => $totalCv,
                'psikotes_lolos' => $totalPsi,
                'kompetensi_lolos' => $totalKomp,
                'interview_hr' => $totalHr,
                'interview_user' => $totalUser,
                'pemberkasan' => $totalPemberkasan
            ]
        ]);
    }
}
