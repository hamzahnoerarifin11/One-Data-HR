@extends('layouts.app')

@section('title','Pemberkasan Monitor')

@section('content')
<div class="px-4 py-6">
    <x-rekrutmen.card title="Pemberkasan - Monitoring">
        <x-slot name="actions">
            <div class="flex items-center gap-3">
                <select id="posisi-filter" class="px-3 py-2 border rounded">
                    <option value="">-- Semua Posisi --</option>
                    @foreach(App\Models\Posisi::orderBy('nama_posisi')->get() as $p)
                        <option value="{{ $p->id_posisi }}">{{ $p->nama_posisi }}</option>
                    @endforeach
                </select>
                <button id="load-btn" class="btn btn-primary">Load</button>
            </div>
        </x-slot>

        <div id="table" class="bg-white rounded shadow p-4">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-2 text-left">Posisi</th>
                        <th class="p-2 text-left">Total</th>
                        <th class="p-2 text-left">Done Recruitment</th>
                        <th class="p-2 text-left">% Done</th>
                    </tr>
                </thead>
                <tbody id="monitor-body"></tbody>
            </table>
        </div>
    </x-rekrutmen.card>
</div>

<script>
document.getElementById('load-btn').addEventListener('click', async ()=>{
    const pos = document.getElementById('posisi-filter').value;
    const params = new URLSearchParams(); if(pos) params.set('posisi_id', pos);
    const r = await fetch(`{{ route('rekrutmen.metrics.pemberkasan') }}?${params.toString()}`, {credentials:'same-origin'});
    if(!r.ok) return alert('Gagal memuat');
    const data = await r.json();
    const tbody = document.getElementById('monitor-body'); tbody.innerHTML = '';
    data.forEach(d=>{ const tr = document.createElement('tr'); tr.innerHTML = `<td class="p-2">${d.nama_posisi}</td><td class="p-2">${d.total}</td><td class="p-2">${d.done_recruitment}</td><td class="p-2">${d.percent_done_recruitment}%</td>`; tbody.appendChild(tr); });
});

// load initial
document.getElementById('load-btn').click();
</script>

@endsection
