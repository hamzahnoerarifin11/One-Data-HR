<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan KPI - {{ $karyawan->Nama_Lengkap_Sesuai_Ijazah }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11pt;
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
            <td>: {{ $karyawan->pekerjaan->first()?->jabatan ?? '-' }}</td>
            <td><strong>Status KPI</strong></td>
            <td>: {{ strtoupper($kpi->status) }}</td>
        </tr>
    </table>

    {{-- TABEL KPI --}}
    <table class="table-data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Indikator Kinerja (KPI)</th>
                <th width="10%">Bobot</th>
                <th width="10%">Target</th>
                <th width="15%">Realisasi (Total)</th>
                <th width="10%">Skor Akhir</th>
                <th width="15%">Ket</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            @php 
                $score = $item->scores->first(); 
                // Hitung total realisasi (Smt1 + Smt2) untuk tampilan report
                $totalRealisasi = 0;
                if($score) {
                    $totalRealisasi = $score->real_smt1 + $score->total_real_smt2;
                }
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="text-left">
                    <b>{{ $item->key_performance_indicator }}</b><br>
                    <span style="font-size: 8pt; color: #555;">Perspektif: {{ $item->perspektif }}</span>
                </td>
                <td>{{ $item->bobot }}%</td>
                <td>{{ $item->target }} {{ $item->units }}</td>
                <td>
                    {{ number_format($totalRealisasi, 2) }}
                </td>
                <td style="font-weight: bold;">
                    {{ $score ? number_format($score->skor_akhir, 2) : '0.00' }}
                </td>
                <td>{{ $item->polaritas }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align: right; padding-right: 10px;"><strong>TOTAL SKOR AKHIR</strong></td>
                <td style="background-color: #eee;"><strong>{{ number_format($kpi->total_skor_akhir, 2) }}</strong></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: right; padding-right: 10px;"><strong>GRADE / PREDIKAT</strong></td>
                <td><strong>{{ $kpi->grade }}</strong></td>
                <td></td>
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
                <u>_______________________</u>
            </td>
        </tr>
    </table>

</body>
</html>