@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Proses Rekrutmen - {{ $kandidat->nama }}</h3>
    <form method="POST" action="{{ route('rekrutmen.proses.store') }}">
        @csrf
        <input type="hidden" name="kandidat_id" value="{{ $kandidat->id_kandidat }}" />
        @if(!auth()->user() || auth()->user()->role !== 'admin')
            <div class="alert alert-warning">Anda tidak memiliki izin untuk mengubah proses; hanya admin yang dapat mengubah.</div>
        @endif
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="cv_lolos" value="1" {{ optional($proses)->cv_lolos ? 'checked' : '' }} {{ auth()->user() && auth()->user()->role === 'admin' ? '' : 'disabled' }}>
            <label class="form-check-label">CV Lolos</label>
        </div>
        <div class="mb-2"><label>Tanggal CV</label><input type="date" name="tanggal_cv" class="form-control" value="{{ optional($proses)->tanggal_cv }}" {{ auth()->user() && auth()->user()->role === 'admin' ? '' : 'disabled' }}></div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="psikotes_lolos" value="1" {{ optional($proses)->psikotes_lolos ? 'checked' : '' }} {{ auth()->user() && auth()->user()->role === 'admin' ? '' : 'disabled' }}>
            <label class="form-check-label">Psikotes Lolos</label>
        </div>
        <div class="mb-2"><label>Tanggal Psikotes</label><input type="date" name="tanggal_psikotes" class="form-control" value="{{ optional($proses)->tanggal_psikotes }}" {{ auth()->user() && auth()->user()->role === 'admin' ? '' : 'disabled' }}></div>
        <div class="mb-2"><button class="btn btn-primary" {{ auth()->user() && auth()->user()->role === 'admin' ? '' : 'disabled' }}>Simpan</button></div>
    </form>
</div>
@endsection
