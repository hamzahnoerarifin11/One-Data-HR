<?php
namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProsesRekrutmen;

class InterviewHrController extends Controller
{
    public function index(Request $r)
    {
        return ProsesRekrutmen::selectRaw('DATE(tanggal_interview_hr) date, COUNT(*) total')
            ->where('interview_hr_lolos', 1)
            ->whereHas('kandidat', fn($q) =>
                $q->where('posisi_id', $r->posisi_id)
            )
            ->whereMonth('tanggal_interview_hr', $r->month)
            ->whereYear('tanggal_interview_hr', $r->year)
            ->groupBy('date')
            ->get();
    }
}
