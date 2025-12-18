@extends('layouts.app')

@section('title','Kalender Rekrutmen')

@section('content')
<div class="px-4 py-6">
    <x-rekrutmen.card title="Kalender Rekrutmen">
        <x-slot name="actions">
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
        </x-slot>

        <div id="calendar" class="grid grid-cols-7 gap-2"></div>
    </x-rekrutmen.card>
</div>

{{-- MODAL INPUT HARIAN --}}
<x-modal id="edit-daily" title="Input Rekrutmen Harian" size="lg">
    <div class="mb-4 text-sm font-medium text-gray-700">
        Tanggal: <span id="edit-daily-date"></span>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-600">Total Pelamar</label>
            <input id="total_pelamar" type="number" class="mt-1 block w-full rounded-md border border-gray-200 px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-600">Lolos Screening CV</label>
            <input id="lolos_cv" type="number" class="mt-1 block w-full rounded-md border border-gray-200 px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-600">Lolos Psikotes</label>
            <input id="lolos_psikotes" type="number" class="mt-1 block w-full rounded-md border border-gray-200 px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-600">Lolos Tes Kompetensi</label>
            <input id="lolos_kompetensi" type="number" class="mt-1 block w-full rounded-md border border-gray-200 px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-600">Lolos Interview HR</label>
            <input id="lolos_hr" type="number" class="mt-1 block w-full rounded-md border border-gray-200 px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-600">Lolos Interview User</label>
            <input id="lolos_user" type="number" class="mt-1 block w-full rounded-md border border-gray-200 px-3 py-2 text-sm">
        </div>
    </div>

    <hr class="my-4" />

    <div>
        <h4 class="text-sm font-medium mb-2">Candidates on this date</h4>
        <ul id="daily-candidates-list" class="mb-3 divide-y rounded border bg-white max-h-48 overflow-auto"></ul>

        <div class="flex gap-2 mb-2">
            <select id="daily-candidate-select" class="flex-1 px-3 py-2 border rounded">
                <option value="">-- Pilih Kandidat yang ada --</option>
            </select>
            <button id="add-existing-candidate" type="button" class="btn btn-sm btn-primary">Add</button>
        </div>

        <div class="flex gap-2">
            <input id="daily-candidate-name" type="text" placeholder="Nama kandidat baru (quick add)" class="flex-1 px-3 py-2 border rounded" />
            <button id="add-new-candidate" type="button" class="btn btn-sm btn-primary">Quick Add</button>
        </div>
    </div>

    <div class="mt-4 flex items-center justify-between">
        <div class="text-xs text-gray-500">Hanya admin dapat mengubah data rekrutmen harian.</div>
        <div class="flex gap-2">
            <button class="btn btn-secondary" data-modal-id="edit-daily">Batal</button>
            @if(auth()->check() && auth()->user()->role === 'admin')
                <button id="save-daily" class="btn btn-primary">Simpan</button>
            @else
                <button class="btn btn-primary" disabled title="Hanya admin yang bisa mengubah data">Simpan (admin only)</button>
            @endif
        </div>
    </div>
</x-modal>
@endsection

