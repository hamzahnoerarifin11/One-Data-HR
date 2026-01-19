@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    @php
        $namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
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

    <div class="rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">

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
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total: {{ $absensis->count() }} peserta</p>
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Terakhir diperbarui: {{ now()->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>

        <div class="max-w-full overflow-x-auto">
            <table class="w-full min-w-[{{ $bulan ? '1200px' : '2000px' }}]">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider sticky left-0 bg-gray-50 dark:bg-gray-900 z-10 min-w-[200px]">
                            Info Peserta
                        </th>
                        <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[100px]">
                            Status
                        </th>
                        <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[100px]">
                            NIK
                        </th>
                        <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[150px]">
                            Kelompok
                        </th>
                        <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[120px]">
                            Mentor
                        </th>
                        <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[120px]">
                            Lokasi
                        </th>

                        <!-- Kolom Bulanan -->
                        @if($bulan)
                            <!-- Tampilkan hanya bulan yang dipilih -->
                            @php
                                $selectedMonth = $bulan;
                                $monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                $monthCodes = ['jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'agu', 'sep', 'okt', 'nov', 'des'];
                                $bgColors = ['bg-blue-50 dark:bg-blue-900/20', 'bg-green-50 dark:bg-green-900/20', 'bg-yellow-50 dark:bg-yellow-900/20', 'bg-red-50 dark:bg-red-900/20', 'bg-purple-50 dark:bg-purple-900/20', 'bg-pink-50 dark:bg-pink-900/20', 'bg-indigo-50 dark:bg-indigo-900/20', 'bg-gray-50 dark:bg-gray-900/20', 'bg-orange-50 dark:bg-orange-900/20', 'bg-teal-50 dark:bg-teal-900/20', 'bg-cyan-50 dark:bg-cyan-900/20', 'bg-rose-50 dark:bg-rose-900/20'];
                            @endphp
                            <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider {{ $bgColors[$selectedMonth-1] }}">
                                {{ $monthNames[$selectedMonth-1] }}
                            </th>
                        @else
                            <!-- Tampilkan semua bulan -->
                            <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-blue-50 dark:bg-blue-900/20">
                                Januari
                            </th>
                            <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-green-50 dark:bg-green-900/20">
                                Februari
                            </th>
                            <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-yellow-50 dark:bg-yellow-900/20">
                                Maret
                            </th>
                            <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-red-50 dark:bg-red-900/20">
                                April
                            </th>
                            <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-purple-50 dark:bg-purple-900/20">
                                Mei
                            </th>
                            <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-pink-50 dark:bg-pink-900/20">
                                Juni
                            </th>
                            <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-indigo-50 dark:bg-indigo-900/20">
                                Juli
                            </th>
                            <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-900/20">
                                Agustus
                            </th>
                            <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-orange-50 dark:bg-orange-900/20">
                                September
                            </th>
                            <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-teal-50 dark:bg-teal-900/20">
                                Oktober
                            </th>
                            <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-cyan-50 dark:bg-cyan-900/20">
                                November
                            </th>
                            <th colspan="5" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-rose-50 dark:bg-rose-900/20">
                                Desember
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
                        <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-purple-100 dark:bg-purple-900/30 min-w-[120px]">
                            Bukti Foto
                        </th>
                        <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider sticky right-0 bg-gray-50 dark:bg-gray-900 z-10 min-w-[120px]">
                            Aksi
                        </th>
                    </tr>

                    <!-- Sub-header untuk nomor minggu -->
                    <tr class="border-b border-gray-200 bg-gray-25 dark:border-gray-700 dark:bg-gray-800">
                        <th colspan="6" class="px-4 py-2"></th>
                        @if($bulan)
                            <!-- Tampilkan hanya minggu untuk bulan yang dipilih -->
                            @for($week = 1; $week <= 5; $week++)
                                <th class="px-1 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 min-w-[30px]">{{ $week }}</th>
                            @endfor
                        @else
                            <!-- Tampilkan semua minggu untuk semua bulan -->
                            @for($month = 1; $month <= 12; $month++)
                                @for($week = 1; $week <= 5; $week++)
                                    <th class="px-1 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 min-w-[30px]">{{ $week }}</th>
                                @endfor
                            @endfor
                        @endif
                        <th colspan="5" class="px-2 py-2"></th>
                    </tr>
                </thead>

                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($absensis as $absensi)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <!-- Info Peserta -->
                        <td class="px-4 py-4 whitespace-nowrap sticky left-0 bg-white dark:bg-gray-900 z-10">
                            <div class="flex items-center">
                                <!-- <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ substr($absensi->peserta->nama_peserta, 0, 1) }}
                                        </span>
                                    </div>
                                </div> -->
                                <!-- <div class="ml-4"> -->
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $absensi->peserta->nama_peserta }}
                                    </div>
                                    <!-- <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $absensi->peserta->tempa->nama_tempa ?? '-' }}
                                    </div> -->
                                </div>
                            </div>
                        </td>

                        <!-- Status Peserta -->
                        <td class="px-2 py-4 whitespace-nowrap text-center">
                            @if($absensi->peserta->status_peserta == 1)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                    Aktif
                                </span>
                            @elseif($absensi->peserta->status_peserta == 2)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                    Pindah
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                    Keluar
                                </span>
                            @endif
                        </td>

                        <!-- NIK -->
                        <td class="px-2 py-4 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white">
                            {{ $absensi->peserta->nik_karyawan }}
                        </td>

                        <!-- Kelompok -->
                        <td class="px-2 py-4 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white">
                            {{ $absensi->peserta->kelompok->nama_kelompok ?? '-' }}
                        </td>

                        <!-- Mentor -->
                        <td class="px-2 py-4 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white">
                            {{ $absensi->peserta->kelompok->nama_mentor ?? '-' }}
                        </td>

                        <!-- Lokasi -->
                        <td class="px-2 py-4 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white">
                            @if($absensi->peserta->kelompok->tempat === 'pusat')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                    Pusat
                                </span>
                            @elseif($absensi->peserta->kelompok->tempat === 'cabang')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                    Cabang{{ $absensi->peserta->kelompok->keterangan_cabang ? ' - ' . $absensi->peserta->kelompok->keterangan_cabang : '' }}
                                </span>
                            @else
                                -
                            @endif
                        </td>

                        <!-- Data Absensi Bulanan -->
                        @if($bulan)
                            <!-- Tampilkan hanya data bulan yang dipilih -->
                            @for($week = 1; $week <= 5; $week++)
                                <td class="px-1 py-4 whitespace-nowrap text-center">
                                    @php
                                        $status = $absensi->getAbsensiBulan($bulan)[$week] ?? null;
                                    @endphp
                                    @if($status === 'hadir')
                                        <div class="w-6 h-6 mx-auto bg-green-500 rounded-full flex items-center justify-center" title="Hadir">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    @elseif($status === 'tidak_hadir')
                                        <div class="w-6 h-6 mx-auto bg-red-500 rounded-full flex items-center justify-center" title="Tidak Hadir">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-6 h-6 mx-auto bg-gray-200 dark:bg-gray-700 rounded-full" title="Tidak Ada Pertemuan"></div>
                                    @endif
                                </td>
                            @endfor
                        @else
                            <!-- Tampilkan data semua bulan -->
                            @php
                                $months = ['jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'agu', 'sep', 'okt', 'nov', 'des'];
                            @endphp

                            @foreach($months as $month)
                                @for($week = 1; $week <= 5; $week++)
                                    <td class="px-1 py-4 whitespace-nowrap text-center">
                                        @php
                                            $status = $absensi->getAbsensiBulan(array_search($month, $months) + 1)[$week] ?? null;
                                        @endphp
                                        @if($status === 'hadir')
                                            <div class="w-6 h-6 mx-auto bg-green-500 rounded-full flex items-center justify-center" title="Hadir">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        @elseif($status === 'tidak_hadir')
                                            <div class="w-6 h-6 mx-auto bg-red-500 rounded-full flex items-center justify-center" title="Tidak Hadir">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-6 h-6 mx-auto bg-gray-200 dark:bg-gray-700 rounded-full" title="Tidak Ada Pertemuan"></div>
                                        @endif
                                    </td>
                                @endfor
                            @endforeach
                        @endif

                        <!-- Total Hadir -->
                        <td class="px-2 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900 dark:text-white bg-green-50 dark:bg-green-900/20">
                            {{ $absensi->total_hadir }}
                        </td>

                        <!-- Total Pertemuan -->
                        <td class="px-2 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900 dark:text-white bg-blue-50 dark:bg-blue-900/20">
                            {{ $absensi->total_pertemuan }}
                        </td>

                        <!-- Persentase -->
                        <td class="px-2 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900 dark:text-white bg-yellow-50 dark:bg-yellow-900/20">
                            {{ number_format($absensi->persentase, 1) }}%
                        </td>

                        <!-- Bukti Foto -->
                        <td class="px-2 py-4 whitespace-nowrap text-center">
                            @if($absensi->bukti_foto)
                                <a href="{{ Storage::url($absensi->bukti_foto) }}" target="_blank" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="px-2 py-4 whitespace-nowrap text-center sticky right-0 bg-white dark:bg-gray-900 z-10">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('tempa.absensi.show', $absensi->id_absensi) }}" class="inline-flex items-center justify-center rounded-lg bg-blue-50 p-2 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/40 transition" title="Lihat Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>

                                @can('editTempaAbsensi')
                                <a href="{{ route('tempa.absensi.edit', $absensi->id_absensi) }}" class="inline-flex items-center justify-center rounded-lg bg-yellow-50 p-2 text-yellow-600 hover:bg-yellow-100 dark:bg-yellow-900/20 dark:text-yellow-400 dark:hover:bg-yellow-900/40 transition" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @endcan

                                @can('deleteTempaAbsensi')
                                <form action="{{ route('tempa.absensi.destroy', $absensi->id_absensi) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data absensi ini?')">
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
                    @empty
                    <tr>
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
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function changeFilter() {
    const tahun = document.getElementById('tahunFilter').value;
    const bulan = document.getElementById('bulanFilter').value;

    const url = new URL(window.location);
    url.searchParams.set('tahun', tahun);
    if (bulan) {
        url.searchParams.set('bulan', bulan);
    } else {
        url.searchParams.delete('bulan');
    }
    window.location.href = url.toString();
}
</script>
@endsection
