<?php
namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pemberkasan;

class PemberkasanController extends Controller
{
    public function index(Request $r)
    {
        return Pemberkasan::whereHas('kandidat', fn($q) =>
                $q->where('posisi_id', $r->posisi_id)
            )
            ->selectRaw('status, COUNT(*) total')
            ->groupBy('status')
            ->get();
    }
}
