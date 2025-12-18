<?php
namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RekrutmenDaily;

class PelamarHarianController extends Controller
{
    public function index(Request $r)
    {
        return RekrutmenDaily::selectRaw('date, SUM(count) total')
            ->where('posisi_id', $r->posisi_id)
            ->whereMonth('date', $r->month)
            ->whereYear('date', $r->year)
            ->groupBy('date')
            ->get();
    }

    public function store(Request $r)
    {
        RekrutmenDaily::updateOrCreate(
            [
                'posisi_id' => $r->posisi_id,
                'date'      => $r->date
            ],
            [
                'count' => $r->count,
                'notes' => $r->notes
            ]
        );

        return response()->json(['success' => true]);
    }
}
