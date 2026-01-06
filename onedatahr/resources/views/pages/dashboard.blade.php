@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <!-- HEADER -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Dashboard One Data HR
        </h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Selamat datang {{ auth()->user()->name ?? 'Pengguna' }},
            berikut ringkasan data HR Anda
        </p>
    </div>

    <!-- KPI -->
    <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">

        <!-- TOTAL KARYAWAN -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-xl dark:bg-gray-800">
                        <svg class="fill-gray-800 dark:fill-white/90" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.80443 5.60156C7.59109 5.60156 6.60749 6.58517 6.60749 7.79851C6.60749 9.01185 7.59109 9.99545 8.80443 9.99545C10.0178 9.99545 11.0014 9.01185 11.0014 7.79851C11.0014 6.58517 10.0178 5.60156 8.80443 5.60156ZM5.10749 7.79851C5.10749 5.75674 6.76267 4.10156 8.80443 4.10156C10.8462 4.10156 12.5014 5.75674 12.5014 7.79851C12.5014 9.84027 10.8462 11.4955 8.80443 11.4955C6.76267 11.4955 5.10749 9.84027 5.10749 7.79851ZM4.86252 15.3208C4.08769 16.0881 3.70377 17.0608 3.51705 17.8611C3.48384 18.0034 3.5211 18.1175 3.60712 18.2112C3.70161 18.3141 3.86659 18.3987 4.07591 18.3987H13.4249C13.6343 18.3987 13.7992 18.3141 13.8937 18.2112C13.9797 18.1175 14.017 18.0034 13.9838 17.8611C13.7971 17.0608 13.4132 16.0881 12.6383 15.3208C11.8821 14.572 10.6899 13.955 8.75042 13.955C6.81096 13.955 5.61877 14.572 4.86252 15.3208ZM3.8071 14.2549C4.87163 13.2009 6.45602 12.455 8.75042 12.455C11.0448 12.455 12.6292 13.2009 13.6937 14.2549C14.7397 15.2906 15.2207 16.5607 15.4446 17.5202C15.7658 18.8971 14.6071 19.8987 13.4249 19.8987H4.07591C2.89369 19.8987 1.73504 18.8971 2.05628 17.5202C2.28015 16.5607 2.76117 15.2906 3.8071 14.2549ZM15.3042 11.4955C14.4702 11.4955 13.7006 11.2193 13.0821 10.7533C13.3742 10.3314 13.6054 9.86419 13.7632 9.36432C14.1597 9.75463 14.7039 9.99545 15.3042 9.99545C16.5176 9.99545 17.5012 9.01185 17.5012 7.79851C17.5012 6.58517 16.5176 5.60156 15.3042 5.60156C14.7039 5.60156 14.1597 5.84239 13.7632 6.23271C13.6054 5.73284 13.3741 5.26561 13.082 4.84371C13.7006 4.37777 14.4702 4.10156 15.3042 4.10156C17.346 4.10156 19.0012 5.75674 19.0012 7.79851C19.0012 9.84027 17.346 11.4955 15.3042 11.4955ZM19.9248 19.8987H16.3901C16.7014 19.4736 16.9159 18.969 16.9827 18.3987H19.9248C20.1341 18.3987 20.2991 18.3141 20.3936 18.2112C20.4796 18.1175 20.5169 18.0034 20.4837 17.861C20.2969 17.0607 19.913 16.088 19.1382 15.3208C18.4047 14.5945 17.261 13.9921 15.4231 13.9566C15.2232 13.6945 14.9995 13.437 14.7491 13.1891C14.5144 12.9566 14.262 12.7384 13.9916 12.5362C14.3853 12.4831 14.8044 12.4549 15.2503 12.4549C17.5447 12.4549 19.1291 13.2008 20.1936 14.2549C21.2395 15.2906 21.7206 16.5607 21.9444 17.5202C22.2657 18.8971 21.107 19.8987 19.9248 19.8987Z"/>
                        </svg>
                    </div>
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Total Karyawan</p>
                    <h3 class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($totalKaryawan) }}
                    </h3>
                </div>
            </div>
        </div>

    </div>

    <!-- CHART GRID -->
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        <!-- GENDER -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                Distribusi Jenis Kelamin
            </h3>
            <div class="flex h-[260px] items-center justify-center">
                <div id="chart-gender" class="w-full"></div>
            </div>
        </div>

        <!-- JABATAN -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                Jumlah per Jabatan
            </h3>
            <div class="flex h-[260px] items-center justify-center">
                <div id="chart-jabatan" class="w-full"></div>
            </div>
        </div>

        <!-- DIVISI -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                Jumlah per Divisi
            </h3>
            <div class="flex h-[260px] items-center justify-center">
                <div id="chart-divisi" class="w-full"></div>
            </div>
        </div>

        <!-- PENDIDIKAN -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                Pendidikan Terakhir
            </h3>
            <div class="flex h-[260px] items-center justify-center">
                <div id="chart-pendidikan" class="w-full"></div>
            </div>
        </div>

        <!-- MASA KERJA -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                Masa Kerja
            </h3>
            <div class="flex h-[260px] items-center justify-center">
                <div id="chart-tenure" class="w-full"></div>
            </div>
        </div>

        <!-- USIA -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                Rentang Usia
            </h3>
            <div class="flex h-[260px] items-center justify-center">
                <div id="chart-age" class="w-full"></div>
            </div>
        </div>

        <!-- PERUSAHAAN (FULL WIDTH) -->
        <div class="xl:col-span-3 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                Jumlah Karyawan per Perusahaan
            </h3>
            <div class="flex h-[300px] items-center justify-center">
                <div id="chart-perusahaan" class="w-full"></div>
            </div>
        </div>

    </div>
