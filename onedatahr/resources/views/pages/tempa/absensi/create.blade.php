@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Tambah Data Absensi TEMPA</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Input data absensi peserta TEMPA untuk satu tahun</p>
        </div>
        <a href="{{ route('tempa.absensi.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.05] transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali
        </a>
    </div>

    <form action="{{ route('tempa.absensi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Informasi Dasar -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Informasi Dasar</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Peserta -->
                <div>
                    <label for="id_peserta" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Pilih Peserta <span class="text-red-500">*</span>
                    </label>
                    <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                    <select id="id_peserta" name="id_peserta"
                             class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true"
                                required>
                        <option value="">Pilih Peserta</option>
                        @foreach($pesertas as $peserta)
                            <option value="{{ $peserta->id_peserta }}"
                                    data-kelompok="{{ $peserta->kelompok->nama_kelompok ?? '-' }}"
                                    data-mentor="{{ $peserta->kelompok->nama_mentor ?? '-' }}"
                                    data-lokasi="{{ $peserta->kelompok->tempat ?? '-' }}"
                                    data-keterangan="{{ $peserta->kelompok->keterangan_cabang ?? '' }}">
                                {{ $peserta->nama_peserta }} - {{ $peserta->nik_karyawan }}
                            </option>
                        @endforeach
                    </select>
                    <span
                                class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                    </div>
                    @error('id_peserta')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tahun Absensi -->
                <div>
                    <label for="tahun_absensi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tahun Absensi <span class="text-red-500">*</span>
                    </label>
                    <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                    <select id="tahun_absensi" name="tahun_absensi"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true"
                                required>
                        @for($year = date('Y') - 1; $year <= date('Y') + 1; $year++)
                            <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                    <span
                                class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                    </div>
                    @error('tahun_absensi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Info Peserta Terpilih -->
            <div id="peserta-info" class="mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg hidden">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Informasi Peserta Terpilih</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-600 dark:text-gray-400">Kelompok:</span>
                        <span id="info-kelompok" class="ml-2 text-gray-900 dark:text-white">-</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-600 dark:text-gray-400">Mentor:</span>
                        <span id="info-mentor" class="ml-2 text-gray-900 dark:text-white">-</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-600 dark:text-gray-400">Lokasi:</span>
                        <span id="info-lokasi" class="ml-2 text-gray-900 dark:text-white">-</span>
                    </div>
                </div>
            </div>
        </div>

       <div class="rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Data Absensi Bulanan</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Pilih status kehadiran untuk setiap minggu (1-5 minggu per bulan). Kosongkan jika tidak ada pertemuan TEMPA.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $months = [
                        'jan' => 'Januari', 'feb' => 'Februari', 'mar' => 'Maret',
                        'apr' => 'April', 'mei' => 'Mei', 'jun' => 'Juni',
                        'jul' => 'Juli', 'agu' => 'Agustus', 'sep' => 'September',
                        'okt' => 'Oktober', 'nov' => 'November', 'des' => 'Desember'
                    ];
                @endphp

                @foreach($months as $key => $name)
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b pb-2">{{ $name }}</h3>
                    @for($i = 1; $i <= 5; $i++)
                        <div class="flex items-center justify-between">
                            <label for="absensi_{{ $key }}_{{ $i }}" class="text-sm text-gray-700 dark:text-gray-300">
                                Minggu {{ $i }}
                            </label>
                            <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                                <select id="absensi_{{ $key }}_{{ $i }}" name="absensi[{{ $key }}][{{ $i }}]"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                        :class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                                        @change="isOptionSelected = true">
                                    <option value="">Kosong</option>
                                    <option value="hadir">Hadir</option>
                                    <option value="tidak_hadir">Tidak Hadir</option>
                                </select>
                                <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                                    <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    @endfor
                </div>
                @endforeach
            </div>
        </div>
        <!-- Upload Bukti Foto -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Bukti Foto</h2>

            <div>
                <label for="bukti_foto" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Upload Bukti Foto Absensi
                </label>
                <input type="file" id="bukti_foto" name="bukti_foto"
                       class="focus:border-ring-brand-300 shadow-theme-xs focus:file:ring-brand-300 h-11 w-full overflow-hidden rounded-lg border border-gray-300 bg-transparent text-sm text-gray-500 transition-colors file:mr-5 file:border-collapse file:cursor-pointer file:rounded-l-lg file:border-0 file:border-r file:border-solid file:border-gray-200 file:bg-gray-50 file:py-3 file:pr-3 file:pl-3.5 file:text-sm file:text-gray-700 placeholder:text-gray-400 hover:file:bg-gray-100 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 dark:text-white/90 dark:file:border-gray-800 dark:file:bg-white/[0.03] dark:file:text-gray-400 dark:placeholder:text-gray-400"
                       accept="image/*">
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Upload foto bukti absensi (format: JPG, PNG, maksimal 2MB)
                </p>
                @error('bukti_foto')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('tempa.absensi.index') }}"
               class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                Batal
            </a>
            <button type="submit"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">
                Simpan Data Absensi
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('id_peserta').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const pesertaInfo = document.getElementById('peserta-info');

    if (this.value) {
        document.getElementById('info-kelompok').textContent = selectedOption.getAttribute('data-kelompok') || '-';
        document.getElementById('info-mentor').textContent = selectedOption.getAttribute('data-mentor') || '-';

        const tempat = selectedOption.getAttribute('data-lokasi') || '-';
        const keterangan = selectedOption.getAttribute('data-keterangan') || '';
        const lokasiText = tempat === 'cabang' && keterangan ? `Cabang - ${keterangan}` : tempat.charAt(0).toUpperCase() + tempat.slice(1);
        document.getElementById('info-lokasi').textContent = lokasiText;

        pesertaInfo.classList.remove('hidden');
    } else {
        pesertaInfo.classList.add('hidden');
    }
});
</script>
@endsection
