<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Export Turnover - {{ $periodeLabel }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f4f4f4; }
        .header { margin-bottom: 10px; }
        .meta { margin-bottom: 6px; }
        .chart-title { margin: 15px 0 5px; font-weight: bold; }
    </style>
</head>
<body>

@php
    use Carbon\Carbon;

    /* =========================
     | LOGIKA PERIODE (SAMA SISTEM)
     ========================= */
    $startMonth = 1;
    $endMonth = 12;

    if (!empty($periodeLabel)) {
        $exp = explode('-', $periodeLabel);
        $semester = $exp[1] ?? null;

        if ((int)$semester === 1) {
            $startMonth = 1;
            $endMonth = 6;
        } elseif ((int)$semester === 2) {
            $startMonth = 7;
            $endMonth = 12;
        }
    }

    /* =========================
     | DATA GRAFIK BULANAN
     ========================= */
    $grafik = [];
    for ($m = $startMonth; $m <= $endMonth; $m++) {
        $grafik[$m] = 0;
    }

    foreach ($onboardings as $it) {
        if (
            $it->status_turnover_auto === 'turnover' &&
            $it->tanggal_resign &&
            Carbon::parse($it->tanggal_resign)
                ->lt(Carbon::parse($it->jadwal_ttd_kontrak)->addMonths(3))
        ) {
            $bulan = Carbon::parse($it->tanggal_resign)->month;
            if ($bulan >= $startMonth && $bulan <= $endMonth) {
                $grafik[$bulan]++;
            }
        }
    }

    $maxVal = max($grafik) ?: 1;
    $width = 520;
    $height = 180;
    $padding = 30;
    $stepX = ($width - $padding * 2) / max(count($grafik) - 1, 1);
@endphp

<div class="header">
    <h2>Laporan Turnover</h2>
    <div class="meta">Periode: <strong>{{ $periodeLabel }}</strong></div>
    <div class="meta">
        Total Data: <strong>{{ $totalData }}</strong> |
        Total Lolos: <strong>{{ $totalLolos }}</strong> |
        Turnover: <strong>{{ $totalTurnover }}</strong> |
        Turnover Rate: <strong>{{ $turnoverRate }}%</strong>
    </div>
</div>

<div class="chart-title">Grafik Turnover Bulanan</div>

<!-- =========================
 | SVG LINE CHART (DOMPDF SAFE)
 ========================= -->
<svg width="{{ $width }}" height="{{ $height }}">
    <!-- Axis -->
    <line x1="{{ $padding }}" y1="{{ $height-$padding }}" x2="{{ $width-$padding }}" y2="{{ $height-$padding }}" stroke="#000"/>
    <line x1="{{ $padding }}" y1="{{ $padding }}" x2="{{ $padding }}" y2="{{ $height-$padding }}" stroke="#000"/>

    @php
        $points = [];
        $i = 0;
    @endphp

    @foreach($grafik as $bulan => $val)
        @php
            $x = $padding + ($i * $stepX);
            $y = ($height - $padding) - (($val / $maxVal) * ($height - $padding * 2));
            $points[] = "$x,$y";
        @endphp

        <!-- Titik -->
        <circle cx="{{ $x }}" cy="{{ $y }}" r="3" fill="#1d4ed8"/>

        <!-- Label bulan -->
        <text x="{{ $x }}" y="{{ $height - 10 }}" font-size="9" text-anchor="middle">
            {{ Carbon::create()->month($bulan)->translatedFormat('M') }}
        </text>

        <!-- Nilai -->
        <text x="{{ $x }}" y="{{ $y - 6 }}" font-size="9" text-anchor="middle">
            {{ $val }}
        </text>

        @php $i++; @endphp
    @endforeach

    <!-- Line -->
    <polyline
        fill="none"
        stroke="#1d4ed8"
        stroke-width="2"
        points="{{ implode(' ', $points) }}"
    />
</svg>

<br><br>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Nama</th>
            <th>Posisi</th>
            <th>Jadwal Kontrak</th>
            <th>Tanggal Resign</th>
            <th>Masa Kerja (bln)</th>
            <th>Masa Kerja (hari)</th>
            <th>Status</th>
            <th>Alasan Resign</th>
        </tr>
    </thead>
    <tbody>
        @foreach($onboardings as $i => $it)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $it->kandidat->nama ?? '-' }}</td>
                <td>{{ $it->posisi->nama_posisi ?? '-' }}</td>
                <td>{{ $it->formatTanggal('jadwal_ttd_kontrak') }}</td>
                <td>{{ $it->formatTanggal('tanggal_resign') ?? '-' }}</td>
                <td>{{ $it->masa_kerja_bulan }}</td>
                <td>{{ $it->masa_kerja_hari }}</td>
                <td>{{ strtoupper($it->status_turnover_auto) }}</td>
                <td>{{ $it->alasan_resign ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
