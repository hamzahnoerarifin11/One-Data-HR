@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-2xl p-4">
    <h3 class="text-xl font-semibold mb-4">Edit Proses Rekrutmen - {{ $kandidat->nama }}</h3>
    <form method="POST" action="{{ route('rekrutmen.proses.store') }}">
        @csrf
        <input type="hidden" name="kandidat_id" value="{{ $kandidat->id_kandidat }}" />
        @if(!auth()->user() || auth()->user()->role !== 'admin')
            <div class="rounded-lg bg-yellow-50 p-3 text-sm text-yellow-700">Anda tidak memiliki izin untuk mengubah proses; hanya admin yang dapat mengubah.</div>
        @endif

        <div class="grid gap-4 mt-3">
            <div class="flex items-center gap-3">
                <input type="checkbox" id="cv_lolos" name="cv_lolos" value="1" class="h-4 w-4" {{ optional($proses)->cv_lolos ? 'checked' : '' }} {{ auth()->user() && auth()->user()->role === 'admin' ? '' : 'disabled' }}>
                <label for="cv_lolos" class="text-sm text-gray-700">CV Lolos</label>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Tanggal CV</label>
                <input type="date" name="tanggal_cv" class="mt-1 block w-full rounded-md border border-gray-200 px-3 py-2" value="{{ optional($proses)->tanggal_cv }}" {{ auth()->user() && auth()->user()->role === 'admin' ? '' : 'disabled' }}>
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" id="psikotes_lolos" name="psikotes_lolos" value="1" class="h-4 w-4" {{ optional($proses)->psikotes_lolos ? 'checked' : '' }} {{ auth()->user() && auth()->user()->role === 'admin' ? '' : 'disabled' }}>
                <label for="psikotes_lolos" class="text-sm text-gray-700">Psikotes Lolos</label>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Tanggal Psikotes</label>
                <input type="date" name="tanggal_psikotes" class="mt-1 block w-full rounded-md border border-gray-200 px-3 py-2" value="{{ optional($proses)->tanggal_psikotes }}" {{ auth()->user() && auth()->user()->role === 'admin' ? '' : 'disabled' }}>
            </div>

            <div class="flex justify-end mt-2">
                <button class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white" {{ auth()->user() && auth()->user()->role === 'admin' ? '' : 'disabled' }}>Simpan</button>
            </div>
        </div>
    </form>
</div>
@endsection
