<?php
namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProsesRekrutmen;

class ScreeningCvController extends Controller
{
    public function index(Request $r)
    {
        return ProsesRekrutmen::selectRaw('DATE(tanggal_cv) date, COUNT(*) total')
            ->where('cv_lolos', 1)
            ->whereHas('kandidat', fn($q) =>
                $q->where('posisi_id', $r->posisi_id)
            )
            ->whereMonth('tanggal_cv', $r->month)
            ->whereYear('tanggal_cv', $r->year)
            ->groupBy('date')
            ->get();
    }
}
