@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-lg font-bold">
            Preview Excel – {{ $kandidat->nama }}
        </h1>

        <a href="{{ route('rekrutmen.kandidat.laporan', $kandidat->id_kandidat) }}"
           class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
            Download PDF
        </a>
    </div>

    <div class="border rounded overflow-auto bg-white p-4">
        {!! $html !!}
    </div>

</div>
@endsection
