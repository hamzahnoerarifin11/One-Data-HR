@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Tambah Kandidat</h3>
    <form method="POST" action="{{ route('rekrutmen.kandidat.store') }}">
        @csrf
        <div class="mb-2">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" required />
        </div>
        <div class="mb-2">
            <label>Posisi</label>
            <select name="posisi_id" class="form-control" required>
                @foreach($posisis as $pos)
                    <option value="{{ $pos->id_posisi }}">{{ $pos->nama_posisi }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-2">
            <button class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>
@endsection
