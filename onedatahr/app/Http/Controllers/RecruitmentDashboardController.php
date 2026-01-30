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
        // 1. Filter Posisi dan Tahun
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

        // --- HITUNGAN FUNNEL AKURAT ---
        // Logika: SATU KANDIDAT = SATU PERHITUNGAN berdasarkan status_akhir mereka (tahap terakhir)
        // Tidak berdasarkan tanggal agar tidak ada double counting

        // A. Total Pelamar (status_akhir != 'Tidak Lolos')
        $totalPelamar = (clone $query)
            ->where('status_akhir', '!=', 'Tidak Lolos')
            ->count();

        // B. Lolos CV (status_akhir mulai dari CV Lolos ke atas)
        $cvLolos = (clone $query)
            ->whereIn('status_akhir', [
                'CV Lolos',
                'Psikotes Lolos',
                'Tes Kompetensi Lolos',
                'Interview HR Lolos',
                'Interview User Lolos',
                'Diterima'
            ])
            ->count();

        // C. Lolos Psikotes (status_akhir dari Psikotes Lolos ke atas)
        $psikotesLolos = (clone $query)
            ->whereIn('status_akhir', [
                'Psikotes Lolos',
                'Tes Kompetensi Lolos',
                'Interview HR Lolos',
                'Interview User Lolos',
                'Diterima'
            ])
            ->count();

        // D. Lolos Kompetensi (status_akhir dari Tes Kompetensi Lolos ke atas)
        $kompetensiLolos = (clone $query)
            ->whereIn('status_akhir', [
                'Tes Kompetensi Lolos',
                'Interview HR Lolos',
                'Interview User Lolos',
                'Diterima'
            ])
            ->count();

        // E. Lolos Interview HR (status_akhir dari Interview HR Lolos ke atas)
        $hrLolos = (clone $query)
            ->whereIn('status_akhir', [
                'Interview HR Lolos',
                'Interview User Lolos',
                'Diterima'
            ])
            ->count();

        // F. Lolos User (status_akhir dari Interview User Lolos ke atas)
        $userLolos = (clone $query)
            ->whereIn('status_akhir', [
                'Interview User Lolos',
                'Diterima'
            ])
            ->count();

        // G. Hired / Diterima (status_akhir = 'Diterima')
        $totalHired = (clone $query)
            ->where('status_akhir', 'Diterima')
            ->count();

        // H. Ditolak (status_akhir = 'Tidak Lolos')
        $rejected = (clone $query)
            ->where('status_akhir', 'Tidak Lolos')
            ->count();

        // 3. Hitung conversion rate
        $conversionRates = [
            'cv' => $totalPelamar > 0 ? round(($cvLolos / $totalPelamar) * 100, 2) : 0,
            'psikotes' => $cvLolos > 0 ? round(($psikotesLolos / $cvLolos) * 100, 2) : 0,
            'kompetensi' => $psikotesLolos > 0 ? round(($kompetensiLolos / $psikotesLolos) * 100, 2) : 0,
            'hr' => $kompetensiLolos > 0 ? round(($hrLolos / $kompetensiLolos) * 100, 2) : 0,
            'user' => $hrLolos > 0 ? round(($userLolos / $hrLolos) * 100, 2) : 0,
            'hired' => $userLolos > 0 ? round(($totalHired / $userLolos) * 100, 2) : 0,
        ];

        // 4. Statistik per posisi (jika belum filter posisi)
        $statsByPosition = [];
        if (!$posisiId) {
            $statsByPosition = DB::table('kandidat')
                ->join('posisi', 'kandidat.posisi_id', 'posisi.id_posisi')
                ->whereYear('kandidat.tanggal_melamar', $year)
                ->select(
                    'posisi.id_posisi',
                    'posisi.nama_posisi',
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN status_akhir IN ("CV Lolos", "Psikotes Lolos", "Tes Kompetensi Lolos", "Interview HR Lolos", "Interview User Lolos", "Diterima") THEN 1 ELSE 0 END) as cv_lolos'),
                    DB::raw('SUM(CASE WHEN status_akhir IN ("Psikotes Lolos", "Tes Kompetensi Lolos", "Interview HR Lolos", "Interview User Lolos", "Diterima") THEN 1 ELSE 0 END) as psikotes_lolos'),
                    DB::raw('SUM(CASE WHEN status_akhir IN ("Tes Kompetensi Lolos", "Interview HR Lolos", "Interview User Lolos", "Diterima") THEN 1 ELSE 0 END) as kompetensi_lolos')
                )
                ->groupBy('posisi.id_posisi', 'posisi.nama_posisi')
                ->orderBy('total', 'desc')
                ->get();
        }

        // 5. Data bulanan
        $monthlyData = DB::table('kandidat')
            ->selectRaw('MONTH(tanggal_melamar) as month, COUNT(*) as total')
            ->whereYear('tanggal_melamar', $year);
        if ($posisiId) $monthlyData->where('posisi_id', $posisiId);
        $monthlyData = $monthlyData->groupBy('month')
            ->orderBy('month')
            ->get();

        // 6. Susun Data Funnel
        $funnelData = [
            'Total Pelamar'    => $totalPelamar,
            'Lolos CV'         => $cvLolos,
            'Lolos Psikotes'   => $psikotesLolos,
            'Lolos Kompetensi' => $kompetensiLolos,
            'Lolos Interview HR' => $hrLolos,
            'Lolos User'       => $userLolos,
            'Hired (Selesai)'  => $totalHired,
            'Ditolak'          => $rejected
        ];

        // 7. Kirim ke View
        return view('pages.rekrutmen.dashboard', [
            'posisi' => $posisi,
            'posisiId' => $posisiId,
            'funnelData' => $funnelData,
            'conversionRates' => $conversionRates,
            'statsByPosition' => $statsByPosition,
            'monthlyData' => $monthlyData,
            'availableYears' => $availableYears,
            'year' => $year,
            'totalPelamar' => $totalPelamar
        ]);
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
        // 1. Validasi Input
        $this->validateFilters($request);

        // 2. Base Query untuk Kandidat (dengan filter tanggal_melamar)
        $baseQuery = DB::table('kandidat');
        if ($request->filled('posisi_id')) {
            $baseQuery->where('posisi_id', $request->posisi_id);
        }
        if ($request->filled('from')) {
            $baseQuery->whereDate('tanggal_melamar', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $baseQuery->whereDate('tanggal_melamar', '<=', $request->to);
        }

        // --- A. TOTAL KANDIDAT ---
        $totalKandidat = (clone $baseQuery)->count();

        // --- B. PERHITUNGAN PER TAHAPAN BERDASARKAN status_akhir ---
        // SATU KANDIDAT = SATU PERHITUNGAN (tidak double counting)

        // 1. Lolos CV (status_akhir mulai dari CV Lolos ke atas)
        $totalCv = (clone $baseQuery)
            ->whereIn('status_akhir', [
                'CV Lolos',
                'Psikotes Lolos',
                'Tes Kompetensi Lolos',
                'Interview HR Lolos',
                'Interview User Lolos',
                'Diterima'
            ])
            ->count();

        // 2. Lolos Psikotes (status_akhir dari Psikotes Lolos ke atas)
        $totalPsi = (clone $baseQuery)
            ->whereIn('status_akhir', [
                'Psikotes Lolos',
                'Tes Kompetensi Lolos',
                'Interview HR Lolos',
                'Interview User Lolos',
                'Diterima'
            ])
            ->count();

        // 3. Lolos Kompetensi (status_akhir dari Tes Kompetensi Lolos ke atas)
        $totalKomp = (clone $baseQuery)
            ->whereIn('status_akhir', [
                'Tes Kompetensi Lolos',
                'Interview HR Lolos',
                'Interview User Lolos',
                'Diterima'
            ])
            ->count();

        // 4. Lolos Interview HR (status_akhir dari Interview HR Lolos ke atas)
        $totalHr = (clone $baseQuery)
            ->whereIn('status_akhir', [
                'Interview HR Lolos',
                'Interview User Lolos',
                'Diterima'
            ])
            ->count();

        // 5. Lolos Interview User (status_akhir dari Interview User Lolos ke atas)
        $totalUser = (clone $baseQuery)
            ->whereIn('status_akhir', [
                'Interview User Lolos',
                'Diterima'
            ])
            ->count();

        // --- C. PEMBERKASAN (HIRED) ---
        // Hired = status_akhir = 'Diterima' (atau bisa juga cek pemberkasan.selesai_recruitment)
        $totalPemberkasan = (clone $baseQuery)
            ->where('status_akhir', 'Diterima')
            ->count();

        // --- D. TOTAL POSISI ---
        $qPosisi = DB::table('posisi');
        if ($request->filled('posisi_id')) {
            $qPosisi->where('id_posisi', $request->posisi_id);
        }
        $totalPosisi = $qPosisi->count();

        // --- E. RETURN JSON ---
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
