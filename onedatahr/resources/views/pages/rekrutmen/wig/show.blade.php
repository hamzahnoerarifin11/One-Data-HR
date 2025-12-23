@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-lg p-6">

    <h2 class="mb-6 text-title-md font-bold">
        Detail Funnel: {{ $posisi->nama_posisi }}
    </h2>

    <table class="w-full table-auto border">
        <thead class="bg-gray-100">
            <tr class="text-center">
                <th class="p-3">Tanggal</th>
                <th>Pelamar</th>
                <th>CV</th>
                <th>Psikotes</th>
                <th>Kompetensi</th>
                <th>HR</th>
                <th>User</th>
                <th>Yield</th>
            </tr>
        </thead>
        <tbody>
            @foreach($posisi->rekrutmenDaily as $row)
            <tr class="text-center border-t">
                <td class="p-3">{{ $row->tanggal }}</td>
                <td>{{ $row->total_pelamar }}</td>
                <td>{{ $row->lolos_cv }}</td>
                <td>{{ $row->lolos_psikotes }}</td>
                <td>{{ $row->lolos_kompetensi }}</td>
                <td>{{ $row->lolos_hr }}</td>
                <td class="font-bold text-success">{{ $row->lolos_user }}</td>
                <td>{{ $row->yield }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
