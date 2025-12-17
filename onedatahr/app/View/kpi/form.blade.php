<!DOCTYPE html>
<html lang="id">
<head>
    <title>KPI - {{ $karyawan->Nama_Lengkap_Sesuai_Ijazah }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-kpi th { background-color: #9bc2e6; vertical-align: middle; text-align: center; font-size: 12px; }
        .table-kpi td { vertical-align: middle; font-size: 13px; }
        .input-realisasi { width: 80px; text-align: center; }
        .bg-header-blue { background-color: #daeef3; }
    </style>
</head>
<body class="p-4">

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5>Form Penilaian Kinerja (KPI)</h5>
    </div>
    <div class="card-body">
        
        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-borderless table-sm">
                    <tr><td width="150">Nama Lengkap</td><td>: <strong>{{ $karyawan->Nama_Lengkap_Sesuai_Ijazah }}</strong></td></tr>
                    <tr><td>Jabatan</td><td>: {{ $karyawan->pekerjaan->first()->Jabatan ?? '-' }}</td></tr> <tr><td>Divisi/Unit</td><td>: {{ $karyawan->pekerjaan->first()->Divisi ?? '-' }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless table-sm">
                    <tr><td>Periode Penilaian</td><td>: {{ $kpi->periode }} {{ $kpi->tahun }}</td></tr>
                    <tr><td>Tanggal Penilaian</td><td>: {{ $kpi->tanggal_penilaian ?? date('d M Y') }}</td></tr>
                </table>
            </div>
        </div>

        <form action="{{ route('kpi.update', $kpi->id_kpi_assessment) }}" method="POST">
            @csrf
            
            <div class="table-responsive">
                <table class="table table-bordered table-kpi table-hover">
                    <thead>
                        <tr>
                            <th rowspan="2">No</th>
                            <th rowspan="2">Perspektif</th>
                            <th rowspan="2" style="width: 20%">Key Performance Indicator</th>
                            <th rowspan="2">Bobot</th>
                            <th rowspan="2">Target Tahunan</th>
                            
                            <th colspan="3" class="bg-header-blue">Semester 1</th>
                        </tr>
                        <tr>
                            <th class="bg-header-blue">Target</th>
                            <th class="bg-header-blue">Realisasi</th>
                            <th class="bg-header-blue">Skor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kpi->items as $index => $item)
                        @php
                            // Ambil data skor Semester 1 (jika ada)
                            $scoreSem1 = $item->getScoreByMonth('Semester 1'); 
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->perspektif }}</td>
                            <td>
                                <b>{{ $item->key_result_area }}</b><br>
                                {{ $item->key_performance_indicator }}
                            </td>
                            <td class="text-center">{{ $item->bobot }}%</td>
                            <td class="text-center">{{ $item->target_tahunan }}</td>

                            <td class="text-center bg-light">
                                {{-- Target Semester --}}
                                {{ $scoreSem1->target ?? '-' }}
                            </td>
                            <td class="text-center">
                                {{-- Input Realisasi --}}
                                <input type="text" 
                                       name="realisasi[{{ $item->id_kpi_item }}][Semester 1]" 
                                       value="{{ $scoreSem1->realisasi ?? '' }}" 
                                       class="form-control form-control-sm input-realisasi"
                                       placeholder="0%">
                            </td>
                            <td class="text-center fw-bold">
                                {{-- Skor Hasil Hitungan --}}
                                {{ $scoreSem1->skor ?? 0 }}%
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada item KPI yang di-setting.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" class="text-end fw-bold">Total Skor Akhir</td>
                            <td class="text-center fw-bold bg-warning">{{ $kpi->total_skor_akhir }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-3 text-end">
                <button type="submit" class="btn btn-success">
                    💾 Simpan Realisasi
                </button>
            </div>
        </form>

    </div>
</div>

</body>
</html>