@extends('layouts.app')

@section('title','Psikotes Lolos')

@section('content')
<div class="px-4 py-6">
    <x-rekrutmen.card title="Psikotes Lolos per Posisi">
        <x-slot name="actions">
            <div class="flex items-center gap-3">
                <select id="posisi-filter" class="px-3 py-2 border rounded">
                    <option value="">-- Semua Posisi --</option>
                    @foreach($posisis as $p)
                        <option value="{{ $p->id_posisi }}">{{ $p->nama_posisi }}</option>
                    @endforeach
                </select>
                <button id="load-btn" class="btn btn-primary">Load</button>
            </div>
        </x-slot>

        <div id="chart" class="bg-white rounded p-4 shadow"></div>
    </x-rekrutmen.card>
</div>

<script>
document.getElementById('load-btn').addEventListener('click', async ()=>{
    const pos = document.getElementById('posisi-filter').value;
    const params = new URLSearchParams(); if(pos) params.set('posisi_id', pos);
    const r = await fetch(`{{ route('rekrutmen.metrics.psikotes') }}?${params.toString()}`, {credentials:'same-origin'});
    if(!r.ok) return alert('Gagal memuat');
    const data = await r.json();
    if(window.ApexCharts){ const labels = data.map(x=> x.nama_posisi); const values = data.map(x=> x.total); const options = { chart:{type:'bar',height:320}, series:[{name:'Psikotes Lolos',data:values}], xaxis:{categories:labels} }; new ApexCharts(document.querySelector('#chart'), options).render(); }
    else document.getElementById('chart').innerHTML = '<pre>'+JSON.stringify(data,null,2)+'</pre>';
});
</script>

@endsection
