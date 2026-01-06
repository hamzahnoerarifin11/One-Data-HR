<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
td, th { border: 1px solid #000; padding: 5px; }
</style>
</head>
<body>

<h3>LAPORAN PSIKOLOGI</h3>

<p>Nama: {{ $nama }}</p>
<p>Posisi: {{ $posisi }}</p>
<p>Tanggal: {{ $tanggal }}</p>

<table>
<tr>
<th>Aspek</th>
<th>Skor</th>
<th>Keterangan</th>
</tr>
@foreach($psikogram as $p)
<tr>
<td>{{ $p['aspek'] }}</td>
<td>{{ $p['score'] ?: '-' }}</td>
<td>{{ $p['desc'] ?: '-' }}</td>
</tr>
@endforeach
</table>

<p><strong>Kesimpulan:</strong><br>{{ $kesimpulan ?: '-' }}</p>
<p><strong>Saran:</strong><br>{{ $saran ?: '-' }}</p>

</body>
</html>


