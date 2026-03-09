@extends('layouts.app')
@section('title','Dashboard Rekrutmen')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    {{-- Header Section --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Dashboard Rekrutmen
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Overview statistik funnel rekrutmen tahun <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $year }}</span>
                </p>
            </div>
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 px-4 py-2 rounded-lg">
                <p class="text-xs font-semibold text-blue-700 dark:text-blue-300">Update: {{ date('d M Y H:i') }}</p>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="mb-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form method="GET" action="{{ route('rekrutmen.dashboard') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>Tahun
                </label>
                <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">

                <select name="year" onchange="this.form.submit()" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                           :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                            Semua Tahun {{ $y }}
                        </option>
                    @endforeach
                </select>
                <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </div>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-briefcase mr-2 text-purple-600"></i>Posisi
                </label>
                <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                <select name="posisi_id" onchange="this.form.submit()" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                           :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                    <option value="">-- Semua Posisi --</option>
                    @foreach($posisi as $p)
                        <option value="{{ $p->id_posisi }}" {{ $posisiId == $p->id_posisi ? 'selected' : '' }}>
                            {{ $p->nama_posisi }}
                        </option>
                    @endforeach
                </select>
                <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </div>
            </div>
        </form>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Total Pelamar --}}
        <div class="group rounded-xl border border-gray-200 bg-gradient-to-br from-blue-50 to-blue-100 p-6 shadow-sm hover:shadow-md dark:border-gray-700 dark:from-blue-900/20 dark:to-blue-900/10 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Pelamar</p>
                    <h3 class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $funnelData['Total Pelamar'] }}</h3>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Orang</p>
                </div>
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-blue-200 text-blue-700 dark:bg-blue-800 dark:text-blue-300 group-hover:scale-110 transition-transform">
                    <i class="fas fa-user-plus text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Lolos CV --}}
        <div class="group rounded-xl border border-gray-200 bg-gradient-to-br from-green-50 to-green-100 p-6 shadow-sm hover:shadow-md dark:border-gray-700 dark:from-green-900/20 dark:to-green-900/10 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Lolos CV</p>
                    <h3 class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $funnelData['Lolos CV'] }}</h3>
                    <p class="mt-1 text-xs text-green-600 dark:text-green-400 font-semibold">
                        {{ $conversionRates['cv'] }}% dari total
                    </p>
                </div>
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-green-200 text-green-700 dark:bg-green-800 dark:text-green-300 group-hover:scale-110 transition-transform">
                    <i class="fas fa-file text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Lolos Psikotes --}}
        <div class="group rounded-xl border border-gray-200 bg-gradient-to-br from-purple-50 to-purple-100 p-6 shadow-sm hover:shadow-md dark:border-gray-700 dark:from-purple-900/20 dark:to-purple-900/10 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Lolos Psikotes</p>
                    <h3 class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $funnelData['Lolos Psikotes'] }}</h3>
                    <p class="mt-1 text-xs text-purple-600 dark:text-purple-400 font-semibold">
                        {{ $conversionRates['psikotes'] }}% dari CV
                    </p>
                </div>
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-purple-200 text-purple-700 dark:bg-purple-800 dark:text-purple-300 group-hover:scale-110 transition-transform">
                    <i class="fas fa-brain text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Hired --}}
        <div class="group rounded-xl border border-gray-200 bg-gradient-to-br from-orange-50 to-orange-100 p-6 shadow-sm hover:shadow-md dark:border-gray-700 dark:from-orange-900/20 dark:to-orange-900/10 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Hired (Selesai)</p>
                    <h3 class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $funnelData['Hired (Selesai)'] }}</h3>
                    <p class="mt-1 text-xs text-orange-600 dark:text-orange-400 font-semibold">
                        {{ $totalPelamar > 0 ? round(($funnelData['Hired (Selesai)'] / $totalPelamar) * 100, 2) : 0 }}% dari total
                    </p>
                </div>
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-orange-200 text-orange-700 dark:bg-orange-800 dark:text-orange-300 group-hover:scale-110 transition-transform">
                    <i class="fas fa-thumbs-up text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Funnel Visualization --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        {{-- Left: Funnel Progress --}}
        <div class="lg:col-span-2 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-filter text-blue-600"></i>Funnel Progress
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Visualisasi progres kandidat melalui setiap tahap</p>
            </div>

            <div class="space-y-5">
                @php
                    $stages = [
                        ['label' => '1. Total Pelamar', 'value' => $funnelData['Total Pelamar'], 'color' => 'from-blue-500 to-blue-600', 'icon' => 'fa-users', 'percent' => 100],
                        ['label' => '2. Lolos CV', 'value' => $funnelData['Lolos CV'], 'color' => 'from-green-500 to-green-600', 'icon' => 'fa-file', 'percent' => $totalPelamar > 0 ? ($funnelData['Lolos CV'] / $totalPelamar) * 100 : 0],
                        ['label' => '3. Lolos Psikotes', 'value' => $funnelData['Lolos Psikotes'], 'color' => 'from-purple-500 to-purple-600', 'icon' => 'fa-brain', 'percent' => $totalPelamar > 0 ? ($funnelData['Lolos Psikotes'] / $totalPelamar) * 100 : 0],
                        ['label' => '4. Lolos Kompetensi', 'value' => $funnelData['Lolos Kompetensi'], 'color' => 'from-cyan-500 to-cyan-600', 'icon' => 'fa-check-circle', 'percent' => $totalPelamar > 0 ? ($funnelData['Lolos Kompetensi'] / $totalPelamar) * 100 : 0],
                        ['label' => '5. Lolos Interview HR', 'value' => $funnelData['Lolos Interview HR'], 'color' => 'from-yellow-500 to-yellow-600', 'icon' => 'fa-handshake', 'percent' => $totalPelamar > 0 ? ($funnelData['Lolos Interview HR'] / $totalPelamar) * 100 : 0],
                        ['label' => '6. Lolos User', 'value' => $funnelData['Lolos User'], 'color' => 'from-red-500 to-red-600', 'icon' => 'fa-star', 'percent' => $totalPelamar > 0 ? ($funnelData['Lolos User'] / $totalPelamar) * 100 : 0],
                        ['label' => '7. Hired (Selesai)', 'value' => $funnelData['Hired (Selesai)'], 'color' => 'from-orange-500 to-orange-600', 'icon' => 'fa-trophy', 'percent' => $totalPelamar > 0 ? ($funnelData['Hired (Selesai)'] / $totalPelamar) * 100 : 0],
                    ];
                @endphp

                @foreach($stages as $stage)
                <div>
                    <div class="mb-2 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r {{ $stage['color'] }} text-white">
                                <i class="fas {{ $stage['icon'] }} text-sm"></i>
                            </div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $stage['label'] }}</span>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $stage['value'] }}</div>
                            <div class="text-xs text-gray-500">{{ round($stage['percent'], 1) }}%</div>
                        </div>
                    </div>
                    <div class="h-3 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                        <div class="h-3 rounded-full bg-gradient-to-r {{ $stage['color'] }} transition-all duration-500" style="width: {{ $stage['percent'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Right: Summary Cards --}}
        <div class="space-y-4">
            {{-- Conversion Rate --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-line text-green-600"></i>Conversion Rate
                </h4>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">CV → Psikotes</span>
                        <span class="font-bold text-green-600 dark:text-green-400">{{ $conversionRates['psikotes'] }}%</span>
                    </div>
                    <div class="h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-1 bg-green-500" style="width: {{ $conversionRates['psikotes'] }}%"></div>
                    </div>

                    <div class="flex justify-between items-center pt-2">
                        <span class="text-gray-600 dark:text-gray-400">Psikotes → Kompetensi</span>
                        <span class="font-bold text-purple-600 dark:text-purple-400">{{ $conversionRates['kompetensi'] }}%</span>
                    </div>
                    <div class="h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-1 bg-purple-500" style="width: {{ $conversionRates['kompetensi'] }}%"></div>
                    </div>

                    <div class="flex justify-between items-center pt-2">
                        <span class="text-gray-600 dark:text-gray-400">HR → User</span>
                        <span class="font-bold text-cyan-600 dark:text-cyan-400">{{ $conversionRates['user'] }}%</span>
                    </div>
                    <div class="h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-1 bg-cyan-500" style="width: {{ $conversionRates['user'] }}%"></div>
                    </div>

                    <div class="flex justify-between items-center pt-2">
                        <span class="text-gray-600 dark:text-gray-400">User → Hired</span>
                        <span class="font-bold text-orange-600 dark:text-orange-400">{{ $conversionRates['hired'] }}%</span>
                    </div>
                    <div class="h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-1 bg-orange-500" style="width: {{ $conversionRates['hired'] }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Ditolak --}}
            <div class="rounded-xl border border-red-200 dark:border-red-800 bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-900/10 p-5 shadow-sm">
                <h4 class="text-sm font-bold text-red-900 dark:text-red-300 mb-3 flex items-center gap-2">
                    <i class="fas fa-times-circle"></i>Ditolak
                </h4>
                <div class="text-3xl font-bold text-red-600 dark:text-red-400 mb-2">{{ $funnelData['Ditolak'] }}</div>
                <div class="text-xs text-red-600 dark:text-red-400">
                    {{ $totalPelamar > 0 ? round(($funnelData['Ditolak'] / $totalPelamar) * 100, 2) : 0 }}% dari total pelamar
                </div>
            </div>

            {{-- Effective Rate --}}
            <div class="rounded-xl border border-blue-200 dark:border-blue-800 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-900/10 p-5 shadow-sm">
                <h4 class="text-sm font-bold text-blue-900 dark:text-blue-300 mb-3 flex items-center gap-2">
                    <i class="fas fa-chart-bar"></i>Effective Rate
                </h4>
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">
                    {{ $totalPelamar > 0 ? round(($funnelData['Hired (Selesai)'] / $totalPelamar) * 100, 2) : 0 }}%
                </div>
                <div class="text-xs text-blue-600 dark:text-blue-400">
                    {{ $funnelData['Hired (Selesai)'] }} dari {{ $totalPelamar }} pelamar diterima
                </div>
            </div>
        </div>
    </div>

    <!-- {{-- Statistics by Position (jika belum filter) --}}
    @if(!$posisiId && count($statsByPosition) > 0)
    <div class="mb-8 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            <i class="fas fa-layer-group text-purple-600"></i>Statistik per Posisi
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($statsByPosition as $stat)
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-all">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white">{{ $stat->nama_posisi }}</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $stat->total }} pelamar</p>
                    </div>
                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-bold">
                        {{ $stat->total }}
                    </span>
                </div>

                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">CV Lolos</span>
                        <span class="font-bold text-green-600 dark:text-green-400">{{ $stat->cv_lolos }} ({{ $stat->total > 0 ? round(($stat->cv_lolos / $stat->total) * 100) : 0 }}%)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Psikotes</span>
                        <span class="font-bold text-purple-600 dark:text-purple-400">{{ $stat->psikotes_lolos }} ({{ $stat->total > 0 ? round(($stat->psikotes_lolos / $stat->total) * 100) : 0 }}%)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Kompetensi</span>
                        <span class="font-bold text-cyan-600 dark:text-cyan-400">{{ $stat->kompetensi_lolos }} ({{ $stat->total > 0 ? round(($stat->kompetensi_lolos / $stat->total) * 100) : 0 }}%)</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif -->

    {{-- Monthly Distribution --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            <i class="fas fa-calendar text-indigo-600"></i>Distribusi Pelamar per Bulan ({{ $year }})
        </h3>

        <div class="overflow-x-auto">
            <div class="grid grid-cols-12 gap-2 min-w-max">
                @php
                    $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    $monthData = [];
                    foreach($monthlyData as $data) {
                        $monthData[$data->month] = $data->total;
                    }
                    $maxMonth = count($monthData) > 0 ? max($monthData) : 1;
                @endphp

                @for($month = 1; $month <= 12; $month++)
                    @php $count = $monthData[$month] ?? 0; @endphp
                    <div class="flex flex-col items-center">
                        <div class="mb-2">
                            <div class="h-24 w-10 rounded-t-lg bg-gradient-to-t from-blue-500 to-blue-400 dark:from-blue-600 dark:to-blue-500 flex items-end justify-center pb-1 transition-all hover:shadow-lg" style="height: {{ $count > 0 ? (($count / $maxMonth) * 100) . 'px' : '8px' }}">
                                <span class="text-xs font-bold text-white mb-1">{{ $count }}</span>
                            </div>
                        </div>
                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">{{ $monthNames[$month-1] }}</span>
                    </div>
                @endfor
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Pelamar</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalPelamar }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Rata-rata/Bulan</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ round($totalPelamar / 12, 0) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Bulan Tertinggi</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $maxMonth }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Bulan Terdata</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ count($monthData) }}</p>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    @media (prefers-color-scheme: dark) {
        .group:hover {
            border-color: rgb(59, 130, 246);
        }
    }
</style>
@endsection
