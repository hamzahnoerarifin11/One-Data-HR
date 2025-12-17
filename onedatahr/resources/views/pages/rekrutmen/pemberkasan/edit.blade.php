@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-2xl p-4">
    <h3 class="text-xl font-semibold mb-4">Edit Pemberkasan</h3>
    <form method="POST" action="{{ route('rekrutmen.pemberkasan.update', $item->id_pemberkasan) }}">
        @csrf
        @method('PUT')
        @if(!auth()->user() || auth()->user()->role !== 'admin')
            <div class="rounded-lg bg-yellow-50 p-3 text-sm text-yellow-700">Anda tidak memiliki izin untuk mengubah pemberkasan; hanya admin yang dapat mengubah.</div>
        @endif

        <div class="grid gap-4 mt-3">
            <div>
                <label class="block text-sm font-medium text-gray-700">Follow Up</label>
                <textarea name="follow_up" class="mt-1 block w-full rounded-md border border-gray-200 px-3 py-2" {{ auth()->user() && auth()->user()->role === 'admin' ? '' : 'disabled' }}>{{ $item->follow_up }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Selesai Recruitment</label>
                <input type="date" name="selesai_recruitment" class="mt-1 block w-full rounded-md border border-gray-200 px-3 py-2" value="{{ $item->selesai_recruitment }}" {{ auth()->user() && auth()->user()->role === 'admin' ? '' : 'disabled' }}>
            </div>

            <div class="flex justify-end mt-2">
                <button class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white" {{ auth()->user() && auth()->user()->role === 'admin' ? '' : 'disabled' }}>Simpan</button>
            </div>
        </div>
    </form>
</div>
@endsection