</div>
@endsection



@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Data from PHP
        const genderLabels = {!! json_encode(array_keys($genderData)) !!};
        const genderSeries = {!! json_encode(array_values($genderData)) !!};

        const jabatanLabels = {!! json_encode(array_keys($jabatanData)) !!};
        const jabatanSeries = {!! json_encode(array_values($jabatanData)) !!};

        const divisiLabels = {!! json_encode(array_keys($divisiData)) !!};
        const divisiSeries = {!! json_encode(array_values($divisiData)) !!};

        const pendidikanLabels = {!! json_encode(array_keys($pendidikanData)) !!};
        const pendidikanSeries = {!! json_encode(array_values($pendidikanData)) !!};

        const tenureLabels = {!! json_encode(array_keys($tenureCounts)) !!};
        const tenureSeries = {!! json_encode(array_values($tenureCounts)) !!};

        const ageLabels = {!! json_encode(array_keys($ageCounts)) !!};
        const ageSeries = {!! json_encode(array_values($ageCounts)) !!};

        const perusahaanLabels = {!! json_encode(array_keys($perusahaanData)) !!};
        const perusahaanSeries = {!! json_encode(array_values($perusahaanData)) !!};
        const tailadminColors = [
            '#3C50E0', '#80CAEE', '#A5D8FF', '#06B6D4', '#10B981',
            '#22C55E', '#84CC16', '#FACC15', '#F97316', '#FB7185',
            '#EF4444', '#A855F7', '#8B5CF6', '#EC4899', '#64748B'
        ];


        // Helper to render charts if ApexCharts available
        const renderChart = (selector, options) => {
            const el = document.querySelector(selector);
            if (!el || typeof ApexCharts === 'undefined') return;
            const chart = new ApexCharts(el, options);
            chart.render();
            return chart;
        };

        // Gender pie
       renderChart('#chart-gender', {
        series: genderSeries,
        chart: {
            type: 'pie', // Donut biasanya terlihat lebih modern daripada Pie standar
            height: 300,   // Sedikit ditambah agar teks tidak berdesakan
            fontFamily: 'Inter, system-ui, sans-serif'
        },
        labels: genderLabels,
        colors: ['#06B6D4', '#D946EF', '#F97316'], // Warna sedikit disesuaikan agar kontras
        stroke: {
            show: true,
            width: 2,
            colors: ['#ffff'] // Memberi jarak antar potongan (border putih)
        },
        plotOptions: {
            pie: {
                pie: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            show: false,
                            label: 'Total',
                            fontSize: '16px',
                            fontWeight: 600,
                        }
                    }
                }
            }
        },
        dataLabels: {
            enabled: true,
            style: {
                fontSize: '14px',
                fontWeight: 'bold',
                colors: ['#ffffff'] // Memastikan teks di dalam grafik terbaca
            },
            dropShadow: {
                enabled: true,
                top: 1,
                left: 1,
                blur: 1,
                opacity: 0.45
            }
        },
        legend: {
            position: 'bottom',
            fontSize: '14px',
            fontWeight: 500,
            markers: { radius: 12 },
            itemMargin: { horizontal: 10, vertical: 5 }
        },
        responsive: [{
            breakpoint: 640,
            options: {
                chart: { height: 280 },
                legend: { position: 'bottom' }
            }
        }]
    });

        // Jabatan bar (top 10)
        // renderChart('#chart-jabatan', {
        //     series: [{ name: 'Total', data: jabatanSeries }],
        //     chart: { type: 'bar', height: 240 },
        //     plotOptions: { bar: { borderRadius: 6, horizontal: false } },
        //     dataLabels: { enabled: false },
        //     xaxis: { categories: jabatanLabels },
        //     colors: ['#10B981']
        // });
        renderChart('#chart-jabatan', {
        series: jabatanSeries,
        chart: {
            type: 'donut',
            height: 260,
            fontFamily: 'Inter, sans-serif'
        },
        labels: jabatanLabels,
        colors: tailadminColors,
        legend: {
            position: 'bottom',
            fontSize: '14px',
            markers: { width: 10, height: 10, radius: 999 }
        },
        dataLabels: { enabled: false },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        name: { show: true, fontSize: '14px', color: '#6B7280' },
                        value: { show: true, fontSize: '24px', fontWeight: 700 },
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: w =>
                                w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                        }
                    }
                }
            }
        },
        stroke: { width: 0 }
    });


        // Divisi bar
        // renderChart('#chart-divisi', {
        //     series: [{ name: 'Total', data: divisiSeries }],
        //     chart: { type: 'bar', height: 240 },
        //     plotOptions: { bar: { borderRadius: 6 } },
        //     dataLabels: { enabled: false },
        //     xaxis: { categories: divisiLabels },
        //     colors: ['#FB923C']
        // });
        //divisi pie
        // renderChart('#chart-divisi', {
        //     series: divisiSeries,
        //     chart: { type: 'donut', height: 240 },
        //     labels: divisiLabels,
        //     colors: ['#06B6D4', '#f946ffff', '#F97316'],
        //     legend: { position: 'bottom' },
        //     responsive: [{ breakpoint: 640, options: { chart: { height: 200 } } }]
        // });
        renderChart('#chart-divisi', {
        series: divisiSeries,
        chart: {
            type: 'donut',
            height: 260,
            fontFamily: 'Inter, sans-serif'
        },
        labels: divisiLabels,
        // colors: ['#3C50E0', '#80CAEE', '#A5D8FF',
        //             '#06B6D4', // Cyan
        //             '#10B981', // Emerald
        //             '#22C55E', // Green
        //             '#84CC16', // Lime
        //             '#FACC15', // Yellow
        //             '#F97316', // Orange
        //             '#FB7185', // Rose
        //             '#EF4444', // Red
        //             '#A855F7', // Purple
        //             '#8B5CF6', // Violet
        //             '#EC4899', // Pink
        //             '#64748B'  // Slate],
        //             ],
        colors: tailadminColors,
        legend: {
            position: 'bottom',
            fontSize: '14px',
            markers: {
                width: 10,
                height: 10,
                radius: 999
            }
        },
        dataLabels: {
            enabled: false
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',   // ketebalan donut (mirip TailAdmin)
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            fontSize: '14px',
                            fontWeight: 500,
                            color: '#6B7280',
                            offsetY: -5
                        },
                        value: {
                            show: true,
                            fontSize: '24px',
                            fontWeight: 700,
                            color: '#111827',
                            offsetY: 5
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            fontSize: '14px',
                            fontWeight: 600,
                            color: '#374151',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                            }
                        }
                    }
                }
            }
        },
        stroke: {
            width: 0
        },
        responsive: [{
            breakpoint: 640,
            options: {
                chart: {
                    height: 220
                }
            }
        }]
    });


        // Pendidikan bar
        // renderChart('#chart-pendidikan', {
        //     series: [{ name: 'Jumlah', data: pendidikanSeries }],
        //     chart: { type: 'bar', height: 240 },
        //     plotOptions: { bar: { borderRadius: 6 } },
        //     dataLabels: { enabled: false },
        //     xaxis: { categories: pendidikanLabels },
        //     colors: ['#6366F1']
        // });
        renderChart('#chart-pendidikan', {
        series: pendidikanSeries,
        chart: {
            type: 'donut',
            height: 260,
            fontFamily: 'Inter, sans-serif'
        },
        labels: pendidikanLabels,
        colors: tailadminColors,
        legend: {
            position: 'bottom',
            fontSize: '14px',
            markers: { width: 10, height: 10, radius: 999 }
        },
        dataLabels: { enabled: false },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            fontWeight: 600,
                            color: '#374151',
                            formatter: w =>
                                w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                        }
                    }
                }
            }
        },
        stroke: { width: 0 }
    });


        // Tenure
        renderChart('#chart-tenure', {
            series: [{ name: 'Karyawan', data: tenureSeries }],
            chart: { type: 'bar', height: 240 },
            plotOptions: { bar: { borderRadius: 6 } },
            dataLabels: { enabled: false },
            xaxis: { categories: tenureLabels },
            colors: ['#60A5FA']
        });

        // Age
        renderChart('#chart-age', {
            series: [{ name: 'Karyawan', data: ageSeries }],
            chart: { type: 'bar', height: 240 },
            plotOptions: { bar: { borderRadius: 6 } },
            dataLabels: { enabled: false },
            xaxis: { categories: ageLabels },
            colors: ['#F43F5E']
        });

        // Perusahaan (wide)
        // renderChart('#chart-perusahaan', {
        //     series: [{ name: 'Total', data: perusahaanSeries }],
        //     chart: { type: 'bar', height: 350 },
        //     plotOptions: { bar: { borderRadius: 6 } },
        //     dataLabels: { enabled: false },
        //     xaxis: { categories: perusahaanLabels, labels: { rotate: -45 } },
        //     colors: ['#06B6D4']
        // });
        renderChart('#chart-perusahaan', {
        series: perusahaanSeries,
        chart: {
            type: 'donut',
            height: 300,
            fontFamily: 'Inter, sans-serif'
        },
        labels: perusahaanLabels,
        colors: tailadminColors,
        legend: {
            position: 'bottom',
            fontSize: '14px',
            markers: { width: 10, height: 10, radius: 999 }
        },
        dataLabels: { enabled: false },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: w =>
                                w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                        }
                    }
                }
            }
        },
        stroke: { width: 0 }
    });

    });
</script>
@endpush
