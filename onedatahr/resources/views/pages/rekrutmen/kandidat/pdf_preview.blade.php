<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Psikologis</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            margin-bottom: 15px;
        }

        .header h2 {
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .info td {
            padding: 4px;
        }

        .section {
            margin-top: 15px;
            font-weight: bold;
            background: #f2f2f2;
            padding: 6px;
        }

        .table th, .table td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        .table th {
            background: #eaeaea;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>LAPORAN HASIL PEMERIKSAAN PSIKOLOGIS</h2>
</div>

<table class="info">
    <tr>
        <td width="25%">Nama</td>
        <td width="75%">: {{ $kandidat->nama }}</td>
    </tr>
    <tr>
        <td>Posisi</td>
        <td>: {{ $kandidat->posisi->nama_posisi ?? '-' }}</td>
    </tr>
    <tr>
        <td>Tanggal Tes</td>
        <td>: {{ $tanggalTes }}</td>
    </tr>
</table>

<div class="section">ASPEK PSIKOGRAM</div>

<table class="table">
    <thead>
        <tr>
            <th width="30%">Aspek Psikologis</th>
            <th width="10%">Nilai</th>
            <th width="60%">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($psikogram as $row)
        <tr>
            <td>{{ $row['aspek'] }}</td>
            <td style="text-align:center">{{ $row['score'] }}</td>
            <td>{{ $row['desc'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="section">KESIMPULAN</div>
<p>{{ $kesimpulan }}</p>

<div class="section">SARAN</div>
<p>{{ $saran }}</p>

</body>
</html>
