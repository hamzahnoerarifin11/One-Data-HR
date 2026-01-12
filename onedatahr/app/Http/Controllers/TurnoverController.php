<?php

namespace App\Http\Controllers;

use App\Models\OnboardingKaryawan;
use Illuminate\Support\Facades\DB;

class TurnoverController extends Controller
{
    public function index()
    {
        // Tahun dari filter (default: tahun sekarang)
        $tahun = request('tahun', now()->year);

        /* =======================
         | DATA UTAMA
         ======================= */

        $onboardings = OnboardingKaryawan::with(['kandidat', 'posisi'])
            ->orderBy('jadwal_ttd_kontrak', 'desc')
            ->get();

        $totalData = $onboardings->count();

        /* =======================
         | STATISTIK STATUS (DINAMIS)
         ======================= */

        $totalTurnover = $onboardings->filter(fn ($item) =>
            $item->status_turnover_auto === 'turnover'
        )->count();

        $totalLolos = $onboardings->filter(fn ($item) =>
            $item->status_turnover_auto === 'lolos'
        )->count();

        $turnoverRate = $totalData > 0
            ? round(($totalTurnover / $totalData) * 100)
            : 0;

        /* =======================
         | GRAFIK TURNOVER BULANAN (VALID)
         ======================= */

        // Ambil data turnover yang BENAR
        $rawTurnover = OnboardingKaryawan::whereNotNull('tanggal_resign')
            ->whereRaw('tanggal_resign < DATE_ADD(jadwal_ttd_kontrak, INTERVAL 3 MONTH)')
            ->whereYear('tanggal_resign', $tahun)
            ->selectRaw('MONTH(tanggal_resign) as bulan, COUNT(*) as total')
            ->groupByRaw('MONTH(tanggal_resign)')
            ->pluck('total', 'bulan');

        // Paksa Januari - Desember
        $turnoverBulanan = collect(range(1, 12))->map(function ($bulan) use ($rawTurnover) {
            return [
                'bulan' => $bulan,
                'total' => $rawTurnover->get($bulan, 0)
            ];
        });

        /* =======================
         | LIST TAHUN UNTUK FILTER
         ======================= */

        $listTahun = OnboardingKaryawan::whereNotNull('tanggal_resign')
            ->selectRaw('YEAR(tanggal_resign) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        return view('pages.turnover.index', compact(
            'onboardings',
            'totalData',
            'totalLolos',
            'totalTurnover',
            'turnoverRate',
            'turnoverBulanan',
            'tahun',
            'listTahun'
        ));
    }
}
