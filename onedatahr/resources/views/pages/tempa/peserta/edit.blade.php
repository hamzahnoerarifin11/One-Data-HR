@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Peserta TEMPA</h1>
    </div>

    <form action="{{ route('tempa.peserta.update', $peserta->id) }}" method="POST" class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Nama Peserta</label>
                <input type="text" name="nama_peserta" value="{{ old('nama_peserta', $peserta->nama_peserta) }}" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" required>
                @error('nama_peserta') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">NIK Karyawan</label>
                <input type="text" name="nik_karyawan" value="{{ old('nik_karyawan', $peserta->nik_karyawan) }}" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" required>
                @error('nik_karyawan') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Status Peserta</label>
                <select name="status_peserta" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" required>
                    <option value="1" {{ old('status_peserta', $peserta->status_peserta) == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="2" {{ old('status_peserta', $peserta->status_peserta) == '2' ? 'selected' : '' }}>Pindah</option>
                    <option value="0" {{ old('status_peserta', $peserta->status_peserta) == '0' ? 'selected' : '' }}>Keluar</option>
                </select>
                @error('status_peserta') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Kelompok</label>
                <select name="kelompok_id" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" required>
                    @foreach($kelompoks as $kelompok)
                    <option value="{{ $kelompok->id }}" {{ old('kelompok_id', $peserta->kelompok_id) == $kelompok->id ? 'selected' : '' }}>{{ $kelompok->nama_kelompok }}</option>
                    @endforeach
                </select>
                @error('kelompok_id') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Mentor</label>
                <select name="mentor_id" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" required>
                    @foreach($mentors as $mentor)
                    <option value="{{ $mentor->id }}" {{ old('mentor_id', $peserta->mentor_id) == $mentor->id ? 'selected' : '' }}>{{ $mentor->name }}</option>
                    @endforeach
                </select>
                @error('mentor_id') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700">
                Update
            </button>
        </div>
    </form>
</div>
@endsection
