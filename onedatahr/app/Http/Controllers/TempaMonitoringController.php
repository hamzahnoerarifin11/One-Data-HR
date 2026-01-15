<?php

namespace App\Http\Controllers;

use App\Models\TempaPeserta;
use App\Services\TempaService;
use Illuminate\Http\Request;

class TempaMonitoringController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(TempaService $service)
    {
        $this->authorize('viewTempaMonitoring');

        // Pastikan jika query kosong, tetap menghasilkan Collection kosong, bukan null
        $pesertas = TempaPeserta::with(['kelompok', 'mentor', 'absensi'])->get() ?? collect();
        $persentaseNasional = $service->hitungPersentaseNasional() ?? 0;
        $rekapKelompok = $service->getStatistikKelompok() ?? [];

        return view('pages.tempa.monitoring.index', compact(
            'pesertas',
            'persentaseNasional',
            'rekapKelompok'
        ));
    }
}
