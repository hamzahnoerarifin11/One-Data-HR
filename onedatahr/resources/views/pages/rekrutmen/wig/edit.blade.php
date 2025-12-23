@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-md p-6">

    <h2 class="mb-6 text-title-md font-bold">
        Edit Data WIG Rekrutmen
    </h2>

    <form action="{{ route('wig-rekrutmen.update', $data->id) }}" method="POST">
        @csrf
        @method('PUT')

        <input type="date" name="tanggal" value="{{ $data->tanggal }}" class="w-full mb-3 rounded border p-2">

        <select name="posisi_id" class="w-full mb-3 rounded border p-2">
            @foreach($posisi as $p)
                <option value="{{ $p->id_posisi }}" @selected($data->posisi_id == $p->id_posisi)>
                    {{ $p->nama_posisi }}
                </option>
            @endforeach
        </select>

        @foreach(['total_pelamar','lolos_cv','lolos_psikotes','lolos_kompetensi','lolos_hr','lolos_user'] as $field)
        <input type="number" name="{{ $field }}"
               value="{{ $data->$field }}"
               class="w-full mb-2 rounded border p-2">
        @endforeach

        <button class="mt-4 w-full rounded bg-success py-2 text-white">
            Update Data
        </button>
    </form>

</div>
@endsection
