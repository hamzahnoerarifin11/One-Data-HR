@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <!-- Header -->
    <div class="mb-6 flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Recruitment Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ringkasan aktivitas rekrutmen dan metrik penting</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('rekrutmen.kandidat.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary px-3 py-2 text-sm font-medium text-white shadow hover:bg-primary-dark transition">Manage Kandidat</a>
            <a href="{{ route('rekrutmen.posisi.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">Manage Posisi</a>
            <a href="{{ route('rekrutmen.calendar') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">Kalender Rekrutmen</a>
            <a href="{{ route('rekrutmen.metrics.pemberkasan.page') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">Pemberkasan Monitor</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Filters -->
        <div class="lg:col-span-1">
            <div class="rounded-xl border bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-3 text-lg font-semibold text-gray-800 dark:text-white">Filters</h3>
                <form id="filter-form">
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Posisi</label>
                        <div class="flex items-center gap-2">
                            <select name="posisi_id" id="posisi_id" class="mt-1 block w-full rounded-md border border-gray-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-primary focus:ring-primary">
                                <option value="">-- Semua Posisi --</option>
                                @foreach($posisis as $pos)
                                    <option value="{{ $pos->id_posisi }}">{{ $pos->nama_posisi }}</option>
                                @endforeach
                            </select>

                            <!-- small add button to create new posisi inline -->
                            <button type="button" data-modal-id="add-posisi" class="mt-1 inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-200 bg-white text-sm text-gray-700 hover:bg-gray-50" title="Tambah Posisi">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">From</label>
                            <input type="month" name="from" class="mt-1 block w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-primary focus:ring-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">To</label>
                            <input type="month" name="to" class="mt-1 block w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-primary focus:ring-primary">
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <button type="button" id="apply-filters" class="inline-flex items-center gap-2 rounded-lg bg-primary px-3 py-2 text-sm font-medium text-white shadow hover:bg-primary-dark transition">Apply</button>
                        <button type="button" id="reset-filters" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">Reset</button>

                        <div class="relative">
                            <button id="export-toggle" type="button" class="inline-flex items-center gap-2 rounded-lg border border-green-100 bg-white px-3 py-2 text-sm font-medium text-green-600 shadow-sm hover:bg-green-50">Export CSV</button>
                            <div id="export-menu" class="hidden absolute right-0 mt-2 w-44 rounded-md bg-white border shadow z-10 py-1 dark:bg-gray-800">
                                <a id="export-candidates" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50" href="#">Kandidat</a>
                                <a id="export-cv" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50" href="#">CV Lolos</a>
                                <a id="export-psikotes" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50" href="#">Psikotes Lolos</a>
                                <a id="export-progress" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50" href="#">Progress</a>
                            </div>
                        </div>

                        <p class="w-full text-xs text-gray-400">Exports reflect current filter selection.</p>
                    </div>
                </form>
            </div>
        </div>

        <!-- Main content: KPIs and Charts -->
        <div class="lg:col-span-2">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-xl border bg-white p-6 shadow-sm text-center dark:border-gray-800 dark:bg-white/[0.03]">
                    <p class="text-sm text-gray-500">Total Kandidat</p>
                    <div id="total-kandidat" class="mt-2 text-2xl font-bold text-gray-900">-</div>
                </div>

                <div class="rounded-xl border bg-white p-6 shadow-sm text-center dark:border-gray-800 dark:bg-white/[0.03]">
                    <p class="text-sm text-gray-500">Total CV Lolos</p>
                    <div id="total-cv" class="mt-2 text-2xl font-bold text-gray-900">-</div>
                </div>

                <div class="rounded-xl border bg-white p-6 shadow-sm text-center dark:border-gray-800 dark:bg-white/[0.03]">
                    <p class="text-sm text-gray-500">Total Psikotes Lolos</p>
                    <div id="total-psikotes" class="mt-2 text-2xl font-bold text-gray-900">-</div>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4">
                <x-chart-card title="Grafik Kandidat Masuk (per posisi & per bulan)">
                    <div id="chartCandidates" class="h-72"></div>
                </x-chart-card>

                <x-chart-card title="Grafik Lolos per Tahap">
                    <div id="chartStages" class="h-72"></div>
                </x-chart-card>
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
        Object.keys(params).forEach(k => params[k] ? urlCv.searchParams.append(k, params[k]) : null);
        Object.keys(params).forEach(k => params[k] ? urlPs.searchParams.append(k, params[k]) : null);
        Promise.all([fetch(urlCv).then(r => r.json()), fetch(urlPs).then(r => r.json())])
        .then(([cv, ps]) => {
            const totalCv = (cv || []).reduce((s,i)=> s + (i.total||0),0);
            const totalPs = (ps || []).reduce((s,i)=> s + (i.total||0),0);
            document.getElementById('total-cv').innerText = totalCv;
            document.getElementById('total-psikotes').innerText = totalPs;
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
        const urlProgress = new URL("{{ route('rekrutmen.metrics.progress.export') }}", window.location.origin);
        urlCandidates.search = params.toString();
        urlCv.search = params.toString();
        urlPs.search = params.toString();
        urlProgress.search = params.toString();
        document.getElementById('export-candidates').href = urlCandidates.toString();
        document.getElementById('export-cv').href = urlCv.toString();
        document.getElementById('export-psikotes').href = urlPs.toString();
        document.getElementById('export-progress').href = urlProgress.toString();
    }

    // Apply filters
    document.getElementById('apply-filters').addEventListener('click', function(){
        const form = document.getElementById('filter-form');
        const data = {
            posisi_id: form.posisi_id.value,
            from: form.from.value ? form.from.value + '-01' : null,
            to: form.to.value ? form.to.value + '-31' : null,
        };
        fetchCandidates(data).then(renderCandidatesChart);
        fetchStages(data).then(renderStages);
        fetchSummary(data);
        updateExportLink();
    });

    // Reset filters
    document.getElementById('reset-filters').addEventListener('click', function(){
        const form = document.getElementById('filter-form');
        form.posisi_id.value = '';
        form.from.value = '';
        form.to.value = '';
        fetchCandidates().then(renderCandidatesChart);
        fetchStages().then(renderStages);
        fetchSummary();
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
    fetchCandidates().then(renderCandidatesChart);
    fetchStages().then(renderStages);
    fetchSummary();

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
</script>
@endsection
