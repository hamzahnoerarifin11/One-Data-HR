@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Kelompok TEMPA</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Perbarui informasi kelompok TEMPA
            </p>
        </div>
        <a href="{{ route('tempa.kelompok.index') }}"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.05] transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali
        </a>
    </div>
    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-300 bg-red-50 p-4 text-sm text-red-700">
            <strong>Terjadi Kesalahan:</strong>
            <ul class="mt-2 list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tempa.kelompok.update', $kelompok->id_kelompok) }}" method="POST" class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Nama Kelompok <span class="text-red-500">*</span></label>
                <input type="text" name="nama_kelompok" value="{{ old('nama_kelompok', $kelompok->nama_kelompok) }}" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="Contoh: Kelompok A" required>
                @error('nama_kelompok') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Nama Mentor <span class="text-red-500">*</span></label>
                <input type="text" name="nama_mentor" value="{{ old('nama_mentor', $kelompok->nama_mentor) }}" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="Contoh: Budi Santoso" required>
                @error('nama_mentor') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Tempat <span class="text-red-500">*</span></label>
                <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                <select name="tempat" id="tempat_edit" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                           :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true" required>
                    <option value="pusat" {{ old('tempat', $kelompok->tempat) == 'pusat' ? 'selected' : '' }}>Pusat</option>
                    <option value="cabang" {{ old('tempat', $kelompok->tempat) == 'cabang' ? 'selected' : '' }}>Cabang</option>
                </select>
                <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </div>
                @error('tempat') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            <div id="keterangan_cabang_field_edit" style="display: {{ old('tempat', $kelompok->tempat) == 'cabang' ? 'block' : 'none' }};">
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Keterangan Cabang</label>
                <input type="text" name="keterangan_cabang" value="{{ old('keterangan_cabang', $kelompok->keterangan_cabang) }}" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="Contoh: Jakarta">
                @error('keterangan_cabang') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-6">
             <a href="{{ route('tempa.kelompok.index') }}"
                   class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.05] transition">Batal</a>
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700">
                Update
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tempatSelect = document.getElementById('tempat_edit');
    const keteranganCabangField = document.getElementById('keterangan_cabang_field_edit');

    function toggleKeteranganCabang() {
        if (tempatSelect.value === 'cabang') {
            keteranganCabangField.style.display = 'block';
        } else {
            keteranganCabangField.style.display = 'none';
        }
    }

    tempatSelect.addEventListener('change', toggleKeteranganCabang);
    toggleKeteranganCabang(); // Initial check
});
</script>
@endsection
