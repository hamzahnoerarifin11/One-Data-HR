@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-6">

<h2 class="mb-6 text-title-md font-bold">WIG Rekrutmen</h2>

<a href="{{ route('wig-rekrutmen.create') }}"
   class="mb-4 inline-block rounded bg-primary px-5 py-2 text-white">
   + Tambah WIG
</a>

<div class="rounded border bg-white p-4 shadow">
<table class="w-full table-auto">
<thead class="bg-gray-100">
<tr>
<th>Posisi</th>
<th>Pelamar</th>
<th>Lolos User</th>
<th>Training</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
@foreach($data as $row)
<tr class="border-t">
<td>{{ $row->posisi->nama_posisi }}</td>
<td>{{ $row->total_pelamar }}</td>
<td class="font-bold text-success">{{ $row->lolos_interview_user }}</td>
<td>{{ $row->tanggal_mulai_training }}</td>
<td class="space-x-2">
<a href="{{ route('wig-rekrutmen.show',$row->id_wig_rekrutmen) }}" class="text-primary">Detail</a>
<a href="{{ route('wig-rekrutmen.edit',$row->id_wig_rekrutmen) }}" class="text-warning">Edit</a>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>
@endsection
