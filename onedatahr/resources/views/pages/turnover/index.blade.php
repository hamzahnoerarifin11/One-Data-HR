@extends('layouts.app')

@section('content')
<div class="p-4 md:p-6 2xl:p-10 min-h-screen">

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Data Turnover Karyawan
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">
                Monitoring Data Turnover Karyawan Baru
            </p>
        </div>

        <div class="flex items-center gap-3 bg-white dark:bg-gray-800 py-2 px-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 w-fit">
            <div class="flex -space-x-2">
                <div class="h-8 w-8 rounded-full border-2 bg-blue-500 flex items-center justify-center text-white text-xs">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="text-sm">
                <p class="text-gray-500 dark:text-gray-400 leading-none text-xs">Total Data</p>
                <p class="font-bold text-gray-800 dark:text-white">{{ $totalData }} Karyawan</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 mb-6">

        <div class="bg-white dark:bg-gray-800 p-5 rounded-sm border border-gray-200 dark:border-gray-700 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Karyawan</p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalData }}</h3>

                </div>
                <div class="h-12 w-12 flex items-center justify-center rounded-lg bg-blue-50 text-blue-500 dark:bg-blue-900/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-3">Jumlah Data Karyawan</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-sm border border-gray-200 dark:border-gray-700 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Lolos (> 3 Bln)</p>
                    <h3 class="text-2xl font-bold text-green-500">{{ $totalLolos }}</h3>
                </div>
                <div class="h-12 w-12 flex items-center justify-center rounded-lg bg-green-50 text-green-500 dark:bg-green-900/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
             <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-3">Tetap bertahan > 3 bulan</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-sm border border-gray-200 dark:border-gray-700 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Turnover (< 3 Bln)</p>
                    <h3 class="text-2xl font-bold text-red-500">{{ $totalTurnover }}</h3>
                </div>
                <div class="h-12 w-12 flex items-center justify-center rounded-lg bg-red-50 text-red-500 dark:bg-red-900/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-3">Resign < 3 bulan</p>
            </div>

        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-sm border border-gray-200 dark:border-gray-700 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Turnover Rate</p>
                    <h3 class="text-2xl font-bold text-yellow-500">{{ $turnoverRate }}%</h3>
                </div>
                <div class="h-12 w-12 flex items-center justify-center rounded-lg bg-yellow-50 text-yellow-500 dark:bg-yellow-900/30">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 17 13.5 8.5 8.5 13.5 2 7"></polyline><polyline points="16 17 22 17 22 11"></polyline></svg>
                </div>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-3">Presentase turnover</p>
            </div>
        </div>
    </div>

    <div id="turnoverExportArea" class="grid grid-cols-1 gap-6">

        <div class="bg-white dark:bg-gray-800 rounded-sm border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h4 class="text-lg font-bold text-gray-800 dark:text-white">Trend Turnover Bulanan</h4>
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <form method="GET" class="w-full sm:w-auto">
                        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                        <select name="periode"
                            onchange="this.form.submit()"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                        :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                            <option value="">-- Semua Periode (Default: Tahun Saat Ini) --</option>
                            @foreach($listPeriode as $p)
                                <option value="{{ $p['value'] }}" {{ ((isset($periode) ? $periode : $tahun.'-0') == $p['value']) ? 'selected' : '' }}>
                                    {{ $p['label'] }}
                                </option>

                            @endforeach
                        </select>
                        <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                                    <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                        <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                        </div>
                    </form>

                    <div class="flex items-center gap-2">
                        <form method="GET" action="{{ route('turnover.export.excel') }}" class="inline-block">
                            <input type="hidden" name="periode" value="">
                            <button id="exportExcelBtn" type="submit" class="px-3 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700">Excel</button>
                        </form>
                        <form method="GET" action="{{ route('turnover.export.pdf') }}" class="inline-block" target="_blank">
                            <input type="hidden" name="periode" value="">
                            <button id="exportPdfBtn" type="submit" class="px-3 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700">PDF</button>
                        </form>
                        <!-- <button id="exportJpgBtn" type="button" class="px-3 py-2 bg-yellow-600 text-white rounded-md text-sm hover:bg-yellow-700">JPG (Area)</button> -->
                        <button id="exportChartJpgBtn" type="button" class="px-3 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">JPG (Chart)</button>
                    </div>
                </div>
            </div>
            <x-common.component-card title="Line Chart Turnover">
                <div class="custom-scrollbar max-w-full overflow-x-auto">
                    <div id="turnoverLineChart" class="min-w-[1000px]"></div>
                </div>
            </x-common.component-card>

            <!-- <div class="p-4 md:p-6">
                <div class="relative h-[300px] w-full">
                    <canvas id="turnoverChart"></canvas>
                </div>
            </div> -->
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-sm border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex flex-wrap items-center justify-between gap-4">
                <h4 class="text-lg font-bold text-gray-800 dark:text-white">Daftar Status Karyawan</h4>
                <div class="flex items-center gap-2">
                    <span class="inline-block w-3 h-3 rounded-full bg-green-500"></span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Lolos Verifikasi > 90 Hari</span>
                </div>
            </div>

            <div class="overflow-x-auto w-full">
                <table id="turnoverTable" class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50">
                            <th class="py-4 px-6 text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Nama Kandidat</th>
                            <th class="py-4 px-6 text-xs font-bold uppercase text-gray-500 dark:text-gray-400 whitespace-nowrap">Posisi</th>
                            <th class="py-4 px-6 text-xs font-bold uppercase text-gray-500 dark:text-gray-400 whitespace-nowrap text-center">Masa Kerja</th>
                            <th class="py-4 px-6 text-xs font-bold uppercase text-gray-500 dark:text-gray-400 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($onboardings as $item)
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="py-4 px-6">
                                <div class="font-semibold text-gray-800 dark:text-white">{{ $item->kandidat->nama ?? '-' }}</div>
                                <div class="text-[11px] text-gray-500 dark:text-gray-400 italic">Kontrak: {{ $item->formatTanggal('jadwal_ttd_kontrak') }}</div>
                                @if($item->status_turnover_auto === 'turnover')
                                    <div class="text-red-600 text-[11px] dark:text-red-400 text-xs font-semibold">
                                        Resign
                                    </div>
                                    <div class="text-[11px] text-gray-500 dark:text-gray-400">
                                        {{ $item->formatTanggal('tanggal_resign') ?? '-' }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-600 dark:text-gray-300">
                                {{ $item->posisi->nama_posisi ?? '-' }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-200">{{ $item->masa_kerja_bulan }} Bulan</span>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400">{{ $item->masa_kerja_hari }} Hari</p>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($item->status_turnover_auto === 'lolos')
                                    <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800">
                                        LOLOS
                                    </span>
                                @elseif($item->status_turnover_auto === 'turnover')
                                    <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800">
                                        TURNOVER
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 border border-gray-200">
                                        PROCESS
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-800/50">
                <p class="text-xs text-gray-500 dark:text-gray-400">Total data ditampilkan: <strong>{{ $onboardings->count() }}</strong></p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const data = {!! json_encode($turnoverBulanan) !!};
    const chartYear = {!! json_encode($chartYear) !!};

    const seriesData = data.map(item => item.total);
    const categories = data.map(item =>
        new Date(chartYear, item.bulan - 1, 1).toLocaleString('id-ID', { month: 'long' })
    );

    const options = {
        chart: {
            height: 350,
            type: 'line',
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        series: [{
            name: 'Turnover',
            data: seriesData
        }],
        stroke: {
            curve: 'smooth',
            width: 3
        },
        markers: {
            size: 5,
            strokeWidth: 2,
            hover: { size: 7 }
        },
        colors: ['#ef4444'],
        xaxis: {
            categories,
            labels: {
                style: {
                    colors: '#94a3b8',
                    fontSize: '12px'
                }
            }
        },
        yaxis: {
            min: 0,
            tickAmount: 5,
            labels: {
                style: {
                    colors: '#94a3b8',
                    fontSize: '12px'
                }
            }
        },
        grid: {
            borderColor: '#e5e7eb',
            strokeDashArray: 4
        },
        tooltip: {
            className: 'mb-0 border-b-0 bg-transparent p-0 text-[10px] leading-4 text-gray-800 dark:text-white/90'
            // classmb-0 !border-b-0 !bg-transparent !p-0 !text-[10px] !leading-4 !text-gray-800 dark:!text-white/90;
        }
    };

    const turnoverChart = new ApexCharts(
        document.querySelector("#turnoverLineChart"),
        options
    );

    turnoverChart.render();
    // expose for export
    window.turnoverChart = turnoverChart;

    // Helper: get periode label for filenames
    function getPeriodeLabel() {
        const sel = document.querySelector('select[name="periode"]');
        return sel && sel.selectedOptions && sel.selectedOptions[0] ? sel.selectedOptions[0].text.trim().replace(/\s+/g, '_') : 'periode_all';
    }

    // Helper: download blob
    function downloadBlob(blob, filename) {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
    }

    // Replace client-side Excel/PDF export with form submission to server endpoints
    const excelRoute = "{{ route('turnover.export.excel') }}";
    const pdfRoute = "{{ route('turnover.export.pdf') }}";
    document.querySelectorAll(`form[action="${excelRoute}"], form[action="${pdfRoute}"]`).forEach(function (form) {
        form.addEventListener('submit', function (e) {
            const sel = document.querySelector('select[name="periode"]');
            let val = '';
            if (sel && sel.value) val = sel.value;
            let input = this.querySelector('input[name="periode"]');
            if (! input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'periode';
                this.appendChild(input);
            }
            input.value = val;
            // allow submit to proceed
        });
    });

    // Export entire area to JPG
    // document.getElementById('exportJpgBtn').addEventListener('click', function () {
    //     const el = document.getElementById('turnoverExportArea');
    //     html2canvas(el, { scale: 2 }).then(canvas => {
    //         canvas.toBlob(function (blob) {
    //             const filename = `turnover_${getPeriodeLabel()}_${Date.now()}.jpg`;
    //             downloadBlob(blob, filename);
    //         }, 'image/jpeg', 0.92);
    //     }).catch(err => { console.error(err); alert('Gagal membuat JPG'); });
    // });


    // Export only chart to JPG using ApexCharts dataURI
    document.getElementById('exportChartJpgBtn').addEventListener('click', function () {
        if (!window.turnoverChart) return alert('Chart belum siap');
        window.turnoverChart.dataURI().then(({ imgURI }) => {
            const image = new Image();
            image.onload = function () {
                const canvas = document.createElement('canvas');
                canvas.width = image.width;
                canvas.height = image.height;
                const ctx = canvas.getContext('2d');
                // white background
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(image, 0, 0);
                canvas.toBlob(function (blob) {
                    const filename = `turnover_chart_${getPeriodeLabel()}_${Date.now()}.jpg`;
                    downloadBlob(blob, filename);
                }, 'image/jpeg', 0.92);
            };
            image.src = imgURI;
        }).catch(err => { console.error(err); alert('Gagal mengekspor chart'); });
    });
});

</script>
<style>
    /* Mengunci Layout agar tidak ada double scrollbar */
    body {
        margin: 0;
        padding: 0;
        overflow-x: hidden;
    }

    /* Custom Scrollbar Tipis untuk Tabel */
    .overflow-x-auto::-webkit-scrollbar {
        height: 6px;
    }
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    /* Font Standard TailAdmin */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
    :root { font-family: 'Inter', sans-serif; }
</style>
@endsection
