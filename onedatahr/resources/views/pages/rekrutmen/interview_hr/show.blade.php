@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-xl p-6">

    <div class="rounded-xl border bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <h1 class="text-2xl font-bold mb-4">Detail Interview HR</h1>

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><b>Nama Kandidat:</b> {{ $interview->nama_kandidat }}</div>
            <div><b>Posisi:</b> {{ $interview->posisi_dilamar }}</div>
            <div><b>Total Skor:</b> {{ $interview->total }}</div>
            <div><b>Keputusan:</b> {{ $interview->keputusan }}</div>
        </div>

        <hr class="my-4">

        <h3 class="font-semibold mb-2">Catatan Tambahan</h3>
        <p class="text-gray-600 dark:text-gray-400">
            {{ $interview->catatan_tambahan }}
        </p>
    </div>

</div>
@endsection