<script>
(function(){
    const cal = document.getElementById('calendar');
    const monthSel = document.getElementById('month-select');
    const yearSel = document.getElementById('year-select');
    const posSel = document.getElementById('posisi-filter');

    const now = new Date();

    for(let m=0;m<12;m++){
        const o=document.createElement('option');
        o.value=m+1;
        o.text=new Date(2000,m,1).toLocaleString('id',{month:'short'});
        monthSel.appendChild(o);
    }

    for(let y=now.getFullYear()-2;y<=now.getFullYear()+1;y++){
        const o=document.createElement('option');
        o.value=y;o.text=y;
        yearSel.appendChild(o);
    }

    monthSel.value=now.getMonth()+1;
    yearSel.value=now.getFullYear();

    let editingDate=null;

    async function load(){
        if(!posSel.value){
            cal.innerHTML='<div class="col-span-7 p-4 text-gray-500">Pilih posisi terlebih dahulu</div>';
            return;
        }

        const q=new URLSearchParams({
            month:monthSel.value,
            year:yearSel.value,
            posisi_id:posSel.value
        });

        const res=await fetch(`{{ route('rekrutmen.daily.index') }}?${q}`);
        const data=await res.json();

        const map={};
        data.forEach(d=>map[d.date]=d);

        const d0=new Date(yearSel.value,monthSel.value-1,1);
        const days=new Date(yearSel.value,monthSel.value,0).getDate();

        cal.innerHTML='';

        for(let i=0;i<d0.getDay();i++){
            cal.appendChild(document.createElement('div'));
        }

        for(let d=1;d<=days;d++){
            const date=`${yearSel.value}-${String(monthSel.value).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            const e=map[date]||{total_pelamar:0};

            const el=document.createElement('div');
            el.className='border rounded p-2 bg-white cursor-pointer hover:bg-blue-50';
            el.innerHTML=`
                <div class="text-xs">${d}</div>
                <div class="text-lg font-bold">${e.total_pelamar||0}</div>
                <div class="text-xs text-gray-500">Pelamar</div>
            `;

            el.onclick=()=>openEditor(date,e);
            cal.appendChild(el);
        }
    }

    async function openEditor(date,e){
        editingDate=date;
        document.getElementById('edit-daily-date').innerText=date;

        ['total_pelamar','lolos_cv','lolos_psikotes','lolos_kompetensi','lolos_hr','lolos_user']
            .forEach(f=>{
                document.getElementById(f).value=e[f]||0;
            });

        // load per-date candidate entries and candidate select
        if(posSel.value){
            await Promise.all([fetchEntries(date, posSel.value), fetchCandidateSelect(posSel.value)]);
        }

        window.dispatchEvent(new CustomEvent('open-modal',{detail:{id:'edit-daily'}}));
    }

    async function fetchEntries(date, posisiId){
        const url = new URL('{{ route('rekrutmen.daily.entries.index') }}', window.location.origin);
        url.searchParams.set('posisi_id', posisiId);
        url.searchParams.set('date', date);
        try{
            const r = await fetch(url.toString(), {credentials:'same-origin'});
            if(!r.ok) return;
            const data = await r.json();
            const ul = document.getElementById('daily-candidates-list'); ul.innerHTML = '';
            data.forEach(item=>{
                const li = document.createElement('li'); li.className = 'p-2 flex items-center justify-between';
                const name = item.kandidat ? item.kandidat.nama : (item.candidate_name || 'Unnamed');
                li.innerHTML = `<div>${name}</div><div><button data-id="${item.id}" class="btn btn-sm btn-danger delete-entry">Delete</button></div>`;
                ul.appendChild(li);
            });
        }catch(e){ console.error('fetch entries error', e); }
    }

    async function fetchCandidateSelect(posisiId){
        const sel = document.getElementById('daily-candidate-select'); sel.innerHTML = '<option value="">-- Pilih Kandidat yang ada --</option>';
        const url = new URL('{{ route('rekrutmen.kandidat.list') }}', window.location.origin);
        url.searchParams.set('posisi_id', posisiId);
        try{
            const r = await fetch(url.toString(), {credentials:'same-origin'});
            if(!r.ok) return;
            const data = await r.json();
            data.forEach(c=>{ const o = document.createElement('option'); o.value=c.id_kandidat; o.text=c.nama; sel.appendChild(o); });
        }catch(e){ console.error('fetch kandidat list error', e); }
    }

    // add existing candidate to date
    document.getElementById('add-existing-candidate')?.addEventListener('click', async ()=>{
        const kandidatId = document.getElementById('daily-candidate-select').value;
        if(!kandidatId || !posSel.value || !editingDate) return alert('Pilih posisi dan kandidat');
        try{
            const r = await fetch('{{ route('rekrutmen.daily.entries.store') }}',{
                method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
                body: JSON.stringify({ posisi_id: posSel.value, date: editingDate, kandidat_id: kandidatId })
            });
            if(r.ok){ await fetchEntries(editingDate, posSel.value); load(); }
            else { console.error('add existing failed', r.status); }
        }catch(e){ console.error('network error', e); }
    });

    // quick add candidate and assign
    document.getElementById('add-new-candidate')?.addEventListener('click', async ()=>{
        const name = document.getElementById('daily-candidate-name').value.trim();
        if(!name || !posSel.value || !editingDate) return alert('Isi nama kandidat, posisi, dan tanggal');
        try{
            const r = await fetch('{{ route('rekrutmen.daily.entries.store') }}',{
                method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
                body: JSON.stringify({ posisi_id: posSel.value, date: editingDate, candidate_name: name })
            });
            if(r.ok){ document.getElementById('daily-candidate-name').value=''; await fetchEntries(editingDate, posSel.value); load(); }
            else { console.error('quick add failed', r.status); }
        }catch(e){ console.error('network error', e); }
    });

    // delegated delete handler for entries
    document.addEventListener('click', async function(e){
        const btn = e.target.closest('.delete-entry'); if(!btn) return;
        if(!confirm('Hapus entry ini?')) return;
        const id = btn.dataset.id;
        try{
            const r = await fetch('/rekrutmen/daily/entries/'+id, { method: 'DELETE', credentials:'same-origin', headers:{ 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }});
            if(r.ok){ await fetchEntries(editingDate, posSel.value); load(); }
            else console.error('delete failed', r.status);
        }catch(e){ console.error('network error', e); }
    });

    const saveBtn = document.getElementById('save-daily');
    if (saveBtn) {
        saveBtn.onclick = () => {
            const payload = {
                posisi_id: posSel.value,
                date: editingDate,
            };

            ['total_pelamar','lolos_cv','lolos_psikotes','lolos_kompetensi','lolos_hr','lolos_user']
                .forEach(f=>payload[f]=parseInt(document.getElementById(f).value||0));

            fetch('{{ route('rekrutmen.daily.store') }}',{
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
                },
                body:JSON.stringify(payload)
            }).then((r)=>{
                if (!r.ok) {
                    // simple error handling: show a message or keep modal open
                    console.error('save failed', r.status);
                } else {
                    window.dispatchEvent(new CustomEvent('close-modal',{detail:{id:'edit-daily'}}));
                    load();
                }
            }).catch((e)=>{ console.error('network error', e); });
        };
    }

    [posSel,monthSel,yearSel].forEach(el=>el.onchange=load);
    load();
})();
</script>
