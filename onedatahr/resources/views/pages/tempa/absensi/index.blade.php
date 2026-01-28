@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    @php
        $namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        // Prepare data untuk Alpine.js
        $tableData = $absensis->map(function($absensi) {
            // Prepare attendance data for all months - ensure it's always an array of 5 elements
            $attendanceData = [];
            $months = ['jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'agu', 'sep', 'okt', 'nov', 'des'];
            $absensiData = $absensi->absensi_data ?? [];
            foreach ($months as $month) {
                // Ensure each month has exactly 5 elements (weeks 0-4)
                $monthData = $absensiData[$month] ?? [];
                if (!is_array($monthData)) {
                    $monthData = [];
                }
                // Pad or truncate to exactly 5 elements
                $monthData = array_pad($monthData, 5, null);
                $monthData = array_slice($monthData, 0, 5);
                $attendanceData[$month] = $monthData;
            }

            $peserta = $absensi->peserta;
            $kelompok = $peserta ? $peserta->kelompok : null;

            return [
                'id' => $absensi->id_absensi,
                'nama_peserta' => $peserta ? $peserta->nama_peserta : '-',
                'nik_karyawan' => $peserta ? $peserta->nik_karyawan : '-',
                'status_peserta' => $peserta ? $peserta->status_peserta : null,
                'keterangan_pindah' => $peserta ? $peserta->keterangan_pindah : '',
                'nama_kelompok' => $kelompok ? $kelompok->nama_kelompok : '-',
                'nama_mentor' => $kelompok ? $kelompok->nama_mentor : '-',
                'tempat' => $kelompok ? $kelompok->tempat : '-',
                'keterangan_cabang' => $kelompok ? $kelompok->keterangan_cabang : '',
                'total_hadir' => $absensi->total_hadir ?? 0,
                'total_pertemuan' => $absensi->total_pertemuan ?? 0,
                'persentase' => $absensi->persentase ?? 0,
                'bukti_foto' => $absensi->bukti_foto,
                'attendance_data' => $attendanceData,
                'raw_absensi_data' => $absensi->absensi_data, // Keep original for debugging
            ];
        })->values();
    @endphp

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Absensi TEMPA
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">
                Data absensi peserta TEMPA per tahun
            </p>
        </div>

        <div class="flex items-center gap-3">
            <!-- Filter Tahun -->
            <div class="relative">
                <select id="tahunFilter" onchange="changeFilter()"
                        class="h-11 w-24 appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-8 text-sm text-gray-800 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    @for($year = date('Y') - 2; $year <= date('Y') + 1; $year++)
                        <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endfor
                </select>
                <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500">
                    <svg class="fill-current" width="18" height="18" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" /></svg>
                </span>
            </div>

            <!-- Filter Bulan -->
            <div class="relative">
                <select id="bulanFilter" onchange="changeFilter()"
                        class="h-11 w-32 appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-8 text-sm text-gray-800 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="">Semua Bulan</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>{{ $namaBulan[$i-1] }}</option>
                    @endfor
                </select>
                <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500">
                    <svg class="fill-current" width="18" height="18" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" /></svg>
                </span>
            </div>

            @can('createTempaAbsensi')
            <a href="{{ route('tempa.absensi.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Absensi
            </a>
            @endcan
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Cari Peserta
                </label>
                <div class="relative">
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                           placeholder="Nama peserta atau NIK..."
                           onkeyup="if(event.key === 'Enter') changeFilter()"
                           class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-gray-800 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500">
            <!-- class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-12 pr-4 text-sm text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 xl:w-[300px]" -->
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <button type="button" onclick="changeFilter()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Filter Lokasi -->
            <div>
                <label for="lokasiFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Lokasi
                </label>
                <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                <select id="lokasiFilter" name="lokasi" onchange="changeFilter()"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                    <option value="">Semua Lokasi</option>
                    <option value="pusat" {{ request('lokasi') == 'pusat' ? 'selected' : '' }}>Pusat</option>
                    <option value="cabang" {{ request('lokasi') == 'cabang' ? 'selected' : '' }}>Cabang</option>
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
            </div>

            <!-- Filter Kelompok -->
            <div>
                <label for="kelompokFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Kelompok
                </label>
                <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                <select id="kelompokFilter" name="kelompok" onchange="changeFilter()"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                        <option value="">Semua Kelompok</option>
                    @foreach($kelompoks as $kelompok)
                        <option value="{{ $kelompok->id_kelompok }}" {{ request('kelompok') == $kelompok->id_kelompok ? 'selected' : '' }}>
                            {{ $kelompok->nama_kelompok }}
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
            </div>

             <!-- Filter Status -->
            <div>
                <label for="statusFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Status Peserta
                </label>
                <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                <select id="statusFilter" name="status" onchange="changeFilter()"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Pindah</option>
                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Keluar</option>
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
            </div>

            <!-- Clear Filters -->
            <div class="flex items-end">
                <button type="button" onclick="clearFilters()"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.05] transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset Filter
                </button>
            </div>
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

    <div class="rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden" x-data="absensiTable()">

        <!-- Header Info -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Data Absensi Tahun {{ $tahun }}
                        @if($bulan)
                            - {{ $namaBulan[$bulan-1] }}
                        @endif
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Total: <span x-text="filtered.length"></span> peserta
                        (Menampilkan <span x-text="startItem"></span>-<span x-text="endItem"></span> dari <span x-text="filtered.length"></span>)
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Show</span>

                    <div x-data="{ isOptionSelected: false }" class="relative z-20">
                        <select
                            x-model.number="perPage"
                            @change="resetPage(); isOptionSelected = true"
                            class="h-11 w-20 appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-8 text-sm text-gray-800 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                            :class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                        >
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>

                        <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500 dark:text-gray-400">
                            <svg class="fill-current" width="18" height="18" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                            </svg>
                        </span>
                    </div>

                    <span class="text-sm text-gray-500 dark:text-gray-400">entries</span>
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Terakhir diperbarui: {{ now()->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="block md:hidden">
            <template x-for="absensi in paginated" :key="absensi.id">
            <div class="border-b border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white" x-text="absensi.nama_peserta"></h3>
                            <span x-show="absensi.status_peserta == 1" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                Aktif
                            </span>
                            <span x-show="absensi.status_peserta == 2" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                Pindah<span x-show="absensi.keterangan_pindah"> - </span><span x-text="absensi.keterangan_pindah"></span>
                            </span>
                            <span x-show="absensi.status_peserta != 1 && absensi.status_peserta != 2" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                Keluar
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm mb-3">
                            <div>
                                <span class="font-medium text-gray-600 dark:text-gray-400">NIK:</span>
                                <span class="ml-2 text-gray-900 dark:text-white" x-text="absensi.nik_karyawan"></span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600 dark:text-gray-400">Kelompok:</span>
                                <span class="ml-2 text-gray-900 dark:text-white" x-text="absensi.nama_kelompok"></span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600 dark:text-gray-400">Mentor:</span>
                                <span class="ml-2 text-gray-900 dark:text-white" x-text="absensi.nama_mentor"></span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600 dark:text-gray-400">Lokasi:</span>
                                <span class="ml-2 text-gray-900 dark:text-white">
                                    <span x-show="absensi.tempat === 'pusat'">Pusat</span>
                                    <span x-show="absensi.tempat === 'cabang'">
                                        Cabang<span x-show="absensi.keterangan_cabang"> - </span><span x-text="absensi.keterangan_cabang"></span>
                                    </span>
                                    <span x-show="absensi.tempat !== 'pusat' && absensi.tempat !== 'cabang'">-</span>
                                </span>
                            </div>
                        </div>

                        <!-- Absensi Summary -->
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 mb-3">
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <div class="text-lg font-bold text-green-600 dark:text-green-400" x-text="absensi.total_hadir"></div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">Hadir</div>
                                </div>
                                <div>
                                    <div class="text-lg font-bold text-blue-600 dark:text-blue-400" x-text="absensi.total_pertemuan"></div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">Total</div>
                                </div>
                                <div>
                                    <div class="text-lg font-bold text-purple-600 dark:text-purple-400" x-text="absensi.persentase + '%'"></div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">Persen</div>
                                </div>
                            </div>
                        </div>

                        <!-- Bukti Foto -->
                        <div x-show="absensi.bukti_foto" class="mb-3">
                            <a :href="`{{ url('storage') }}/${absensi.bukti_foto}`" target="_blank" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Lihat Bukti Foto
                            </a>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col gap-2 ml-4">
                        @can('viewTempaAbsensi')
                        <a :href="`{{ url('tempa/absensi') }}/${absensi.id}`" class="inline-flex items-center justify-center rounded-lg bg-blue-50 p-2 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/40 transition" title="Lihat Detail">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        @endcan

                        @can('editTempaAbsensi')
                        <a :href="`{{ url('tempa/absensi') }}/${absensi.id}/edit`" class="inline-flex items-center justify-center rounded-lg bg-yellow-50 p-2 text-yellow-600 hover:bg-yellow-100 dark:bg-yellow-900/20 dark:text-yellow-400 dark:hover:bg-yellow-900/40 transition" title="Edit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                        @endcan

                        @can('deleteTempaAbsensi')
                        <form :action="`{{ url('tempa/absensi') }}/${absensi.id}`" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data absensi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-red-50 p-2 text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 transition" title="Hapus">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
            </template>

            <div x-show="paginated.length === 0" class="px-6 py-12 text-center">
                <div class="flex flex-col items-center">
                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Belum ada data absensi</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Data absensi untuk tahun {{ $tahun }} belum tersedia.</p>
                    @can('createTempaAbsensi')
                    <a href="{{ route('tempa.absensi.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Tambah Data Absensi
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider sticky left-0 bg-gray-50 dark:bg-gray-900 z-10 min-w-[200px] max-w-[200px]">
                                Info Peserta
                            </th>
                            <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[80px]">
                                Status
                            </th>
                            <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[100px]">
                                NIK
                            </th>
                            <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[120px]">
                                Kelompok
                            </th>
                            <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[100px]">
                                Mentor
                            </th>
                            <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[100px]">
                                Lokasi
                            </th>

                            <!-- Kolom Bulanan - Responsive -->
                            @if($bulan)
                                <!-- Tampilkan hanya bulan yang dipilih -->
                                @php
                                    $selectedMonth = $bulan;
                                    $monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                    $bgColors = ['bg-blue-50 dark:bg-blue-900/20', 'bg-green-50 dark:bg-green-900/20', 'bg-yellow-50 dark:bg-yellow-900/20', 'bg-red-50 dark:bg-red-900/20', 'bg-purple-50 dark:bg-purple-900/20', 'bg-pink-50 dark:bg-pink-900/20', 'bg-indigo-50 dark:bg-indigo-900/20', 'bg-gray-50 dark:bg-gray-900/20', 'bg-orange-50 dark:bg-orange-900/20', 'bg-teal-50 dark:bg-teal-900/20', 'bg-cyan-50 dark:bg-cyan-900/20', 'bg-rose-50 dark:bg-rose-900/20'];
                                @endphp
                                <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider {{ $bgColors[$selectedMonth-1] }} min-w-[250px]">
                                    {{ $monthNames[$selectedMonth-1] }}
                                </th>
                            @else
                                <!-- Tampilkan semua bulan dengan scroll -->
                                <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-blue-50 dark:bg-blue-900/20 min-w-[250px]">
                                    Jan
                                </th>
                                <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-green-50 dark:bg-green-900/20 min-w-[250px]">
                                    Feb
                                </th>
                                <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-yellow-50 dark:bg-yellow-900/20 min-w-[250px]">
                                    Mar
                                </th>
                                <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-red-50 dark:bg-red-900/20 min-w-[250px]">
                                    Apr
                                </th>
                                <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-purple-50 dark:bg-purple-900/20 min-w-[250px]">
                                    Mei
                                </th>
                                <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-pink-50 dark:bg-pink-900/20 min-w-[250px]">
                                    Jun
                                </th>
                                <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-indigo-50 dark:bg-indigo-900/20 min-w-[250px]">
                                    Jul
                                </th>
                                <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-900/20 min-w-[250px]">
                                    Agu
                                </th>
                                <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-orange-50 dark:bg-orange-900/20 min-w-[250px]">
                                    Sep
                                </th>
                                <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-teal-50 dark:bg-teal-900/20 min-w-[250px]">
                                    Okt
                                </th>
                                <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-cyan-50 dark:bg-cyan-900/20 min-w-[250px]">
                                    Nov
                                </th>
                                <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-rose-50 dark:bg-rose-900/20 min-w-[250px]">
                                    Des
                                </th>
                            @endif

                            <!-- Kolom Total -->
                            <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-green-100 dark:bg-green-900/30 min-w-[80px]">
                                Total
                            </th>
                            <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-blue-100 dark:bg-blue-900/30 min-w-[80px]">
                                Jumlah
                            </th>
                            <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-yellow-100 dark:bg-yellow-900/30 min-w-[80px]">
                                Persen
                            </th>
                            <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-purple-100 dark:bg-purple-900/30 min-w-[100px]">
                                Bukti
                            </th>
                            <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider sticky right-0 bg-gray-50 dark:bg-gray-900 z-10 min-w-[120px]">
                                Aksi
                            </th>
                        </tr>

                        <!-- Sub-header untuk nomor minggu -->
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-25 dark:bg-gray-800">
                            <th colspan="6" class="px-4 py-2"></th>
                            @if($bulan)
                                <!-- Tampilkan hanya minggu untuk bulan yang dipilih -->
                                @for($week = 1; $week <= 5; $week++)
                                    <th class="px-1 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 min-w-[50px]">{{ $week }}</th>
                                @endfor
                            @else
                                <!-- Tampilkan semua minggu untuk semua bulan -->
                                @for($month = 1; $month <= 12; $month++)
                                    @for($week = 1; $week <= 5; $week++)
                                        <th class="px-1 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 min-w-[50px]">{{ $week }}</th>
                                    @endfor
                                @endfor
                            @endif
                            <th colspan="5" class="px-2 py-2"></th>
                        </tr>
                    </thead>

                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="absensi in paginated" :key="absensi.id">
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <!-- Info Peserta -->
                        <td class="px-4 py-4 whitespace-nowrap sticky left-0 bg-white dark:bg-gray-900 z-10 max-w-[200px]">
                            <div class="flex items-center">
                                <div class="truncate">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="absensi.nama_peserta"></div>
                                </div>
                            </div>
                        </td>

                        <!-- Status Peserta -->
                        <td class="px-6 py-4 text-sm">
                            <div class="flex flex-col items-center gap-1.5">

                                <!-- STATUS AKTIF -->
                                <span x-show="absensi.status_peserta == 1"
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold tracking-wide uppercase
                                            bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                    Aktif
                                </span>

                                <!-- STATUS PINDAH -->
                                <span x-show="absensi.status_peserta == 2"
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold tracking-wide uppercase
                                            bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                    Pindah
                                </span>

                                <!-- KETERANGAN PINDAH -->
                                <template x-if="absensi.status_peserta == 2 && absensi.keterangan_pindah && absensi.keterangan_pindah !== '-'">
                                    <div class="flex items-start gap-1 text-gray-500 dark:text-gray-400">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-[11px] leading-tight italic"
                                            x-text="absensi.keterangan_pindah"></span>
                                    </div>
                                </template>

                                <!-- STATUS KELUAR -->
                                <span x-show="absensi.status_peserta != 1 && absensi.status_peserta != 2"
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold tracking-wide uppercase
                                            bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                    Keluar
                                </span>

                            </div>
                        </td>

<!-- <template x-if="absensi.keterangan_pindah">
                                    <span class="block text-[11px] font-normal text-yellow-700 dark:text-yellow-200 mt-0.5" x-text="absensi.keterangan_pindah"></span>
                                </template> -->
                        <!-- NIK -->
                        <td class="px-2 py-4 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white" x-text="absensi.nik_karyawan"></td>

                        <!-- Kelompok -->
                        <td class="px-2 py-4 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white truncate" x-text="absensi.nama_kelompok"></td>

                        <!-- Mentor -->
                        <td class="px-2 py-4 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white truncate" x-text="absensi.nama_mentor"></td>

                        <!-- Lokasi -->
                        <td class="px-2 py-4 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white">
                            <span x-show="absensi.tempat === 'pusat'" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                Pusat
                            </span>
                            <span x-show="absensi.tempat === 'cabang'" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                Cabang<span x-show="absensi.keterangan_cabang"> - </span><span x-text="absensi.keterangan_cabang"></span>
                            </span>
                            <span x-show="absensi.tempat !== 'pusat' && absensi.tempat !== 'cabang'" class="text-gray-500">-</span>
                        </td>

                        <!-- Data Absensi Bulanan -->
                        @if($bulan)
                            <!-- Tampilkan hanya data bulan yang dipilih -->
                            @php
                                $monthMap = ['jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'agu', 'sep', 'okt', 'nov', 'des'];
                                $selectedMonth = $monthMap[$bulan - 1];
                            @endphp
                            @for($week = 1; $week <= 5; $week++)
                                <td class="px-1 py-4 whitespace-nowrap text-center">
                                    <div x-show="absensi.attendance_data['{{ $selectedMonth }}'] && absensi.attendance_data['{{ $selectedMonth }}'][{{ $week-1 }}] === 'hadir'" class="w-6 h-6 mx-auto bg-green-500 rounded-full flex items-center justify-center" title="Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="absensi.attendance_data['{{ $selectedMonth }}'] && absensi.attendance_data['{{ $selectedMonth }}'][{{ $week-1 }}] === 'tidak_hadir'" class="w-6 h-6 mx-auto bg-red-500 rounded-full flex items-center justify-center" title="Tidak Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="!absensi.attendance_data['{{ $selectedMonth }}'] || !absensi.attendance_data['{{ $selectedMonth }}'][{{ $week-1 }}]" class="w-6 h-6 mx-auto bg-gray-200 dark:bg-gray-700 rounded-full" title="Tidak Ada Pertemuan"></div>
                                </td>
                            @endfor
                        @else
                            <!-- Tampilkan data semua bulan -->
                            @for($week = 1; $week <= 5; $week++)
                                <td class="px-1 py-4 whitespace-nowrap text-center">
                                    <div x-show="absensi.attendance_data['jan'] && absensi.attendance_data['jan'][{{ $week-1 }}] === 'hadir'" class="w-6 h-6 mx-auto bg-green-500 rounded-full flex items-center justify-center" title="Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="absensi.attendance_data['jan'] && absensi.attendance_data['jan'][{{ $week-1 }}] === 'tidak_hadir'" class="w-6 h-6 mx-auto bg-red-500 rounded-full flex items-center justify-center" title="Tidak Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="!absensi.attendance_data['jan'] || !absensi.attendance_data['jan'][{{ $week-1 }}]" class="w-6 h-6 mx-auto bg-gray-200 dark:bg-gray-700 rounded-full" title="Tidak Ada Pertemuan"></div>
                                </td>
                            @endfor

                            @for($week = 1; $week <= 5; $week++)
                                <td class="px-1 py-4 whitespace-nowrap text-center">
                                    <div x-show="absensi.attendance_data['feb'] && absensi.attendance_data['feb'][{{ $week-1 }}] === 'hadir'" class="w-6 h-6 mx-auto bg-green-500 rounded-full flex items-center justify-center" title="Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="absensi.attendance_data['feb'] && absensi.attendance_data['feb'][{{ $week-1 }}] === 'tidak_hadir'" class="w-6 h-6 mx-auto bg-red-500 rounded-full flex items-center justify-center" title="Tidak Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="!absensi.attendance_data['feb'] || !absensi.attendance_data['feb'][{{ $week-1 }}]" class="w-6 h-6 mx-auto bg-gray-200 dark:bg-gray-700 rounded-full" title="Tidak Ada Pertemuan"></div>
                                </td>
                            @endfor

                            @for($week = 1; $week <= 5; $week++)
                                <td class="px-1 py-4 whitespace-nowrap text-center">
                                    <div x-show="absensi.attendance_data['mar'] && absensi.attendance_data['mar'][{{ $week-1 }}] === 'hadir'" class="w-6 h-6 mx-auto bg-green-500 rounded-full flex items-center justify-center" title="Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="absensi.attendance_data['mar'] && absensi.attendance_data['mar'][{{ $week-1 }}] === 'tidak_hadir'" class="w-6 h-6 mx-auto bg-red-500 rounded-full flex items-center justify-center" title="Tidak Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="!absensi.attendance_data['mar'] || !absensi.attendance_data['mar'][{{ $week-1 }}]" class="w-6 h-6 mx-auto bg-gray-200 dark:bg-gray-700 rounded-full" title="Tidak Ada Pertemuan"></div>
                                </td>
                            @endfor

                            @for($week = 1; $week <= 5; $week++)
                                <td class="px-1 py-4 whitespace-nowrap text-center">
                                    <div x-show="absensi.attendance_data['apr'] && absensi.attendance_data['apr'][{{ $week-1 }}] === 'hadir'" class="w-6 h-6 mx-auto bg-green-500 rounded-full flex items-center justify-center" title="Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="absensi.attendance_data['apr'] && absensi.attendance_data['apr'][{{ $week-1 }}] === 'tidak_hadir'" class="w-6 h-6 mx-auto bg-red-500 rounded-full flex items-center justify-center" title="Tidak Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="!absensi.attendance_data['apr'] || !absensi.attendance_data['apr'][{{ $week-1 }}]" class="w-6 h-6 mx-auto bg-gray-200 dark:bg-gray-700 rounded-full" title="Tidak Ada Pertemuan"></div>
                                </td>
                            @endfor

                            @for($week = 1; $week <= 5; $week++)
                                <td class="px-1 py-4 whitespace-nowrap text-center">
                                    <div x-show="absensi.attendance_data['mei'] && absensi.attendance_data['mei'][{{ $week-1 }}] === 'hadir'" class="w-6 h-6 mx-auto bg-green-500 rounded-full flex items-center justify-center" title="Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="absensi.attendance_data['mei'] && absensi.attendance_data['mei'][{{ $week-1 }}] === 'tidak_hadir'" class="w-6 h-6 mx-auto bg-red-500 rounded-full flex items-center justify-center" title="Tidak Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="!absensi.attendance_data['mei'] || !absensi.attendance_data['mei'][{{ $week-1 }}]" class="w-6 h-6 mx-auto bg-gray-200 dark:bg-gray-700 rounded-full" title="Tidak Ada Pertemuan"></div>
                                </td>
                            @endfor

                            @for($week = 1; $week <= 5; $week++)
                                <td class="px-1 py-4 whitespace-nowrap text-center">
                                    <div x-show="absensi.attendance_data['jun'] && absensi.attendance_data['jun'][{{ $week-1 }}] === 'hadir'" class="w-6 h-6 mx-auto bg-green-500 rounded-full flex items-center justify-center" title="Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="absensi.attendance_data['jun'] && absensi.attendance_data['jun'][{{ $week-1 }}] === 'tidak_hadir'" class="w-6 h-6 mx-auto bg-red-500 rounded-full flex items-center justify-center" title="Tidak Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="!absensi.attendance_data['jun'] || !absensi.attendance_data['jun'][{{ $week-1 }}]" class="w-6 h-6 mx-auto bg-gray-200 dark:bg-gray-700 rounded-full" title="Tidak Ada Pertemuan"></div>
                                </td>
                            @endfor

                            @for($week = 1; $week <= 5; $week++)
                                <td class="px-1 py-4 whitespace-nowrap text-center">
                                    <div x-show="absensi.attendance_data['jul'] && absensi.attendance_data['jul'][{{ $week-1 }}] === 'hadir'" class="w-6 h-6 mx-auto bg-green-500 rounded-full flex items-center justify-center" title="Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="absensi.attendance_data['jul'] && absensi.attendance_data['jul'][{{ $week-1 }}] === 'tidak_hadir'" class="w-6 h-6 mx-auto bg-red-500 rounded-full flex items-center justify-center" title="Tidak Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="!absensi.attendance_data['jul'] || !absensi.attendance_data['jul'][{{ $week-1 }}]" class="w-6 h-6 mx-auto bg-gray-200 dark:bg-gray-700 rounded-full" title="Tidak Ada Pertemuan"></div>
                                </td>
                            @endfor

                            @for($week = 1; $week <= 5; $week++)
                                <td class="px-1 py-4 whitespace-nowrap text-center">
                                    <div x-show="absensi.attendance_data['agu'] && absensi.attendance_data['agu'][{{ $week-1 }}] === 'hadir'" class="w-6 h-6 mx-auto bg-green-500 rounded-full flex items-center justify-center" title="Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="absensi.attendance_data['agu'] && absensi.attendance_data['agu'][{{ $week-1 }}] === 'tidak_hadir'" class="w-6 h-6 mx-auto bg-red-500 rounded-full flex items-center justify-center" title="Tidak Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="!absensi.attendance_data['agu'] || !absensi.attendance_data['agu'][{{ $week-1 }}]" class="w-6 h-6 mx-auto bg-gray-200 dark:bg-gray-700 rounded-full" title="Tidak Ada Pertemuan"></div>
                                </td>
                            @endfor

                            @for($week = 1; $week <= 5; $week++)
                                <td class="px-1 py-4 whitespace-nowrap text-center">
                                    <div x-show="absensi.attendance_data['sep'] && absensi.attendance_data['sep'][{{ $week-1 }}] === 'hadir'" class="w-6 h-6 mx-auto bg-green-500 rounded-full flex items-center justify-center" title="Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="absensi.attendance_data['sep'] && absensi.attendance_data['sep'][{{ $week-1 }}] === 'tidak_hadir'" class="w-6 h-6 mx-auto bg-red-500 rounded-full flex items-center justify-center" title="Tidak Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="!absensi.attendance_data['sep'] || !absensi.attendance_data['sep'][{{ $week-1 }}]" class="w-6 h-6 mx-auto bg-gray-200 dark:bg-gray-700 rounded-full" title="Tidak Ada Pertemuan"></div>
                                </td>
                            @endfor

                            @for($week = 1; $week <= 5; $week++)
                                <td class="px-1 py-4 whitespace-nowrap text-center">
                                    <div x-show="absensi.attendance_data['okt'] && absensi.attendance_data['okt'][{{ $week-1 }}] === 'hadir'" class="w-6 h-6 mx-auto bg-green-500 rounded-full flex items-center justify-center" title="Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="absensi.attendance_data['okt'] && absensi.attendance_data['okt'][{{ $week-1 }}] === 'tidak_hadir'" class="w-6 h-6 mx-auto bg-red-500 rounded-full flex items-center justify-center" title="Tidak Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="!absensi.attendance_data['okt'] || !absensi.attendance_data['okt'][{{ $week-1 }}]" class="w-6 h-6 mx-auto bg-gray-200 dark:bg-gray-700 rounded-full" title="Tidak Ada Pertemuan"></div>
                                </td>
                            @endfor

                            @for($week = 1; $week <= 5; $week++)
                                <td class="px-1 py-4 whitespace-nowrap text-center">
                                    <div x-show="absensi.attendance_data['nov'] && absensi.attendance_data['nov'][{{ $week-1 }}] === 'hadir'" class="w-6 h-6 mx-auto bg-green-500 rounded-full flex items-center justify-center" title="Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="absensi.attendance_data['nov'] && absensi.attendance_data['nov'][{{ $week-1 }}] === 'tidak_hadir'" class="w-6 h-6 mx-auto bg-red-500 rounded-full flex items-center justify-center" title="Tidak Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="!absensi.attendance_data['nov'] || !absensi.attendance_data['nov'][{{ $week-1 }}]" class="w-6 h-6 mx-auto bg-gray-200 dark:bg-gray-700 rounded-full" title="Tidak Ada Pertemuan"></div>
                                </td>
                            @endfor

                            @for($week = 1; $week <= 5; $week++)
                                <td class="px-1 py-4 whitespace-nowrap text-center">
                                    <div x-show="absensi.attendance_data['des'] && absensi.attendance_data['des'][{{ $week-1 }}] === 'hadir'" class="w-6 h-6 mx-auto bg-green-500 rounded-full flex items-center justify-center" title="Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="absensi.attendance_data['des'] && absensi.attendance_data['des'][{{ $week-1 }}] === 'tidak_hadir'" class="w-6 h-6 mx-auto bg-red-500 rounded-full flex items-center justify-center" title="Tidak Hadir">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div x-show="!absensi.attendance_data['des'] || !absensi.attendance_data['des'][{{ $week-1 }}]" class="w-6 h-6 mx-auto bg-gray-200 dark:bg-gray-700 rounded-full" title="Tidak Ada Pertemuan"></div>
                                </td>
                            @endfor
                        @endif

                        <!-- Total Hadir -->
                        <td class="px-2 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900 dark:text-white bg-green-50 dark:bg-green-900/20" x-text="absensi.total_hadir"></td>

                        <!-- Total Pertemuan -->
                        <td class="px-2 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900 dark:text-white bg-blue-50 dark:bg-blue-900/20" x-text="absensi.total_pertemuan"></td>

                        <!-- Persentase -->
                        <td class="px-2 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900 dark:text-white bg-yellow-50 dark:bg-yellow-900/20" x-text="absensi.persentase + '%'"></td>

                        <!-- Bukti Foto -->
                        <td class="px-2 py-4 whitespace-nowrap text-center">
                            <a x-show="absensi.bukti_foto" :href="`{{ url('storage') }}/${absensi.bukti_foto}`" target="_blank" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </a>
                            <span x-show="!absensi.bukti_foto" class="text-gray-400">-</span>
                        </td>

                        <!-- Aksi -->
                        <td class="px-2 py-4 whitespace-nowrap text-center sticky right-0 bg-white dark:bg-gray-900 z-10">
                            <div class="flex items-center justify-center gap-2">
                                @can('viewTempaAbsensi')
                                <a :href="`{{ url('tempa/absensi') }}/${absensi.id}`" class="inline-flex items-center justify-center rounded-lg bg-blue-50 p-2 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/40 transition" title="Lihat Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @endcan

                                @can('editTempaAbsensi')
                                <a :href="`{{ url('tempa/absensi') }}/${absensi.id}/edit`" class="inline-flex items-center justify-center rounded-lg bg-yellow-50 p-2 text-yellow-600 hover:bg-yellow-100 dark:bg-yellow-900/20 dark:text-yellow-400 dark:hover:bg-yellow-900/40 transition" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @endcan

                                @can('deleteTempaAbsensi')
                                <form :action="`{{ url('tempa/absensi') }}/${absensi.id}`" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data absensi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-red-50 p-2 text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 transition" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    </template>
                    <tr x-show="paginated.length === 0">
                        <td colspan="{{ $bulan ? 17 : 78 }}" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Belum ada data absensi</h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-6">Data absensi untuk tahun {{ $tahun }} belum tersedia.</p>
                                @can('createTempaAbsensi')
                                <a href="{{ route('tempa.absensi.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Tambah Data Absensi
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Alpine.js Pagination -->
    <div class="mt-6 mb-6 flex items-center justify-between px-6">
        <div class="text-sm text-gray-600 dark:text-gray-400">
            Showing <span x-text="startItem"></span> to <span x-text="endItem"></span> of <span x-text="filtered.length"></span> entries
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

<script>
function changeFilter() {
    const tahun = document.getElementById('tahunFilter').value;
    const bulan = document.getElementById('bulanFilter').value;
    const search = document.getElementById('search').value;
    const kelompok = document.getElementById('kelompokFilter').value;
    const status = document.getElementById('statusFilter').value;
    const lokasi = document.getElementById('lokasiFilter').value;

    const params = new URLSearchParams();
    if (tahun) params.append('tahun', tahun);
    if (bulan) params.append('bulan', bulan);
    if (search) params.append('search', search);
    if (kelompok) params.append('kelompok', kelompok);
    if (status) params.append('status', status);
    if (lokasi) params.append('lokasi', lokasi);

    const url = '{{ url("tempa/absensi") }}' + (params.toString() ? '?' + params.toString() : '');
    window.location.href = url;
}

function absensiTable() {
    return {
        data: @json($tableData),
        search: '',
        page: 1,
        perPage: 10,

        resetPage() {
            this.page = 1;
        },

        get filtered() {
            let filteredData = this.data;

            // Search functionality
            if (this.search) {
                const q = this.search.toLowerCase();
                filteredData = filteredData.filter(d =>
                    Object.values(d).join(' ').toLowerCase().includes(q)
                );
            }

            return filteredData;
        },

        get totalPages() {
            return Math.max(1, Math.ceil(this.filtered.length / this.perPage));
        },

        get paginated() {
            const start = (this.page - 1) * this.perPage;
            return this.filtered.slice(start, start + this.perPage);
        },

        prevPage() { if (this.page > 1) this.page--; },
        nextPage() { if (this.page < this.totalPages) this.page++; },
        goToPage(p) { if (typeof p === 'number' && p >= 1 && p <= this.totalPages) this.page = p; },

        get displayedPages() {
            const range = [];
            for (let i = 1; i <= this.totalPages; i++) {
                if (i === 1 || i === this.totalPages || (i >= this.page - 1 && i <= this.page + 1)) {
                    range.push(i);
                } else if (range[range.length - 1] !== '...') {
                    range.push('...');
                }
            }
            return range;
        },

        get startItem() {
            if (this.filtered.length === 0) return 0;
            return (this.page - 1) * this.perPage + 1;
        },

        get endItem() {
            return Math.min(this.page * this.perPage, this.filtered.length);
        }
    }
}
</script>
<script>
function clearFilters() {
    // Reset semua filter ke default (tanpa query string)
    window.location.href = '{{ url('tempa/absensi') }}';
}
</script>
@endsection
