<?php

namespace App\Http\Controllers;

use App\Models\OnboardingKaryawan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Exports\TurnoverSpreadsheet;

class TurnoverController extends Controller
{
    private function resolvePeriode(string $periode = null)
    {
        $tahun = now()->year;
        $semester = null;
        $startMonth = 1;
        $endMonth = 12;
        $isAllYears = false;

        if ($periode) {
            [$tahunPart, $semesterPart] = array_pad(explode('-', $periode), 2, 0);
            if (strtolower($tahunPart) === 'all') {
                $isAllYears = true;
            } else {
                $tahun = (int) $tahunPart;
            }
            $semester = (int) $semesterPart;

            if ($semester === 1) {
                $startMonth = 1; $endMonth = 6;
            } elseif ($semester === 2) {
                $startMonth = 7; $endMonth = 12;
            } else {
                $startMonth = 1; $endMonth = 12;
            }
        }

        $chartYear = $isAllYears ? now()->year : $tahun;

        return compact('tahun', 'semester', 'startMonth', 'endMonth', 'isAllYears', 'chartYear');
    }

    private function getOnboardingsByPeriode(?string $periode = null)
    {
        extract($this->resolvePeriode($periode));

        $query = OnboardingKaryawan::with(['kandidat', 'posisi'])
            ->orderBy('jadwal_ttd_kontrak', 'desc')
            ->whereBetween(DB::raw('MONTH(jadwal_ttd_kontrak)'), [$startMonth, $endMonth]);

        if (! $isAllYears) {
            $query->whereYear('jadwal_ttd_kontrak', $tahun);
        }

        return $query->get();
    }

