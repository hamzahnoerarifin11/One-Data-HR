<!DOCTYPE html>
<html>
<head>
    <style>
        table { width:100%; border-collapse: collapse; font-size: 10px; }
        td, th { border:1px solid #000; padding:4px; }
    </style>
</head>
<body>
<h3>Hasil Tes — {{ $kandidat->nama }}</h3>

<table>
@foreach($sheet as $row)
<tr>
@foreach($row as $cell)
<td>{{ $cell }}</td>
@endforeach
</tr>
@endforeach
</table>
</body>
</html>
