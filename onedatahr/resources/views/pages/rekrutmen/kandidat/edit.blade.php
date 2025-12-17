@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Kandidat</h3>
    <form method="POST" action="{{ route('rekrutmen.kandidat.update', $kandidat->id_kandidat) }}">
        @csrf
        @method('PUT')
        <div class="mb-2">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" value="{{ $kandidat->nama }}" required />
        </div>
        <div class="mb-2">
            <label>Posisi</label>
            <select name="posisi_id" class="form-control" required>
                @foreach($posisis as $pos)
                    <option value="{{ $pos->id_posisi }}" {{ $kandidat->posisi_id == $pos->id_posisi ? 'selected' : '' }}>{{ $pos->nama_posisi }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-2">
            <label>Tanggal Melamar</label>
            <input type="date" name="tanggal_melamar" class="form-control" value="{{ $kandidat->tanggal_melamar? $kandidat->tanggal_melamar->format('Y-m-d') : '' }}" />
        </div>
        <div class="mb-2">
            <label>Sumber</label>
            <input type="text" name="sumber" class="form-control" value="{{ $kandidat->sumber }}" />
        </div>
        <div class="mb-2">
            <label>Status Akhir</label>
            <input type="text" name="status_akhir" class="form-control" value="{{ $kandidat->status_akhir }}" />
        </div>
        <div class="mb-2"><button class="btn btn-primary">Simpan</button></div>
    </form>
</div>
@endsection
