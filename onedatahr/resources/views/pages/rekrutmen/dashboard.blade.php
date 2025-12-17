@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Recruitment Dashboard</h1>
        <div>
            <a href="{{ route('rekrutmen.kandidat.index') }}" class="btn btn-sm btn-primary">Manage Kandidat</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Filters</h5>
                    <form id="filter-form">
                        <div class="mb-2">
                            <label class="form-label">Posisi</label>
                            <select name="posisi_id" id="posisi_id" class="form-select">
                                <option value="">-- Semua Posisi --</option>
                                @foreach($posisis as $pos)
                                    <option value="{{ $pos->id_posisi }}">{{ $pos->nama_posisi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-2">
                                <label class="form-label">From</label>
                                <input type="month" class="form-control" name="from" />
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label">To</label>
                                <input type="month" class="form-control" name="to" />
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" id="apply-filters" class="btn btn-sm btn-primary">Apply</button>
                            <button type="button" id="reset-filters" class="btn btn-sm btn-outline-secondary">Reset</button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Export CSV">Export CSV</button>
                                <ul class="dropdown-menu">
                                    <li><a id="export-candidates" class="dropdown-item" href="#" aria-label="Export Kandidat CSV">Kandidat</a></li>
                                    <li><a id="export-cv" class="dropdown-item" href="#" aria-label="Export CV CSV">CV Lolos</a></li>
                                    <li><a id="export-psikotes" class="dropdown-item" href="#" aria-label="Export Psikotes CSV">Psikotes Lolos</a></li>
                                    <li><a id="export-progress" class="dropdown-item" href="#" aria-label="Export Progress CSV">Progress</a></li>
                                </ul>
                            </div>
                            <small class="form-text text-muted">Exports reflect current filter selection.</small>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Total Kandidat</h6>
                            <div class="fs-4 fw-bold" id="total-kandidat">-</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Total CV Lolos</h6>
                            <div class="fs-4 fw-bold" id="total-cv">-</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Total Psikotes Lolos</h6>
                            <div class="fs-4 fw-bold" id="total-psikotes">-</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h5 class="card-title">Grafik Kandidat Masuk (per posisi & per bulan)</h5>
                    <div id="chartCandidates"></div>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h5 class="card-title">Grafik Lolos per Tahap</h5>
                    <div id="chartStages"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    function fetchCandidates(params = {}){
        const url = new URL("{{ route('rekrutmen.metrics.candidates') }}", window.location.origin);
        Object.keys(params).forEach(k => params[k] ? url.searchParams.append(k, params[k]) : null);
        return fetch(url).then(r => r.json()).then(data => { window.dataTotalCandidates = (data || []).reduce((s,i)=> s + (i.total||0),0); return data; });
    }

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
    });

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

    // update export links according to current filters
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

    // keep link updated on apply
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

    function fetchSummary(params = {}){
        // fetch totals for summary cards using multiple endpoints
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
            // also total candidates
            const totalCandidates = dataTotalCandidates || '-';
            if(document.getElementById('total-kandidat')) document.getElementById('total-kandidat').innerText = totalCandidates;
        }).catch(()=>{});
    }

    function fetchStages(params = {}){
        const url = new URL("{{ route('rekrutmen.metrics.progress') }}", window.location.origin);
        Object.keys(params).forEach(k => params[k] ? url.searchParams.append(k, params[k]) : null);
        return fetch(url).then(r => r.json());
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
</script>
@endsection
