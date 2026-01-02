<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; line-height: 1.4; }
        .title { text-align: center; font-weight: bold; font-size: 14pt; text-decoration: underline; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid black; padding: 5px; vertical-align: top; }
        .center { text-align: center; }
        .bg-gray { background-color: #e0e0e0; font-weight: bold; }
        .aspek-col { width: 20%; }
        .desc-col { width: 30%; }
        .score-col { width: 4%; }
    </style>
</head>
<body>
    <div class="title">LAPORAN PSIKOLOGIS</div>

    <table>
        <tr class="bg-gray">
            <th class="aspek-col">ASPEK PSIKOLOGIS</th>
            <th class="desc-col">GAMBARAN INDIVIDU JIKA SKOR RENDAH</th>
            <th class="score-col">KS</th>
            <th class="score-col">K</th>
            <th class="score-col">C</th>
            <th class="score-col">B</th>
            <th class="score-col">BS</th>
            <th class="desc-col">GAMBARAN INDIVIDU JIKA SKOR TINGGI</th>
        </tr>

        @foreach($rows as $row)
        @if(isset($row['aspek']))
        <tr>
            <td>{{ $row['aspek'] }}</td>
            <td style="font-size: 8pt;">{{ $row['desc_low'] }}</td>
            <td class="center">{{ $row['score'] == 'KS' ? 'V' : '' }}</td>
            <td class="center">{{ $row['score'] == 'K' ? 'V' : '' }}</td>
            <td class="center">{{ $row['score'] == 'C' ? 'V' : '' }}</td>
            <td class="center">{{ $row['score'] == 'B' ? 'V' : '' }}</td>
            <td class="center">{{ $row['score'] == 'BS' ? 'V' : '' }}</td>
            <td style="font-size: 8pt;">{{ $row['desc_high'] }}</td>
        </tr>
        @endif
        @endforeach
    </table>

    <div style="margin-top: 20px;">
        <strong>KESIMPULAN:</strong>
        <p style="border: 1px solid #000; padding: 10px; min-height: 50px;">
            {{ $kesimpulan }}
        </p>
    </div>
</body>
</html>
