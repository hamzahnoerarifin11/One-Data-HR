@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Tambah Absensi TEMPA</h1>
    </div>

    <form action="{{ route('tempa.absensi.store') }}" method="POST" enctype="multipart/form-data" class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        @csrf
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Peserta</label>
                <select name="peserta_id" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" required>
                    @foreach($pesertas as $peserta)
                    <option value="{{ $peserta->id }}">{{ $peserta->nama_peserta }}</option>
                    @endforeach
                </select>
                @error('peserta_id') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Bulan</label>
                <select name="bulan" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" required>
                    @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}">{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                    @endfor
                </select>
                @error('bulan') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Tahun</label>
                <input type="number" name="tahun" value="{{ date('Y') }}" min="2020" max="{{ date('Y') + 1 }}" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" required>
                @error('tahun') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Pertemuan Ke</label>
                <select name="pertemuan_ke" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" required>
                    @for($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
                @error('pertemuan_ke') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Status Hadir</label>
                <select name="status_hadir" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" required>
                    <option value="1">Hadir</option>
                    <option value="0">Tidak Hadir</option>
                </select>
                @error('status_hadir') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Bukti Foto</label>
                <input type="file" name="bukti_foto" accept="image/*" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" required>
                @error('bukti_foto') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
