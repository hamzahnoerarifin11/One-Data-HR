@extends('layouts.app')

@section('content')
<style>
    .divisi-content {
        transition: opacity 0.3s ease-in-out, max-height 0.3s ease-in-out;
        max-height: 10000px;
        opacity: 1;
    }
    
    .divisi-content.hidden {
        max-height: 0;
        opacity: 0;
        overflow: hidden;
    }
</style>

<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    {{-- HEADER & JUDUL --}}
    <div class="mb-6 flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                Rekapitulasi Kinerja
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Laporan gabungan KPI (70%) dan KBI (30%) Tahun {{ $tahun }}.
            </p>
        </div>
        
        {{-- Mode Toggle (hanya untuk user dengan multiple roles) --}}
        @if(auth()->user()->hasRole(['superadmin', 'admin']))
        <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Mode:</span>
            <div class="flex gap-2">
                <a href="{{ request()->fullUrlWithQuery(['mode' => 'manager']) }}" 
                   class="rounded px-3 py-1 text-sm font-medium transition {{ $mode === 'manager' 
                       ? 'bg-blue-600 text-white' 
                       : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                    Manager
                </a>
                <a href="{{ request()->fullUrlWithQuery(['mode' => 'superadmin']) }}" 
                   class="rounded px-3 py-1 text-sm font-medium transition {{ $mode === 'superadmin' 
                       ? 'bg-blue-600 text-white' 
                       : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                    Superadmin
                </a>
            </div>
        </div>
        @endif
    </div>

    {{-- ====================================
        EXECUTIVE SUMMARY CARDS
        ==================================== --}}
    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2 lg:grid-cols-5">
        {{-- Card 1: Rata-rata Final Score --}}
        <div class="rounded-lg border border-gray-200 bg-gradient-to-br from-blue-50 to-blue-100 p-5 dark:border-gray-700 dark:from-blue-900/30 dark:to-blue-800/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Avg. Final Score</p>
                    <p class="text-2xl font-bold text-blue-700 dark:text-blue-400">{{ number_format($avgFinalScore, 2) }}</p>
                </div>
                <div class="rounded-full bg-blue-200 p-3 dark:bg-blue-900">
                    <svg class="h-6 w-6 text-blue-700 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Card 2: Total Karyawan Dinilai --}}
        <div class="rounded-lg border border-gray-200 bg-gradient-to-br from-green-50 to-green-100 p-5 dark:border-gray-700 dark:from-green-900/30 dark:to-green-800/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Dinilai</p>
                    <p class="text-2xl font-bold text-green-700 dark:text-green-400">{{ $totalKaryawan }}</p>
                </div>
                <div class="rounded-full bg-green-200 p-3 dark:bg-green-900">
                    <svg class="h-6 w-6 text-green-700 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM6 20H1v-2a6 6 0 016-6v0"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Card 3: Grade A --}}
        <div class="rounded-lg border border-gray-200 bg-gradient-to-br from-green-50 to-green-100 p-5 dark:border-gray-700 dark:from-green-900/30 dark:to-green-800/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Grade A</p>
                    <p class="text-2xl font-bold text-green-700 dark:text-green-400">{{ $gradeDistribution['A'] }}</p>
                </div>
                <div class="rounded-full bg-green-200 p-3 dark:bg-green-900">
                    <span class="inline-flex items-center justify-center h-6 w-6 text-lg font-bold text-purple-700 dark:text-green-400">A</span>
                </div>
            </div>
        </div>

        {{-- Card 4: Grade B & C --}}
        <div class="rounded-lg border border-gray-200 bg-gradient-to-br from-yellow-50 to-yellow-100 p-5 dark:border-gray-700 dark:from-yellow-900/30 dark:to-yellow-800/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Grade B+C</p>
                    <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-400">{{ $gradeDistribution['B'] + $gradeDistribution['C'] }}</p>
                </div>
                <div class="rounded-full bg-yellow-200 p-3 dark:bg-yellow-900">
                    <svg class="h-6 w-6 text-yellow-700 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Card 5: % Di Bawah Standar --}}
        <div class="rounded-lg border border-gray-200 bg-gradient-to-br from-red-50 to-red-100 p-5 dark:border-gray-700 dark:from-red-900/30 dark:to-red-800/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Bawah Standar</p>
                    <p class="text-2xl font-bold text-red-700 dark:text-red-400">{{ $pctBelowStandard }}%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">({{ $belowStandard }} karyawan)</p>
                </div>
                <div class="rounded-full bg-red-200 p-3 dark:bg-red-900">
                    <svg class="h-6 w-6 text-red-700 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 0H9m3 0h3m-8 6h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ====================================
        MODE INDICATOR (Superadmin Mode)
        ==================================== --}}
    @if($mode === 'superadmin')
    <div class="mb-6 rounded-lg border border-purple-200 bg-purple-50 p-4 dark:border-purple-700 dark:bg-purple-900/20">
        <div class="flex items-start gap-3">
            <div class="rounded-full bg-purple-200 p-2 dark:bg-purple-800">
                <svg class="h-5 w-5 text-purple-700 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h4 class="font-semibold text-gray-900 dark:text-white">👑 Mode Superadmin Aktif</h4>
                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                    Anda sedang melihat data <strong>semua karyawan</strong> di seluruh organisasi, bukan hanya bawahan langsung Anda.
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- ====================================
        ANOMALI HIGHLIGHTS
        ==================================== --}}
    @if($divisiGradeD || $lonjakan)
    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2">
        {{-- Anomali 1: Divisi dengan Grade D Terbanyak --}}
        @if($divisiGradeD)
        <div class="rounded-lg border border-orange-200 bg-orange-50 p-4 dark:border-orange-700 dark:bg-orange-900/20">
            <div class="flex items-start gap-3">
                <div class="rounded-full bg-orange-200 p-2 dark:bg-orange-800">
                    <svg class="h-5 w-5 text-orange-700 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 0H9m3 0h3m-8 6h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-900 dark:text-white">⚠ Perhatian: Grade D Terbanyak</h4>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                        Divisi <strong>{{ $divisiGradeD['divisi'] ?? 'N/A' }}</strong> memiliki {{ $divisiGradeD['count'] ?? 0 }} karyawan dengan Grade D
                    </p>
                </div>
            </div>
        </div>
        @endif

        {{-- Anomali 2: Divisi dengan Performa Terbaik --}}
        @if($lonjakan)
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-700 dark:bg-green-900/20">
            <div class="flex items-start gap-3">
                <div class="rounded-full bg-green-200 p-2 dark:bg-green-800">
                    <svg class="h-5 w-5 text-green-700 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-900 dark:text-white">📈 Performa Terbaik</h4>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                        Divisi <strong>{{ $lonjakan['divisi'] ?? 'N/A' }}</strong> dengan rata-rata score <strong>{{ $lonjakan['avg_score'] ?? 0 }}</strong>
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- ====================================
        FILTER & PENCARIAN
        ==================================== --}}
    <form action="{{ route('performance.rekap') }}" method="GET"
        class="mb-6 rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        
        {{-- Hidden input untuk preserve mode --}}
        <input type="hidden" name="mode" value="{{ $mode }}">
        
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-6">
            {{-- Search Input --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cari Nama/NIK</label>
                <input
                    type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau NIK..."
                    class="w-full h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800
                        focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                        dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm"
                >
            </div>

            {{-- Filter Tahun --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tahun</label>
                <select name="tahun"
                    class="w-full h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800
                        focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                        dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm cursor-pointer">
                    @php
                        $startYear = date('Y') - 4;
                        $endYear = date('Y') + 1;
                    @endphp
                    @for($y = $endYear; $y >= $startYear; $y--)
                        <option value="{{ $y }}" {{ request('tahun', $tahun) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>

            {{-- Filter Perusahaan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Perusahaan</label>
                <select id="perusahaan" name="perusahaan"
                    class="w-full h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800
                        focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                        dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm cursor-pointer">
                    <option value="">Semua Perusahaan</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('perusahaan') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Divisi --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Divisi</label>
                <select id="divisi" name="divisi"
                    class="w-full h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800
                        focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                        dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm cursor-pointer"
                    {{ request('perusahaan') ? '' : 'disabled' }}>
                    <option value="">Semua Divisi</option>
                    @foreach($divisions as $division)
                        <option value="{{ $division->id }}" data-company-id="{{ $division->company_id }}" {{ request('divisi') == $division->id ? 'selected' : '' }}>
                            {{ $division->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Departemen --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Departemen</label>
                <select id="departemen" name="departemen"
                    class="w-full h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800
                        focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                        dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm cursor-pointer"
                    {{ request('divisi') ? '' : 'disabled' }}>
                    <option value="">Semua Departemen</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" data-division-id="{{ $department->division_id }}" {{ request('departemen') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Grade --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Grade</label>
                <select name="grade"
                    class="w-full h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800
                        focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                        dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm cursor-pointer">
                    <option value="">Semua Grade</option>
                    <option value="A" {{ request('grade') == 'A' ? 'selected' : '' }}>Grade A</option>
                    <option value="B" {{ request('grade') == 'B' ? 'selected' : '' }}>Grade B</option>
                    <option value="C" {{ request('grade') == 'C' ? 'selected' : '' }}>Grade C</option>
                    <option value="D" {{ request('grade') == 'D' ? 'selected' : '' }}>Grade D</option>
                </select>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex gap-3 mt-4">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-all">
                🔍 Cari & Filter
            </button>
            <a href="{{ route('performance.rekap') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 text-sm font-medium transition-all dark:bg-gray-600 dark:text-gray-600">
                ↺ Reset
            </a>
        </div>
    </form>

    {{-- ====================================
        GROUPING BY DIVISI + SUMMARY TABLE
        ==================================== --}}
    @forelse($groupedByDivisi as $divisiIndex => $group)
    <div class="mb-6 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900 overflow-hidden">
        {{-- Header Divisi (Clickable Toggle) --}}
        <button onclick="toggleDivisi('divisi-{{ $divisiIndex }}')" class="w-full px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-900 dark:to-blue-800 hover:from-blue-700 hover:to-blue-800 dark:hover:from-blue-950 dark:hover:to-blue-900 transition-all flex items-center justify-between cursor-pointer group">
            <div class="flex items-center justify-between flex-1">
                <div class="text-left">
                    <h3 class="text-lg font-bold text-white">{{ $group['divisi'] }}</h3>
                    <p class="text-sm text-blue-100 mt-1">Total {{ $group['total'] }} Karyawan</p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-white">{{ $group['avg_score'] }}</p>
                    <p class="text-xs text-blue-100">Rata-rata Score</p>
                </div>
            </div>
            
            {{-- Toggle Arrow Icon --}}
            <div class="ml-4 shrink-0">
                <svg id="arrow-{{ $divisiIndex }}" class="h-6 w-6 text-white transform transition-transform duration-300 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
            </div>
        </button>

        {{-- Collapsible Content --}}
        <div id="divisi-{{ $divisiIndex }}" class="divisi-content">
            {{-- Grade Distribution for this Division --}}
            <div class="px-6 py-3 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col-4 items-center justify-center gap-3">
                <div class="text-center">
                    <p class="text-xs text-gray-600 dark:text-gray-400">Grade A</p>
                    <p class="text-lg font-bold text-green-500 dark:text-green-500">{{ $group['grade_a'] }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-600 dark:text-gray-400">Grade B</p>
                    <p class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $group['grade_b'] }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-600 dark:text-gray-400">Grade C</p>
                    <p class="text-lg font-bold text-yellow-600 dark:text-yellow-400">{{ $group['grade_c'] }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-600 dark:text-gray-400">Grade D</p>
                    <p class="text-lg font-bold text-red-600 dark:text-red-400">{{ $group['grade_d'] }}</p>
                </div>
            </div>
        </div>

        {{-- Table Karyawan dalam Divisi --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300">Nama</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-700 dark:text-gray-300">NIK</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-700 dark:text-gray-300">KPI (70%)</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-700 dark:text-gray-300">KBI (30%)</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-700 dark:text-gray-300">Final Score</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-700 dark:text-gray-300">Grade</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-700 dark:text-gray-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($group['items'] as $data)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <td class="px-4 py-3">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $data->nama }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $data->perusahaan ?? '-' }} | {{ $data->level ?? '-' }} | {{ $data->divisi ?? '-' }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ $data->nik }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center rounded-md bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-500/90 dark:text-blue-900">
                                {{ $data->skor_kpi }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center rounded-md bg-purple-100 px-2.5 py-1 text-xs font-semibold text-purple-700 dark:bg-purple-500/30 dark:text-purple-900">
                                {{ $data->skor_kbi }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <p class="text-base font-bold text-gray-900 dark:text-white">{{ $data->final_score_formatted }}</p>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $badgeColor = match($data->grade) {
                                    'A' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                    'B' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                    'C' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                    default => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                };
                            @endphp
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold {{ $badgeColor }}">
                                {{ $data->grade }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                {{-- Lihat Detail (Link ke halaman asli KPI) --}}
                                <a href="{{ route('kpi.show', ['karyawan_id' => $data->id_karyawan, 'tahun' => $tahun]) }}" title="Lihat Detail" 
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-400 transition-all">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        {{-- <div class="mt-6 flex justify-center">
            {{ $rekap->links('components.pagination-custom') }}
        </div> --}}
        </div>
    </div>
    @empty
    <div class="rounded-lg border border-gray-200 bg-white p-10 text-center shadow-lg dark:border-gray-700 dark:bg-gray-900">
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <p class="text-gray-600 dark:text-gray-400 text-lg">Data tidak ditemukan.</p>
        <p class="text-gray-500 dark:text-gray-500 text-sm mt-1">Coba ubah filter atau kriteria pencarian Anda.</p>
    </div>
    @endforelse

    {{-- ====================================
        ACTION & EXPORT SECTION
        ==================================== --}}
    <div class="mt-8 rounded-lg border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-900">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📋 Aksi & Keputusan</h3>
        
        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
            {{-- Export Laporan --}}
            <button onclick="showExportModal()" class="flex items-center justify-center gap-2 px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-all font-medium">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                📥 Export Laporan (Tahun {{ $tahun }})
            </button>

            {{-- Kunci Nilai (Lock Period) --}}
            <!-- <button onclick="kunciNilai()" class="flex items-center justify-center gap-2 px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-all font-medium">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                🔒 Kunci Nilai (Lock Period)
            </button> -->
        </div>
    </div>

</div>

{{-- JAVASCRIPT FUNCTIONS --}}
<script>
// ======================================================
// TOGGLE DIVISI COLLAPSE/EXPAND
// ======================================================
function toggleDivisi(divisiId) {
    const content = document.getElementById(divisiId);
    // Mengembalikan getElementById dengan prefix 'arrow-' dari parameter divisiId yang mengandung 'divisi-0' 
    // Wait, kalau divisiId = 'divisi-0', kita butuh ID arrow-0 atau arrow-divisi-0?
    // Di atas ID svg diperbarui menjadi 'arrow-{{ $divisiIndex }}'. Sementara parameter fungsi toggleDivisi('divisi-{{ $divisiIndex }}').
    // Berarti id div-nya 'divisi-X', jadi kalau cuma dimaunya 'arrow-X', kita replace dulu.
    const index = divisiId.replace('divisi-', '');
    const arrow = document.getElementById('arrow-' + index);
    
    if (content) {
        content.classList.toggle('hidden');
        // Rotate arrow
        if (arrow) {
            arrow.classList.toggle('rotate-180');
        }
    }
}

// ======================================================
// DEPENDENT DROPDOWN LOGIC
// ======================================================
const perusahaanSelect = document.getElementById('perusahaan');
const divisiSelect = document.getElementById('divisi');
const departemenSelect = document.getElementById('departemen');

// Handle Perusahaan change
if (perusahaanSelect) {
    perusahaanSelect.addEventListener('change', function() {
        const selectedCompanyId = this.value;
        
        // Filter divisi berdasarkan perusahaan yang dipilih
        const divisiOptions = divisiSelect.querySelectorAll('option[data-company-id]');
        
        // Reset divisi dropdown
        divisiSelect.value = '';
        divisiSelect.disabled = !selectedCompanyId; // Disable jika belum pilih perusahaan
        
        // Tampilkan/sembunyikan divisi sesuai perusahaan
        divisiOptions.forEach(option => {
            if (selectedCompanyId === '' || option.dataset.companyId === selectedCompanyId) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        });
        
        // Reset departemen karena divisi berubah
        departemenSelect.value = '';
        departemenSelect.disabled = true;
        departemenSelect.querySelectorAll('option[data-division-id]').forEach(option => {
            option.style.display = 'none';
        });
    });
}

// Handle Divisi change
if (divisiSelect) {
    divisiSelect.addEventListener('change', function() {
        const selectedDivisionId = this.value;
        
        // Filter departemen berdasarkan divisi yang dipilih
        const departemenOptions = departemenSelect.querySelectorAll('option[data-division-id]');
        
        // Reset departemen dropdown
        departemenSelect.value = '';
        departemenSelect.disabled = !selectedDivisionId; // Disable jika belum pilih divisi
        
        // Tampilkan/sembunyikan departemen sesuai divisi
        departemenOptions.forEach(option => {
            if (selectedDivisionId === '' || option.dataset.divisionId === selectedDivisionId) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        });
    });
}

// Initialize on page load - pastikan state awal sesuai
document.addEventListener('DOMContentLoaded', function() {
    // Trigger events untuk set initial state
    if (perusahaanSelect && perusahaanSelect.value) {
        perusahaanSelect.dispatchEvent(new Event('change'));
    }
    if (divisiSelect && divisiSelect.value) {
        divisiSelect.dispatchEvent(new Event('change'));
    }
});

// ======================================================
// ACTION BUTTONS FUNCTIONS
// ======================================================
function showExportModal() {
    const modal = `
        <div id="exportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" onclick="if(event.target.id === 'exportModal') closeExportModal()">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📥 Pilih Format Export</h3>
                
                <div class="space-y-3 mb-6">
                    <a href="{{ route('performance.rekap.export.excel') }}?{{ request()->getQueryString() }}" 
                       class="block w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-all font-medium text-center flex items-center justify-center gap-2">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"/>
                        </svg>
                        📊 Export ke Excel
                    </a>
                    
                    <a href="{{ route('performance.rekap.export.pdf') }}?{{ request()->getQueryString() }}" 
                       class="block w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all font-medium text-center flex items-center justify-center gap-2">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"/>
                        </svg>
                        📄 Export ke PDF
                    </a>
                </div>
                
                <button onclick="closeExportModal()" class="w-full px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-600 transition-all font-medium">
                    Batal
                </button>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modal);
}

function closeExportModal() {
    const modal = document.getElementById('exportModal');
    if (modal) {
        modal.remove();
    }
}

function kunciNilai() {
    const tahun = new URLSearchParams(window.location.search).get('tahun') || '{{ $tahun }}';
    
    // Fetch lock history first
    fetch("{{ route('performance.rekap.lock-history') }}?tahun=" + tahun)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const history = data.data;
                const isLocked = history.is_locked;
                
                // Show appropriate modal based on lock status
                if (isLocked) {
                    showUnlockModal(tahun, history);
                } else {
                    showLockModal(tahun);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Gagal memuat status lock. Silakan coba lagi.');
        });
}

function showLockModal(tahun) {
    const modal = `
        <div id="lockModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" onclick="if(event.target.id === 'lockModal') closeLockModal()">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">🔒 Kunci Periode Performance</h3>
                <p class="text-gray-700 dark:text-gray-300 mb-4">Tahun: <strong>${tahun}</strong></p>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alasan Penguncian (Opsional)</label>
                    <textarea id="lockReason" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white text-sm" rows="3" placeholder="Contoh: Periode evaluasi ditutup"></textarea>
                </div>
                
                <div class="flex gap-3">
                    <button onclick="performLock(${tahun})" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all font-medium">
                        🔒 Kunci Sekarang
                    </button>
                    <button onclick="closeLockModal()" class="flex-1 px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-600 transition-all font-medium">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modal);
}

function showUnlockModal(tahun, history) {
    const modal = `
        <div id="unlockModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" onclick="if(event.target.id === 'unlockModal') closeUnlockModal()">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md max-h-96 overflow-y-auto">
                <h3 class="text-lg font-bold text-red-600 dark:text-red-400 mb-4">🔐 Periode Terkunci</h3>
                <p class="text-gray-700 dark:text-gray-300 mb-4">Tahun: <strong>${tahun}</strong></p>
                
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-red-900 dark:text-red-300 mb-2">📋 Riwayat Penguncian</h4>
                    <div class="space-y-2 text-sm text-red-800 dark:text-red-200">
                        <p><span class="font-medium">Dikunci oleh:</span> ${history.locked_by_name}</p>
                        <p><span class="font-medium">Tanggal Kunci:</span> ${history.locked_at}</p>
                        <p><span class="font-medium">Alasan:</span> ${history.locked_reason}</p>
                        ${history.unlocked_by_name !== '-' ? `
                            <hr class="border-red-300 dark:border-red-700 my-2">
                            <p><span class="font-medium">Dibuka oleh:</span> ${history.unlocked_by_name}</p>
                            <p><span class="font-medium">Tanggal Dibuka:</span> ${history.unlocked_at}</p>
                            <p><span class="font-medium">Alasan:</span> ${history.unlock_reason}</p>
                        ` : ''}
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alasan Pembukaan Kunci (Opsional)</label>
                    <textarea id="unlockReason" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white text-sm" rows="3" placeholder="Contoh: Ada koreksi data yang diperlukan"></textarea>
                </div>
                
                <div class="flex gap-3">
                    <button onclick="performUnlock(${tahun})" class="flex-1 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-all font-medium">
                        🔓 Buka Kunci
                    </button>
                    <button onclick="closeUnlockModal()" class="flex-1 px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-600 transition-all font-medium">
                        Tutup
                    </button>
                </div>
                
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-4 text-center">
                    ⚠️ Hanya Superadmin yang dapat membuka kunci periode
                </p>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modal);
}

function performLock(tahun) {
    const reason = document.getElementById('lockReason')?.value || '';
    
    fetch("{{ route('performance.rekap.lock') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            tahun: tahun,
            reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            closeLockModal();
            location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Gagal mengunci periode. Silakan coba lagi.');
    });
}

function performUnlock(tahun) {
    const reason = document.getElementById('unlockReason')?.value || '';
    
    fetch("{{ route('performance.rekap.unlock') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            tahun: tahun,
            reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            closeUnlockModal();
            location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Gagal membuka kunci periode. Silakan coba lagi.');
    });
}

function closeLockModal() {
    const modal = document.getElementById('lockModal');
    if (modal) {
        modal.remove();
    }
}

function closeUnlockModal() {
    const modal = document.getElementById('unlockModal');
    if (modal) {
        modal.remove();
    }
}

</script>
@endsection