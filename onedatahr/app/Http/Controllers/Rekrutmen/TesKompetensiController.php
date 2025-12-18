<?php
namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProsesRekrutmen;

class TesKompetensiController extends Controller
{
    public function index(Request $r)
    {
        return ProsesRekrutmen::selectRaw('DATE(tanggal_tes_kompetensi) date, COUNT(*) total')
            ->where('tes_kompetensi_lolos', 1)
            ->whereHas('kandidat', fn($q) =>
                $q->where('posisi_id', $r->posisi_id)
            )
            ->whereMonth('tanggal_tes_kompetensi', $r->month)
            ->whereYear('tanggal_tes_kompetensi', $r->year)
            ->groupBy('date')
            ->get();
    }
}
