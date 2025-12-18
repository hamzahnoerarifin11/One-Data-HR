<?php
namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProsesRekrutmen;

class PsikotesController extends Controller
{
    public function index(Request $r)
    {
        return ProsesRekrutmen::selectRaw('DATE(tanggal_psikotes) date, COUNT(*) total')
            ->where('psikotes_lolos', 1)
            ->whereHas('kandidat', fn($q) =>
                $q->where('posisi_id', $r->posisi_id)
            )
            ->whereMonth('tanggal_psikotes', $r->month)
            ->whereYear('tanggal_psikotes', $r->year)
            ->groupBy('date')
            ->get();
    }
}
