@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 space-y-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white tracking-tight">Dashboard SDM Eksekutif</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Ringkasan Metrik Strategis & Analitik Kepegawaian</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm flex items-center gap-2">
                <i class="fas fa-calendar text-blue-500"></i>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ \Carbon\Carbon::parse($filters['start_date'])->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($filters['end_date'])->translatedFormat('d M Y') }}
                </span>
            </div>
            <button @click="openFilters = !openFilters" x-data="{ openFilters: false }" class="md:hidden p-2 text-gray-500 rounded hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-filter"></i>
            </button>
        </div>
    </div>

    <!-- Filter Section (Collapsible) -->
    <div x-data="{ open: true }" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center cursor-pointer" @click="open = !open">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                <i class="fas fa-sliders-h mr-2"></i> Filter Data
            </h3>
            <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300" :class="{ 'rotate-180': open }"></i>
        </div>
        <div x-show="open" x-collapse>
             <form method="GET" action="{{ route('dashboard.index') }}" class="p-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">
                <div class="flex items-center gap-2">
                <!-- Holding -->
                <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                    Holding
                </label>
                <select name="holding_id" onchange="this.form.submit()"
                    class="w-full h-10 text-sm border-gray-300 rounded-lg shadow-sm
                        focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                        dark:bg-gray-700 dark:border-gray-600 dark:text-white transition">
                    <option value="">Semua Holding</option>
                    @foreach($holdings as $h)
                        <option value="{{ $h->id }}" {{ request('holding_id') == $h->id ? 'selected' : '' }}>
                            {{ $h->name }}
                        </option>
                    @endforeach
                </select>
                </div>
            </div>

                <!-- Company -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                        Perusahaan
                    </label>
                    <select name="company_id" onchange="this.form.submit()"
                        class="w-full h-10 text-sm border-gray-300 rounded-lg shadow-sm
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                               dark:bg-gray-700 dark:border-gray-600 dark:text-white transition">
                        <option value="">Semua Perusahaan</option>
                        @foreach($companies as $c)
                            <option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>


                 <!-- Division -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                        Divisi
                    </label>
                    <select name="division_id" onchange="this.form.submit()"
                        class="w-full h-10 text-sm border-gray-300 rounded-lg shadow-sm
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                               dark:bg-gray-700 dark:border-gray-600 dark:text-white transition">
                        <option value="">Semua Divisi</option>
                        @foreach($divisions as $d)
                            <option value="{{ $d->id }}" {{ request('division_id') == $d->id ? 'selected' : '' }}>
                                {{ $d->name }}
                            </option>
                        @endforeach
                    </select>
                </div>


                 <!-- Date Range -->
                 <!-- Tanggal Mulai -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                            Tanggal Mulai
                        </label>

                        <input type="date"
                            name="start_date"
                            value="{{ $filters['start_date'] }}"
                            class="w-full h-10 text-sm border-gray-300 rounded-lg shadow-sm
                                focus:ring-blue-500
                                dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <!-- Tanggal Akhir -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                            Tanggal Akhir
                        </label>

                        <div class="flex items-center gap-2">
                            <input type="date"
                                name="end_date"
                                value="{{ $filters['end_date'] }}"
                                class="flex-1 h-10 text-sm border-gray-300 rounded-lg shadow-sm
                                    focus:ring-blue-500
                                    dark:bg-gray-700 dark:border-gray-600 dark:text-white">

                            <button type="submit"
                                class="h-10 px-4 bg-blue-600 hover:bg-blue-700 
                                    text-white rounded-lg shadow-sm transition-colors"
                                title="Terapkan Filter">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>


                                </form>
                            </div>
                        </div>

    <!-- Main Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Stat Card: Total Employees -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-5 text-white shadow-lg shadow-blue-200 dark:shadow-none relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-blue-100 text-sm font-medium mb-1">Total Karyawan</p>
                <h3 class="text-3xl font-bold">{{ number_format($totalKaryawan) }}</h3>
                <div class="mt-4 flex items-center gap-2 text-xs bg-white/20 w-fit px-2 py-1 rounded-md backdrop-blur-sm">
                    <i class="fas fa-check-circle"></i> {{ $karyawanAktif }} Aktif
                </div>
            </div>
            <i class="fas fa-users absolute -right-4 -bottom-4 text-8xl text-white/10 group-hover:scale-110 transition-transform"></i>
        </div>

        <!-- Stat Card: Turnover Rate -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:border-blue-500 transition-colors">
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-1">Turnover Rate (Bulan Ini)</p>
                @php $currentRate = end($turnoverData)['rate'] ?? 0; @endphp
                <h3 class="text-3xl font-bold text-gray-800 dark:text-white">{{ $currentRate }}%</h3>
                <p class="text-xs text- {{ $currentRate > 2 ? 'text-red-500' : 'text-green-500' }} mt-2 flex items-center gap-1">
                    <i class="fas fa-{{ $currentRate > 2 ? 'arrow-up' : 'arrow-down' }}"></i>  Target < 2%
                </p>
            </div>
            <div class="absolute right-4 top-4 p-3 bg-red-50 dark:bg-red-900/30 text-red-500 rounded-full">
                <i class="fas fa-chart-line text-xl"></i>
            </div>
        </div>

        <!-- Stat Card: Total Departemen -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:border-purple-500 transition-colors">
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-1">Total Departemen</p>
                <h3 class="text-3xl font-bold text-gray-800 dark:text-white">{{ $totaldepartment_id }}</h3>
                <p class="text-xs text-gray-400 mt-2">Unit Operasional</p>
            </div>
            <div class="absolute right-4 top-4 p-3 bg-purple-50 dark:bg-purple-900/30 text-purple-500 rounded-full">
                <i class="fas fa-building text-xl"></i>
            </div>
        </div>

        <!-- Stat Card: Gender Ratio -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-1">Rasio Gender</p>
            <div class="flex items-center gap-4 mt-2">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-blue-600 dark:text-blue-300">
                        <i class="fas fa-mars"></i>
                    </div>
                    <div>
                        <span class="block text-lg font-bold text-gray-800 dark:text-white">{{ $genderData['Laki-laki'] ?? 0 }}</span>
                    </div>
                </div>
                <div class="h-8 w-px bg-gray-200 dark:bg-gray-700"></div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-pink-100 dark:bg-pink-900 flex items-center justify-center text-pink-600 dark:text-pink-300">
                        <i class="fas fa-venus"></i>
                    </div>
                    <div>
                        <span class="block text-lg font-bold text-gray-800 dark:text-white">{{ $genderData['Perempuan'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section: Main Analysis -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Turnover Trend (Large) -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex justify-between items-center mb-6">
                <h4 class="text-lg font-bold text-gray-800 dark:text-white">Analisis Turnover & Rekrutmen</h4>
                <select id="turnoverFilter" class="text-xs border-gray-300 rounded-md dark:bg-gray-700 dark:text-white">
                    <option value="6">6 Bulan Terakhir</option>
                    <option value="12">1 Tahun</option>
                </select>
            </div>
            <div id="turnoverChart" class="w-full h-[350px]"></div>
        </div>

        <!-- Level Distribution -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-6">Distribusi Level Jabatan</h4>
            <div class="space-y-4 overflow-y-auto max-h-[350px] custom-scrollbar pr-2">
                @foreach($levelData as $level => $count)
                <div class="group">
                    <div class="flex justify-between text-sm mb-1 text-gray-700 dark:text-gray-300">
                        <span class="font-medium group-hover:text-blue-600 transition">{{ $level }}</span>
                        <span class="font-bold">{{ $count }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2 dark:bg-gray-700">
                        @php $percent = $totalKaryawan > 0 ? ($count / $totalKaryawan) * 100 : 0; @endphp
                        <div class="bg-blue-500 h-2 rounded-full transition-all duration-500 group-hover:bg-blue-600" style="width: {{ $percent }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Entity Stats Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Holding Stats -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg">
                    <i class="fas fa-sitemap"></i>
                </div>
                <h4 class="text-lg font-bold text-gray-800 dark:text-white">Holding</h4>
            </div>
             <div class="space-y-3">
                @foreach($holdingData as $name => $count)
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-750 rounded-lg">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $name }}</span>
                        <span class="text-sm font-bold bg-white dark:bg-gray-600 px-2 py-1 rounded shadow-sm">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Parent Company Stats -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-emerald-100 text-emerald-600 rounded-lg">
                    <i class="fas fa-building"></i>
                </div>
                <h4 class="text-lg font-bold text-gray-800 dark:text-white">Perusahaan Induk</h4>
            </div>
             <div class="space-y-3 overflow-y-auto max-h-60 custom-scrollbar pr-2">
                @foreach($parentCompanyData as $name => $count)
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-750 rounded-lg">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate w-3/4">{{ $name }}</span>
                        <span class="text-sm font-bold bg-white dark:bg-gray-600 px-2 py-1 rounded shadow-sm">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Subsidiary Stats -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-3 mb-6">
                 <div class="p-2 bg-amber-100 text-amber-600 rounded-lg">
                    <i class="fas fa-network-wired"></i>
                </div>
                <h4 class="text-lg font-bold text-gray-800 dark:text-white">Anak Perusahaan</h4>
            </div>
             <div class="space-y-3 overflow-y-auto max-h-60 custom-scrollbar pr-2">
                @foreach($subsidiaryData as $name => $count)
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-750 rounded-lg">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate w-3/4">{{ $name }}</span>
                        <span class="text-sm font-bold bg-white dark:bg-gray-600 px-2 py-1 rounded shadow-sm">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Secondary Charts: Tenure, Age, Education -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Age Demographics -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <h4 class="text-md font-bold text-gray-800 dark:text-white mb-4">Kelompok Umur</h4>
            <div id="ageChart" class="h-64"></div>
        </div>

        <!-- Tenure -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <h4 class="text-md font-bold text-gray-800 dark:text-white mb-4">Masa Kerja</h4>
            <div id="tenureChart" class="h-64"></div>
        </div>

        <!-- Education -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <h4 class="text-md font-bold text-gray-800 dark:text-white mb-4">Pendidikan</h4>
            <div class="space-y-3 overflow-y-auto max-h-64 custom-scrollbar">
                @foreach($pendidikanData as $edu => $count)
                    <div class="flex items-center justify-between border-b border-gray-50 dark:border-gray-700 pb-2">
                         <span class="text-xs font-semibold text-gray-500 uppercase">{{ $edu ?: 'N/A' }}</span>
                         <span class="text-sm font-bold text-gray-800 dark:text-white">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Data ---
    const turnoverRaw = @json($turnoverData);
    const months = turnoverRaw.map(d => d.month);
    const masuk = turnoverRaw.map(d => d.masuk);
    const keluar = turnoverRaw.map(d => d.keluar);
    const netGrowth = turnoverRaw.map(d => d.masuk - d.keluar); // Net Growth

    // 1. Enhanced Turnover Chart (Mixed)
    const turnoverOptions = {
        series: [{
            name: 'Karyawan Masuk',
            type: 'column',
            data: masuk
        }, {
            name: 'Karyawan Keluar',
            type: 'column',
            data: keluar
        }, {
            name: 'Pertumbuhan Bersih',
            type: 'line',
            data: netGrowth
        }],
        chart: {
            height: 350,
            type: 'line',
            fontFamily: 'Inter, sans-serif',
            toolbar: { show: false }
        },
        stroke: {
            width: [0, 0, 3],
            curve: 'smooth'
        },
        plotOptions: {
            bar: {
                columnWidth: '50%',
                borderRadius: 4
            }
        },
        fill: {
            opacity: [0.85, 0.85, 1],
        },
        labels: months,
        colors: ['#10B981', '#EF4444', '#3B82F6'], // Green, Red, Blue
        markers: { size: 4 },
        yaxis: [{
            title: { text: 'Jumlah Karyawan' },
        }],
        legend: { position: 'top' },
        grid: { borderColor: '#f3f4f6' }
    };
    new ApexCharts(document.querySelector("#turnoverChart"), turnoverOptions).render();

    // 2. Age Chart (Radial Bar for variety)
    const ageRaw = @json($ageCounts);
    const ageOptions = {
        series: Object.values(ageRaw),
        labels: Object.keys(ageRaw),
        chart: {
            type: 'polarArea',
            height: 280,
            fontFamily: 'Inter, sans-serif'
        },
        stroke: { colors: ['#fff'] },
        fill: { opacity: 0.8 },
        colors: ['#60A5FA', '#34D399', '#FBBF24', '#F472B6', '#A78BFA', '#9CA3AF'],
        legend: { position: 'bottom' }
    };
    new ApexCharts(document.querySelector("#ageChart"), ageOptions).render();

    // 3. Tenure Chart (Horizontal Bar)
    const tenureRaw = @json($tenureCounts);
    const tenureOptions = {
        series: [{
            name: 'Jumlah',
            data: Object.values(tenureRaw)
        }],
        chart: {
            type: 'bar',
            height: 250,
            toolbar: { show: false },
            fontFamily: 'Inter, sans-serif'
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: true,
            }
        },
        colors: ['#F59E0B'],
        xaxis: {
            categories: Object.keys(tenureRaw),
        }
    };
    new ApexCharts(document.querySelector("#tenureChart"), tenureOptions).render();
});
</script>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
.dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #4b5563; }
</style>
@endsection

