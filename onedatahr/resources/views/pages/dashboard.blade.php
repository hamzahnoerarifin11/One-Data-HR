@extends('layouts.app') {{-- Sesuaikan dengan layout utama Anda --}}

@section('content')
<div class="p-4 sm:p-6 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">HR Dashboard</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Ringkasan Data Kepegawaian & Statistik</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Login sebagai:
                {{ auth()->user()->roles->pluck('name')->implode(', ') }}
            </p>
        </div>
        <div class="mt-2 sm:mt-0">
            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                Update Terakhir: {{ date('d M Y') }}
            </span>
        </div>
    </div>

    {{-- 1. Kartu Utama --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Karyawan</p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalKaryawan }}</h3>
                </div>
                <div class="p-3 bg-blue-50 text-blue-600 rounded-full dark:bg-blue-900/30"><i class="fas fa-users"></i></div>
            </div>
            <div class="mt-2 text-xs text-green-600 font-semibold">
                {{ $karyawanAktif }} Status Aktif
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Kontrak</p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalKontrak }}</h3>
                </div>
                <div class="p-3 bg-purple-50 text-purple-600 rounded-full dark:bg-purple-900/30"><i class="fas fa-file-contract"></i></div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Departemen</p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totaldepartment_id }}</h3>
                </div>
                <div class="p-3 bg-orange-50 text-orange-600 rounded-full dark:bg-orange-900/30"><i class="fas fa-building"></i></div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Gender Ratio</p>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <i class="fas fa-male text-blue-500"></i>
                    <span class="font-bold dark:text-white">{{ $genderData['Laki-laki'] ?? 0 }}</span>
                </div>
                <div class="w-px h-6 bg-gray-300"></div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-female text-pink-500"></i>
                    <span class="font-bold dark:text-white">{{ $genderData['Perempuan'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Statistik Detail (Grid 2 Kolom) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <h4 class="font-bold text-gray-800 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">Masa Kerja</h4>
            <div class="space-y-3">
                @foreach($tenureCounts as $label => $count)
                <div>
                    <div class="flex justify-between text-sm mb-1 dark:text-gray-300">
                        <span>{{ $label }}</span>
                        <span class="font-semibold">{{ $count }} Org</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                        @php $percent = $totalKaryawan > 0 ? ($count / $totalKaryawan) * 100 : 0; @endphp
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percent }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        {{-- 5. Kelompok Umur --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <h4 class="font-bold text-gray-800 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">
                Kelompok Umur Karyawan
            </h4>

            <div class="space-y-4">
                @foreach($ageCounts as $label => $count)
                    @php
                        $percent = $totalKaryawan > 0 ? ($count / $totalKaryawan) * 100 : 0;
                    @endphp

                    <div>
                        <div class="flex justify-between text-sm mb-1 dark:text-gray-300">
                            <span>{{ $label }} Tahun</span>
                            <span class="font-semibold">{{ $count }} Org</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                            <div
                                class="bg-green-500 h-2 rounded-full transition-all duration-300"
                                style="width: {{ $percent }}%">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>


        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <h4 class="font-bold text-gray-800 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">Pendidikan Terakhir</h4>
            <div class="grid grid-cols-2 gap-4">
                @foreach($pendidikanData as $pendidikan => $count)
                <div class="bg-gray-50 dark:bg-gray-700/50 p-3 rounded border border-gray-100 dark:border-gray-600">
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $pendidikan }}</div>
                    <div class="font-bold text-lg text-gray-800 dark:text-white">{{ $count }}</div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- 3. Statistik Jabatan --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <h4 class="font-bold text-gray-800 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">
            Total Karyawan per Jabatan
        </h4>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($jabatanData as $jabatan => $count)
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-100 dark:border-gray-600 hover:shadow transition">
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                        {{ $jabatan }}
                    </p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                        {{ $count }}
                    </p>
                    <p class="text-xs text-gray-400">Karyawan</p>
                </div>
            @endforeach
        </div>
    </div>
    {{-- 4. Statistik Divisi --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <h4 class="font-bold text-gray-800 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">
            Total Karyawan per Divisi
        </h4>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($divisiData as $divisi => $count)
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-100 dark:border-gray-600 hover:shadow transition">
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                        {{ $divisi }}
                    </p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                        {{ $count }}
                    </p>
                    <p class="text-xs text-gray-400">Karyawan</p>
                </div>
            @endforeach
        </div>
    </div>
    {{-- 5. Statistik Perusahaan --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <h4 class="font-bold text-gray-800 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">
            Total Karyawan per Perusahaan
        </h4>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($perusahaanData as $perusahaan => $count)
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-100 dark:border-gray-600 hover:shadow transition">
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                        {{ $perusahaan }}
                    </p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                        {{ $count }}
                    </p>
                    <p class="text-xs text-gray-400">Karyawan</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- 6. Grafik Turnover --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <h4 class="font-bold text-gray-800 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">
            Turnover Karyawan per Bulan ({{ date('Y') }})
        </h4>
        <div id="turnoverChart" class="w-full h-80"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const turnoverData = @json($turnoverData);
    const companies = Object.keys(turnoverData[1] || {});

    const series = [];
    companies.forEach(company => {
        const masukData = [];
        const keluarData = [];
        for (let month = 1; month <= 12; month++) {
            masukData.push(turnoverData[month]?.[company]?.masuk || 0);
            keluarData.push(turnoverData[month]?.[company]?.keluar || 0);
        }
        series.push({
            name: company + ' - Masuk',
            data: masukData,
            type: 'column'
        });
        series.push({
            name: company + ' - Keluar',
            data: keluarData,
            type: 'column'
        });
    });

    const options = {
        series: series,
        chart: {
            type: 'line',
            height: 320,
            toolbar: { show: false }
        },
        stroke: {
            width: [2, 2, 2, 2],
            curve: 'smooth'
        },
        plotOptions: {
            bar: {
                columnWidth: '50%'
            }
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            title: { text: 'Jumlah Karyawan' }
        },
        colors: ['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899'],
        grid: {
            borderColor: '#E5E7EB',
            strokeDashArray: 3
        },
        legend: {
            position: 'top',
            horizontalAlign: 'left'
        }
    };

    const chart = new ApexCharts(document.querySelector("#turnoverChart"), options);
    chart.render();
});
</script>
@endsection
