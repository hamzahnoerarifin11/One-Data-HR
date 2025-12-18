@extends('layouts.app')

@section('title','CV Lolos')

@section('content')
<div class="px-4 py-6">
    <x-rekrutmen.card title="CV Lolos per Posisi">
        <x-slot name="actions">
            <div class="flex items-center gap-3">
                <select id="posisi-filter" class="px-3 py-2 border rounded">
                    <option value="">-- Semua Posisi --</option>
                    @foreach($posisis as $p)
                        <option value="{{ $p->id_posisi }}">{{ $p->nama_posisi }}</option>
                    @endforeach
                </select>
                <input type="month" id="month" class="px-3 py-2 border rounded" />
                <button id="load-btn" class="btn btn-primary">Load</button>
            </div>
        </x-slot>

        <div id="chart" class="bg-white rounded p-4 shadow"></div>
    </x-rekrutmen.card>
</div>

<script>
document.getElementById('load-btn').addEventListener('click', async ()=>{
    const pos = document.getElementById('posisi-filter').value;
    const m = document.getElementById('month').value;
    const params = new URLSearchParams();
    if(pos) params.set('posisi_id', pos);
    if(m){ const [y,mo] = m.split('-'); params.set('from', y+'-'+mo+'-01'); params.set('to', y+'-'+mo+'-31'); }
    const r = await fetch(`{{ route('rekrutmen.metrics.cv') }}?${params.toString()}` , {credentials:'same-origin'});
    if(!r.ok) return alert('Gagal memuat');
    const data = await r.json();
    if(!data.length) return document.getElementById('chart').innerHTML = '<div class="p-4">Tidak ada data</div>';
    const labels = data.map(x=> x.year + '-'+ String(x.month).padStart(2,'0'));
    const values = data.map(x=> x.total);
    // simple chart using ApexCharts if present
    if(window.ApexCharts){ const options = { chart:{type:'bar',height:320}, series:[{name:'CV Lolos',data:values}], xaxis:{categories:labels} }; new ApexCharts(document.querySelector('#chart'), options).render(); }
    else { document.getElementById('chart').innerHTML = '<pre>'+JSON.stringify(data,null,2)+'</pre>'; }
});
</script>

@endsection
