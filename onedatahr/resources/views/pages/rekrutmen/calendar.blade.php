@extends('layouts.app')

@section('title','Kalender Rekrutmen')

@section('content')
<div class="px-4 py-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold">Kalender Rekrutmen</h2>
        <div class="flex items-center gap-3">
            <select id="posisi-filter" class="px-3 py-2 border rounded">
                <option value="">Semua Posisi</option>
                @foreach(App\Models\Posisi::orderBy('nama_posisi')->get() as $p)
                    <option value="{{ $p->id_posisi }}">{{ $p->nama_posisi }}</option>
                @endforeach
            </select>
            <select id="month-select" class="px-3 py-2 border rounded"></select>
            <select id="year-select" class="px-3 py-2 border rounded"></select>
            <button id="refresh-calendar" class="btn btn-primary">Refresh</button>
        </div>
    </div>

    <div id="calendar" class="grid grid-cols-7 gap-2"></div>

<x-modal id="edit-daily" title="Edit Rekrutmen Harian" size="sm">
    <div class="mb-3">
        <label class="block text-sm">Tanggal</label>
        <div id="edit-daily-date" class="mt-1 text-sm font-medium text-gray-800"></div>
    </div>
    <div class="mb-3">
        <label class="block text-sm">Jumlah Pelamar</label>
        <input id="edit-daily-count" type="number" class="mt-1 block w-full rounded border px-3 py-2" />
    </div>
    <div class="mb-3">
        <label class="block text-sm">Catatan</label>
        <textarea id="edit-daily-notes" class="mt-1 block w-full rounded border px-3 py-2"></textarea>
    </div>
    <div class="flex justify-end">
        <button type="button" class="btn btn-secondary mr-2" data-modal-id="edit-daily">Batal</button>
        <button id="save-daily" type="button" class="btn btn-primary">Simpan</button>
    </div>
</x-modal>

<script>
(function(){
    const monthSel = document.getElementById('month-select');
    const yearSel = document.getElementById('year-select');
    const posSel = document.getElementById('posisi-filter');
    const cal = document.getElementById('calendar');

    const now = new Date();
    for(let m=0;m<12;m++){ const opt = document.createElement('option'); opt.value = m+1; opt.text = new Date(2000,m,1).toLocaleString('default',{month:'short'}); monthSel.appendChild(opt); }
    for(let y=now.getFullYear()-2;y<=now.getFullYear()+1;y++){ const opt = document.createElement('option'); opt.value = y; opt.text = y; yearSel.appendChild(opt); }
    monthSel.value = now.getMonth()+1; yearSel.value = now.getFullYear();

    async function load(){
        const month = monthSel.value; const year = yearSel.value; const pos = posSel.value;
        const params = new URLSearchParams(); params.set('month', month); params.set('year', year); if(pos) params.set('posisi_id', pos);
        const r = await fetch(`{{ route('rekrutmen.daily.index') }}?${params.toString()}`, {credentials: 'same-origin', headers:{'Accept':'application/json'}});
        if(!r.ok){ cal.innerHTML = '<div class="col-span-7 p-4 bg-red-50 text-red-700">Gagal memuat data kalender.</div>'; return; }
        const data = await r.json();
        // build map date->entry
        const map = {};
        data.forEach(d=>{ map[d.date] = {count: d.count, id: d.id, notes: d.notes}; });

        const d0 = new Date(year, month-1, 1);
        const firstWeekday = d0.getDay();
        const days = new Date(year, month, 0).getDate();
        cal.innerHTML = '';

        // fill leading blanks
        for(let i=0;i<firstWeekday;i++){ const el = document.createElement('div'); el.className='p-3 border rounded h-24 bg-gray-50'; cal.appendChild(el); }

        for(let day=1; day<=days; day++){
            const dateStr = `${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
            const entry = map[dateStr] || {count:0,id:null,notes:null};
            const el = document.createElement('div'); el.className = 'p-3 border rounded h-24 bg-white flex flex-col justify-between';
            el.innerHTML = `<div class="text-sm text-gray-700">${day}</div><div class="text-2xl font-semibold">${entry.count ?? 0}</div>`;
            el.addEventListener('click', ()=>{ openEditor(dateStr, entry); });
            cal.appendChild(el);
        }
    }

    let _editing = { date: null, id: null };
    function openEditor(date, entry){
        const pos = posSel.value;
        if(!pos) return alert('Pilih posisi terlebih dahulu untuk menambah/edit hitungan.');
        _editing.date = date;
        _editing.id = entry?.id || null;
        document.getElementById('edit-daily-date').innerText = date;
        document.getElementById('edit-daily-count').value = entry?.count ?? 0;
        document.getElementById('edit-daily-notes').value = entry?.notes ?? '';
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'edit-daily' } }));
    }

    document.getElementById('save-daily').addEventListener('click', ()=>{
        const pos = posSel.value;
        const date = _editing.date;
        const count = parseInt(document.getElementById('edit-daily-count').value || 0);
        const notes = document.getElementById('edit-daily-notes').value || null;
        if(isNaN(count)) return alert('Masukkan angka yang valid');
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch('{{ route('rekrutmen.daily.store') }}', {
            method: 'POST', credentials: 'same-origin', headers: {
                'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token
            }, body: JSON.stringify({ posisi_id: pos, date: date, count: count, notes: notes })
        }).then(async r=>{
            if(r.ok){ window.dispatchEvent(new CustomEvent('close-modal',{detail:{id:'edit-daily'}})); load(); return; }
            const j = await r.json().catch(()=>null); alert((j&&j.message) ? j.message : 'Gagal menyimpan');
        }).catch(err=>{ console.error(err); alert('Gagal menyimpan'); });
    });

    document.getElementById('refresh-calendar').addEventListener('click', load);
    posSel.addEventListener('change', load);
    monthSel.addEventListener('change', load);
    yearSel.addEventListener('change', load);
    load();
})();
</script>

@endsection
