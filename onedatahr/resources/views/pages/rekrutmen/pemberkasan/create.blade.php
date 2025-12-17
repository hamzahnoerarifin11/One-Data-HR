@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-2xl p-4">
    <h3 class="text-xl font-semibold mb-4">Tambah Pemberkasan</h3>
    <form method="POST" action="{{ route('rekrutmen.pemberkasan.store') }}">
        @csrf
        <div class="grid gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Kandidat</label>
                <select name="kandidat_id" class="mt-1 block w-full rounded-md border border-gray-200 px-3 py-2" required>
                    @if($kandidat_id)
                        <option value="{{ $kandidat_id }}">Pilihan kandidat awal ({{ $kandidat_id }})</option>
                    @endif
                    @foreach(\App\Models\Kandidat::orderBy('nama')->get() as $k)
                        <option value="{{ $k->id_kandidat }}">{{ $k->nama }} - {{ $k->posisi->nama_posisi ?? '-' }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Follow Up</label>
                <textarea name="follow_up" class="mt-1 block w-full rounded-md border border-gray-200 px-3 py-2"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Selesai Recruitment</label>
                <input type="date" name="selesai_recruitment" class="mt-1 block w-full rounded-md border border-gray-200 px-3 py-2">
            </div>

            <div class="flex justify-end mt-2">
                <button class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white">Simpan</button>
            </div>
        </div>
    </form>
</div>
@endsection
