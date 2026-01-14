<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <title>Form Penilaian KPI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>tailwind.config = { darkMode: 'class' }</script>
    <style>
        .custom-scrollbar::-webkit-scrollbar { height: 10px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 5px; border: 2px solid #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }
        input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 p-2 md:p-6 font-sans">

<div class="w-full max-w-7xl mx-auto">
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

    {{-- HEADER --}}
    <div class="mb-6 flex flex-col lg:flex-row justify-between items-start lg:items-center bg-white dark:bg-gray-800 p-4 md:p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Form Penilaian KPI</h1>
            <p class="text-gray-500">Periode Penilaian: {{ $tahun }}</p>
        </div>

        {{-- FILTER TAHUN KPI --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 whitespace-nowrap">
                    <i class="fas fa-calendar-alt mr-1 text-blue-600"></i>Pilih Tahun:
                </label>
                <select id="yearFilterForm" onchange="changeKpiYear(this.value)" class="flex-1 sm:flex-none px-3 py-2 border border-blue-300 dark:border-blue-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm transition cursor-pointer">
                    @for($y = date('Y'); $y >= date('Y')-5; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                            Tahun {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="text-xs text-gray-600 dark:text-gray-400 italic">
                Menampilkan KPI tahun <strong class="text-blue-600 dark:text-blue-400" id="currentYearDisplay">{{ $tahun }}</strong>
            </div>
        </div>
        
        <div class="flex flex-wrap gap-2 w-full lg:w-auto justify-start lg:justify-end items-center">
            {{-- LOGIKA TOMBOL KEMBALI DINAMIS --}}
            @if(auth()->user()->hasAnyRole(['superadmin', 'admin']))
                {{-- 1. Jika ADMIN: Kembali ke Tabel List KPI --}}
                <a href="{{ route('kpi.index') }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition flex-1 lg:flex-none text-center text-gray-600 dark:text-gray-300">
                    <i class="fas fa-arrow-left mr-1"></i> List Karyawan
                </a>
            @else
                {{-- 2. Jika MANAGER/STAFF: Kembali ke Dashboard Utama (Supaya tidak Looping) --}}
                <a href="{{ url('/dashboard') }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition flex-1 lg:flex-none text-center text-gray-600 dark:text-gray-300">
                    <i class="fas fa-home mr-1"></i> Dashboard
                </a>
            @endif

            {{-- 2. [BARU] Tombol Export Dropdown --}}
            <div class="relative group">
                <button type="button" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition flex items-center gap-2 border border-gray-200 dark:border-gray-600">
                    <i class="fas fa-download"></i> 
                    <span class="hidden sm:inline font-medium">Export</span>
                    <i class="fas fa-chevron-down text-xs ml-1"></i>
                </button>
                
                {{-- Isi Dropdown (Muncul saat Hover) --}}
                <div class="absolute right-0 mt-1 w-40 bg-white dark:bg-gray-800 rounded-md shadow-xl border border-gray-100 dark:border-gray-700 hidden group-hover:block z-50 overflow-hidden">
                    {{-- Link Excel --}}
                    <a href="{{ route('performance.export.excel', ['karyawan_id' => $karyawan->id_karyawan, 'tahun' => $kpi->tahun]) }}" 
                       class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-green-50 hover:text-green-700 dark:hover:bg-gray-700 flex items-center gap-2 transition">
                        <i class="fas fa-file-excel text-green-600 w-4"></i> Export Excel
                    </a>
                    
                    {{-- Link PDF --}}
                    <a href="{{ route('performance.export.pdf', ['karyawan_id' => $karyawan->id_karyawan, 'tahun' => $kpi->tahun]) }}" 
                       class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-red-50 hover:text-red-700 dark:hover:bg-gray-700 flex items-center gap-2 transition border-t border-gray-100 dark:border-gray-700">
                        <i class="fas fa-file-pdf text-red-600 w-4"></i> Export PDF
                    </a>
                </div>
            </div>
            @php
                $isManager = false;
                // Asumsi user sudah terhubung ke data karyawan lewat auth
                $loggedInKaryawan = \App\Models\Karyawan::where('nik', auth()->user()->nik)->first();
                if($loggedInKaryawan && $karyawan->atasan_id == $loggedInKaryawan->id_karyawan) {
                    $isManager = true;
                }
            @endphp
            {{-- 3. Tombol Simpan --}}
            <button id="btnSimpan" type="button" onclick="submitKpiForm()" 
                class="px-3 py-2 {{ $isManager ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white rounded-lg text-sm transition shadow-lg flex items-center justify-center gap-2 flex-1 lg:flex-none">
                
                @if($isManager)
                    <i class="fas fa-check-double"></i> <span class="hidden sm:inline">Simpan & Approve</span>
                @else
                    <i class="fas fa-save"></i> <span class="hidden sm:inline">Simpan</span>
                @endif
            </button>
        </div>
    </div>

    {{-- ACTION BAR --}}
    <div class="mb-4 flex flex-col sm:flex-row justify-between items-center gap-4">
        <div id="total-bobot-alert" class="text-2xl font-bold w-full sm:w-auto text-center sm:text-left"></div>
        <button type="button" onclick="document.getElementById('modalTambahKPI').classList.remove('hidden')" class="w-full sm:w-auto px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-bold hover:bg-green-700 transition shadow flex items-center justify-center gap-2">
            <i class="fas fa-plus"></i> Tambah KPI Baru
        </button>
    </div>

    {{-- PESAN JIKA FORM KOSONG --}}
    @if($items->isEmpty())
    <div class="mb-6 bg-blue-50 dark:bg-blue-900/30 border-l-4 border-blue-500 p-5 rounded-lg shadow-sm">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 text-2xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-blue-900 dark:text-blue-200 mb-2">
                    Form KPI Tahun {{ $tahun }} Kosong
                </h3>
                <p class="text-sm text-blue-800 dark:text-blue-300 mb-3">
                    Data KPI untuk tahun ini belum ada. Silakan tambahkan KPI baru dengan menekan tombol <strong>"Tambah KPI Baru"</strong> di atas, atau Anda bisa mengisi detail KPI ketika semua item sudah ditambahkan.
                </p>
                <div class="text-xs text-blue-700 dark:text-blue-400 space-y-1">
                    <p><i class="fas fa-arrow-right mr-2"></i>Klik tombol <strong>Tambah KPI Baru</strong> untuk memulai</p>
                    <p><i class="fas fa-arrow-right mr-2"></i>Isi informasi KPI sesuai kebutuhan</p>
                    <p><i class="fas fa-arrow-right mr-2"></i>Setelah semua KPI ditambah, klik <strong>Simpan</strong> untuk menyimpan</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    @php
    // Cek apakah user yang login berhak melakukan adjustment?
    // Staff TIDAK BOLEH (False), Manager/Admin BOLEH (True)
    $isStaff = auth()->user()->role == 'staff';
    
    // Class CSS untuk input yang dikunci (Abu-abu & tidak bisa diklik)
    $readonlyClass = $isStaff ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-transparent text-orange-700 font-bold border-b border-orange-300';
    @endphp

    {{-- FORM UTAMA --}}
    <form id="kpiForm" action="{{ route('kpi.update', $kpi->id_kpi_assessment) }}" method="POST">
        @csrf
        @if(!$items->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden border border-gray-200 dark:border-gray-700 relative">
            <div class="w-full overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm text-left border-collapse min-w-[3000px] md:min-w-[4500px]"> 
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 sticky top-0 z-20 shadow-sm">
                        <tr>
                            <th rowspan="2" class="sticky left-0 bg-gray-200 z-30 p-2 md:p-3 w-10 md:w-12 text-center border border-gray-300">No</th>
                            <th rowspan="2" class="md:sticky left-10 md:left-12 bg-gray-200 z-30 p-2 md:p-3 w-60 md:w-72 border border-gray-300">Key Performance Indicator</th>
                            <th rowspan="2" class="p-2 md:p-3 w-28 border border-gray-300 bg-gray-50">Perspektif</th>
                            <th rowspan="2" class="p-2 md:p-3 w-16 text-center border border-gray-300 bg-gray-50">Bobot</th>
                            <th rowspan="2" class="p-2 md:p-3 w-16 text-center border border-gray-300 bg-gray-50">Target</th>
                            <th colspan="3" class="p-1 text-center border border-gray-300 bg-blue-50">Semester 1</th>
                            @foreach(['Juli','Agustus','September','Oktober','November','Desember'] as $bulan) <th colspan="4" class="p-1 text-center border border-gray-300 bg-green-50">{{ $bulan }}</th> @endforeach
                            <th colspan="4" class="p-1 text-center border border-gray-300 bg-gray-100">Total Semester 2</th>
                            <th colspan="3" class="p-1 text-center border border-gray-300 bg-orange-50">Adjustment S-I</th>
                            <th colspan="4" class="p-1 text-center border border-gray-300 bg-orange-50">Adjustment S-II</th>
                            <th rowspan="2" class="p-2 w-20 text-center border border-gray-300 bg-gray-200 font-bold">FINAL SCORE</th>
                        </tr>
                        <tr>
                            <th class="p-1 border w-14">Real</th><th class="p-1 border w-14">Skor</th><th class="p-1 border w-16 bg-blue-100">Nilai</th>
                            @foreach(['jul','aug','sep','okt','nov','des'] as $bln) <th class="p-1 border w-14">Tgt</th><th class="p-1 border w-14">Real</th><th class="p-1 border w-14">Skor</th><th class="p-1 border w-14">Nilai</th> @endforeach
                            <th class="p-1 border w-14">Tgt</th><th class="p-1 border w-14">Real</th><th class="p-1 border w-14">Skor</th><th class="p-1 border w-16 bg-gray-200">Nilai</th>
                            <th class="p-1 border w-14">Real</th><th class="p-1 border w-14">Skor</th><th class="p-1 border w-16 bg-orange-100">Nilai</th>
                            <th class="p-1 border w-14">Tgt</th><th class="p-1 border w-14">Real</th><th class="p-1 border w-14">Skor</th><th class="p-1 border w-16 bg-orange-100">Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($items as $index => $item)
                        @php $score = $item->scores->first(); @endphp
                        <tr class="row-kpi hover:bg-gray-50 dark:hover:bg-gray-600 transition group text-xs md:text-sm">
                            {{-- IDENTITAS --}}
                            <td class="sticky left-0 bg-white z-10 p-2 md:p-3 text-center border-r font-medium">{{ $items->firstItem() + $index }}</td>
                            <td class="md:sticky left-10 md:left-12 bg-white z-10 p-2 md:p-3 border-r align-top shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                                <div class="flex flex-col sm:flex-row justify-between items-start gap-2">
                                    <div class="font-semibold text-gray-900 leading-snug">{{ $item->key_performance_indicator ?? $item->indikator }}</div>
                                    <div class="flex gap-1 shrink-0">
                                        <button type="button" onclick="openEditModal({{ json_encode($item) }}, '{{ route('kpi.update-item', $item->id_kpi_item) }}')" class="text-gray-400 hover:text-yellow-600 p-1"><i class="fas fa-pencil-alt text-[10px]"></i></button>
                                        <button type="button" onclick="confirmDelete('{{ route('kpi.delete-item', $item->id_kpi_item) }}')" class="text-gray-400 hover:text-red-600 p-1"><i class="fas fa-trash-alt text-[10px]"></i></button>
                                    </div>
                                </div>
                                <div class="text-[15px] text-gray-500 mt-1">{{ $item->units ?? $item->satuan }} | {{ $item->polaritas }}</div>
                                <input type="hidden" class="input-bobot" value="{{ $item->bobot }}">
                                <input type="hidden" class="input-polaritas" value="{{ $item->polaritas }}">
                            </td>
                            <td class="p-2 md:p-3 border-r align-top">{{ $item->perspektif }}</td>
                            <td class="p-2 md:p-3 text-center border-r align-top font-bold text-blue-600 bg-blue-50/10">{{ $item->bobot }}%</td>
                            <td class="p-2 md:p-3 text-center border-r align-top font-bold text-gray-700">
                                {{ $item->target }}
                                {{-- FIX 2: GANTI ID -> ID_KPI_ITEM (PENTING AGAR TIDAK KOSONG SAAT DISIMPAN) --}}
                                <input type="hidden" class="input-target-smt1" name="kpi[{{ $item->id_kpi_item }}][target_smt1]" value="{{ $item->target }}">
                            </td>

                            {{-- SEMESTER 1 --}}
                            @php $bln = 'smt1'; @endphp
                            <td class="p-1 border-r align-center">
                                <input type="number" step="0.01" name="kpi[{{ $item->id_kpi_item }}][real_{{ $bln }}]" value="{{ $score->{'real_'.$bln} ?? 0 }}" class="input-real-smt1 w-full h-8 px-1 border rounded text-center"></td>
                            <td class="p-1 border-r align-center text-center bg-gray-50">
                                <div class="py-1.5 font-medium text-gray-600"><span class="span-skor-smt1">0</span>%</div>
                            </td>
                            <td class="p-1 border-r-2 align-center text-center bg-blue-50/20">
                                <div class="py-1.5 font-bold text-blue-700"><span class="span-nilai-smt1">0</span>%</div>
                            </td>

                            {{-- BULANAN --}}
                            @foreach(['jul','aug','sep','okt','nov','des'] as $bln)
                                <td class="p-1 border-r align center"><input type="number" step="0.01" name="kpi[{{ $item->id_kpi_item }}][target_{{ $bln }}]" value="{{ $score->{'target_'.$bln} ?? 0 }}" class="input-target-{{ $bln }} w-full h-8 px-1 border rounded text-center"></td>
                                <td class="p-1 border-r align center"><input type="number" step="0.01" name="kpi[{{ $item->id_kpi_item }}][real_{{ $bln }}]" value="{{ $score->{'real_'.$bln} ?? 0 }}" class="input-real-{{ $bln }} w-full h-8 px-1 border rounded text-center"></td>
                                <td class="p-1 border-r align center text-center bg-gray-50"><div class="py-1.5 font-medium text-gray-600"><span class="span-skor-{{ $bln }}">0</span>%</div></td>
                                <td class="p-1 border-r-2 align center text-center bg-blue-50/20"><div class="py-1.5 font-bold text-blue-700"><span class="span-nilai-{{ $bln }}">0</span>%</div></td>
                            @endforeach

                            {{-- TOTAL SEMESTER 2 --}}
                            <td class="p-1 text-center border-r bg-gray-50 align-center">
                                <input type="number" step="0.01" name="kpi[{{ $item->id_kpi_item }}][total_target_smt2]" value="{{ old('kpi.'.$item->id_kpi_item.'.total_target_smt2', $score->total_target_smt2 ?? 0) }}" class="input-total-target-smt2 w-full h-8 px-1 bg-white border rounded text-center focus:border-blue-500 outline-none placeholder-gray-400" placeholder="0">
                            </td>
                            <td class="p-1 text-center  border-r bg-gray-50 align-center">
                                <input type="number" step="0.01" name="kpi[{{ $item->id_kpi_item }}][total_real_smt2]" value="{{ old('kpi.'.$item->id_kpi_item.'.total_real_smt2', $score->total_real_smt2 ?? 0) }}" class="input-total-real-smt2 w-full h-8 px-1 bg-white border rounded text-center focus:border-green-500 outline-none placeholder-gray-400" placeholder="0">
                            </td>
                            <td class="p-2 text-center border-r bg-gray-50 text-gray-500"><span class="span-total-skor-smt2">0</span>%</td>
                            <td class="p-2 text-center border-r bg-gray-100 font-bold text-gray-700"><span class="span-total-nilai-smt2">0</span>%</td>

                            {{-- ADJ S-I --}}
                            <td class="p-1 text-center border-r bg-orange-50/30 align-center">
                                <input 
                                type="number" 
                                name="kpi[{{ $item->id_kpi_item }}][adjustment_real_smt1]" 
                                value="{{ old('kpi.'.$item->id_kpi_item.'.adjustment_real_smt1', $score->adjustment_real_smt1 ?? '') }}"
                                {{ $isStaff ? 'readonly' : '' }} 
                                step="0.01" class="input-adj-real-smt1 w-full h-8 px-1 bg-transparent text-center border-b border-orange-200 outline-none" placeholder="Real"></td>
                            <td class="p-1 border-r bg-orange-50/30 align-center text-center pt-2">
                                <span class="span-adj-skor-smt1 font-bold text-orange-600">0</span>%</td>
                            <td class="p-1 text-center border-r bg-orange-50/30 align-center">
                                <input type="number" step="0.01" name="kpi[{{ $item->id_kpi_item }}][adjustment_smt1]" value="{{ old('kpi.'.$item->id_kpi_item.'.adjustment_smt1', $score->adjustment_smt1 ?? '') }}" class="input-adj-nilai-smt1 w-full h-8 px-1 bg-transparent text-center font-bold text-orange-600 border-b border-orange-200 outline-none" placeholder="Nilai" readonly></td>

                            {{-- ADJ S-II --}}
                            <td class="p-1 border-r bg-orange-50/30 align-center">
                                <input type="number" 
                                name="kpi[{{ $item->id_kpi_item }}][adjustment_target_smt2]" 
                                value="{{ old('kpi.'.$item->id_kpi_item.'.adjustment_target_smt2', $score->adjustment_target_smt2 ?? '') }}"
                                {{ $isStaff ? 'readonly' : '' }}
                                step="0.01" class="input-adj-target-smt2 w-full h-8 px-1 bg-transparent text-center border-b border-orange-200 outline-none" placeholder="Tgt"></td>
                            <td class="p-1 border-r bg-orange-50/30 align-center">
                                <input type="number" 
                                name="kpi[{{ $item->id_kpi_item }}][adjustment_real_smt2]" 
                                value="{{ old('kpi.'.$item->id_kpi_item.'.adjustment_real_smt2', $score->adjustment_real_smt2 ?? '') }}"
                                {{ $isStaff ? 'readonly' : '' }} 
                                step="0.01" class="input-adj-real-smt2 w-full h-8 px-1 bg-transparent text-center border-b border-orange-200 outline-none" placeholder="Real"></td>
                            <td class="p-1 border-r bg-orange-50/30 align-center text-center pt-2"><span class="span-adj-skor-smt2 font-bold text-orange-600">0</span>%</td>
                            <td class="p-1 border-r bg-orange-50/30 align-center"><input type="number" step="0.01" name="kpi[{{ $item->id_kpi_item }}][adjustment_smt2]" value="{{ old('kpi.'.$item->id_kpi_item.'.adjustment_smt2', $score->adjustment_smt2 ?? '') }}" class="input-adj-nilai-smt2 w-full h-8 px-1 bg-transparent text-center font-bold text-orange-600 border-b border-orange-200 outline-none" placeholder="Nilai" readonly></td>

                            {{-- FINAL --}}
                            <td class="p-2 md:p-3 text-center border-r bg-gray-100 font-bold text-blue-800"><span class="span-final-score">0</span>%</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-white border-t-4 border-gray-300 sticky bottom-0 z-40 text-xs md:text-sm">
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <td colspan="2" class="sticky left-0 bg-gray-100 p-2 font-bold uppercase border-r">Total Skor Akhir :</td>
                            <td colspan="3" class="border-r bg-gray-50"></td>
                            <td colspan="2" class="border-r"></td><td class="p-2 text-center font-bold text-blue-800 border-r-2"><span id="footer-total-smt1">0</span>%</td>
                            @foreach(['jul','aug','sep','okt','nov','des'] as $bln) <td colspan="3" class="border-r"></td><td class="p-2 text-center font-bold text-blue-800 border-r-2"><span id="footer-total-{{ $bln }}">0</span>%</td> @endforeach
                            <td colspan="3" class="border-r"></td><td class="p-2 text-center font-bold text-gray-700 border-r"><span id="footer-total-sem">0</span>%</td>
                            <td colspan="2" class="border-r bg-orange-50"></td><td class="p-2 border-r bg-orange-50 font-bold text-orange-800 text-center"><span id="footer-adj-smt1">0</span>%</td>
                            <td colspan="3" class="border-r bg-orange-50"></td><td class="p-2 border-r bg-orange-50 font-bold text-orange-800 text-center"><span id="footer-adj-smt2">0</span>%</td>
                            <td class="p-2 text-center font-extrabold text-blue-900 bg-gray-200"><span id="footer-grand-total">0</span>%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif
    </form>
</div>

{{-- MODALS --}}
<div id="modalTambahKPI" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50 flex justify-center items-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl p-6 relative max-h-[90vh] overflow-y-auto">
        <h2 class="text-xl font-bold mb-4 dark:text-white">Tambah KPI</h2>
        <form action="{{ route('kpi.store-item') }}" method="POST">
            @csrf <input type="hidden" name="kpi_assessment_id" value="{{ $kpi->id_kpi_assessment }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="key_performance_indicator" class="block text-sm font-medium text-gray-700">KPI</label>
                    <input type="text" name="key_performance_indicator" class="border p-2 w-full rounded text-sm" placeholder="KPI" required>
                </div>
                <div>
                    <label for="bobot" class="block text-sm font-medium text-gray-700">Bobot (%)</label>
                    <input type="number" step="0.01" name="bobot" class="border p-2 w-full rounded text-sm" placeholder="Bobot" required>
                </div>
                <div>
                    <label for="target" class="block text-sm font-medium text-gray-700">Target</label>
                    <input type="text" name="target" class="border p-2 w-full rounded text-sm" placeholder="Target (misal: 100)" required>
                </div>
                <div>
                    <label for="perspektif" class="block text-sm font-medium text-gray-700">Perspektif</label>
                    <select name="perspektif" class="border p-2 w-full rounded text-sm">
                        <option value="Financial">Financial</option><option value="Customer">Customer</option>
                    </select>
                </div>
                <div>
                    <label for="key_result_area" class="block text-sm font-medium text-gray-700">KRA</label>
                    <input type="text" name="key_result_area" class="border p-2 w-full rounded text-sm" placeholder="KRA" required>
                </div>
                <div>
                    <label for="units" class="block text-sm font-medium text-gray-700">Units</label>
                    <input type="text" name="units" class="border p-2 w-full rounded text-sm" placeholder="Satuan" required>
                </div>
                <div>
                    <label for="polaritas" class="block text-sm font-medium text-gray-700">Polaritas</label>
                    <select name="polaritas" class="border p-2 w-full rounded text-sm">
                        <option value="Maximize">Positif</option><option value="Minimize">Negatif</option>
                    </select>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalTambahKPI').classList.add('hidden')" class="px-4 py-2 border rounded">Batal</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="modalEditKPI" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50 flex justify-center items-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl p-4 md:p-6 relative max-h-[90vh] overflow-y-auto">
        <h2 class="text-lg md:text-xl font-bold mb-4 text-gray-800 dark:text-white">Edit Indikator Kinerja</h2>
        <form id="formEditKPI" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                <select id="edit_perspektif" name="perspektif" class="w-full border rounded p-2 text-sm"><option value="Financial">Financial</option><option value="Customer">Customer</option></select>
                <input type="text" id="edit_kra" name="key_result_area" class="w-full border rounded p-2 text-sm" required>
                <textarea id="edit_kpi" name="key_performance_indicator" class="w-full border rounded p-2 text-sm md:col-span-2" rows="2" required></textarea>
                <input type="text" id="edit_units" name="units" class="w-full border rounded p-2 text-sm" required>
                <select id="edit_polaritas" name="polaritas" class="w-full border rounded p-2 text-sm"><option value="Maximize">Positif</option><option value="Minimize">Negatif</option></select>
                <input type="number" step="0.01" id="edit_bobot" name="bobot" class="w-full border rounded p-2 text-sm" required>
                <input type="text" id="edit_target" name="target" class="w-full border rounded p-2 text-sm" required>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalEditKPI').classList.add('hidden')" class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-100 text-sm">Batal</button>
                <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 text-sm">Update KPI</button>
            </div>
        </form>
    </div>
</div>

<form id="globalDeleteForm" method="POST" class="hidden">@csrf @method('DELETE')</form>

{{-- SCRIPT --}}
<script>
    // Fungsi Filter KPI Berdasarkan Tahun
    function changeKpiYear(selectedYear) {
        console.log('Mengubah tahun ke:', selectedYear);
        
        // Validasi input
        if (!selectedYear || selectedYear === '') {
            alert('Pilih tahun terlebih dahulu!');
            return;
        }

        // Update display di halaman sebelum redirect
        const currentYearDisplay = document.getElementById('currentYearDisplay');
        if (currentYearDisplay) {
            currentYearDisplay.textContent = selectedYear;
        }

        // Buat URL dengan template dan placeholder
        let rawUrl = "{{ route('kpi.show', ['karyawan_id' => $karyawan->id_karyawan, 'tahun' => '8888']) }}";
        console.log('Raw URL template:', rawUrl);
        
        // Ganti placeholder tahun dengan tahun yang dipilih
        let finalUrl = rawUrl.replace('8888', selectedYear);
        console.log('Final URL:', finalUrl);
        
        // Loading indicator
        const selectElement = document.querySelector('select[onchange="changeKpiYear(this.value)"]');
        if (selectElement) {
            selectElement.disabled = true;
        }
        
        // Redirect dengan delay minimal untuk memastikan visual update
        setTimeout(function() {
            window.location.href = finalUrl;
        }, 100);
    }

    // Fungsi Submit Manual
    function submitKpiForm() {
        const form = document.getElementById('kpiForm');
        const btn = document.getElementById('btnSimpan');
        
        // Cek Total Bobot
        const alertBobot = document.getElementById('total-bobot-alert');
        if(alertBobot && alertBobot.innerText.includes('Harus 100%')) {
            alert('Total Bobot belum 100%. Silakan perbaiki sebelum menyimpan.');
            return;
        }
        
        if(!form) { alert('Error: Form tidak ditemukan!'); return; }
        
        if(btn) { 
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...'; 
            btn.disabled = true; 
            btn.classList.add('opacity-50', 'cursor-not-allowed');
        }
        form.submit();
    }

    // Fungsi Modal & Delete
    function openEditModal(data, updateUrl) {
        document.getElementById('formEditKPI').action = updateUrl;
        document.getElementById('edit_perspektif').value = data.perspektif;
        document.getElementById('edit_kra').value = data.key_result_area || data.kra;
        document.getElementById('edit_kpi').value = data.key_performance_indicator || data.indikator;
        document.getElementById('edit_units').value = data.units || data.satuan;
        document.getElementById('edit_polaritas').value = data.polaritas;
        document.getElementById('edit_bobot').value = data.bobot;
        document.getElementById('edit_target').value = data.target || data.target_tahunan;
        document.getElementById('modalEditKPI').classList.remove('hidden');
    }

    function confirmDelete(deleteUrl) {
        if (confirm('Yakin ingin menghapus KPI ini?')) {
            const form = document.getElementById('globalDeleteForm');
            form.action = deleteUrl; 
            form.submit();
        }
    }

    // --- LOGIKA PERHITUNGAN (FULL MANUAL SEMESTER 2) ---
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.row-kpi');
        const monthsSmt2 = ['jul', 'aug', 'sep', 'okt', 'nov', 'des'];

        function parseNumber(val) { 
            if (!val || val === '') return 0; 
            return parseFloat(val.toString().replace(',', '.')) || 0; 
        }

        function formatNumber(num) { 
            return num.toFixed(2).replace(/\.00$/, ''); 
        }

        function calculateSingleScore(target, real, polaritas) {
            if (target === 0) return 0;
            let score = 0;
            const p = polaritas.toLowerCase();
            
            if (p.includes('positif') || p.includes('maximize')) {
                score = (real / target) * 100;
            } else if (p.includes('negatif') || p.includes('minimize')) {
                score = (real === 0) ? 100 : (target / real) * 100;
            } else if (p.includes('yes') || p.includes('no')) {
                score = (real >= target) ? 100 : 0;
            }
            return Math.max(0, score); 
        }

        function calculateAll() {
            let footerSmt1 = 0; 
            let footerSmt2 = 0; 
            let footerGrandTotal = 0;
            let footerAdjSmt1 = 0; 
            let footerAdjSmt2 = 0;
            let footerMonthly = { jul:0, aug:0, sep:0, okt:0, nov:0, des:0 };

            rows.forEach(row => {
                const bobotInput = row.querySelector('.input-bobot');
                const polaritasInput = row.querySelector('.input-polaritas');
                
                if (!bobotInput || !polaritasInput) return;

                const bobot = parseNumber(bobotInput.value);
                const polaritas = polaritasInput.value;
                const targetTahunan = parseNumber(row.querySelector('.input-target-smt1').value);

                // --- 1. HITUNG SEMESTER 1 ---
                const rSmt1 = parseNumber(row.querySelector('.input-real-smt1').value);
                let skorSmt1 = calculateSingleScore(targetTahunan, rSmt1, polaritas);
                let nilaiSmt1 = (skorSmt1 * bobot) / 100;
                
                row.querySelector('.span-skor-smt1').textContent = formatNumber(skorSmt1);
                row.querySelector('.span-nilai-smt1').textContent = formatNumber(nilaiSmt1);

                // --- 2. HITUNG BULANAN (Hanya untuk Footer & Tampilan Bulan) ---
                // Tidak ada lagi logika penjumlahan ke Semester 2 disini
                monthsSmt2.forEach(bln => {
                    const inputTgt = row.querySelector(`.input-target-${bln}`);
                    const inputReal = row.querySelector(`.input-real-${bln}`);
                    
                    if(inputTgt && inputReal) {
                        const t = parseNumber(inputTgt.value);
                        const r = parseNumber(inputReal.value);
                        
                        let skor = (t !== 0) ? calculateSingleScore(t, r, polaritas) : 0;
                        let nilai = (skor * bobot) / 100;
                        
                        const spanSkor = row.querySelector(`.span-skor-${bln}`);
                        const spanNilai = row.querySelector(`.span-nilai-${bln}`);
                        if(spanSkor) spanSkor.textContent = formatNumber(skor);
                        if(spanNilai) spanNilai.textContent = formatNumber(nilai);
                        
                        footerMonthly[bln] += nilai;
                    }
                });

                // --- 3. TOTAL SEMESTER 2 (MURNI MANUAL) ---
                // Kita HAPUS semua logika otomatisasi disini.
                // Sistem hanya membaca apa yang Anda ketik.
                const inputTotalTgt2 = row.querySelector('.input-total-target-smt2');
                const inputTotalReal2 = row.querySelector('.input-total-real-smt2');

                const tSmt2 = parseNumber(inputTotalTgt2.value); // Baca Input Manual Target
                const rSmt2 = parseNumber(inputTotalReal2.value); // Baca Input Manual Realisasi

                // Hitung Skor berdasarkan input manual tersebut
                let skorTotalSmt2 = calculateSingleScore(tSmt2, rSmt2, polaritas);
                let nilaiTotalSmt2 = (skorTotalSmt2 * bobot) / 100;
                
                row.querySelector('.span-total-skor-smt2').textContent = formatNumber(skorTotalSmt2);
                row.querySelector('.span-total-nilai-smt2').textContent = formatNumber(nilaiTotalSmt2);

                // --- 4. ADJUSTMENT SMT 1 ---
                const adjReal1Input = row.querySelector('.input-adj-real-smt1');
                const adjNilaiInput1 = row.querySelector('.input-adj-nilai-smt1');
                let finalSmt1 = nilaiSmt1; 

                if (adjReal1Input && adjReal1Input.value !== "") {
                    let adjSkor1 = calculateSingleScore(targetTahunan, parseNumber(adjReal1Input.value), polaritas);
                    let adjNilai1 = (adjSkor1 * bobot) / 100;
                    row.querySelector('.span-adj-skor-smt1').textContent = formatNumber(adjSkor1);
                    adjNilaiInput1.value = formatNumber(adjNilai1);
                    finalSmt1 = adjNilai1;
                } else {
                    row.querySelector('.span-adj-skor-smt1').textContent = '0';
                    adjNilaiInput1.value = '';
                }

                // --- 5. ADJUSTMENT SMT 2 ---
                const adjTarget2Input = row.querySelector('.input-adj-target-smt2');
                const adjReal2Input = row.querySelector('.input-adj-real-smt2');
                const adjNilaiInput2 = row.querySelector('.input-adj-nilai-smt2');
                let finalSmt2 = nilaiTotalSmt2;

                if (adjTarget2Input && adjReal2Input && adjTarget2Input.value !== "" && adjReal2Input.value !== "") {
                    let adjSkor2 = calculateSingleScore(parseNumber(adjTarget2Input.value), parseNumber(adjReal2Input.value), polaritas);
                    let adjNilai2 = (adjSkor2 * bobot) / 100;
                    row.querySelector('.span-adj-skor-smt2').textContent = formatNumber(adjSkor2);
                    adjNilaiInput2.value = formatNumber(adjNilai2);
                    finalSmt2 = adjNilai2;
                } else {
                    row.querySelector('.span-adj-skor-smt2').textContent = '0';
                    adjNilaiInput2.value = '';
                }

                // --- 6. AKUMULASI FOOTER ---
                footerSmt1 += nilaiSmt1;
                footerSmt2 += nilaiTotalSmt2;
                
                if(adjReal1Input && adjReal1Input.value !== "") footerAdjSmt1 += finalSmt1;
                else footerAdjSmt1 += nilaiSmt1;

                if(adjTarget2Input && adjReal2Input.value !== "") footerAdjSmt2 += finalSmt2;
                else footerAdjSmt2 += nilaiTotalSmt2;
                
                let grandFinal = (finalSmt1 + finalSmt2) / 2; 
                row.querySelector('.span-final-score').textContent = formatNumber(grandFinal);
                footerGrandTotal += grandFinal;
            });

            // Update Footer UI
            const setFooterText = (id, val) => {
                const el = document.getElementById(id);
                if(el) el.textContent = formatNumber(val);
            };

            setFooterText('footer-total-smt1', footerSmt1);
            setFooterText('footer-total-sem', footerSmt2);
            setFooterText('footer-grand-total', footerGrandTotal);
            
            monthsSmt2.forEach(bln => {
                setFooterText(`footer-total-${bln}`, footerMonthly[bln]);
            });

            if(document.getElementById('footer-adj-smt1')) {
                document.getElementById('footer-adj-smt1').textContent = formatNumber(footerAdjSmt1);
            }
            if(document.getElementById('footer-adj-smt2')) {
                document.getElementById('footer-adj-smt2').textContent = formatNumber(footerAdjSmt2);
            }
            
            checkBobot();
        }

        function checkBobot() {
            let totalBobot = 0;
            document.querySelectorAll('.input-bobot').forEach(el => totalBobot += parseNumber(el.value));
            totalBobot = Math.round(totalBobot * 100) / 100;
            
            const alertBox = document.getElementById('total-bobot-alert');
            if (alertBox) {
                if (totalBobot != 100) {
                    alertBox.innerHTML = `<span class="text-red-600 bg-red-100 px-2 py-1 rounded border border-red-200"><i class="fas fa-exclamation-triangle"></i> Total Bobot: ${totalBobot}% (Harus 100%)</span>`;
                } else {
                    alertBox.innerHTML = `<span class="text-green-600 bg-green-100 px-2 py-1 rounded border border-green-200"><i class="fas fa-check-circle"></i> Total Bobot: 100% (OK)</span>`;
                }
            }
        }

        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('input', calculateAll);
            input.addEventListener('change', calculateAll);
        });

        calculateAll();
    });
</script>
</body>
</html>