<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <title>Form Penilaian KPI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>tailwind.config = { darkMode: 'class' }</script>
    <!-- Tooltip Library (Tippy.js) -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css" />
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/animations/shift-away.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        :root { color-scheme: light; }
        body { font-family: 'Inter', sans-serif; }
        
        /* Custom Scrollbar styled for sleekness */
        .custom-scrollbar::-webkit-scrollbar { height: 8px; width: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* Hide Number Spinners */
        input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        
        /* KPI Grid System */
        .kpi-gridline td, .kpi-gridline th { border-color: #e2e8f0; } /* Slate-200 */
        
        /* Input Styling */
        .kpi-input { 
            background: transparent; 
            border: 1px solid transparent; 
            transition: all 0.2s;
        }
        .kpi-input:hover:not(:disabled) { border-color: #cbd5e1; background: #fff; }
        .kpi-input:focus { 
            outline: none; 
            border-color: #6366f1; /* Indigo-500 */
            background: #fff;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); 
        }
        
        /* Sticky Column Shadow Mask */
        .kpi-sticky-shadow { 
            box-shadow: 4px 0 12px -4px rgba(15, 23, 42, 0.1); 
            z-index: 30;
        }

        /* Group borders for semesters */
        .border-semester-1 { border-right: 2px solid #cbd5e1 !important; }
        .bg-semester-1 { background-color: #f8fafc; }
        .bg-semester-2 { background-color: #fff; }
    </style>
    @vite(['resources/js/react-app.jsx'])
</head>
<body class="bg-slate-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 p-3 md:p-6 font-sans">
@php
    // Cek apakah user yang login berhak melakukan adjustment?
    // Value $canAdjust Passed from Controller
    $isStaff = auth()->user()->hasRole('staff');
    // $canAdjust = defined in controller now
    $canManageKpi = true;

    // Class CSS untuk input yang dikunci (Abu-abu & tidak bisa diklik)
    $readonlyClass = $isStaff ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-transparent text-orange-700 font-bold border-b border-orange-300';
@endphp
<div class="w-full max-w-[1400px] mx-auto">
    {{-- ALERT --}}
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 text-xl flex-shrink-0"></i>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan:</h3>
                    <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            <strong class="font-bold">Error!</strong> <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    @if (session('warning'))
        <div class="mb-6 bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded relative">
            <strong class="font-bold">Peringatan!</strong> <span class="block sm:inline">{{ session('warning') }}</span>
        </div>
    @endif
    @if (session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            <strong class="font-bold">Berhasil!</strong> <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @php
        $isManager = false;
        $loggedInKaryawan = \App\Models\Karyawan::where('nik', auth()->user()->nik)->first();
        if($loggedInKaryawan && $karyawan->atasan_id == $loggedInKaryawan->id_karyawan) {
            $isManager = true;
        }
    @endphp

    {{-- HEADER --}}
    <div class="mb-6 flex flex-col xl:flex-row gap-6 items-start xl:items-center justify-between p-6 rounded-2xl bg-white shadow-sm border border-slate-200">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100 text-xs font-semibold">
                <i class="fas fa-chart-pie"></i>
                <span>Performance Management</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900 tracking-tight">Form Penilaian KPI</h1>
            <div class="text-slate-500 text-sm flex flex-col md:flex-row md:items-center gap-2 md:gap-4">
                @if($isManager)
                    <span class="flex items-center gap-2"><i class="fas fa-user-circle text-slate-400"></i> Karyawan: <strong class="text-slate-800">{{ $karyawan->nama_karyawan }}</strong> <span class="text-xs bg-slate-100 px-1.5 py-0.5 rounded text-slate-500">{{ $karyawan->nik }}</span></span>
                @endif
                <span class="hidden md:inline text-slate-300">|</span>
                <span class="flex items-center gap-2"><i class="fas fa-calendar-alt text-slate-400"></i> Periode: <strong class="text-slate-800">{{ $tahun }}</strong></span>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto">
             {{-- YEAR FILTER --}}
             <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-calendar text-slate-400 text-sm"></i>
                </div>
                <select id="yearFilterForm" onchange="changeKpiYear(this.value)" class="pl-9 pr-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-sm text-slate-700 font-medium cursor-pointer hover:border-indigo-300 transition w-full sm:w-32">
                    @for($y = date('Y'); $y >= date('Y')-5; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                {{-- BACK BUTTON --}}
                @if(auth()->user()->hasRole(['superadmin', 'admin']))
                    <a href="{{ route('kpi.index') }}" class="px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition flex items-center justify-center gap-2 shadow-sm">
                        <i class="fas fa-arrow-left"></i> <span>List</span>
                    </a>
                @else
                    @php
                        // Jika Staff, arahkan ke Dashboard Utama (karena akses ke KPI Dashboard akan di-redirect kembali ke form ini)
                        $dashboardLink = $isStaff ? route('dashboard.index') : route('kpi.index');
                    @endphp
                    <a href="{{ $dashboardLink }}" class="px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition flex items-center justify-center gap-2 shadow-sm">
                        <i class="fas fa-arrow-left"></i> <span>Dashboard</span>
                    </a>
                @endif
    
                {{-- EXPORT DROPDOWN --}}
                <div class="relative group">
                    <button type="button" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition flex items-center gap-2 shadow-sm">
                        <i class="fas fa-download text-slate-400"></i>
                        <span>Export</span>
                        <i class="fas fa-chevron-down text-xs ml-1 text-slate-400"></i>
                    </button>
                    <div class="absolute right-0 mt-1 w-44 bg-white rounded-lg shadow-xl border border-slate-100 hidden group-hover:block z-50 overflow-hidden ring-1 ring-black ring-opacity-5">
                        <div class="p-1">
                            <a href="{{ route('performance.export.excel', ['karyawan_id' => $karyawan->id_karyawan, 'tahun' => $kpi->tahun]) }}" class="flex items-center gap-3 px-3 py-2 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-md transition">
                                <i class="fas fa-file-excel text-green-600"></i> Excel
                            </a>
                            <a href="{{ route('performance.export.pdf', ['karyawan_id' => $karyawan->id_karyawan, 'tahun' => $kpi->tahun]) }}" class="flex items-center gap-3 px-3 py-2 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-md transition">
                                <i class="fas fa-file-pdf text-red-500"></i> PDF
                            </a>
                        </div>
                    </div>
                </div>

                {{-- SAVE BUTTON --}}
                <button id="btnSimpan" type="button" onclick="submitKpiForm()" disabled
                    class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-semibold transition-all shadow-md shadow-indigo-200 flex items-center justify-center gap-2 opacity-50 cursor-not-allowed hover:bg-indigo-700 disabled:hover:bg-indigo-600 w-full sm:w-auto">
                    @if($isManager)
                        <i class="fas fa-check-double"></i> <span>Simpan & Approve</span>
                    @else
                        <i class="fas fa-save"></i> <span>Simpan Perubahan</span>
                    @endif
                </button>
            </div>
        </div>
    </div>

    {{-- SUMMARY & ACTIONS BAR --}}
    <div class="mb-4 grid grid-cols-1 lg:grid-cols-[1fr_auto] gap-4 items-end">
        {{-- STATS STRIP --}}
        <div class="flex flex-wrap items-center gap-3">
            {{-- Total KPI Card --}}
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-slate-200 rounded-xl shadow-sm">
                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                    <i class="fas fa-list-ul"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total KPI</p>
                    <p class="text-xl font-bold text-slate-800 leading-none" id="summary-total-kpi">0</p>
                </div>
            </div>

            {{-- Status Card --}}
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-slate-200 rounded-xl shadow-sm">
                <div class="w-10 h-10 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center text-lg">
                    <i class="fas fa-info"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</p>
                    <div class="mt-0.5 inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-bold bg-yellow-100 text-yellow-700" id="summary-status">
                         Draft
                    </div>
                </div>
            </div>

             {{-- Total Bobot Warning --}}
             <div id="total-bobot-alert" class="px-4 py-2 rounded-lg font-medium text-sm"></div>
             
             {{-- Unsaved Changes Badge --}}
             <div id="unsaved-badge" class="hidden flex items-center gap-2 px-3 py-2 bg-amber-50 text-amber-700 text-sm font-semibold rounded-lg border border-amber-200 shadow-sm animate-pulse">
                <i class="fas fa-pen-nib"></i>
                <span>Terdapat perubahan belum disimpan</span>
            </div>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex items-center gap-2 w-full lg:w-auto">
            @if ($canManageKpi)
            <button type="button" onclick="document.getElementById('modalTambahKPI').classList.remove('hidden')" class="flex-1 lg:flex-none px-4 py-2.5 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 transition shadow-sm hover:shadow flex items-center justify-center gap-2">
                <i class="fas fa-plus"></i> Tambah KPI
            </button>
            @endif
            <button type="button" id="bulk-delete-btn" class="hidden flex-1 lg:flex-none px-4 py-2.5 bg-red-50 text-red-600 border border-red-200 rounded-lg text-sm font-semibold hover:bg-red-100 transition flex items-center justify-center gap-2">
                <i class="fas fa-trash-alt"></i> Hapus
            </button>
        </div>
    </div>

    {{-- PESAN JIKA FORM KOSONG --}}
    @if($items->isEmpty())
    <div class="mb-6 bg-indigo-50 dark:bg-indigo-900/30 border-l-4 border-indigo-500 p-5 rounded-lg shadow-sm">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-indigo-600 dark:text-indigo-400 text-2xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-indigo-900 dark:text-indigo-200 mb-2">
                    Form KPI Tahun {{ $tahun }} Kosong
                </h3>
                <p class="text-sm text-indigo-800 dark:text-indigo-300 mb-3">
                    Data KPI untuk tahun ini belum ada. Silakan tambahkan KPI baru dengan menekan tombol <strong>"Tambah KPI Baru"</strong> di atas, atau Anda bisa mengisi detail KPI ketika semua item sudah ditambahkan.
                </p>
                <div class="text-xs text-indigo-700 dark:text-indigo-400 space-y-1">
                    <p><i class="fas fa-arrow-right mr-2"></i>Klik tombol <strong>Tambah KPI Baru</strong> untuk memulai</p>
                    <p><i class="fas fa-arrow-right mr-2"></i>Isi informasi KPI sesuai kebutuhan</p>
                    <p><i class="fas fa-arrow-right mr-2"></i>Setelah semua KPI ditambah, klik <strong>Simpan</strong> untuk menyimpan</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    

    {{-- FORM UTAMA --}}
    <form id="kpiForm" action="{{ route('kpi.update', $kpi->id_kpi_assessment) }}" method="POST">
        @csrf
        @if(!$items->isEmpty())
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-200 relative">
            <div class="w-full overflow-x-auto custom-scrollbar">
                <table
                    class="w-full text-sm text-left min-w-[3200px] md:min-w-[4600px] border-collapse kpi-gridline"
                    style="--col-no:48px; --col-kra:220px;"
                    >
                    <thead class="text-[11px] text-slate-500 font-bold uppercase tracking-wider bg-slate-50 sticky top-0 z-20 shadow-sm">
                        <tr>
                            <th rowspan="2" class="sticky left-0 z-40 bg-slate-100 p-2 w-10 text-center border-r border-slate-300 kpi-sticky-shadow">
                                <input type="checkbox" id="select-all-checkbox" class="rounded border-slate-400 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th rowspan="2" class="sticky left-[48px] ml-4 bg-slate-100 z-40 p-3 w-48 border-r border-slate-300 kpi-sticky-shadow text-slate-700">Area Kinerja Utama (KRA)</th>
                            <th rowspan="2" class="p-3 w-48 border-r border-slate-200 text-slate-700">Indikator Kinerja (KPI)</th>
                            <th rowspan="2" class="p-3 w-32 border-r border-slate-200 text-slate-700 bg-slate-50">Perspektif</th>
                            <th rowspan="2" class="p-3 w-20 text-center border-r border-slate-200 bg-slate-50">Satuan</th>
                            <th rowspan="2" class="p-3 w-16 text-center border-r border-slate-200 bg-slate-50">Bobot</th>
                            <th rowspan="2" class="p-3 w-20 text-center border-r border-slate-200 bg-slate-50">Target</th>
                            
                            <!-- SEMESTER 1 HEADERS -->
                            @foreach(['Januari','Februari','Maret','April','Mei','Juni'] as $bulan) 
                                <th colspan="4" class="p-1 px-2 text-center border-r border-slate-200 bg-emerald-50/50 text-emerald-800">{{ $bulan }}</th> 
                            @endforeach
                            
                            <!-- ADJ SEMESTER 1 (TENGAH TAHUN) - MOVED HERE -->
                            <th rowspan="2" class="p-1 px-2 text-center border-r border-slate-200 bg-indigo-50/50 text-indigo-800 font-bold w-20">Skor H1</th>
                            <th colspan="3" class="p-1 px-2 text-center border-r border-slate-200 bg-amber-50/50 text-amber-800">Adj. Tengah Tahun</th>

                            <!-- SEMESTER 2 HEADERS -->
                            @foreach(['Juli','Agustus','September','Oktober','November','Desember'] as $bulan) 
                                <th colspan="4" class="p-1 px-2 text-center border-r border-slate-200 bg-sky-50/50 text-sky-800">{{ $bulan }}</th> 
                            @endforeach
                            
                            <!-- ADJ SEMESTER 2 (AKHIR TAHUN) -->
                            <th rowspan="2" class="p-1 px-2 text-center border-r border-slate-200 bg-indigo-50/50 text-indigo-800 font-bold w-20">Skor H2</th>
                            <th colspan="3" class="p-1 px-2 text-center border-r border-slate-200 bg-amber-50/50 text-amber-800">Adj. Akhir Tahun</th>
                            <th rowspan="2" class="p-2 w-24 text-center border-l bg-slate-100 text-slate-800 font-bold sticky right-0 z-30 shadow-l">FINAL<br>SCORE</th>
                        </tr>
                        <tr>
                            <!-- SUB-HEADERS -->
                            @foreach(['jan','feb','mar','apr','mei','jun'] as $bln) 
                                <th class="p-1 border-r border-slate-200 w-16 text-center font-semibold bg-emerald-50/30">Tgt</th>
                                <th class="p-1 border-r border-slate-200 w-16 text-center font-semibold bg-emerald-50/30">Real</th>
                                <th class="p-1 border-r border-slate-200 w-14 text-center font-semibold bg-emerald-50/30 text-emerald-600">Skor</th>
                                <th class="p-1 border-r border-slate-200 w-16 text-center font-semibold bg-emerald-100/50 text-emerald-700">Nilai</th> 
                            @endforeach
                            
                            <!-- ADJ S-1 SUBHEADERS -->
                            <th class="p-1 border-r border-amber-100 w-16 bg-amber-50/30 text-amber-700">Real</th>
                            <th class="p-1 border-r border-amber-100 w-14 bg-amber-50/30 text-amber-700">Skor</th>
                            <th class="p-1 border-r border-amber-200 w-16 bg-amber-100/50 text-amber-800 font-bold">Nilai</th>

                            @foreach(['jul','aug','sep','okt','nov','des'] as $bln) 
                                <th class="p-1 border-r border-slate-200 w-16 text-center font-semibold bg-sky-50/30">Tgt</th>
                                <th class="p-1 border-r border-slate-200 w-16 text-center font-semibold bg-sky-50/30">Real</th>
                                <th class="p-1 border-r border-slate-200 w-14 text-center font-semibold bg-sky-50/30 text-sky-600">Skor</th>
                                <th class="p-1 border-r border-slate-200 w-16 text-center font-semibold bg-sky-100/50 text-sky-700">Nilai</th> 
                            @endforeach
                            
                            <!-- ADJ S-2 SUBHEADERS -->
                            <th class="p-1 border-r border-amber-100 w-16 bg-amber-50/30 text-amber-700">Real</th>
                            <th class="p-1 border-r border-amber-100 w-14 bg-amber-50/30 text-amber-700">Skor</th>
                            <th class="p-1 border-r border-amber-200 w-16 bg-amber-100/50 text-amber-800 font-bold">Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @foreach($items as $index => $item)
                        @php $score = $item->scores->first(); @endphp
                        <tr class="row-kpi hover:bg-slate-50 transition group text-xs md:text-sm">
                            {{-- IDENTITAS --}}
                            <td class="sticky left-0 bg-white z-10 p-2 text-center border-r border-slate-200 font-medium kpi-sticky-shadow group-hover:bg-slate-50 transition-colors">
                                <input type="checkbox" name="selected_items[]" value="{{ $item->id_kpi_item }}" class="item-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                            </td>
                            <td class="sticky left-[48px] bg-white z-10 p-3 border-r border-slate-200 align-top kpi-sticky-shadow group-hover:bg-slate-50 transition-colors">
                                <div class="flex flex-col sm:flex-row justify-between items-start gap-2">
                                    <div class="font-semibold text-slate-800 leading-snug group-hover:text-indigo-700 transition-colors">{{ $item->key_result_area ?? $item->indikator }}</div>

                                    @if ($canManageKpi)
                                    <div class="flex gap-1 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button type="button" onclick="openEditModal({{ json_encode($item) }}, '{{ route('kpi.update-item', $item->id_kpi_item) }}')" class="text-slate-400 hover:text-amber-600 p-1 rounded hover:bg-amber-50"><i class="fas fa-pencil-alt text-xs"></i></button>
                                        <button type="button" onclick="confirmDelete('{{ route('kpi.delete-item', $item->id_kpi_item) }}')" class="text-slate-400 hover:text-rose-600 p-1 rounded hover:bg-rose-50"><i class="fas fa-trash-alt text-xs"></i></button>
                                    </div>
                                    @endif
                                </div>
                                <div class="text-[11px] font-mono text-slate-500 mt-1.5 flex items-center gap-2">
                                    <!-- Unit removed from here -->
                                    <span class="bg-slate-100 px-1.5 rounded flex items-center gap-1.5 cursor-help" 
                                          data-tippy-content="{{ 
                                            $item->calculation_method === 'positive' ? 'Target Positif: Semakin tinggi realisasi, semakin baik skornya. Cocok untuk Volume Produksi.' : 
                                            ($item->calculation_method === 'negative' ? 'Target Negatif: Semakin rendah realisasi, semakin baik skornya. Cocok untuk keterlambatan atau jumlah reject.' : 
                                            'Progress Project: Menghitung selisih kenaikan progres dari bulan sebelumnya. Cocok untuk proyek jangka panjang.') 
                                          }}">
                                        {{ $item->polaritas }}
                                        <i class="far fa-question-circle text-[9px] text-slate-400"></i>
                                    </span>
                                </div>
                                <input type="hidden" class="input-bobot" value="{{ $item->bobot }}">
                                <input type="hidden" class="input-polaritas" value="{{ $item->polaritas }}">
                            </td>
                            <td class="p-3 border-r border-slate-200 align-top text-slate-600">{{ $item->key_performance_indicator ?? $item->indikator }}</td>
                            <td class="p-3 border-r border-slate-200 align-top text-slate-500 bg-slate-50/30">{{ $item->perspektif }}</td>
                            <td class="p-3 text-center border-r border-slate-200 align-top text-slate-600 bg-slate-50/30">{{ $item->units }}</td>
                            <td class="p-3 text-center border-r border-slate-200 align-top font-bold text-indigo-600 bg-indigo-50/10">{{ $item->bobot }}%</td>
                            <td class="p-3 text-center border-r border-slate-200 align-top font-bold text-slate-700 bg-slate-50/50">
                                {{ $item->target }}
                                <input type="hidden" class="input-target-smt1" name="kpi[{{ $item->id_kpi_item }}][target_smt1]" value="{{ $item->target }}">
                            </td>

                            {{-- BULANAN JANUARI - JUNI (Semester 1) --}}
                            @foreach(['jan','feb','mar','apr','mei','jun'] as $bln)
                                <td class="p-1 border-r border-slate-100 align-middle"><input type="number" step="0.01" name="kpi[{{ $item->id_kpi_item }}][target_{{ $bln }}]" value="{{ $score->{'target_'.$bln} ?? '' }}" class="input-target-{{ $bln }} kpi-input w-full h-8 px-1 rounded text-center text-slate-600 font-medium {{ $isStaff ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : '' }}" placeholder="-" {{ $isStaff ? 'readonly' : '' }}></td>
                                <td class="p-1 border-r border-slate-100 align-middle relative group/cell">
                                    <input type="number" step="0.01" name="kpi[{{ $item->id_kpi_item }}][real_{{ $bln }}]" value="{{ $score->{'real_'.$bln} ?? '' }}" class="input-real-{{ $bln }} kpi-input w-full h-8 px-1 rounded text-center text-slate-900 font-semibold" placeholder="-">
                                    <button type="button" onclick="openJustificationModal('{{ $item->id_kpi_item }}', '{{ $bln }}')" class="absolute top-1 right-1 text-slate-300 hover:text-indigo-500 opacity-0 group-hover/cell:opacity-100 transition-opacity p-0.5">
                                        <i class="fas fa-comment-alt text-xs"></i>
                                        @php
                                            $rawNote = $score->justification[$bln] ?? '';
                                            if (is_string($rawNote)) {
                                                $staffNote = $rawNote;
                                                $managerNote = '';
                                            } else {
                                                $staffNote = $rawNote['staff'] ?? '';
                                                $managerNote = $rawNote['manager'] ?? '';
                                            }
                                            $hasNote = !empty($staffNote) || !empty($managerNote);
                                        @endphp
                                        @if($hasNote) <span class="absolute top-0 right-0 w-1.5 h-1.5 bg-red-500 rounded-full"></span> @endif
                                    </button>
                                    <input type="hidden" name="kpi[{{ $item->id_kpi_item }}][justification][{{ $bln }}][staff]" id="justification-staff-{{ $item->id_kpi_item }}-{{ $bln }}" value="{{ $staffNote }}">
                                    <input type="hidden" name="kpi[{{ $item->id_kpi_item }}][justification][{{ $bln }}][manager]" id="justification-manager-{{ $item->id_kpi_item }}-{{ $bln }}" value="{{ $managerNote }}">
                                </td>
                                <td class="p-1 border-r border-slate-100 align-middle text-center bg-emerald-50/10"><div class="py-1.5 font-medium text-emerald-600/80 text-[11px]"><span class="span-skor-{{ $bln }}"></span>%</div></td>
                                <td class="p-1 border-r border-slate-200 align-middle text-center bg-emerald-50/30 border-semester-1"><div class="py-1.5 font-bold text-emerald-700"><span class="span-nilai-{{ $bln }}"></span>%</div></td>
                            @endforeach

                            <!-- SUBTOTAL SMT 1 -->
                            <td class="p-1 border-r border-indigo-100 bg-indigo-50/20 align-middle text-center font-bold text-indigo-700">
                                <div class="text-[10px] text-slate-400 font-normal leading-tight">Avg</div>
                                <span class="span-subtotal-smt1 text-sm">0</span>%
                                <div class="text-[10px] text-slate-500 font-semibold mt-1 border-t border-indigo-100 pt-0.5">
                                    Tot: <span class="span-total-real-smt1">0</span>
                                </div>
                                <input type="hidden" name="kpi[{{ $item->id_kpi_item }}][subtotal_smt1]" class="input-subtotal-smt1" value="{{ $score->subtotal_smt1 ?? 0 }}">
                            </td>

                            {{-- ADJ S-I (MOVED) --}}
                            <td class="p-1 text-center border-r border-amber-100 bg-amber-50/20 align-middle">
                                <input
                                type="number"
                                name="kpi[{{ $item->id_kpi_item }}][adjustment_real_smt1]"
                                value="{{ old('kpi.'.$item->id_kpi_item.'.adjustment_real_smt1', $score->adjustment_real_smt1 ?? '') }}"
                                {{ !$canAdjust ? 'readonly' : '' }}
                                step="0.01" class="input-adj-real-smt1 kpi-input w-full h-8 px-1 text-center text-amber-800" placeholder="-"></td>
                            <td class="p-1 border-r border-amber-100 bg-amber-50/20 align-middle text-center">
                                <span class="span-adj-skor-smt1 font-bold text-amber-600/80 text-[11px]"></span>%</td>
                            <td class="p-1 text-center border-r border-amber-200 bg-amber-50/40 align-middle">
                                <input type="number" step="0.01" name="kpi[{{ $item->id_kpi_item }}][adjustment_smt1]" value="{{ old('kpi.'.$item->id_kpi_item.'.adjustment_smt1', $score->adjustment_smt1 ?? '') }}" class="input-adj-nilai-smt1 kpi-input w-full h-8 px-1 text-center font-bold text-amber-700 bg-transparent border-none" placeholder="-" readonly></td>


                            {{-- BULANAN SMT 2 --}}
                            @foreach(['jul','aug','sep','okt','nov','des'] as $bln)
                                <td class="p-1 border-r border-slate-100 align-middle"><input type="number" step="0.01" name="kpi[{{ $item->id_kpi_item }}][target_{{ $bln }}]" value="{{ $score->{'target_'.$bln} ?? '' }}" class="input-target-{{ $bln }} kpi-input w-full h-8 px-1 rounded text-center text-slate-600 font-medium {{ $isStaff ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : '' }}" placeholder="-" {{ $isStaff ? 'readonly' : '' }}></td>
                                <td class="p-1 border-r border-slate-100 align-middle relative group/cell">
                                    <input type="number" step="0.01" name="kpi[{{ $item->id_kpi_item }}][real_{{ $bln }}]" value="{{ $score->{'real_'.$bln} ?? '' }}" class="input-real-{{ $bln }} kpi-input w-full h-8 px-1 rounded text-center text-slate-900 font-semibold" placeholder="-">
                                    <button type="button" onclick="openJustificationModal('{{ $item->id_kpi_item }}', '{{ $bln }}')" class="absolute top-1 right-1 text-slate-300 hover:text-indigo-500 opacity-0 group-hover/cell:opacity-100 transition-opacity p-0.5">
                                        <i class="fas fa-comment-alt text-xs"></i>
                                        @php
                                            $rawNote2 = $score->justification[$bln] ?? '';
                                            if (is_string($rawNote2)) {
                                                $staffNote2 = $rawNote2;
                                                $managerNote2 = '';
                                            } else {
                                                $staffNote2 = $rawNote2['staff'] ?? '';
                                                $managerNote2 = $rawNote2['manager'] ?? '';
                                            }
                                            $hasNote2 = !empty($staffNote2) || !empty($managerNote2);
                                        @endphp
                                        @if($hasNote2) <span class="absolute top-0 right-0 w-1.5 h-1.5 bg-red-500 rounded-full"></span> @endif
                                    </button>
                                    <input type="hidden" name="kpi[{{ $item->id_kpi_item }}][justification][{{ $bln }}][staff]" id="justification-staff-{{ $item->id_kpi_item }}-{{ $bln }}" value="{{ $staffNote2 }}">
                                    <input type="hidden" name="kpi[{{ $item->id_kpi_item }}][justification][{{ $bln }}][manager]" id="justification-manager-{{ $item->id_kpi_item }}-{{ $bln }}" value="{{ $managerNote2 }}">
                                </td>
                                <td class="p-1 border-r border-slate-100 align-middle text-center bg-sky-50/10"><div class="py-1.5 font-medium text-sky-600/80 text-[11px]"><span class="span-skor-{{ $bln }}"></span>%</div></td>
                                <td class="p-1 border-r border-slate-200 align-middle text-center bg-sky-50/30"><div class="py-1.5 font-bold text-sky-700"><span class="span-nilai-{{ $bln }}"></span>%</div></td>
                            @endforeach

                            <!-- SUBTOTAL SMT 2 -->
                            <td class="p-1 border-r border-indigo-100 bg-indigo-50/20 align-middle text-center font-bold text-indigo-700">
                                <div class="text-[10px] text-slate-400 font-normal leading-tight">Avg</div>
                                <span class="span-subtotal-smt2 text-sm">0</span>%
                                <div class="text-[10px] text-slate-500 font-semibold mt-1 border-t border-indigo-100 pt-0.5">
                                    Tot: <span class="span-total-real-smt2">0</span>
                                </div>
                                <input type="hidden" name="kpi[{{ $item->id_kpi_item }}][subtotal_smt2]" class="input-subtotal-smt2" value="{{ $score->subtotal_smt2 ?? 0 }}">
                            </td>

                            {{-- ADJ S-II --}}
                            <td class="p-1 border-r border-amber-100 bg-amber-50/20 align-middle">
                                <input type="number"
                                name="kpi[{{ $item->id_kpi_item }}][adjustment_real_smt2]"
                                value="{{ old('kpi.'.$item->id_kpi_item.'.adjustment_real_smt2', $score->adjustment_real_smt2 ?? '') }}"
                                {{ !$canAdjust ? 'readonly' : '' }}
                                step="0.01" class="input-adj-real-smt2 kpi-input w-full h-8 px-1 text-center text-amber-800" placeholder="-"></td>
                            <td class="p-1 border-r border-amber-100 bg-amber-50/20 align-middle text-center"><span class="span-adj-skor-smt2 font-bold text-amber-600/80 text-[11px]"></span>%</td>
                            <td class="p-1 border-r border-amber-200 bg-amber-50/40 align-middle"><input type="number" step="0.01" name="kpi[{{ $item->id_kpi_item }}][adjustment_smt2]" value="{{ old('kpi.'.$item->id_kpi_item.'.adjustment_smt2', $score->adjustment_smt2 ?? '') }}" class="input-adj-nilai-smt2 kpi-input w-full h-8 px-1 text-center font-bold text-amber-700 bg-transparent border-none" placeholder="-" readonly></td>

                            {{-- FINAL --}}
                            <td class="p-3 text-center border-l bg-slate-100 font-extrabold text-indigo-700 sticky right-0 z-30 shadow-sm border-slate-300"><span class="span-final-score"></span>%</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-300 sticky bottom-0 z-40 text-xs md:text-sm">
                        <tr class="shadow-sm">

                            <!-- LABEL -->
                            <td colspan="2" class="sticky left-0 bg-slate-100 p-3 font-bold uppercase border-r border-slate-300 text-slate-600 kpi-sticky-shadow text-right">
                                Total Skor Akhir 
                            </td>

                            <!-- KPI + Perspektif + Satuan + Bobot + Target -->
                            <td colspan="5" class="border-r border-slate-200 bg-slate-50"></td>

                            <!-- JAN–JUN -->
                            @foreach(['jan','feb','mar','apr','mei','jun'] as $bln)
                                <td colspan="3" class="border-r border-slate-200"></td>
                                <td class="p-2 text-center font-bold text-emerald-800 border-r border-slate-300 bg-emerald-50">
                                    <span id="footer-total-{{ $bln }}"></span>%
                                </td>
                            @endforeach

                            <!-- ADJ SMT 1 (MOVED) -->
                            <td class="border-r border-indigo-200 bg-indigo-50/50 w-20"></td>
                            <td colspan="2" class="border-r border-slate-200 bg-amber-50/50"></td>
                            <td class="p-2 border-r border-slate-300 bg-amber-100/50 font-bold text-amber-800 text-center">
                                <span id="footer-adj-smt1"></span>%
                            </td>

                            <!-- JUL–DES -->
                            @foreach(['jul','aug','sep','okt','nov','des'] as $bln)
                                <td colspan="3" class="border-r border-slate-200"></td>
                                <td class="p-2 text-center font-bold text-sky-800 border-r border-slate-300 bg-sky-50">
                                    <span id="footer-total-{{ $bln }}"></span>%
                                </td>
                            @endforeach

                            <!-- ADJ SMT 2 -->
                            <td class="border-r border-indigo-200 bg-indigo-50/50 w-20"></td>
                            <td colspan="2" class="border-r border-slate-200 bg-amber-50/50"></td>
                            <td class="p-2 border-r border-slate-300 bg-amber-100/50 font-bold text-amber-800 text-center">
                                <span id="footer-adj-smt2"></span>%
                            </td>

                            <!-- FINAL -->
                            <td class="p-2 text-center font-black text-indigo-900 bg-slate-200 border-l border-slate-300 sticky right-0 z-50">
                                <span id="footer-grand-total"></span>%
                            </td>

                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif
    </form>
</div>

{{-- MODALS --}}
{{-- 1. MODAL TAMBAH --}}
<div id="modalTambahKPI" class="fixed inset-0 bg-slate-900/60 hidden z-[60] flex justify-center items-center p-4 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-0 overflow-hidden transform transition-all scale-100">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h2 class="text-lg font-bold text-slate-800"><i class="fas fa-plus-circle text-emerald-600 mr-2"></i>Tambah KPI Baru</h2>
            <button onclick="document.getElementById('modalTambahKPI').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[80vh]">
            <form action="{{ route('kpi.store-item') }}" method="POST">
                @csrf <input type="hidden" name="kpi_assessment_id" value="{{ $kpi->id_kpi_assessment }}">
                <div id="react-kpi-item-form" 
                     data-kpi-id="{{ $kpi->id_kpi_assessment }}"
                     data-perspectives="{{ json_encode($perspektifList ?? []) }}">
                     <!-- React Integration Point - Loading State -->
                     <div class="p-10 flex justify-center text-slate-400">
                        <i class="fas fa-circle-notch fa-spin text-2xl"></i>
                     </div>
                </div>

                <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modalTambahKPI').classList.add('hidden')" class="px-5 py-2.5 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 font-medium text-sm transition">Batal</button>
                    @if ($canManageKpi)
                    <button type="submit" class="px-5 py-2.5 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 font-bold text-sm shadow-lg shadow-emerald-200 transition">Simpan KPI</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 2. MODAL EDIT --}}
<div id="modalEditKPI" class="fixed inset-0 bg-slate-900/60 hidden z-[60] flex justify-center items-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-0 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-amber-50/50 flex justify-between items-center">
            <h2 class="text-lg font-bold text-slate-800"><i class="fas fa-edit text-amber-500 mr-2"></i>Edit KPI</h2>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-6">
            <form id="formEditKPI" method="POST">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Area Kinerja Utama (KRA)</label>
                        <input type="text" id="edit_kra" name="key_result_area" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Indikator Kinerja</label>
                        <textarea id="edit_kpi" name="key_performance_indicator" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none" rows="2" required></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Perspektif</label>
                        <select id="edit_perspektif" name="perspektif" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none bg-white" {{ $perspektifList->isEmpty() ? 'disabled' : '' }}>
                            @if($perspektifList->isEmpty())
                                <option value="">Default</option>
                            @else
                                @foreach($perspektifList as $perspektif)
                                    <option value="{{ $perspektif }}">{{ $perspektif }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div>
                         <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Bobot (%)</label>
                         <input type="number" step="0.01" id="edit_bobot" name="bobot" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Satuan</label>
                        <input type="text" id="edit_units" name="units" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Polaritas</label>
                        <select id="edit_polaritas" name="polaritas" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none bg-white">
                            <option value="Max">Maximize</option>
                            <option value="Min">Minimize</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Target Tahunan</label>
                        <input type="number" step="0.01" name="target" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none {{ $isStaff ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : '' }}" required {{ $isStaff ? 'readonly' : '' }}>
                    </div>
                </div>
                <!-- Logic Adjustment -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Metode Kalkulasi</label>
                        <select id="edit_calculation_method" name="calculation_method" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none bg-white" onchange="toggleEditProgressField()">
                            <option value="positive">Target Angka (Positif)</option>
                            <option value="negative">Target Negatif (Error/Complain)</option>
                            <option value="progress">Progress Project (Akumulatif)</option>
                        </select>
                    </div>
                    <div id="edit_progress_container" class="hidden">
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Progress Awal (Carry Over)</label>
                        <input type="number" step="0.01" id="edit_previous_progress" name="previous_progress" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none" placeholder="0">
                        <p class="text-[10px] text-slate-400 mt-1">Isi jika proyek dimulai sebelum tahun ini.</p>
                    </div>
                </div>
                <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 font-medium text-sm transition">Batal</button>
                    @if ($canManageKpi)
                    <button type="submit" class="px-5 py-2.5 rounded-lg bg-amber-500 text-white hover:bg-amber-600 font-bold text-sm shadow-lg shadow-amber-200 transition">Update Change</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL SUKSES (Muncul otomatis jika Session Success ada) --}}
@if(session('success'))
<div id="successModal" class="fixed inset-0 bg-slate-900/60 z-[100] flex justify-center items-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center transform scale-100 transition-all border border-emerald-100">
        <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl shadow-sm">
            <i class="fas fa-check"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">Berhasil Disimpan!</h3>
        <p class="text-slate-600 mb-6">{{ session('success') }}</p>
        <button onclick="document.getElementById('successModal').remove()" class="bg-emerald-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-emerald-700 transition w-full shadow-lg shadow-emerald-200">
            Tutup
        </button>
    </div>
</div>
@endif

{{-- MODAL UNSAVED CHANGES --}}
<div id="unsavedModal" class="fixed inset-0 bg-slate-900/60 z-[100] hidden justify-center items-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6 text-center border border-slate-200">
        <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-800 mb-2">Perubahan Belum Disimpan</h3>
        <p class="text-sm text-slate-600 mb-6">Anda memiliki perubahan yang belum disimpan. Jika Anda keluar sekarang, perubahan tersebut akan hilang. Yakin ingin keluar?</p>
        <div class="flex gap-3 justify-center">
            <button id="stayBtn" class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200 transition">
                Batal (Tetap di sini)
            </button>
            <button id="discardBtn" class="px-4 py-2 rounded-lg bg-rose-600 text-white font-semibold hover:bg-rose-700 transition shadow-lg shadow-rose-200">
                Ya, Keluar
            </button>
        </div>
    </div>
</div>

{{-- MODAL JUSTIFICATION --}}
<div id="modalJustification" class="fixed inset-0 bg-slate-900/60 hidden z-[70] flex justify-center items-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-0 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h2 class="text-lg font-bold text-slate-800"><i class="fas fa-comment-alt text-indigo-600 mr-2"></i>Catatan / Justifikasi</h2>
            <button onclick="closeJustificationModal()" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-6">
            <input type="hidden" id="justification-item-id">
            <input type="hidden" id="justification-month">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Catatan Staff</label>
                    <textarea id="justification-staff-text" rows="3" 
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none {{ !$isStaff ? 'bg-slate-50 text-slate-500' : '' }}" 
                        placeholder="{{ $isStaff ? 'Berikan penjelasan realisasi Anda...' : '(Belum ada catatan dari staff)' }}"
                        {{ !$isStaff ? 'readonly' : '' }}></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <label class="block text-xs font-bold text-indigo-600 uppercase mb-1">Feedback Manager / Atasan</label>
                    <textarea id="justification-manager-text" rows="3" 
                        class="w-full border border-indigo-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none {{ $isStaff ? 'bg-slate-50 text-slate-500' : '' }}" 
                        placeholder="{{ !$isStaff ? 'Berikan tanggapan atau evaluasi...' : '(Belum ada feedback dari atasan)' }}"
                        {{ $isStaff ? 'readonly' : '' }}></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeJustificationModal()" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 font-medium text-sm transition">Batal</button>
                <button type="button" onclick="saveJustification()" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 font-bold text-sm shadow-lg shadow-indigo-200 transition">Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- FORM DELETE GLOBAL --}}
@if ($canManageKpi)
    <form id="globalDeleteForm" method="POST" class="hidden">
        @csrf @method('DELETE')
    </form>
@endif

{{-- SCRIPT--}}
<script>
    // --- KONFIGURASI & VARIABEL ---
    let initialFormState = null;
    let targetUrl = null;
    let isSubmitting = false;
    let isMonitoring = false;
    function ensurePerspektifOption(selectEl, value) {
        if (!selectEl || !value) return;
        const exists = Array.from(selectEl.options).some(opt => opt.value === value);
        if (!exists) {
            const opt = new Option(`${value} (Tidak aktif)`, value, true, true);
            opt.dataset.inactive = '1';
            selectEl.add(opt, 0);
        }
    }

    // --- HELPER DATA FORM ---
    function getFormDataString() {
        const form = document.getElementById('kpiForm');
        if (!form) return "";
        return new URLSearchParams(new FormData(form)).toString();
    }

    // --- LOGIC UTAMA (LOAD) ---
    console.log("KPI script loaded: initializing form handlers...");
    function initKpiForm() {
        // 1. Matikan Tombol Simpan saat awal load
        toggleSaveButton(false);

        // 2. Jalankan Matematika
        calculateAll();

        // 3. AMBIL SNAPSHOT & AKTIFKAN MONITORING
        setTimeout(() => {
            initialFormState = getFormDataString();
            isMonitoring = true;

            // Pastikan status tombol sesuai kondisi awal (seharusnya mati/disabled)
            updateSystemState();

            console.log("System Ready: Button Logic Active.");
        }, 800);

        // 4. PASANG EVENT LISTENER
        const inputs = document.querySelectorAll('#kpiForm input, #kpiForm select, #kpiForm textarea');
        inputs.forEach(el => {
            // Kalkulasi Matematika
            if(el.tagName === 'INPUT') {
                el.addEventListener('input', calculateAll);
            }

            // Deteksi Perubahan untuk Tombol & Warning
            ['input', 'change'].forEach(evt => {
                el.addEventListener(evt, function() {
                    if (!isMonitoring) return;
                    if (isSubmitting) return;

                    // Cek status setiap kali user mengetik
                    updateSystemState();
                });
            });
        });

        // Delegated listener (fallback if inputs change dynamically)
        const formEl = document.getElementById('kpiForm');
        if (formEl) {
            formEl.addEventListener('input', function(e) {
                if (e.target && e.target.tagName === 'INPUT') {
                    calculateAll();
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initKpiForm);
    } else {
        // DOMContentLoaded has already fired — run init immediately
        initKpiForm();
    }

    // --- FUNGSI PUSAT KONTROL STATUS ---
    function updateSystemState() {
        const isChanged = hasUnsavedChanges();

        // 1. Update Badge Kuning
        const badge = document.getElementById('unsaved-badge');
        if(badge) {
            if (isChanged) badge.classList.remove('hidden');
            else badge.classList.add('hidden');
        }

        // 2. Update Tombol Simpan (Enable/Disable)
        toggleSaveButton(isChanged);
    }

    function hasUnsavedChanges() {
        if (!initialFormState) return false;
        const currentFormState = getFormDataString();
        return currentFormState !== initialFormState;
    }

    // --- LOGIC TOMBOL SIMPAN (ENABLE/DISABLE) ---
    function toggleSaveButton(enable) {
        const btn = document.getElementById('btnSimpan');
        if (!btn) return;

        if (enable) {
            // AKTIFKAN TOMBOL (User melakukan perubahan)
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
            btn.classList.add('hover:bg-blue-700', 'shadow-lg'); // Efek hover aktif
        } else {
            // MATIKAN TOMBOL (Tidak ada perubahan / Baru disimpan)
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            btn.classList.remove('hover:bg-blue-700', 'shadow-lg');
        }
    }

    // --- TOMBOL SIMPAN (SUBMIT ACTION) ---
    function submitKpiForm() {
        const btn = document.getElementById('btnSimpan');

        // Cek Bobot
        // Cek Bobot (Hanya Blokir jika > 100%)
        const alertBobot = document.getElementById('total-bobot-alert');
        if(alertBobot && alertBobot.innerText.includes('Melebihi 100%')) {
            alert('Gagal: Total Bobot melebihi 100%. Harap kurangi bobot item.');
            return;
        }

        // KUNCI: Set flag submitting
        isSubmitting = true;
        isMonitoring = false;

        // UI Loading (Tetap disable tombol agar tidak double click)
        if(btn) {
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Menyimpan...';
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-wait');
        }

        document.getElementById('kpiForm').submit();
    }

    // --- CEK SAAT REFRESH/CLOSE TAB ---
    window.addEventListener('beforeunload', function (e) {
        if (isSubmitting) return;
        if (!isMonitoring) return;
        if (document.getElementById('successModal')) return;

        if (hasUnsavedChanges()) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // --- CEK SAAT PINDAH HALAMAN (LINK) ---
    document.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            // Ignore internal links, js links, new tabs, special actions
            if (!href || href.startsWith('#') || href.startsWith('javascript') || this.target === '_blank' || href.includes('export') || href.includes('download')) return;

            if (isSubmitting) return;
            if (document.getElementById('successModal')) return;

            if (hasUnsavedChanges()) {
                e.preventDefault();
                targetUrl = href;
                const modal = document.getElementById('unsavedModal');
                if(modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            }
        });
    });

    // --- TOMBOL MODAL ALERT ---
    const stayBtn = document.getElementById('stayBtn');
    const discardBtn = document.getElementById('discardBtn');

    if(stayBtn) {
        stayBtn.addEventListener('click', () => {
            const modal = document.getElementById('unsavedModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            targetUrl = null;
        });
    }

    if(discardBtn) {
        discardBtn.addEventListener('click', () => {
            const modal = document.getElementById('unsavedModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            if (targetUrl) {
                isMonitoring = false; // Stop monitoring to allow exit
                window.location.href = targetUrl;
            }
        });
    }

    // --- FILTER TAHUN ---
    function changeKpiYear(selectedYear) {
        if (!selectedYear) { alert('Pilih tahun!'); return; }

        const nextUrl = "{{ route('kpi.show', ['karyawan_id' => $karyawan->id_karyawan, 'tahun' => '8888']) }}".replace('8888', selectedYear);

        if (hasUnsavedChanges() && !document.getElementById('successModal')) {
            targetUrl = nextUrl;
            const modal = document.getElementById('unsavedModal');
            if(modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
            return;
        }
        window.location.href = nextUrl;
    }

    // --- MATEMATIKA KPI (TETAP SAMA) ---
    const rows = document.querySelectorAll('.row-kpi');
    const monthsSmt1 = ['jan','feb','mar','apr','mei','jun'];
    const monthsSmt2 = ['jul', 'aug', 'sep', 'okt', 'nov', 'des'];
    const monthsAll = [...monthsSmt1, ...monthsSmt2];

    function parseNumber(val) { if (!val || val === '') return 0; return parseFloat(val.toString().replace(',', '.')) || 0; }
    function formatNumber(num) { return Number(num).toFixed(2).replace(/\.00$/, ''); }

    function calculateSingleScore(target, real, polaritas) {
        if (target === 0) return 0;
        let score = 0;
        const p = (polaritas || '').toLowerCase();

        // Accept various historical/abbreviated polaritas values (e.g. "I MAX", "MAX", "Min", "Minimize")
        // Logic: Semakin besar semakin bagus (Positive)
        if (p.includes('posit') || p.includes('max') || p.includes('maximize')) {
            score = (real / target) * 100;
        } 
        // Logic: Semakin kecil semakin bagus (Negative / Minimize)
        // Rule: Cap 100 if Real <= Target. Penalty if Real > Target.
        else if (p.includes('neg') || p.includes('min') || p.includes('minimize')) {
            if (real <= target) {
                score = 100;
            } else {
                if (target === 0) {
                    score = 0; // Avoid division by zero
                } else {
                    // Formula: 100 - (((Real - Target) / Target) * 100)
                    let deviation = ((real - target) / target) * 100;
                    score = 100 - deviation;
                }
            }
        } 
        // Logic: Binary (Yes/No)
        else if (p.includes('yes') || p.includes('no')) {
            score = (real >= target) ? 100 : 0;
        } else {
            // Default Fallback (Maximize)
            score = (real / target) * 100;
        }

        return Math.max(0, score); // Ensure min 0
    }

    function calculateAll() {
        try {
            let footerSmt1 = 0; let footerSmt2 = 0; let footerGrandTotal = 0;
            let footerAdjSmt1 = 0; let footerAdjSmt2 = 0;
            let footerMonthly = { jan:0, feb:0, mar:0, apr:0, mei:0, jun:0, jul:0, aug:0, sep:0, okt:0, nov:0, des:0 };

            rows.forEach(row => {
                const bobotInput = row.querySelector('.input-bobot');
                const polaritasInput = row.querySelector('.input-polaritas');
                if (!bobotInput || !polaritasInput) return;

                const bobot = parseNumber(bobotInput.value);
                const polaritas = polaritasInput.value;

                // SMT 1 (Januari - Juni)
                let totalTargetSmt1 = 0; let totalRealSmt1 = 0;
                let sumSkorSmt1 = 0; let countSmt1 = 0;

                monthsSmt1.forEach(bln => {
                    const inputTgt = row.querySelector(`.input-target-${bln}`);
                    const inputReal = row.querySelector(`.input-real-${bln}`);
                    const inputJustification = row.querySelector(`#justification-${row.querySelector('.item-checkbox').value}-${bln}`);
                    
                    let t = 0; let r = 0;
                    if (inputTgt) t = parseNumber(inputTgt.value);
                    if (inputReal) r = parseNumber(inputReal.value);
                    totalTargetSmt1 += t;
                    totalRealSmt1 += r;

                    // compute per-month if target exists (real may be zero)
                    if (inputTgt) {
                        let skor = (t !== 0) ? calculateSingleScore(t, r, polaritas) : 0;
                        let nilai = (skor * bobot) / 100; // Old Logic (still used for monthly footer?)
                        const spanSkor = row.querySelector(`.span-skor-${bln}`);
                        const spanNilai = row.querySelector(`.span-nilai-${bln}`);
                        if (spanSkor) spanSkor.textContent = formatNumber(skor);
                        if (spanNilai) spanNilai.textContent = formatNumber(nilai);
                        footerMonthly[bln] += nilai;
                        
                        // Accumulate for Subtotal (Average of Scores)
                        sumSkorSmt1 += skor;
                        countSmt1++;
                    }
                });

                // Subtotal Smt 1 (Average Score) & Total Real
                let subtotalSmt1 = countSmt1 > 0 ? sumSkorSmt1 / countSmt1 : 0;
                if (row.querySelector('.span-subtotal-smt1')) row.querySelector('.span-subtotal-smt1').textContent = formatNumber(subtotalSmt1);
                if (row.querySelector('.span-total-real-smt1')) row.querySelector('.span-total-real-smt1').textContent = formatNumber(totalRealSmt1);
                if (row.querySelector('.input-subtotal-smt1')) row.querySelector('.input-subtotal-smt1').value = formatNumber(subtotalSmt1);

                // ADJ SMT 1 (Tengah Tahun)
                const adjReal1Input = row.querySelector('.input-adj-real-smt1'); // Adjustment Value Input
                const adjNilaiInput1 = row.querySelector('.input-adj-nilai-smt1'); // Final Value Smt 1
                
                let finalSmt1Score = subtotalSmt1;
                
                // Logic Adjustment: Add/Subtract from Subtotal
                // Input 'adjReal1Input' is treated as the ADJUSTMENT VALUE (e.g., +5, -2, 10) NOT "Real" anymore based on request context "menambah atau mengurangi nilai"
                // But wait, the previous logic used it as "Real Adjusment". 
                // Request says: "Atasan dapat mengisi kolom 'Adj. Tengah Tahun' ... untuk menambah atau mengurangi nilai dari 'Subtotal Skor'"
                // So I will treat `adjustment_real_smt1` as the adjustment (+/- value). 
                
                let adjustment1 = 0;
                if (adjReal1Input && adjReal1Input.value !== "") {
                    adjustment1 = parseNumber(adjReal1Input.value);
                }
                
                finalSmt1Score = subtotalSmt1 + adjustment1;
                // Clamp 0-100? or allow >100? KPI usually allows >100. Let's keep it open.
                finalSmt1Score = Math.max(0, finalSmt1Score);

                // Final Value Smt 1 = (Final Score * Bobot) / 100
                let nilaiTotalSmt1 = (finalSmt1Score * bobot) / 100;
                
                if (row.querySelector('.span-adj-skor-smt1')) row.querySelector('.span-adj-skor-smt1').textContent = formatNumber(finalSmt1Score); // Display Adjusted Score
                if (adjNilaiInput1) adjNilaiInput1.value = formatNumber(nilaiTotalSmt1);



                
                // Subtotal Smt 2 (Average Score)
                // SMT 2 (Juli - Desember)
                let totalTargetSmt2 = 0; let totalRealSmt2 = 0;
                let sumSkorSmt2 = 0; let countSmt2 = 0;

                monthsSmt2.forEach(bln => {
                    const inputTgt = row.querySelector(`.input-target-${bln}`);
                    const inputReal = row.querySelector(`.input-real-${bln}`);
                    const inputJustification = row.querySelector(`#justification-${row.querySelector('.item-checkbox').value}-${bln}`);
                    
                    let t = 0; let r = 0;
                    if (inputTgt) t = parseNumber(inputTgt.value);
                    if (inputReal) r = parseNumber(inputReal.value);
                    totalTargetSmt2 += t;
                    totalRealSmt2 += r;

                    if (inputTgt) {
                        let skor = (t !== 0) ? calculateSingleScore(t, r, polaritas) : 0;
                        let nilai = (skor * bobot)/100;
                        const spanSkor = row.querySelector(`.span-skor-${bln}`);
                        const spanNilai = row.querySelector(`.span-nilai-${bln}`);
                        if (spanSkor) spanSkor.textContent = formatNumber(skor);
                        if (spanNilai) spanNilai.textContent = formatNumber(nilai);
                        footerMonthly[bln] += nilai;
                        
                        sumSkorSmt2 += skor;
                        countSmt2++;
                    }
                });

                let subtotalSmt2 = countSmt2 > 0 ? sumSkorSmt2 / countSmt2 : 0;
                if (row.querySelector('.span-subtotal-smt2')) row.querySelector('.span-subtotal-smt2').textContent = formatNumber(subtotalSmt2);
                if (row.querySelector('.span-total-real-smt2')) row.querySelector('.span-total-real-smt2').textContent = formatNumber(totalRealSmt2);
                if (row.querySelector('.input-subtotal-smt2')) row.querySelector('.input-subtotal-smt2').value = formatNumber(subtotalSmt2);


                // ADJ SMT 2 (Akhir Tahun)
                // const adjTarget2Input = row.querySelector('.input-adj-target-smt2'); // Removed HTML
                const adjReal2Input = row.querySelector('.input-adj-real-smt2'); // Adjustment Value Smt 2
                const adjNilaiInput2 = row.querySelector('.input-adj-nilai-smt2'); // Final Value Smt 2
                
                let finalSmt2Score = subtotalSmt2;
                let adjustment2 = 0;
                if (adjReal2Input && adjReal2Input.value !== "") {
                    adjustment2 = parseNumber(adjReal2Input.value);
                }
                
                finalSmt2Score = subtotalSmt2 + adjustment2;
                finalSmt2Score = Math.max(0, finalSmt2Score);
                
                let nilaiTotalSmt2 = (finalSmt2Score * bobot) / 100;

                if (row.querySelector('.span-adj-skor-smt2')) row.querySelector('.span-adj-skor-smt2').textContent = formatNumber(finalSmt2Score);
                if (adjNilaiInput2) adjNilaiInput2.value = formatNumber(nilaiTotalSmt2);

                // TOTALS
                footerSmt1 += nilaiTotalSmt1;
                footerSmt2 += nilaiTotalSmt2;
                footerAdjSmt1 += nilaiTotalSmt1; // Use final value
                footerAdjSmt2 += nilaiTotalSmt2; // Use final value
                
                let grandFinal = (nilaiTotalSmt1 + nilaiTotalSmt2) / 2; // Average of Values? Or Sum?
                // Wait. If Smt 1 Value = 5 (from 50% score * 10% weight) and Smt 2 Value = 5.
                // Total should be ... 
                // Usually: (Score1 + Score2)/2 * Weight? Or (Value1 + Value2)?
                // Previous logic: (finalSmt1 + finalSmt2) / 2. 
                // If finalSmt1 is ALREADY weighted value (e.g. 10), and finalSmt2 is 10.
                // Then (10+10)/2 = 10. Correct.
                
                if (row.querySelector('.span-final-score')) row.querySelector('.span-final-score').textContent = formatNumber(grandFinal);
                footerGrandTotal += grandFinal;
            });
            
            const setFooterText = (id, val) => { const el = document.getElementById(id); if(el) el.textContent = formatNumber(val); };
            setFooterText('footer-total-smt1', footerSmt1);
            setFooterText('footer-total-smt2', footerSmt2);
            setFooterText('footer-grand-total', footerGrandTotal);
            monthsAll.forEach(bln => setFooterText(`footer-total-${bln}`, footerMonthly[bln]));
            if(document.getElementById('footer-adj-smt1')) document.getElementById('footer-adj-smt1').textContent = formatNumber(footerAdjSmt1);
            if(document.getElementById('footer-adj-smt2')) document.getElementById('footer-adj-smt2').textContent = formatNumber(footerAdjSmt2);
            checkBobot();
            updateSummary();
        } catch (e) {
            console.error('Error in calculateAll: ', e);
        }
    }

    function checkBobot() {
        let totalBobot = 0;
        document.querySelectorAll('.input-bobot').forEach(el => totalBobot += parseNumber(el.value));
        totalBobot = Math.round(totalBobot * 100) / 100;
        const alertBox = document.getElementById('total-bobot-alert');
        if (alertBox) {
            if (totalBobot > 100) {
                 alertBox.innerHTML = `<span class="text-white bg-red-600 px-2 py-1 rounded border border-red-200"><i class="fas fa-exclamation-triangle"></i> Total Bobot: ${totalBobot}% (Melebihi 100%)</span>`;
            } else if (totalBobot < 100) {
                 alertBox.innerHTML = `<span class="text-slate-700 bg-yellow-400 px-2 py-1 rounded border border-yellow-500"><i class="fas fa-info-circle"></i> Total Bobot: ${totalBobot}% (Kurang dari 100%, Boleh Disimpan)</span>`;
            } else {
                 alertBox.innerHTML = `<span class="text-white bg-green-600 px-2 py-1 rounded border border-green-200"><i class="fas fa-check-circle"></i> Total Bobot: 100% (OK)</span>`;
            }
        }
    }

    function updateSummary() {
        const totalKpiEl = document.getElementById('summary-total-kpi');
        const completionEl = document.getElementById('summary-completion');
        const completionTextEl = document.getElementById('summary-completion-text');
        const statusEl = document.getElementById('summary-status');

        const rowsCount = document.querySelectorAll('.row-kpi').length;
        const inputs = Array.from(document.querySelectorAll('#kpiForm input[type="number"]'))
            .filter(input => !input.hasAttribute('readonly') && input.type === 'number');
        const filled = inputs.filter(input => input.value !== '').length;
        const total = inputs.length;
        const completion = total > 0 ? Math.round((filled / total) * 100) : 0;

        if (totalKpiEl) totalKpiEl.textContent = rowsCount.toString();
        if (completionEl) completionEl.textContent = `${completion}%`;
        if (completionTextEl) completionTextEl.textContent = `${filled}/${total}`;

        if (statusEl) {
            const isReady = completion >= 80 && !document.getElementById('total-bobot-alert')?.innerText.includes('Melebihi 100%');
            const isEmpty = rowsCount === 0;
            if (isEmpty) {
                statusEl.className = 'mt-2 inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200';
                statusEl.innerHTML = '<i class="fas fa-inbox"></i> Kosong';
            } else if (isReady) {
                statusEl.className = 'mt-2 inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200';
                statusEl.innerHTML = '<i class="fas fa-check-circle"></i> Siap Disimpan';
            } else {
                statusEl.className = 'mt-2 inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 border border-yellow-200';
                statusEl.innerHTML = '<i class="fas fa-pen"></i> Draft';
            }
        }
    }

    // Modal Helper
    function openEditModal(data, updateUrl) {
        document.getElementById('formEditKPI').action = updateUrl;
        const perspektifSelect = document.getElementById('edit_perspektif');
        if (perspektifSelect) {
            Array.from(perspektifSelect.options).forEach(opt => {
                if (opt.dataset.inactive === '1') opt.remove();
            });
            ensurePerspektifOption(perspektifSelect, data.perspektif);
            perspektifSelect.value = data.perspektif || '';
        }
        document.getElementById('edit_kra').value = data.key_result_area || data.kra;
        document.getElementById('edit_kpi').value = data.key_performance_indicator || data.indikator;
        // Units & Target
        document.getElementById('edit_polaritas').value = data.polaritas;
        document.getElementById('edit_bobot').value = data.bobot;
        document.getElementById('edit_units').value = data.units;
        
        // New Fields
        const calcMethod = data.calculation_method || 'positive';
        document.getElementById('edit_calculation_method').value = calcMethod;
        document.getElementById('edit_previous_progress').value = data.previous_progress || 0;
        
        toggleEditProgressField();

        const modal = document.getElementById('modalEditKPI');
        modal.classList.remove('hidden'); modal.classList.add('flex');
    }

    function toggleEditProgressField() {
        const method = document.getElementById('edit_calculation_method').value;
        const container = document.getElementById('edit_progress_container');
        if (method === 'progress') {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    }
    function closeEditModal() {
        const modal = document.getElementById('modalEditKPI');
        modal.classList.add('hidden'); modal.classList.remove('flex');
    }
    function confirmDelete(deleteUrl) {
        if (confirm('Yakin ingin menghapus KPI ini?')) {
            const form = document.getElementById('globalDeleteForm');
            form.action = deleteUrl; form.submit();
        }
    }

    // --- JUSTIFICATION MODAL LOGIC ---
    function openJustificationModal(itemId, month) {
        const staffInputId = `justification-staff-${itemId}-${month}`;
        const managerInputId = `justification-manager-${itemId}-${month}`;
        
        const staffInputEl = document.getElementById(staffInputId);
        const managerInputEl = document.getElementById(managerInputId);
        
        const modal = document.getElementById('modalJustification');
        const staffTextarea = document.getElementById('justification-staff-text');
        const managerTextarea = document.getElementById('justification-manager-text');
        
        document.getElementById('justification-item-id').value = itemId;
        document.getElementById('justification-month').value = month;
        
        staffTextarea.value = staffInputEl ? staffInputEl.value : '';
        managerTextarea.value = managerInputEl ? managerInputEl.value : '';
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Auto focus the editable one
        if(!staffTextarea.readOnly) {
            setTimeout(() => staffTextarea.focus(), 100);
        } else if(!managerTextarea.readOnly) {
            setTimeout(() => managerTextarea.focus(), 100);
        }
    }

    function closeJustificationModal() {
        const modal = document.getElementById('modalJustification');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function saveJustification() {
        const itemId = document.getElementById('justification-item-id').value;
        const month = document.getElementById('justification-month').value;
        
        const staffText = document.getElementById('justification-staff-text').value;
        const managerText = document.getElementById('justification-manager-text').value;
        
        const staffInputEl = document.getElementById(`justification-staff-${itemId}-${month}`);
        const managerInputEl = document.getElementById(`justification-manager-${itemId}-${month}`);
        
        if (staffInputEl && managerInputEl) {
            staffInputEl.value = staffText;
            managerInputEl.value = managerText;
            
            // Visual feedback on button (add dot if text exists)
            const parent = staffInputEl.parentElement;
            const btn = parent.querySelector('button');
            if(btn) {
                 if(staffText.trim() !== '' || managerText.trim() !== '') {
                     if(!btn.querySelector('.bg-red-500')) {
                         btn.innerHTML += '<span class="absolute top-0 right-0 w-1.5 h-1.5 bg-red-500 rounded-full"></span>';
                     }
                 } else {
                     const dot = btn.querySelector('.bg-red-500');
                     if(dot) dot.remove();
                 }
            }
            
            // Trigger change for unsaved badge
            const event = new Event('change', { bubbles: true });
            staffInputEl.dispatchEvent(event);
        }
        closeJustificationModal();
    }
</script>

<style>
@keyframes modalFadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');

    function toggleBulkDeleteButton() {
        const anyChecked = document.querySelectorAll('.item-checkbox:checked').length > 0;
        if (anyChecked) {
            bulkDeleteBtn.classList.remove('hidden');
        } else {
            bulkDeleteBtn.classList.add('hidden');
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            toggleBulkDeleteButton();
        });
    }

    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            if (!this.checked) {
                selectAllCheckbox.checked = false;
            } else {
                const allChecked = Array.from(itemCheckboxes).every(cb => cb.checked);
                if (allChecked) {
                    selectAllCheckbox.checked = true;
                }
            }
            toggleBulkDeleteButton();
        });
    });

    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function () {
            const selectedIds = Array.from(document.querySelectorAll('.item-checkbox:checked')).map(cb => cb.value);

            if (selectedIds.length > 0 && confirm('Yakin ingin menghapus ' + selectedIds.length + ' item terpilih?')) {
                fetch('{{ route("kpi.items.bulk-delete") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ids: selectedIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Gagal menghapus item: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghubungi server.');
                });
            }
        });
    }

    // Initial check
    toggleBulkDeleteButton();

    // Initialize Tippy
    tippy('[data-tippy-content]', {
        animation: 'shift-away',
        theme: 'dark',
        arrow: true,
    });
});
</script>
</body>
</html>
