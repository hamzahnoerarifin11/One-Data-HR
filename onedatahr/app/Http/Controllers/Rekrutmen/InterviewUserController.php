<?php
namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProsesRekrutmen;

class InterviewUserController extends Controller
{
    public function index(Request $r)
    {
        return ProsesRekrutmen::selectRaw('DATE(tanggal_interview_user) date, COUNT(*) total')
            ->where('interview_user_lolos', 1)
            ->whereHas('kandidat', fn($q) =>
                $q->where('posisi_id', $r->posisi_id)
            )
            ->whereMonth('tanggal_interview_user', $r->month)
            ->whereYear('tanggal_interview_user', $r->year)
            ->groupBy('date')
            ->get();
    }
}
