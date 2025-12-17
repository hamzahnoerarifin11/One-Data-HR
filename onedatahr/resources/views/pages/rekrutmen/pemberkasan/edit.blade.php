@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Pemberkasan</h3>
    <form method="POST" action="{{ route('rekrutmen.pemberkasan.update', $item->id_pemberkasan) }}">
        @csrf
        @method('PUT')
        @if(!auth()->user() || auth()->user()->role !== 'admin')
            <div class="alert alert-warning">Anda tidak memiliki izin untuk mengubah pemberkasan; hanya admin yang dapat mengubah.</div>
        @endif
        <div class="mb-2"><label>Follow Up</label><textarea name="follow_up" class="form-control" {{ auth()->user() && auth()->user()->role === 'admin' ? '' : 'disabled' }}>{{ $item->follow_up }}</textarea></div>
        <div class="mb-2"><label>Selesai Recruitment</label><input type="date" name="selesai_recruitment" class="form-control" value="{{ $item->selesai_recruitment }}" {{ auth()->user() && auth()->user()->role === 'admin' ? '' : 'disabled' }}></div>
        <div class="mb-2"><button class="btn btn-primary" {{ auth()->user() && auth()->user()->role === 'admin' ? '' : 'disabled' }}>Simpan</button></div>
    </form>
</div>
@endsection
