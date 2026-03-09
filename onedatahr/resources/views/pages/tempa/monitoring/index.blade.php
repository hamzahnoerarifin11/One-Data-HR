@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Monitoring TEMPA
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">
                Rekapitulasi dan monitoring program TEMPA secara lengkap dan mudah
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800 dark:border-green-900 dark:bg-green-900/20 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800 dark:border-red-900 dark:bg-red-900/20 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    {{-- Statistik Utama --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        {{-- Persentase Nasional --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Persentase Nasional</p>
                    <h3 class="text-2xl font-bold"
                        :class="{
                            'text-green-700 dark:text-green-400': {{ $persentaseNasional }} >= 80,
                            'text-yellow-700 dark:text-yellow-400': {{ $persentaseNasional }} >= 60 && {{ $persentaseNasional }} < 80,
                            'text-red-700 dark:text-red-400': {{ $persentaseNasional }} < 60
                        }">
                        {{ number_format($persentaseNasional, 1) }}%
                    </h3>
                </div>
                <div class="p-3 bg-blue-50 text-blue-600 rounded-full dark:bg-blue-900/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <rect width="18" height="18" x="3" y="3" rx="3" stroke-width="2" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 14l3-3 3 3 4-4" />
                        <path stroke-linecap="round" stroke-width="2" d="M7 17h10" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                Rata-rata kehadiran peserta aktif
            </div>
        </div>

        {{-- Total Peserta --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Peserta</p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $pesertas->count() }}</h3>
                </div>
                <div class="p-3 bg-orange-50 text-orange-600 rounded-full dark:bg-orange-900/30">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                Peserta dalam filter saat ini
            </div>
        </div>

        {{-- Total Kelompok --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Kelompok</p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ count($rekapKelompok) }}</h3>
                </div>
                <div class="p-3 bg-purple-50 text-purple-600 rounded-full dark:bg-purple-900/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                Kelompok dengan peserta aktif
            </div>
        </div>

        {{-- Peserta Aktif --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Peserta Aktif</p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $pesertas->where('status_peserta', 1)->count() }}</h3>
                </div>
                <div class="p-3 bg-green-50 text-green-600 rounded-full dark:bg-green-900/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                Peserta dengan status aktif
            </div>
        </div>
    </div>
    @php
        $rekapKelompokJs = collect($rekapKelompok)->values();
    @endphp

    {{-- Rekap Kelompok --}}
    @if(!empty($rekapKelompok))
<div
    x-data="rekapKelompokPagination()"
    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6"
>
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            Rekapitulasi per Kelompok
        </h3>
    </div>

    <!-- GRID REKAP -->
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <template x-for="rekap in paginated" :key="rekap.nama_kelompok">
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <div class="flex justify-between items-start mb-2">
                    <h4 class="font-medium text-gray-900 dark:text-white" x-text="rekap.nama_kelompok"></h4>

                    <span class="text-xs">
                        <template x-if="rekap.lokasi === 'pusat'">
                            <span class="inline-flex items-center px-2 py-0.5 rounded font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                Pusat
                            </span>
                        </template>

                        <template x-if="rekap.lokasi === 'cabang'">
                            <span class="inline-flex items-center px-2 py-0.5 rounded font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                Cabang
                                <template x-if="rekap.keterangan_cabang">
                                    <span class="ml-1" x-text="'- ' + rekap.keterangan_cabang"></span>
                                </template>
                            </span>
                        </template>
                    </span>
                </div>

                <div class="space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">Mentor:</span>
                        <span class="font-medium text-gray-900 dark:text-white" x-text="rekap.nama_mentor"></span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">Peserta Aktif:</span>
                        <span class="font-medium text-gray-900 dark:text-white"
                              x-text="rekap.jumlah_peserta_aktif + '/' + rekap.total_peserta">
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">Persentase:</span>
                        <span
                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                            :class="{
                                'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': rekap.persentase >= 80,
                                'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400': rekap.persentase >= 60 && rekap.persentase < 80,
                                'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': rekap.persentase < 60
                            }"
                            x-text="rekap.persentase.toFixed(1) + '%'">
                        </span>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- PAGINATION -->
    <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 dark:border-gray-700">
        <div class="text-sm text-gray-600 dark:text-gray-400">
            Showing <span x-text="startItem"></span>
            to <span x-text="endItem"></span>
            of <span x-text="data.length"></span> entries
        </div>

        <div class="flex items-center gap-2">
            <button @click="prevPage"
                    :disabled="page === 1"
                    class="rounded-lg border px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white disabled:opacity-50">
                Prev
            </button>

            <template x-for="p in displayedPages" :key="p">
                <button x-show="p !== '...'"
                        @click="goToPage(p)"
                        :class="page === p ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-blue-500/[0.08] hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-500'"
                        class="flex h-8 w-8 items-center justify-center rounded-lg text-theme-sm font-medium"
                        x-text="p">
                </button>
                <span x-show="p === '...'" class="flex h-8 w-8 items-center justify-center text-gray-500">...</span>
            </template>

            <button @click="nextPage"
                    :disabled="page === totalPages"
                    class="rounded-lg border px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white disabled:opacity-50">
                Next
            </button>
        </div>
    </div>
</div>
@endif



    {{-- Filter Form --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <form method="GET" x-data="filterLokasiKelompok()" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun</label>
                <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                <select name="tahun" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                           :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true" required>
                    @for($y = date('Y')-2; $y <= date('Y')+1; $y++)
                        <option value="{{ $y }}" {{ request('tahun', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lokasi</label>
                <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">

                <select name="lokasi" x-model="lokasi"
                        @change="filterKelompok()" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                           :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true" >
                        <option value="">Semua Lokasi</option>
                    @foreach($listLokasi ?? [] as $lok)
                        <option value="{{ $lok }}" {{ request('lokasi') == $lok ? 'selected' : '' }}>{{ $lok }}</option>
                    @endforeach
                </select>
                <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kelompok</label>
                <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">

                <select name="kelompok" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                           :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                        <option value="">Semua Kelompok</option>
                        <template x-for="kel in filteredKelompok" :key="kel.id_kelompok">
                            <option :value="kel.id_kelompok"
                                    x-text="kel.nama_kelompok"
                                    :selected="kel.id_kelompok == '{{ request('kelompok') }}'">
                            </option>
                        </template>
                    <!-- @foreach($listKelompok ?? [] as $kel)
                        <option value="{{ $kel->id_kelompok }}" {{ request('kelompok') == $kel->id_kelompok ? 'selected' : '' }}>{{ $kel->nama_kelompok }}</option>
                    @endforeach -->
                </select>
                <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                <select name="status" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                           :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true" >

                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Pindah</option>
                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Keluar</option>
                </select>
                <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari Peserta</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama/NIK..." class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filter
                </button>
                <a href="{{ route('tempa.monitoring.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </a>
            </div>
        </form>
    </div>

    {{-- Tabel Peserta --}}
    @php
        // Menyiapkan data untuk Alpine.js
        $tableData = $pesertas->map(fn($row) => [
            'id' => $row->id_peserta,
            'nama_peserta' => $row->nama_peserta,
            'nik' => $row->nik_karyawan,
            'status_val' => $row->status_peserta,
            'status_label' => $row->status_label,
            'keterangan_pindah' => $row->keterangan_pindah ?? '-',
            'kelompok' => $row->kelompok->nama_kelompok ?? '-',
            'mentor' => $row->kelompok->nama_mentor ?? '-',
            'lokasi' => $row->kelompok->tempat ?? '-',
            'keterangan_cabang' => $row->kelompok->keterangan_cabang ?? '-',
            'total_hadir' => $row->total_hadir,
            'total_pertemuan' => $row->total_pertemuan,
            'persentase' => number_format($row->persentase_kehadiran, 1),
        ])->values();
    @endphp

    <div x-data="monitoringTable()" class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

        <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4">
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">Show</span>
                <div class="relative z-20">
                    <select
                        x-model.number="perPage"
                        @change="resetPage"
                        class="h-11 w-20 appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-8 text-sm text-gray-800 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    >
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500">
                        <svg class="fill-current" width="18" height="18" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" /></svg>
                    </span>
                </div>
                <span class="text-sm text-gray-500 dark:text-gray-400">entries</span>
            </div>

            <div class="relative">
                <button class="absolute text-gray-500 -translate-y-1/2 left-4 top-1/2">
                    <svg class="h-5 w-5 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.04199 9.37363C3.04199 5.87693 5.87735 3.04199 9.37533 3.04199C12.8733 3.04199 15.7087 5.87693 15.7087 9.37363C15.7087 12.8703 12.8733 15.7053 9.37533 15.7053C5.87735 15.7053 3.04199 12.8703 3.04199 9.37363ZM9.37533 1.54199C5.04926 1.54199 1.54199 5.04817 1.54199 9.37363C1.54199 13.6991 5.04926 17.2053 9.37533 17.2053C11.2676 17.2053 13.0032 16.5344 14.3572 15.4176L17.1773 18.238C17.4702 18.5309 17.945 18.5309 18.2379 18.238C18.5308 17.9451 18.5309 17.4703 18.238 17.1773L15.4182 14.3573C16.5367 13.0033 17.2087 11.2669 17.2087 9.37363C17.2087 5.04817 13.7014 1.54199 9.37533 1.54199Z"/></svg>
                </button>
                <input
                    x-model="search"
                    @input="resetPage"
                    type="text"
                    placeholder="Cari nama, NIK, kelompok..."
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-12 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 xl:w-[300px]"
                />
            </div>
        </div>

        <div class="max-w-full overflow-x-auto">
            <table class="w-full min-w-full border-collapse">
                <thead>
                    <tr class="border-y border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 w-12">#</th>
                        <th @click="sortBy('nama_peserta')" class="px-6 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer hover:text-blue-600 transition">
                            <div class="flex items-center gap-1">
                                Peserta
                                <svg :class="sortCol === 'nama_peserta' ? (sortDir === 'asc' ? 'rotate-0' : 'rotate-180') : 'opacity-20'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </div>
                        </th>
                        <th @click="sortBy('status_label')" class="px-6 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer hover:text-blue-600">
                            <div class="flex items-center gap-1">
                                Status
                                <svg :class="sortCol === 'status_label' ? (sortDir === 'asc' ? 'rotate-0' : 'rotate-180') : 'opacity-20'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </div>
                        </th>
                        <th @click="sortBy('kelompok')" class="px-6 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer hover:text-blue-600">
                            <div class="flex items-center gap-1">
                                Kelompok
                                <svg :class="sortCol === 'kelompok' ? (sortDir === 'asc' ? 'rotate-0' : 'rotate-180') : 'opacity-20'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </div>
                        </th>
                        <th @click="sortBy('mentor')" class="px-6 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer hover:text-blue-600">
                            <div class="flex items-center gap-1">
                                Mentor
                                <svg :class="sortCol === 'mentor' ? (sortDir === 'asc' ? 'rotate-0' : 'rotate-180') : 'opacity-20'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </div>
                        </th>
                        <th @click="sortBy('lokasi')" class="px-6 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer hover:text-blue-600">
                            <div class="flex items-center gap-1">
                                Lokasi
                                <svg :class="sortCol === 'lokasi' ? (sortDir === 'asc' ? 'rotate-0' : 'rotate-180') : 'opacity-20'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-400">Kehadiran</th>
                        <th class="px-6 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-400">Persentase</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="(row, index) in paginated" :key="row.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20 transition">
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400" x-text="(page - 1) * perPage + index + 1"></td>
                            <td class="px-6 py-4 text-sm">
                                <div class="font-medium text-gray-900 dark:text-white" x-text="row.nama_peserta"></div>
                                <div class="text-xs text-gray-500 dark:text-gray-400" x-text="row.nik || '-'"></div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex flex-col gap-1.5">

                                    <!-- BADGE STATUS -->
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold tracking-wide uppercase"
                                        :class="{
                                            'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': row.status_val == 1,
                                            'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400': row.status_val == 2,
                                            'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': row.status_val != 1 && row.status_val != 2
                                        }"
                                        x-text="row.status_label">
                                    </span>

                                    <!-- KETERANGAN PINDAH -->
                                    <template x-if="row.status_val == 2 && row.keterangan_pindah && row.keterangan_pindah !== '-'">
                                        <div class="flex items-start gap-1 text-gray-500 dark:text-gray-400">
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="text-[11px] leading-tight italic"
                                                x-text="row.keterangan_pindah">
                                            </span>
                                        </div>
                                    </template>

                                </div>
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300" x-text="row.kelompok"></td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300" x-text="row.mentor"></td>
                            <!-- <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300" x-text="row.lokasi"></td> -->
                            <td class="px-2 py-4 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white">
                                <span x-show="row.lokasi === 'pusat'" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                    Pusat
                                </span>
                                <span x-show="row.lokasi === 'cabang'" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                    Cabang<span x-show="row.keterangan_cabang"> - </span><span x-text="row.keterangan_cabang"></span>
                                </span>
                                <span x-show="row.lokasi !== 'pusat' && row.lokasi !== 'cabang'" class="text-gray-500">-</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-center">
                                <div class="font-semibold text-gray-900 dark:text-white" x-text="row.total_hadir + '/' + row.total_pertemuan"></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                    :class="{
                                        'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': parseFloat(row.persentase) >= 80,
                                        'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400': parseFloat(row.persentase) >= 60 && parseFloat(row.persentase) < 80,
                                        'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': parseFloat(row.persentase) < 60
                                    }"
                                    x-text="row.persentase + '%'">
                                </span>
                            </td>
                        </tr>
                    </template>
                    <template x-if="paginated.length === 0">
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.009-5.203-2.709"/>
                                    </svg>
                                    <span>Data peserta tidak ditemukan</span>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between px-6 py-4">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Showing <span x-text="startItem"></span> to <span x-text="endItem"></span> of <span x-text="filteredData.length"></span> entries
            </div>

            <div class="flex items-center gap-2">
                <button @click="prevPage" :disabled="page === 1" class="rounded-lg border px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white disabled:opacity-50">Prev</button>

                <template x-for="p in displayedPages" :key="p">
                    <button x-show="p !== '...'" @click="goToPage(p)" :class="page === p ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-blue-500/[0.08] hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-500'" class="flex h-8 w-8 items-center justify-center rounded-lg text-theme-sm font-medium" x-text="p"></button>
                    <span x-show="p === '...'" class="flex h-8 w-8 items-center justify-center text-gray-500">...</span>
                </template>

                <button @click="nextPage" :disabled="page === totalPages" class="rounded-lg border px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white disabled:opacity-50">Next</button>
            </div>
        </div>
    </div>
</div>

<script>
function filterLokasiKelompok() {
    return {
        lokasi: '{{ request('lokasi') }}',

        kelompokAll: @json($listKelompok),

        filteredKelompok: [],

        init() {
            this.filterKelompok();
        },

        filterKelompok() {
            if (!this.lokasi) {
                // Jika lokasi kosong / semua → tampilkan semua kelompok
                this.filteredKelompok = this.kelompokAll;
            } else {
                // Jika pilih pusat / cabang → filter berdasarkan lokasi
                this.filteredKelompok = this.kelompokAll.filter(kel =>
                    kel.tempat && kel.tempat.toLowerCase() === this.lokasi.toLowerCase()
                );
            }
        }
    }
}
</script>

<script>
function monitoringTable() {
    return {
        data: @json($tableData),
        search: '',
        sortCol: 'nama_peserta',
        sortDir: 'asc',
        perPage: 10,
        page: 1,

        get filteredData() {
            let filtered = this.data.filter(item => {
                return item.nama_peserta.toLowerCase().includes(this.search.toLowerCase()) ||
                       item.nik.toLowerCase().includes(this.search.toLowerCase()) ||
                       item.kelompok.toLowerCase().includes(this.search.toLowerCase()) ||
                       item.mentor.toLowerCase().includes(this.search.toLowerCase()) ||
                       item.lokasi.toLowerCase().includes(this.search.toLowerCase());
            });

            // Sort
            filtered.sort((a, b) => {
                let aVal = a[this.sortCol];
                let bVal = b[this.sortCol];

                if (typeof aVal === 'string') aVal = aVal.toLowerCase();
                if (typeof bVal === 'string') bVal = bVal.toLowerCase();

                if (this.sortDir === 'asc') {
                    return aVal > bVal ? 1 : -1;
                } else {
                    return aVal < bVal ? 1 : -1;
                }
            });

            return filtered;
        },

        get totalPages() {
            return Math.ceil(this.filteredData.length / this.perPage);
        },

        get paginated() {
            const start = (this.page - 1) * this.perPage;
            const end = start + this.perPage;
            return this.filteredData.slice(start, end);
        },

        get startItem() {
            return (this.page - 1) * this.perPage + 1;
        },

        get endItem() {
            return Math.min(this.page * this.perPage, this.filteredData.length);
        },

        get displayedPages() {
            const pages = [];
            const total = this.totalPages;
            const current = this.page;

            if (total <= 7) {
                for (let i = 1; i <= total; i++) {
                    pages.push(i);
                }
            } else {
                if (current <= 4) {
                    pages.push(1, 2, 3, 4, 5, '...', total);
                } else if (current >= total - 3) {
                    pages.push(1, '...', total - 4, total - 3, total - 2, total - 1, total);
                } else {
                    pages.push(1, '...', current - 1, current, current + 1, '...', total);
                }
            }

            return pages;
        },

        sortBy(col) {
            if (this.sortCol === col) {
                this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortCol = col;
                this.sortDir = 'asc';
            }
            this.resetPage();
        },

        resetPage() {
            this.page = 1;
        },

        prevPage() {
            if (this.page > 1) {
                this.page--;
            }
        },

        nextPage() {
            if (this.page < this.totalPages) {
                this.page++;
            }
        },

        goToPage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.page = page;
            }
        },

        formatCabang(lokasi, keteranganCabang) {
            if (!lokasi || lokasi === '-') {
                return '-';
            }
            // Jika lokasi mengandung kata 'cabang' (case insensitive), gunakan keterangan_cabang
            if (lokasi.toLowerCase().includes('cabang')) {
                return keteranganCabang && keteranganCabang !== '-' ? keteranganCabang : lokasi;
            }
            // Jika tidak mengandung 'cabang', tampilkan lokasi biasa
            return lokasi;
        }
    }
}
</script>
<script>
function rekapKelompokPagination() {
    return {
        data: @json($rekapKelompokJs),
        page: 1,
        perPage: 6,

        get totalPages() {
            return Math.ceil(this.data.length / this.perPage);
        },

        get paginated() {
            const start = (this.page - 1) * this.perPage;
            return this.data.slice(start, start + this.perPage);
        },

        get startItem() {
            return (this.page - 1) * this.perPage + 1;
        },

        get endItem() {
            return Math.min(this.page * this.perPage, this.data.length);
        },

        get displayedPages() {
            const pages = [];
            const total = this.totalPages;
            const current = this.page;

            if (total <= 7) {
                for (let i = 1; i <= total; i++) pages.push(i);
            } else if (current <= 4) {
                pages.push(1,2,3,4,5,'...',total);
            } else if (current >= total - 3) {
                pages.push(1,'...',total-4,total-3,total-2,total-1,total);
            } else {
                pages.push(1,'...',current-1,current,current+1,'...',total);
            }

            return pages;
        },

        prevPage() {
            if (this.page > 1) this.page--;
        },

        nextPage() {
            if (this.page < this.totalPages) this.page++;
        },

        goToPage(p) {
            if (p >= 1 && p <= this.totalPages) this.page = p;
        }
    }
}
</script>


@endsection
