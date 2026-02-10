<!DOCTYPE html>
<html>
<head>
    <title>Data Karyawan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Data Karyawan</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIK</th>
                <th>Email</th>
                <th>No Telepon</th>
                <th>Jabatan</th>
                <th>Divisi</th>
                <th>Perusahaan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($karyawans as $index => $karyawan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $karyawan->Nama_Sesuai_KTP }}</td>
                    <td>{{ $karyawan->NIK }}</td>
                    <td>{{ $karyawan->Email }}</td>
                    <td>{{ $karyawan->Nomor_Telepon_Aktif_Karyawan }}</td>
                    <td>{{ $karyawan->pekerjaan->first()->level->name ?? '-' }}</td>
                    <td>{{ $karyawan->pekerjaan->first()->division->name ?? '-' }}</td>
                    <td>{{ $karyawan->pekerjaan->first()?->company->name ?? $karyawan->pekerjaan->first()?->holding->name ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
