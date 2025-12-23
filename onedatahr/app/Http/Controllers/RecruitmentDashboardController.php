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
    public function index()
    {
        $posisis = Posisi::all();
        return view('pages.rekrutmen.dashboard', compact('posisis'));
    }

    public function candidatesByPositionMonth(Request $request)
    {
        $this->validateFilters($request);
        $query = DB::table('kandidat')
            ->join('posisi','kandidat.posisi_id','posisi.id_posisi')
            ->select('posisi.id_posisi','posisi.nama_posisi', DB::raw('YEAR(kandidat.tanggal_melamar) as year'), DB::raw('MONTH(kandidat.tanggal_melamar) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('posisi.id_posisi','posisi.nama_posisi','year','month')
            ->orderBy('year','desc')->orderBy('month','desc');

        if ($request->filled('posisi_id')) {
            $query->where('posisi.id_posisi', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('kandidat.tanggal_melamar', [$request->from, $request->to]);
        }

        return response()->json($query->get());
    }

    public function exportCandidatesCsv(Request $request)
    {
        $this->validateFilters($request);
        $rows = DB::table('kandidat')
            ->join('posisi','kandidat.posisi_id','posisi.id_posisi')
            ->select('posisi.id_posisi','posisi.nama_posisi', DB::raw('YEAR(kandidat.tanggal_melamar) as year'), DB::raw('MONTH(kandidat.tanggal_melamar) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('posisi.id_posisi','posisi.nama_posisi','year','month')
            ->orderBy('year','desc')->orderBy('month','desc');

        if ($request->filled('posisi_id')) {
            $rows->where('posisi.id_posisi', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $rows->whereBetween('kandidat.tanggal_melamar', [$request->from, $request->to]);
        }

        $data = $rows->get();

        $filename = 'candidates_'.now()->format('Ymd_His').'.csv';
        $callback = function() use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['id_posisi','nama_posisi','year','month','total']);
            foreach($data as $row){
                fputcsv($handle, [(int)$row->id_posisi, $row->nama_posisi, $row->year, $row->month, $row->total]);
            }
            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportCvCsv(Request $request)
    {
        $rows = DB::table('proses_rekrutmen')
            ->join('kandidat','proses_rekrutmen.kandidat_id','kandidat.id_kandidat')
            ->join('posisi','kandidat.posisi_id','posisi.id_posisi')
            ->where('proses_rekrutmen.cv_lolos', 1)
            ->select('posisi.id_posisi','posisi.nama_posisi', DB::raw('YEAR(proses_rekrutmen.tanggal_cv) as year'), DB::raw('MONTH(proses_rekrutmen.tanggal_cv) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('posisi.id_posisi','posisi.nama_posisi','year','month')
            ->orderBy('year','desc')->orderBy('month','desc');

        if ($request->filled('posisi_id')) {
            $rows->where('posisi.id_posisi', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $rows->whereBetween('proses_rekrutmen.tanggal_cv', [$request->from, $request->to]);
        }

        $data = $rows->get();

        $filename = 'cv_passed_'.now()->format('Ymd_His').'.csv';
        $callback = function() use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['id_posisi','nama_posisi','year','month','total']);
            foreach($data as $row){
                fputcsv($handle, [(int)$row->id_posisi, $row->nama_posisi, $row->year, $row->month, $row->total]);
            }
            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportPsikotesCsv(Request $request)
    {
        $rows = DB::table('proses_rekrutmen')
            ->join('kandidat','proses_rekrutmen.kandidat_id','kandidat.id_kandidat')
            ->join('posisi','kandidat.posisi_id','posisi.id_posisi')
            ->where('proses_rekrutmen.psikotes_lolos', 1)
            ->select('posisi.id_posisi','posisi.nama_posisi', DB::raw('COUNT(*) as total'))
            ->groupBy('posisi.id_posisi','posisi.nama_posisi');

        if ($request->filled('posisi_id')) {
            $rows->where('posisi.id_posisi', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $rows->whereBetween('proses_rekrutmen.tanggal_psikotes', [$request->from, $request->to]);
        }

        $data = $rows->get();

        $filename = 'psikotes_passed_'.now()->format('Ymd_His').'.csv';
        $callback = function() use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['id_posisi','nama_posisi','total']);
            foreach($data as $row){
                fputcsv($handle, [(int)$row->id_posisi, $row->nama_posisi, $row->total]);
            }
            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportProgressCsv(Request $request)
    {
        $query = DB::table('kandidat')
            ->leftJoin('proses_rekrutmen','kandidat.id_kandidat','proses_rekrutmen.kandidat_id')
            ->join('posisi','kandidat.posisi_id','posisi.id_posisi')
            ->select('posisi.id_posisi','posisi.nama_posisi',
                DB::raw('COUNT(kandidat.id_kandidat) as total'),
                DB::raw('SUM(COALESCE(proses_rekrutmen.cv_lolos,0)) as cv_lolos'),
                DB::raw('SUM(COALESCE(proses_rekrutmen.psikotes_lolos,0)) as psikotes'),
                DB::raw('SUM(COALESCE(proses_rekrutmen.tes_kompetensi_lolos,0)) as kompetensi'),
                DB::raw('SUM(COALESCE(proses_rekrutmen.interview_hr_lolos,0)) as hr'),
                DB::raw('SUM(COALESCE(proses_rekrutmen.interview_user_lolos,0)) as user')
            )
            ->groupBy('posisi.id_posisi','posisi.nama_posisi');

        if ($request->filled('posisi_id')) {
            $query->where('posisi.id_posisi', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('kandidat.tanggal_melamar', [$request->from, $request->to]);
        }

        $data = $query->get()->map(function($row){
            $row->percent_cv = $row->total ? round(($row->cv_lolos / $row->total) * 100,2) : 0;
            $row->percent_psikotes = $row->total ? round(($row->psikotes / $row->total) * 100,2) : 0;
            $row->percent_kompetensi = $row->total ? round(($row->kompetensi / $row->total) * 100,2) : 0;
            $row->percent_hr = $row->total ? round(($row->hr / $row->total) * 100,2) : 0;
            $row->percent_user = $row->total ? round(($row->user / $row->total) * 100,2) : 0;
            return $row;
        });

        $filename = 'progress_'.now()->format('Ymd_His').'.csv';
        $callback = function() use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['id_posisi','nama_posisi','total','cv_lolos','psikotes','kompetensi','hr','user','percent_cv','percent_psikotes','percent_kompetensi','percent_hr','percent_user']);
            foreach($data as $row){
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
            ->join('kandidat','proses_rekrutmen.kandidat_id','kandidat.id_kandidat')
            ->join('posisi','kandidat.posisi_id','posisi.id_posisi')
            ->where('proses_rekrutmen.cv_lolos', 1)
            ->select('posisi.id_posisi','posisi.nama_posisi', DB::raw('YEAR(proses_rekrutmen.tanggal_cv) as year'), DB::raw('MONTH(proses_rekrutmen.tanggal_cv) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('posisi.id_posisi','posisi.nama_posisi','year','month')
            ->orderBy('year','desc')->orderBy('month','desc');

        if ($request->filled('posisi_id')) {
            $query->where('posisi.id_posisi', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('proses_rekrutmen.tanggal_cv', [$request->from, $request->to]);
        }

        return response()->json($query->get());
    }

    public function psikotesPassedByPosition(Request $request)
    {
        $this->validateFilters($request);
        $query = DB::table('proses_rekrutmen')
            ->join('kandidat','proses_rekrutmen.kandidat_id','kandidat.id_kandidat')
            ->join('posisi','kandidat.posisi_id','posisi.id_posisi')
            ->where('proses_rekrutmen.psikotes_lolos', 1)
            ->select('posisi.id_posisi','posisi.nama_posisi', DB::raw('COUNT(*) as total'))
            ->groupBy('posisi.id_posisi','posisi.nama_posisi');

        if ($request->filled('posisi_id')) {
            $query->where('posisi.id_posisi', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('proses_rekrutmen.tanggal_psikotes', [$request->from, $request->to]);
        }

        return response()->json($query->get());
    }

    public function kompetensiPassedByPosition(Request $request)
    {
        $this->validateFilters($request);
        $query = DB::table('proses_rekrutmen')
            ->join('kandidat','proses_rekrutmen.kandidat_id','kandidat.id_kandidat')
            ->join('posisi','kandidat.posisi_id','posisi.id_posisi')
            ->where('proses_rekrutmen.tes_kompetensi_lolos', 1)
            ->select('posisi.id_posisi','posisi.nama_posisi', DB::raw('COUNT(*) as total'))
            ->groupBy('posisi.id_posisi','posisi.nama_posisi');

        if ($request->filled('posisi_id')) {
            $query->where('posisi.id_posisi', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('proses_rekrutmen.tanggal_tes_kompetensi', [$request->from, $request->to]);
        }

        return response()->json($query->get());
    }

    public function interviewHrPassedByPositionMonth(Request $request)
    {
        $this->validateFilters($request);
        $query = DB::table('proses_rekrutmen')
            ->join('kandidat','proses_rekrutmen.kandidat_id','kandidat.id_kandidat')
            ->join('posisi','kandidat.posisi_id','posisi.id_posisi')
            ->where('proses_rekrutmen.interview_hr_lolos', 1)
            ->select('posisi.id_posisi','posisi.nama_posisi', DB::raw('YEAR(proses_rekrutmen.tanggal_interview_hr) as year'), DB::raw('MONTH(proses_rekrutmen.tanggal_interview_hr) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('posisi.id_posisi','posisi.nama_posisi','year','month')
            ->orderBy('year','desc')->orderBy('month','desc');

        if ($request->filled('posisi_id')) {
            $query->where('posisi.id_posisi', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('proses_rekrutmen.tanggal_interview_hr', [$request->from, $request->to]);
        }

        return response()->json($query->get());
    }

    public function interviewUserPassedByPositionMonth(Request $request)
    {
        $this->validateFilters($request);
        $query = DB::table('proses_rekrutmen')
            ->join('kandidat','proses_rekrutmen.kandidat_id','kandidat.id_kandidat')
            ->join('posisi','kandidat.posisi_id','posisi.id_posisi')
            ->where('proses_rekrutmen.interview_user_lolos', 1)
            ->select('posisi.id_posisi','posisi.nama_posisi', DB::raw('YEAR(proses_rekrutmen.tanggal_interview_user) as year'), DB::raw('MONTH(proses_rekrutmen.tanggal_interview_user) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('posisi.id_posisi','posisi.nama_posisi','year','month')
            ->orderBy('year','desc')->orderBy('month','desc');

        if ($request->filled('posisi_id')) {
            $query->where('posisi.id_posisi', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('proses_rekrutmen.tanggal_interview_user', [$request->from, $request->to]);
        }

        return response()->json($query->get());
    }

    public function recruitmentProgressByPosition(Request $request)
    {
        $this->validateFilters($request);
        $query = DB::table('kandidat')
            ->leftJoin('proses_rekrutmen','kandidat.id_kandidat','proses_rekrutmen.kandidat_id')
            ->join('posisi','kandidat.posisi_id','posisi.id_posisi')
            ->select('posisi.id_posisi','posisi.nama_posisi',
                DB::raw('COUNT(kandidat.id_kandidat) as total'),
                DB::raw('SUM(COALESCE(proses_rekrutmen.cv_lolos,0)) as cv_lolos'),
                DB::raw('SUM(COALESCE(proses_rekrutmen.psikotes_lolos,0)) as psikotes'),
                DB::raw('SUM(COALESCE(proses_rekrutmen.tes_kompetensi_lolos,0)) as kompetensi'),
                DB::raw('SUM(COALESCE(proses_rekrutmen.interview_hr_lolos,0)) as hr'),
                DB::raw('SUM(COALESCE(proses_rekrutmen.interview_user_lolos,0)) as user')
            )
            ->groupBy('posisi.id_posisi','posisi.nama_posisi');

        if ($request->filled('posisi_id')) {
            $query->where('posisi.id_posisi', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('kandidat.tanggal_melamar', [$request->from, $request->to]);
        }

        $data = $query->get()->map(function($row){
            $row->percent_cv = $row->total ? round(($row->cv_lolos / $row->total) * 100,2) : 0;
            $row->percent_psikotes = $row->total ? round(($row->psikotes / $row->total) * 100,2) : 0;
            $row->percent_kompetensi = $row->total ? round(($row->kompetensi / $row->total) * 100,2) : 0;
            $row->percent_hr = $row->total ? round(($row->hr / $row->total) * 100,2) : 0;
            $row->percent_user = $row->total ? round(($row->user / $row->total) * 100,2) : 0;
            return $row;
        });

        return response()->json($data);
    }

    // simple page views for per-stage metrics
    public function cvPage()
    {
        $posisis = Posisi::orderBy('nama_posisi')->get();
        return view('pages.rekrutmen.metrics.cv', compact('posisis'));
    }

    public function psikotesPage()
    {
        $posisis = Posisi::orderBy('nama_posisi')->get();
        return view('pages.rekrutmen.metrics.psikotes', compact('posisis'));
    }

    public function kompetensiPage()
    {
        $posisis = Posisi::orderBy('nama_posisi')->get();
        return view('pages.rekrutmen.metrics.kompetensi', compact('posisis'));
    }

    public function interviewHrPage()
    {
        $posisis = Posisi::orderBy('nama_posisi')->get();
        return view('pages.rekrutmen.metrics.interview_hr', compact('posisis'));
    }

    public function interviewUserPage()
    {
        $posisis = Posisi::orderBy('nama_posisi')->get();
        return view('pages.rekrutmen.metrics.interview_user', compact('posisis'));
    }

    public function pemberkasanProgress(Request $request)
    {
        $this->validateFilters($request);
        $query = DB::table('pemberkasan')
            ->join('kandidat','pemberkasan.kandidat_id','kandidat.id_kandidat')
            ->join('posisi','kandidat.posisi_id','posisi.id_posisi')
            ->select('posisi.id_posisi','posisi.nama_posisi', DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN pemberkasan.selesai_recruitment IS NOT NULL THEN 1 ELSE 0 END) as done_recruitment'),
                DB::raw('SUM(CASE WHEN pemberkasan.selesai_skgk_finance IS NOT NULL THEN 1 ELSE 0 END) as done_skgk_finance'),
                DB::raw('SUM(CASE WHEN pemberkasan.selesai_ttd_manager_hrd IS NOT NULL THEN 1 ELSE 0 END) as done_ttd_manager_hrd'),
                DB::raw('SUM(CASE WHEN pemberkasan.selesai_ttd_user IS NOT NULL THEN 1 ELSE 0 END) as done_ttd_user'),
                DB::raw('SUM(CASE WHEN pemberkasan.selesai_ttd_direktur IS NOT NULL THEN 1 ELSE 0 END) as done_ttd_direktur')
            )
            ->groupBy('posisi.id_posisi','posisi.nama_posisi');

        if ($request->filled('posisi_id')) {
            $query->where('posisi.id_posisi', $request->posisi_id);
        }

        $data = $query->get()->map(function($row){
            $row->percent_done_recruitment = $row->total ? round(($row->done_recruitment / $row->total) * 100,2) : 0;
            return $row;
        });

        return response()->json($data);
    }

    public function pemberkasanPage()
    {
        return view('pages.rekrutmen.pemberkasan.monitor');
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
}
