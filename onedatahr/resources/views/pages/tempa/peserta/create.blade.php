@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Tambah Peserta TEMPA</h1>
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


    <form action="{{ route('tempa.peserta.store') }}" method="POST" class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        @csrf
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Nama Peserta</label>
                <input type="text" name="nama_peserta" value="{{ old('nama_peserta') }}" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" required>
                @error('nama_peserta') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">NIK Karyawan</label>
                <input type="text" name="nik_karyawan" value="{{ old('nik_karyawan') }}" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" required>
                @error('nik_karyawan') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Status Peserta</label>
                <select name="status_peserta" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" required>
                    <option value="1" {{ old('status_peserta') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="2" {{ old('status_peserta') == '2' ? 'selected' : '' }}>Pindah</option>
                    <option value="0" {{ old('status_peserta') == '0' ? 'selected' : '' }}>Keluar</option>
                </select>
                @error('status_peserta') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            <div id="keterangan_pindah_field" style="display: none;">
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Keterangan Pindah</label>
                <textarea name="keterangan_pindah" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" rows="3">{{ old('keterangan_pindah') }}</textarea>
                @error('keterangan_pindah') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            @if($isKetuaTempa)
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Kelompok <span class="text-red-500">*</span></label>
                <input type="text" name="nama_kelompok" value="{{ old('nama_kelompok') }}" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="Contoh: Kelompok A" required>
                @error('nama_kelompok') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Nama Mentor <span class="text-red-500">*</span></label>
                <input type="text" name="nama_mentor" value="{{ old('nama_mentor') }}" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="Contoh: Budi Santoso" required>
                @error('nama_mentor') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            @else
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Kelompok <span class="text-red-500">*</span></label>
                <select name="kelompok_id" id="kelompok_id" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" required>
                    <option value="">Pilih Kelompok</option>
                    @foreach($kelompoks as $kelompok)
                    <option value="{{ $kelompok->id_kelompok }}" data-mentor="{{ $kelompok->nama_mentor }}" data-ketua="{{ $kelompok->ketuaTempa?->name ?? '-' }}" {{ old('kelompok_id') == $kelompok->id_kelompok ? 'selected' : '' }}>
                        {{ $kelompok->nama_kelompok }} (Ketua: {{ $kelompok->ketuaTempa?->name ?? 'Tidak ada' }})
                    </option>
                    @endforeach
                </select>
                @error('kelompok_id') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Nama Mentor</label>
                <input type="text" id="nama_mentor" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" disabled placeholder="Otomatis terisi dari kelompok">
            </div>
            @endif
        </div>
        <div class="mt-6 flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700">
                Simpan
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.querySelector('select[name="status_peserta"]');
    const keteranganField = document.getElementById('keterangan_pindah_field');

    function toggleKeterangan() {
        if (statusSelect.value === '2') {
            keteranganField.style.display = 'block';
        } else {
            keteranganField.style.display = 'none';
        }
    }

    statusSelect.addEventListener('change', toggleKeterangan);
    toggleKeterangan(); // Initial check

    @if(!$isKetuaTempa)
    const kelompokSelect = document.getElementById('kelompok_id');
    const mentorInput = document.getElementById('nama_mentor');

    kelompokSelect.addEventListener('change', function() {
        const selectedOption = kelompokSelect.options[kelompokSelect.selectedIndex];
        const mentorName = selectedOption.getAttribute('data-mentor');
        if (mentorName) {
            mentorInput.value = mentorName;
        } else {
            mentorInput.value = '';
        }
    });
    @endif
});
</script>
@endsection
