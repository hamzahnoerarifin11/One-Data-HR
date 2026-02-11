<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan KPI - {{ $karyawan->Nama_Lengkap_Sesuai_Ijazah }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11pt;
            width: 100%;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h2, .header p {
            margin: 0;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 10pt;
        }
        .info-table td {
            padding: 3px;
            vertical-align: top;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }
        .table-data th, .table-data td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
        .table-data th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .text-left {
            text-align: left !important;
        }
        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
        }
        .footer-sign {
            width: 100%;
            margin-top: 50px;
        }
        .footer-sign td {
            text-align: center;
            width: 33%;
        }
        .sign-space {
            height: 70px;
        }
        tr {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

    {{-- HEADER KOP --}}
    <div class="header">
        <h2>FORM PENILAIAN KINERJA (KPI)</h2>
        <p>Periode Tahun: {{ $kpi->tahun }}</p>
    </div>

    {{-- INFO KARYAWAN --}}
    <table class="info-table">
        <tr>
            <td width="15%"><strong>Nama</strong></td>
            <td width="35%">: {{ $karyawan->Nama_Lengkap_Sesuai_Ijazah }}</td>
            <td width="15%"><strong>NIK</strong></td>
            <td width="35%">: {{ $karyawan->NIK ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Jabatan</strong></td>
            <td>: {{ $karyawan->pekerjaan->first()?->level?->name ?? '-' ||$karyawan->pekerjaan->first()?->division?->name ?? '-' }}</td>
            <td><strong>Status KPI</strong></td>
            <td>: {{ strtoupper($kpi->status) }}</td>
        </tr>
    </table>

    {{-- TABEL KPI --}}
    <table class="table-data" style="font-size: 8pt;">
        <thead>
            <tr>
                <th rowspan="3" width="3%">No</th>
                <th rowspan="3" width="20%">Indikator Kinerja (KPI)</th>
                <th rowspan="3" width="10%">KRA</th>
                <th rowspan="3" width="4%">Bobot</th>
                <th rowspan="3" width="4%">Target</th>
                <th colspan="24" width="54%">Bulan (Target / Real)</th>
                <th rowspan="3" width="5%">Skor Akhir</th>
            </tr>
            <tr>
                @foreach(['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Okt','Nov','Des'] as $m)
                    <th colspan="2">{{ $m }}</th>
                @endforeach
            </tr>
            <tr>
                @foreach(['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Okt','Nov','Des'] as $m)
                    <th width="2.25%" style="font-size: 7pt;">T</th>
                    <th width="2.25%" style="font-size: 7pt;">R</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
            @php
                $score = $item->scores->first();
                // Per-bulan
                $months = ['jan','feb','mar','apr','mei','jun','jul','aug','sep','okt','nov','des'];
            @endphp
            <tr>
                <td align="center">{{ $loop->iteration }}</td>
                <td class="text-left">
                    <b>{{ $item->key_performance_indicator }}</b><br>
                    <span style="font-size: 7pt; color: #555;">Perspektif: {{ $item->perspektif }}</span>
                    <br>
                    <span style="font-size: 7pt; color: #555;">Polaritas: {{ $item->polaritas }}</span>
                </td>
                <td>{{ $item->key_result_area }}</td>
                <td align="center">{{ $item->bobot }}%</td>
                <td align="center">{{ number_format($item->target, 0) }} {{ $item->units }}</td>

                {{-- Bulan 12 kolom: tiap bulan Trg & Real --}}
                @foreach($months as $m)
                    <td align="center" style="font-size: 7pt; padding: 2px;">{{ $score ? number_format($score->{"target_{$m}"} ?? 0, 0) : '0' }}</td>
                    <td align="center" style="font-size: 7pt; padding: 2px;">{{ $score ? number_format($score->{"real_{$m}"} ?? 0, 0) : '0' }}</td>
                @endforeach

                <td align="center" style="font-weight: bold;">{{ $score ? number_format($score->skor_akhir, 2) : '0.00' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="30" style="text-align: center; font-style: italic; color: #666;">
                    Belum ada indikator KPI yang ditambahkan
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="29" style="text-align: right; padding-right: 10px;"><strong>TOTAL SKOR AKHIR</strong></td>
                <td style="background-color: #eee;"><strong>{{ number_format($kpi->total_skor_akhir, 2) }}</strong></td>
            </tr>
            <tr>
                <td colspan="29" style="text-align: right; padding-right: 10px;"><strong>GRADE / PREDIKAT</strong></td>
                <td><strong>{{ $kpi->grade }}</strong></td>
            </tr>
        </tfoot>
    </table>

    {{-- KOLOM TANDA TANGAN (Opsional) --}}
    <table class="footer-sign">
        <tr>
            <td>
                Dibuat Oleh,<br>
                (Karyawan)
                <div class="sign-space"></div>
                <u>{{ $karyawan->Nama_Lengkap_Sesuai_Ijazah }}</u>
            </td>
            <td>
                
            </td>
            <td>
                Disetujui Oleh,<br>
                (Atasan Langsung)
                <div class="sign-space"></div>
                <u>{{ $karyawan->atasan->Nama_Lengkap_Sesuai_Ijazah ?? '-' }}</u>
            </td>
        </tr>
    </table>

</body>
</html>