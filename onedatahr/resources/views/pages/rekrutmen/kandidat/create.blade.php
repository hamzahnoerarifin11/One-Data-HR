@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-2xl p-4">
    <h3 class="text-xl font-semibold mb-4">Tambah Kandidat</h3>

    <form method="POST" action="{{ route('rekrutmen.kandidat.store') }}">
        @csrf
        <div class="grid gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nama</label>
                <input type="text" name="nama" class="mt-1 block w-full rounded-md border border-gray-200 px-3 py-2" required />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Posisi</label>
                <select name="posisi_id" class="mt-1 block w-full rounded-md border border-gray-200 px-3 py-2" required>
                    @foreach($posisis as $pos)
                        <option value="{{ $pos->id_posisi }}">{{ $pos->nama_posisi }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end mt-2">
                <button class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white shadow hover:bg-primary-dark transition">Simpan</button>
            </div>
        </div>
    </form>
</div>
@endsection
