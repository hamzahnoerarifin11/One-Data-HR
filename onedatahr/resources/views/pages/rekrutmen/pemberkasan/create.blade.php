@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Tambah Pemberkasan</h3>
    <form method="POST" action="{{ route('rekrutmen.pemberkasan.store') }}">
        @csrf
        <div class="mb-2">
            <label>Kandidat</label>
            <select name="kandidat_id" class="form-control" required>
                @if($kandidat_id)
                    <option value="{{ $kandidat_id }}">Pilihan kandidat awal ({{ $kandidat_id }})</option>
                @endif
                @foreach(\App\Models\Kandidat::orderBy('nama')->get() as $k)
                    <option value="{{ $k->id_kandidat }}">{{ $k->nama }} - {{ $k->posisi->nama_posisi ?? '-' }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-2"><label>Follow Up</label><textarea name="follow_up" class="form-control"></textarea></div>
        <div class="mb-2"><label>Selesai Recruitment</label><input type="date" name="selesai_recruitment" class="form-control"></div>
        <div class="mb-2"><button class="btn btn-primary">Simpan</button></div>
    </form>
</div>
@endsection
