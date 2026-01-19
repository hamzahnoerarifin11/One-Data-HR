@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <!-- HEADER -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Recruitment Dashboard
        </h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Selamat datang {{ auth()->user()->name ?? 'Pengguna' }},
            berikut ringkasan aktivitas rekrutmen dan metrik penting
        </p>
    </div>
        <!-- <div class="flex items-center gap-2">
            <a href="{{ route('rekrutmen.posisi.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">Manage Posisi</a>
            <a href="{{ route('rekrutmen.kandidat.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">Manage Kandidat</a>
            <a href="{{ route('rekrutmen.calendar') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">Kalender Rekrutmen</a>
            <a href="{{ route('rekrutmen.interview_hr.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">Interview HR</a>
            <a href="{{ route('rekrutmen.wig.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">Database WIG</a>
            <a href="{{ route('rekrutmen.metrics.pemberkasan.page') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">Pemberkasan Monitor</a>
        </div> -->
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- FILTERS -->
        <div class="lg:col-span-1">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                    Filters
                </h3>
                <form id="filter-form">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">Posisi</label>
                        <div class="flex items-center gap-2">
                            <select name="posisi_id" id="posisi_id" class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">-- Semua Posisi --</option>
                                @foreach($posisis as $pos)
                                    <option value="{{ $pos->id_posisi }}">{{ $pos->nama_posisi }}</option>
                                @endforeach
                            </select>

                            <!-- small add button to create new posisi inline -->
                            <button type="button" data-modal-id="add-posisi" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 bg-white text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600" title="Tambah Posisi">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">From</label>
                            <input type="month" name="from" class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">To</label>
                            <input type="month" name="to" class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col gap-3">
                        <div class="flex flex-wrap gap-2">
                            <button type="button" id="apply-filters" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow hover:bg-blue-700 transition dark:bg-blue-500 dark:hover:bg-blue-600">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                </svg>
                                Apply Filters
                            </button>
                            <button type="button" id="reset-filters" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Reset
                            </button>
                        </div>

                        <div class="relative">
                            <button id="export-toggle" type="button" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-green-200 bg-green-50 px-4 py-2.5 text-sm font-medium text-green-700 shadow-sm hover:bg-green-100 dark:border-green-700 dark:bg-green-900/20 dark:text-green-400 dark:hover:bg-green-900/30">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Export CSV
                            </button>
                            <div id="export-menu" class="hidden absolute right-0 mt-2 w-56 rounded-lg bg-white border border-gray-200 shadow-lg z-10 py-1 dark:bg-gray-800 dark:border-gray-700">
                                <a id="export-candidates" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700" href="#">Kandidat</a>
                                <a id="export-cv" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700" href="#">CV Lolos</a>
                                <a id="export-psikotes" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700" href="#">Psikotes Lolos</a>
                                <a id="export-kompetensi" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700" href="#">Kompetensi Lolos</a>
                                <a id="export-interview-hr" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700" href="#">Interview HR Lolos</a>
                                <a id="export-interview-user" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700" href="#">Interview User Lolos</a>
                                <a id="export-pemberkasan" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700" href="#">Pemberkasan Selesai</a>
                                <a id="export-progress" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700" href="#">Progress</a>
                            </div>
                        </div>

                        <p class="text-xs text-gray-500 dark:text-gray-400">Exports reflect current filter selection.</p>
                    </div>
                </form>
            </div>
        </div>

        <!-- Main content: KPIs and Charts -->
        <div class="lg:col-span-2">
            <!-- KPI CARDS -->
            <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">

                <!-- TOTAL KANDIDAT -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-xl dark:bg-blue-900/20">
                                <svg class="fill-blue-600 dark:fill-blue-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M8.80443 5.60156C7.59109 5.60156 6.60749 6.58517 6.60749 7.79851C6.60749 9.01185 7.59109 9.99545 8.80443 9.99545C10.0178 9.99545 11.0014 9.01185 11.0014 7.79851C11.0014 6.58517 10.0178 5.60156 8.80443 5.60156ZM5.10749 7.79851C5.10749 5.75674 6.76267 4.10156 8.80443 4.10156C10.8462 4.10156 12.5014 5.75674 12.5014 7.79851C12.5014 9.84027 10.8462 11.4955 8.80443 11.4955C6.76267 11.4955 5.10749 9.84027 5.10749 7.79851Z"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.86252 15.3208C4.08769 16.0881 3.70377 17.0608 3.51705 17.8611C3.48384 18.0034 3.5211 18.1175 3.60712 18.2112C3.70161 18.3141 3.86659 18.3987 4.07591 18.3987H13.4249C13.6343 18.3987 13.7992 18.3141 13.8937 18.2112C13.9797 18.1175 14.017 18.0034 13.9838 17.8611C13.7971 17.0608 13.4132 16.0881 12.6383 15.3208C11.8821 14.572 10.6899 13.955 8.75042 13.955C6.81096 13.955 5.61877 14.572 4.86252 15.3208Z"/>
                                </svg>
                            </div>
                            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Total Kandidat</p>
                            <h3 class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">
                                <span id="total-kandidat">-</span>
                            </h3>
                        </div>
                    </div>
                </div>

                <!-- TOTAL CV LOLOS -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-xl dark:bg-green-900/20">
                                <svg class="fill-green-600 dark:fill-green-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Total CV Lolos</p>
                            <h3 class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">
                                <span id="total-cv">-</span>
                            </h3>
                        </div>
                    </div>
                </div>

                <!-- TOTAL PSIKOTES LOLOS -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-xl dark:bg-purple-900/20">
                                <svg class="fill-purple-600 dark:fill-purple-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                    <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.1 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                                </svg>
                            </div>
                            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Total Psikotes Lolos</p>
                            <h3 class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">
                                <span id="total-psikotes">-</span>
                            </h3>
                        </div>
                    </div>
                </div>

                <!-- TES KOMPETENSI LOLOS -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center justify-center w-12 h-12 bg-orange-100 rounded-xl dark:bg-orange-900/20">
                                <svg class="fill-orange-600 dark:fill-orange-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Tes Kompetensi Lolos</p>
                            <h3 class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">
                                <span id="total-kompetensi">-</span>
                            </h3>
                        </div>
                    </div>
                </div>

                <!-- INTERVIEW HR LOLOS -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center justify-center w-12 h-12 bg-cyan-100 rounded-xl dark:bg-cyan-900/20">
                                <svg class="fill-cyan-600 dark:fill-cyan-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                    <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
                                </svg>
                            </div>
                            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Interview HR Lolos</p>
                            <h3 class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">
                                <span id="total-interview-hr">-</span>
                            </h3>
                        </div>
                    </div>
                </div>

                <!-- INTERVIEW USER LOLOS -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center justify-center w-12 h-12 bg-pink-100 rounded-xl dark:bg-pink-900/20">
                                <svg class="fill-pink-600 dark:fill-pink-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                    <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63C19.68 7.55 18.92 7 18.06 7h-.12c-.86 0-1.63.55-1.9 1.37l-2.54 7.63H14v6h6zM5.5 7L4 12h2l.5-3h.5c.83 0 1.5-.67 1.5-1.5S8.83 6 8 6H6.5c-.27 0-.5.11-.5.28V7z"/>
                                </svg>
                            </div>
                            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Interview User Lolos</p>
                            <h3 class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">
                                <span id="total-interview-user">-</span>
                            </h3>
                        </div>
                    </div>
                </div>

                <!-- PEMBERKASAN SELESAI -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center justify-center w-12 h-12 bg-emerald-100 rounded-xl dark:bg-emerald-900/20">
                                <svg class="fill-emerald-600 dark:fill-emerald-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                    <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                                    <path d="M9 13l2 2 4-4"/>
                                </svg>
                            </div>
                            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Pemberkasan Selesai</p>
                            <h3 class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">
                                <span id="total-pemberkasan">-</span>
                            </h3>
                        </div>
                    </div>
                </div>

            </div>

            <!-- CHART GRID -->
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

                <!-- GRAFIK KANDIDAT MASUK -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                        Grafik Kandidat Masuk (per posisi & per bulan)
                    </h3>
                    <div class="flex h-[300px] items-center justify-center">
                        <div id="chartCandidates" class="w-full"></div>
                    </div>
                </div>

                <!-- GRAFIK LOLOS PER TAHAP -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                        Grafik Lolos per Tahap
                    </h3>
                    <div class="flex h-[300px] items-center justify-center">
                        <div id="chartStages" class="w-full"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <x-modal id="add-posisi" title="Tambah Posisi" size="sm" closeLabel="Batal" confirmLabel="Tambah">
        <div>
            <label class="block text-sm font-medium text-gray-700">Nama Posisi</label>
            <input id="new-posisi-name" type="text" class="mt-2 block w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm" placeholder="Contoh: Backend Engineer" />
            <div id="new-posisi-error" class="mt-2 text-xs text-red-500 hidden"></div>
        </div>
    </x-modal>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Small helper for export dropdown
    document.addEventListener('click', function (e) {
        const exportToggle = document.getElementById('export-toggle');
        const exportMenu = document.getElementById('export-menu');
        if (!exportToggle || !exportMenu) return;
        if (exportToggle.contains(e.target)) {
            exportMenu.classList.toggle('hidden');
        } else if (!exportMenu.contains(e.target)) {
            exportMenu.classList.add('hidden');
        }
    });

    function fetchCandidates(params = {}){
        const url = new URL("{{ route('rekrutmen.metrics.candidates') }}", window.location.origin);
        Object.keys(params).forEach(k => params[k] ? url.searchParams.append(k, params[k]) : null);
        return fetch(url)
            .then(r => r.json())
            .then(data => { window.dataTotalCandidates = (data || []).reduce((s,i)=> s + (i.total||0),0); return data; });
    }

    function fetchStages(params = {}){
        const url = new URL("{{ route('rekrutmen.metrics.progress') }}", window.location.origin);
        Object.keys(params).forEach(k => params[k] ? url.searchParams.append(k, params[k]) : null);
        return fetch(url).then(r => r.json());
    }

    function fetchSummary(params = {}){
        const urlCv = new URL("{{ route('rekrutmen.metrics.cv') }}", window.location.origin);
        const urlPs = new URL("{{ route('rekrutmen.metrics.psikotes') }}", window.location.origin);
        const urlKompetensi = new URL("{{ route('rekrutmen.metrics.kompetensi') }}", window.location.origin);
        const urlInterviewHr = new URL("{{ route('rekrutmen.metrics.hr') }}", window.location.origin);
        const urlInterviewUser = new URL("{{ route('rekrutmen.metrics.user') }}", window.location.origin);
        const urlPemberkasan = new URL("{{ route('rekrutmen.metrics.pemberkasan') }}", window.location.origin);
        
        Object.keys(params).forEach(k => params[k] ? urlCv.searchParams.append(k, params[k]) : null);
        Object.keys(params).forEach(k => params[k] ? urlPs.searchParams.append(k, params[k]) : null);
        Object.keys(params).forEach(k => params[k] ? urlKompetensi.searchParams.append(k, params[k]) : null);
        Object.keys(params).forEach(k => params[k] ? urlInterviewHr.searchParams.append(k, params[k]) : null);
        Object.keys(params).forEach(k => params[k] ? urlInterviewUser.searchParams.append(k, params[k]) : null);
        Object.keys(params).forEach(k => params[k] ? urlPemberkasan.searchParams.append(k, params[k]) : null);
        
        Promise.all([
            fetch(urlCv).then(r => r.json()),
            fetch(urlPs).then(r => r.json()),
            fetch(urlKompetensi).then(r => r.json()),
            fetch(urlInterviewHr).then(r => r.json()),
            fetch(urlInterviewUser).then(r => r.json()),
            fetch(urlPemberkasan).then(r => r.json())
        ])
        .then(([cv, ps, komp, hr, user, pemberkasan]) => {
            const totalCv = (cv || []).reduce((s,i)=> s + (i.total||0),0);
            const totalPs = (ps || []).reduce((s,i)=> s + (i.total||0),0);
            const totalKomp = (komp || []).reduce((s,i)=> s + (i.total||0),0);
            const totalHr = (hr || []).reduce((s,i)=> s + (i.total||0),0);
            const totalUser = (user || []).reduce((s,i)=> s + (i.total||0),0);
            const totalPemberkasan = (pemberkasan || []).reduce((s,i)=> s + (i.done_recruitment||0),0);
            
            document.getElementById('total-cv').innerText = totalCv;
            document.getElementById('total-psikotes').innerText = totalPs;
            document.getElementById('total-kompetensi').innerText = totalKomp;
            document.getElementById('total-interview-hr').innerText = totalHr;
            document.getElementById('total-interview-user').innerText = totalUser;
            document.getElementById('total-pemberkasan').innerText = totalPemberkasan;
            
            const totalCandidates = window.dataTotalCandidates || '-';
            if(document.getElementById('total-kandidat')) document.getElementById('total-kandidat').innerText = totalCandidates;
        }).catch(()=>{});
    }

    // Update export links according to current filters
    function updateExportLink(){
    const form = document.getElementById('filter-form');
    const params = new URLSearchParams();

    if(form.posisi_id.value) params.append('posisi_id', form.posisi_id.value);
    if(form.from.value) params.append('from', form.from.value + '-01');
    if(form.to.value) params.append('to', form.to.value + '-31');

    const urlCandidates = new URL("{{ route('rekrutmen.metrics.candidates.export') }}", window.location.origin);
    const urlCv = new URL("{{ route('rekrutmen.metrics.cv.export') }}", window.location.origin);
    const urlPs = new URL("{{ route('rekrutmen.metrics.psikotes.export') }}", window.location.origin);
    const urlKompetensi = new URL("{{ route('rekrutmen.metrics.kompetensi.export') }}", window.location.origin);
    const urlProgress = new URL("{{ route('rekrutmen.metrics.progress.export') }}", window.location.origin);

    urlCandidates.search = params.toString();
    urlCv.search = params.toString();
    urlPs.search = params.toString();
    urlKompetensi.search = params.toString();
    urlProgress.search = params.toString();

    document.getElementById('export-candidates').href = urlCandidates;
    document.getElementById('export-cv').href = urlCv;
    document.getElementById('export-psikotes').href = urlPs;
    document.getElementById('export-kompetensi').href = urlKompetensi;

    // Arahkan interview & pemberkasan ke progress (karena memang digabung di backend)
    document.getElementById('export-interview-hr').href = urlProgress;
    document.getElementById('export-interview-user').href = urlProgress;
    document.getElementById('export-pemberkasan').href = urlProgress;
    document.getElementById('export-progress').href = urlProgress;
}

    // Apply filters
    document.getElementById('apply-filters').addEventListener('click', function(){
        const form = document.getElementById('filter-form');
        const data = {
            posisi_id: form.posisi_id.value,
            from: form.from.value ? form.from.value + '-01' : null,
            to: form.to.value ? form.to.value + '-31' : null,
        };
        fetchCandidates(data).then(data => {
            renderCandidatesChart(data);
            fetchSummary(data); // call after fetchCandidates
        });
        fetchStages(data).then(renderStages);
        updateExportLink();
    });

    // Reset filters
    document.getElementById('reset-filters').addEventListener('click', function(){
        const form = document.getElementById('filter-form');
        form.posisi_id.value = '';
        form.from.value = '';
        form.to.value = '';
        fetchCandidates().then(data => {
            renderCandidatesChart(data);
            fetchSummary(); // call after fetchCandidates
        });
        fetchStages().then(renderStages);
        updateExportLink();
    });

    // Initialize export link on load
    updateExportLink();

    function renderCandidatesChart(data){
        const categories = data.map(x => x.nama_posisi + ' ' + x.year + '-' + x.month);
        const counts = data.map(x => x.total);
        const options = {
            chart: { type: 'bar', height: 320 },
            series: [{ name: 'Kandidat', data: counts }],
            xaxis: { categories },
            plotOptions: { bar: { columnWidth: '60%' } }
        };
        if(window.candChart) { try { window.candChart.destroy(); } catch(e){} }
        window.candChart = new ApexCharts(document.querySelector('#chartCandidates'), options);
        window.candChart.render();
    }

    function renderStages(data){
        const labels = data.map(x=> x.nama_posisi);
        const cv = data.map(x=> x.percent_cv);
        const psik = data.map(x=> x.percent_psikotes);
        const komp = data.map(x=> x.percent_kompetensi);
        const hr = data.map(x=> x.percent_hr);
        const user = data.map(x=> x.percent_user);
        const options = {
            chart: { type: 'bar', height: 320 },
            series: [
                {name: 'CV %', data: cv},
                {name: 'Psikotes %', data: psik},
                {name: 'Kompetensi %', data: komp},
                {name: 'HR %', data: hr},
                {name: 'User %', data: user},
            ],
            xaxis: { categories: labels },
            plotOptions: { bar: { horizontal: false, columnWidth: '55%' } },
            yaxis: { max: 100 }
        };
        if(window.stageChart) { try { window.stageChart.destroy(); } catch(e){} }
        window.stageChart = new ApexCharts(document.querySelector('#chartStages'), options);
        window.stageChart.render();
    }

    // initial load
    fetchCandidates().then(data => {
        renderCandidatesChart(data);
        fetchSummary(); // call fetchSummary after fetchCandidates completes
    });
    fetchStages().then(renderStages);

    // handle add-posisi modal confirmation to create a new posisi and update the select
    window.addEventListener('modal-confirmed', function(e){
        if(!e?.detail || e.detail.id !== 'add-posisi') return;
        const nameEl = document.getElementById('new-posisi-name');
        const errEl = document.getElementById('new-posisi-error');
        if(!nameEl) return;
        const name = nameEl.value.trim();
        errEl.classList.add('hidden'); errEl.innerText = '';
        if(!name){ errEl.innerText = 'Nama posisi tidak boleh kosong.'; errEl.classList.remove('hidden'); return; }

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch("{{ route('rekrutmen.posisi.store') }}", {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ nama_posisi: name })
        }).then(async (r) => {
            if (r.ok) return r.json();
            // try to parse JSON for validation or error messages
            let json = null;
            try { json = await r.json(); } catch(e) { /* ignore */ }
            if (r.status === 422 && json && json.errors) {
                const msg = (json.errors.nama_posisi || []).join(' ') || 'Validasi gagal.';
                errEl.innerText = msg; errEl.classList.remove('hidden');
                return null;
            }
            if (r.status === 419) {
                errEl.innerText = 'Session expired. Silakan refresh halaman dan coba lagi.'; errEl.classList.remove('hidden');
                return null;
            }
            errEl.innerText = (json && json.message) ? json.message : 'Terjadi kesalahan server.'; errEl.classList.remove('hidden');
            return null;
        }).then(json => {
            if (!json) return;
            if(json?.success && json.posisi){
                // append to select
                const sel = document.getElementById('posisi_id');
                const opt = document.createElement('option');
                opt.value = json.posisi.id_posisi;
                opt.text = json.posisi.nama_posisi;
                sel.appendChild(opt);
                sel.value = json.posisi.id_posisi;

                // clear input and close modal
                nameEl.value = '';
                window.dispatchEvent(new CustomEvent('close-modal', { detail: { id: 'add-posisi' } }));

                // update exports and re-run filters
                updateExportLink();
                document.getElementById('apply-filters').click();
            }
        }).catch((err) => { console.error('posisi create error', err); errEl.innerText = 'Terjadi kesalahan jaringan.'; errEl.classList.remove('hidden'); });
    });
});
</script>
@endsection
