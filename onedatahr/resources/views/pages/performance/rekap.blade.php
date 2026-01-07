@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    {{-- HEADER & PENCARIAN + FILTER --}}
    <div class="mb-6 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">

        {{-- Kiri: Judul --}}
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                Rekapitulasi Kinerja
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Laporan gabungan KPI (70%) dan KBI (30%) Tahun {{ $tahun }}.
            </p>
        </div>

        {{-- Kanan: Search & Filter --}}
        <form action="{{ route('performance.rekap') }}" method="GET"
            class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center">

            {{-- 1. Search Input --}}
            <div class="relative w-full sm:w-64">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input
                    type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / NIK..."
                    class="h-11 w-full rounded-lg border border-gray-300 bg-white
                        pl-12 pr-4 text-sm text-gray-800
                        focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                        dark:border-gray-700 dark:bg-gray-900 dark:text-white
                        shadow-sm placeholder-gray-400 transition-all"
                >
            </div>

            {{-- 2. Filter Tahun (BARU DITAMBAHKAN) --}}
            <div class="relative w-full sm:w-32">
                <select name="tahun" onchange="this.form.submit()"
                    class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-white px-4
                        text-sm text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                        dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm cursor-pointer">
                    @php
                        $startYear = date('Y') - 4; // 4 tahun ke belakang
                        $endYear = date('Y') + 1;   // 1 tahun ke depan
                    @endphp
                    @for($y = $endYear; $y >= $startYear; $y--)
                        <option value="{{ $y }}" {{ request('tahun', $tahun) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
                <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </span>
            </div>

            {{-- 3. Filter Grade --}}
            <div class="relative w-full sm:w-40">
                <select name="grade" onchange="this.form.submit()"
                    class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-white px-4
                        text-sm text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                        dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm cursor-pointer">
                    <option value="">Semua Grade</option>
                    <option value="A" {{ request('grade') == 'A' ? 'selected' : '' }}>Grade A</option>
                    <option value="B" {{ request('grade') == 'B' ? 'selected' : '' }}>Grade B</option>
                    <option value="C" {{ request('grade') == 'C' ? 'selected' : '' }}>Grade C</option>
                    <option value="D" {{ request('grade') == 'D' ? 'selected' : '' }}>Grade D</option>
                    <option value="E" {{ request('grade') == 'E' ? 'selected' : '' }}>Grade E</option>
                </select>
                <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </span>
            </div>

        </form>
    </div>

    {{-- CARD TABEL DATA --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
        
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                Daftar Hasil Penilaian
            </h3>
            <span class="text-sm text-gray-500 bg-white border border-gray-200 dark:bg-gray-700 dark:border-gray-600 px-3 py-1 rounded-full">
                Total Data: <strong>{{ $rekap->total() }}</strong>
            </span>
        </div>

        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="bg-gray-50 text-left dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-4 font-medium text-gray-900 dark:text-white text-center w-[60px]">No</th>
                        <th class="px-4 py-4 font-medium text-gray-900 dark:text-white min-w-[200px]">Nama Karyawan</th>
                        <th class="px-4 py-4 font-medium text-gray-900 dark:text-white text-center">KPI (70%)</th>
                        <th class="px-4 py-4 font-medium text-gray-900 dark:text-white text-center">KBI (30%)</th>
                        <th class="px-4 py-4 font-medium text-gray-900 dark:text-white text-center">Final Score</th>
                        <th class="px-4 py-4 font-medium text-gray-900 dark:text-white text-center">Grade</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($rekap as $index => $data)
                    <tr class=" dark:hover:bg-gray-800/50 transition duration-150">
                        <td class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">
                            {{ $rekap->firstItem() + $loop->index }}
                        </td>
                        <td class="px-4 py-4">
                            <h5 class="font-bold text-gray-900 dark:text-white text-sm">
                                {{ $data->nama }}
                            </h5>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ $data->nik }} <span class="mx-1">•</span> {{ $data->jabatan }}
                            </p>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700 ring-1 ring-inset ring-blue-700/10 dark:bg-blue-900/30 dark:text-blue-400">
                                {{ number_format($data->skor_kpi, 2) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center rounded-md bg-purple-50 px-2.5 py-1 text-xs font-bold text-purple-700 ring-1 ring-inset ring-purple-700/10 dark:bg-purple-900/30 dark:text-purple-400">
                                {{ number_format($data->skor_kbi_asli, 2) }}
                            </span>
                            <div class="text-[10px] text-gray-400 mt-1">
                                (Konversi: {{ number_format($data->skor_kbi_100, 0) }})
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <h5 class="text-base font-black text-gray-800 dark:text-white">
                                {{ number_format($data->final_score, 2) }}
                            </h5>
                        </td>
                        <td class="px-4 py-4 text-center">
                            @php
                                $badgeColor = match($data->grade) {
                                    'A' => 'bg-green-100 text-green-700 ring-green-600/20 dark:bg-green-900/30 dark:text-green-400',
                                    'B' => 'bg-blue-100 text-blue-700 ring-blue-600/20 dark:bg-blue-900/30 dark:text-blue-400',
                                    'C' => 'bg-yellow-100 text-yellow-700 ring-yellow-600/20 dark:bg-yellow-900/30 dark:text-yellow-400',
                                    'D' => 'bg-orange-100 text-orange-700 ring-orange-600/20 dark:bg-orange-900/30 dark:text-orange-400',
                                    default => 'bg-red-100 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-400',
                                };
                            @endphp
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold ring-1 ring-inset {{ $badgeColor }}">
                                {{ $data->grade }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <p>Data tidak ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER PAGINATION YANG RAPI --}}
        <div class="px-6 py-4 border-t border-gray-800  dark:border-gray-800 dark:bg-gray-800 flex justify-end">
            {{ $rekap->links('components.pagination-custom') }}
        </div>
    </div>

</div>
@endsection