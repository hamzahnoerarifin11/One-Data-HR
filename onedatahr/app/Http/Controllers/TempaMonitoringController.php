<?php

namespace App\Http\Controllers;

use App\Models\TempaPeserta;
use App\Models\TempaKelompok;
use App\Services\TempaService;
use Illuminate\Http\Request;

class TempaMonitoringController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, TempaService $service)
    {
        $this->authorize('viewTempaMonitoring');

        // Ambil parameter filter
        $tahun = $request->get('tahun', date('Y'));
        $kelompokId = $request->get('kelompok');
        $status = $request->get('status');
        $lokasi = $request->get('lokasi');
        $search = $request->get('search');

        // Query peserta dengan filter
        $query = TempaPeserta::with(['kelompok', 'absensi']);

        // Filter berdasarkan tahun (jika ada relasi dengan tempa)
        if ($tahun) {
            // Asumsikan tempa memiliki tahun, tapi untuk sekarang skip
        }

        // Filter kelompok
        if ($kelompokId) {
            $query->where('id_kelompok', $kelompokId);
        }

        // Filter lokasi/tempat melalui relasi kelompok
        if ($lokasi) {
            $query->whereHas('kelompok', function($q) use ($lokasi) {
                $q->where('tempat', $lokasi);
            });
        }

        // Filter status
        if ($status) {
            $query->where('status_peserta', $status);
        }

        // Filter search (nama atau NIK)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_peserta', 'like', '%' . $search . '%')
                  ->orWhere('nik_karyawan', 'like', '%' . $search . '%');
            });
        }

        // Ambil semua data untuk Alpine.js pagination (client-side)
        $pesertas = $query->with(['kelompok', 'absensi'])->get();

        // Hitung persentase nasional berdasarkan peserta yang difilter (semua data untuk statistik)
        $persentaseNasional = $this->hitungPersentaseNasionalFiltered($pesertas);

        // Statistik kelompok (filter berdasarkan kelompok yang ada pesertanya)
        $rekapKelompok = $this->getStatistikKelompokFiltered($pesertas);

        // List kelompok untuk filter dropdown
        $listKelompok = TempaKelompok::orderBy('nama_kelompok')->get();

        // List lokasi untuk filter dropdown
        $listLokasi = TempaKelompok::select('tempat')->distinct()->whereNotNull('tempat')->pluck('tempat')->toArray();

        return view('pages.tempa.monitoring.index', compact(
            'pesertas',
            'persentaseNasional',
            'rekapKelompok',
            'listKelompok',
            'listLokasi'
        ));
    }

    private function hitungPersentaseNasionalFiltered($pesertas)
    {
        $aktifPesertas = $pesertas->where('status_peserta', 1);
        if ($aktifPesertas->isEmpty()) return 0;

        $totalPersentase = $aktifPesertas->sum('persentase_kehadiran');
        return round($totalPersentase / $aktifPesertas->count(), 1);
    }

    private function getStatistikKelompokFiltered($pesertas)
    {
        $statistik = [];
        $kelompokIds = $pesertas->pluck('id_kelompok')->unique();

        foreach ($kelompokIds as $kelompokId) {
            $kelompok = TempaKelompok::find($kelompokId);
            if (!$kelompok) continue;

            $kelompokPesertas = $pesertas->where('id_kelompok', $kelompokId);
            $aktifPesertas = $kelompokPesertas->where('status_peserta', 1);

            $persentase = 0;
            if ($aktifPesertas->isNotEmpty()) {
                $persentase = round($aktifPesertas->avg('persentase_kehadiran'), 1);
            }

            $mentorName = $kelompok->nama_mentor ?? 'N/A';

            $statistik[] = [
                'nama_kelompok' => $kelompok->nama_kelompok,
                'lokasi' => $kelompok->tempat ?? 'N/A',
                'keterangan_cabang' => $kelompok->keterangan_cabang ?? 'N/A',
                'nama_mentor' => $mentorName,
                'jumlah_peserta_aktif' => $aktifPesertas->count(),
                'total_peserta' => $kelompokPesertas->count(),
                'persentase' => $persentase,
            ];
        }

        return $statistik;
    }
}
