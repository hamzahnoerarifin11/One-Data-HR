<?php
namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RekrutmenDaily;
use App\Models\ProsesRekrutmen;

class SummaryController extends Controller
{
    public function index(Request $r)
    {
        $posisi = $r->posisi_id;

        return response()->json([
            'pelamar' => RekrutmenDaily::where('posisi_id',$posisi)->sum('count'),
            'cv' => ProsesRekrutmen::where('cv_lolos',1)->whereHas('kandidat',fn($q)=>$q->where('posisi_id',$posisi))->count(),
            'psikotes' => ProsesRekrutmen::where('psikotes_lolos',1)->whereHas('kandidat',fn($q)=>$q->where('posisi_id',$posisi))->count(),
            'tes' => ProsesRekrutmen::where('tes_kompetensi_lolos',1)->whereHas('kandidat',fn($q)=>$q->where('posisi_id',$posisi))->count(),
            'hr' => ProsesRekrutmen::where('interview_hr_lolos',1)->whereHas('kandidat',fn($q)=>$q->where('posisi_id',$posisi))->count(),
            'user' => ProsesRekrutmen::where('interview_user_lolos',1)->whereHas('kandidat',fn($q)=>$q->where('posisi_id',$posisi))->count(),
        ]);
    }
}