    public function index()
    {
        // Periode filter (format: YYYY-S where S = 0 (all) | 1 (semester 1 Jan-Jun) | 2 (semester 2 Jul-Dec))
        $periode = request('periode');

        // Backwards compatible: allow 'tahun' param if periode not provided
        $tahun = request('tahun', now()->year);
        $semester = null;
        $startMonth = 1;
        $endMonth = 12;

        $isAllYears = false;
        if ($periode) {
            // Support formats: 'YYYY-S' or 'all-S' where S = 0|1|2
            [$tahunPart, $semesterPart] = array_pad(explode('-', $periode), 2, 0);
            if (strtolower($tahunPart) === 'all') {
                $isAllYears = true;
            } else {
                $tahun = (int) $tahunPart;
            }

            $semester = (int) $semesterPart;

            if ($semester === 1) {
                $startMonth = 1;
                $endMonth = 6;
            } elseif ($semester === 2) {
                $startMonth = 7;
                $endMonth = 12;
            } else {
                $startMonth = 1;
                $endMonth = 12;
            }
        }

        // chart year for labeling months (use current year when aggregating all years)
        $chartYear = $isAllYears ? now()->year : $tahun;

        /* =======================
         | DATA UTAMA
         ======================= */

        // Filter onboardings by contract date (jadwal_ttd_kontrak) based on selected periode/year
        $onboardingQuery = OnboardingKaryawan::with(['kandidat', 'posisi'])
            ->orderBy('jadwal_ttd_kontrak', 'desc')
            ->whereBetween(DB::raw('MONTH(jadwal_ttd_kontrak)'), [$startMonth, $endMonth]);

        if (! $isAllYears) {
            $onboardingQuery->whereYear('jadwal_ttd_kontrak', $tahun);
        }

        $onboardings = $onboardingQuery->get();

        $totalData = $onboardings->count();

        /* =======================
         | STATISTIK STATUS (DINAMIS)
         ======================= */

        // Count turnover (resign < 3 months) within the periode
        $totalTurnover = $onboardings->filter(function ($item) use ($tahun, $startMonth, $endMonth, $isAllYears) {
            if ($item->status_turnover_auto !== 'turnover' || !$item->tanggal_resign) return false;
            $dt = Carbon::parse($item->tanggal_resign);
            if (! $isAllYears && $dt->year !== (int) $tahun) return false;
            return $dt->month >= $startMonth && $dt->month <= $endMonth;
        })->count();

        $totalLolos = $onboardings->filter(fn ($item) =>
            $item->status_turnover_auto === 'lolos'
        )->count();

        $turnoverRate = $totalData > 0
            ? round(($totalTurnover / $totalData) * 100)
            : 0;

        /* =======================
         | GRAFIK TURNOVER BULANAN (VALID)
         ======================= */

        // Ambil data turnover yang BENAR (resign < 3 bulan) dan dalam periode bulan yang dipilih
        $rawTurnoverQuery = OnboardingKaryawan::whereNotNull('tanggal_resign')
            ->whereRaw('tanggal_resign < DATE_ADD(jadwal_ttd_kontrak, INTERVAL 3 MONTH)')
            ->whereBetween(DB::raw('MONTH(tanggal_resign)'), [$startMonth, $endMonth]);

        if (! $isAllYears) {
            $rawTurnoverQuery->whereYear('tanggal_resign', $tahun);
        }

        $rawTurnover = $rawTurnoverQuery
            ->selectRaw('MONTH(tanggal_resign) as bulan, COUNT(*) as total')
            ->groupByRaw('MONTH(tanggal_resign)')
            ->pluck('total', 'bulan');

        // Paksa rentang bulan sesuai semester / tahun yang dipilih
        $turnoverBulanan = collect(range($startMonth, $endMonth))->map(function ($bulan) use ($rawTurnover) {
            return [
                'bulan' => $bulan,
                'total' => $rawTurnover->get($bulan, 0)
            ];
        });

        /* =======================
         | LIST TAHUN & PERIODE UNTUK FILTER
         ======================= */

        $listTahun = OnboardingKaryawan::whereNotNull('tanggal_resign')
            ->selectRaw('YEAR(tanggal_resign) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        // Build periode options including global "Semua Tahun" options
        $listPeriode = [
            ['value' => 'all-0', 'label' => 'Semua Tahun - Semua Semester'],
            ['value' => 'all-1', 'label' => 'Semua Tahun - Semester 1 (Jan-Jun)'],
            ['value' => 'all-2', 'label' => 'Semua Tahun - Semester 2 (Jul-Dec)'],
        ];

        foreach ($listTahun as $th) {
            $listPeriode[] = ['value' => $th.'-0', 'label' => $th.' (All)'];
            $listPeriode[] = ['value' => $th.'-1', 'label' => $th.' (Semester 1)'];
            $listPeriode[] = ['value' => $th.'-2', 'label' => $th.' (Semester 2)'];
        }

        return view('pages.turnover.index', compact(
            'onboardings',
            'totalData',
            'totalLolos',
            'totalTurnover',
            'turnoverRate',
            'turnoverBulanan',
            'tahun',
            'listTahun',
            'periode',
            'listPeriode',
            'chartYear'
        ));
    }

    // public function exportExcel()
    // {
    //     $periode = request('periode');
    //     $onboardings = $this->getOnboardingsByPeriode($periode);

    //     if ($periode) {
    //         $label = $periode;
    //     } else {
    //         if ($onboardings->first() && $onboardings->first()->jadwal_ttd_kontrak) {
    //             $label = Carbon::parse($onboardings->first()->jadwal_ttd_kontrak)->format('Y') . '-0';
    //         } else {
    //             $label = 'all-0';
    //         }
    //     }
    //     $label = str_replace([' ', '(', ')'], ['_', '', ''], $label);
    //     $filename = "turnover_{$label}_" . now()->timestamp . ".xlsx";

    //     try {
    //         return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\TurnoverExport($onboardings), $filename);
    //     } catch (\Exception $e) {
    //         return back()->with('error', 'Gagal men-generate Excel');
    //     }
    // }
    public function exportExcel()
    {
        $periode = request('periode');
        $onboardings = $this->getOnboardingsByPeriode($periode);

        return \App\Exports\TurnoverSpreadsheet::download($onboardings, $periode);
    }


    public function exportPdf()
    {
        $periode = request('periode');
        $onboardings = $this->getOnboardingsByPeriode($periode);

        $totalData = $onboardings->count();
        $totalTurnover = $onboardings->filter(fn ($item) => $item->status_turnover_auto === 'turnover')->count();
        $totalLolos = $onboardings->filter(fn ($item) => $item->status_turnover_auto === 'lolos')->count();
        $turnoverRate = $totalData > 0 ? round(($totalTurnover / $totalData) * 100) : 0;

        $periodeLabel = $periode ?: 'All';
        $filename = "turnover_{$periodeLabel}_" . now()->timestamp . ".pdf";

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pages.turnover.export_pdf', compact(
            'onboardings',
            'totalData',
            'totalTurnover',
            'totalLolos',
            'turnoverRate',
            'periodeLabel'
        ));

        return $pdf->download($filename);
    }
}
