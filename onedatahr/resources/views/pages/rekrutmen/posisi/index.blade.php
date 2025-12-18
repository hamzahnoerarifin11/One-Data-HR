@extends('layouts.app')

@section('title','Manajemen Posisi')

@section('content')
<div class="px-4 py-6">
    <x-rekrutmen.card title="Daftar Posisi">
        <x-slot name="actions">
            <button class="btn btn-primary" data-modal-id="add-posisi" data-modal-title="Tambah Posisi">Tambah Posisi</button>
        </x-slot>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-left">#</th>
                        <th class="p-3 text-left">Nama Posisi</th>
                        <th class="p-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($posisis as $p)
                    <tr class="border-t">
                        <td class="p-3">{{ $loop->iteration }}</td>
                        <td class="p-3">{{ $p->nama_posisi }}</td>
                        <td class="p-3">
                            <button class="btn btn-sm" data-modal-id="edit-posisi-{{ $p->id_posisi }}" data-modal-title="Edit Posisi">Edit</button>
                            <button type="button" class="btn btn-danger btn-sm ml-2 delete-posisi-btn" data-id="{{ $p->id_posisi }}" data-name="{{ $p->nama_posisi }}">Hapus</button>

                            {{-- edit modal per posisi (AJAX) --}}
                            <x-modal id="edit-posisi-{{ $p->id_posisi }}" title="Edit Posisi">
                                <div class="mb-3">
                                    <label class="block text-sm">Nama Posisi</label>
                                    <input type="text" id="edit-posisi-name-{{ $p->id_posisi }}" name="nama_posisi" value="{{ old('nama_posisi', $p->nama_posisi) }}" class="mt-1 block w-full rounded border px-3 py-2" />
                                    <p id="edit-posisi-error-{{ $p->id_posisi }}" class="text-sm text-red-600 mt-2 hidden"></p>
                                </div>
                                <div class="flex justify-end">
                                    <button type="button" class="btn btn-secondary mr-2" data-modal-id="edit-posisi-{{ $p->id_posisi }}">Batal</button>
                                    <button type="button" class="btn btn-primary save-posisi-btn" data-id="{{ $p->id_posisi }}">Simpan</button>
                                </div>
                            </x-modal>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-rekrutmen.card>
</div>

{{-- add modal (shared with dashboard) --}}
<x-modal id="add-posisi" title="Tambah Posisi">
    <div class="mb-3">
        <label class="block text-sm">Nama Posisi</label>
        <input type="text" id="new-posisi-name" class="mt-1 block w-full rounded border px-3 py-2" />
        <p id="new-posisi-error" class="text-sm text-red-600 mt-2 hidden"></p>
    </div>
    <div class="flex justify-end">
        <button type="button" class="btn btn-secondary mr-2" data-modal-id="add-posisi">Batal</button>
        <button type="button" class="btn btn-primary" onclick="window.dispatchEvent(new CustomEvent('modal-confirmed',{detail:{id:'add-posisi'}}))">Tambah</button>
    </div>
</x-modal>

<!-- confirm delete modal -->
<x-modal id="confirm-delete" title="Hapus Posisi" size="sm">
    <div class="mb-4 text-sm" id="confirm-delete-message">Yakin?</div>
    <div class="flex justify-end">
        <button type="button" class="btn btn-secondary mr-2" data-modal-id="confirm-delete">Batal</button>
        <button id="confirm-delete-action" type="button" class="btn btn-danger">Hapus</button>
    </div>
</x-modal>

<script>
(function(){
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Handle add-posisi using existing global modal-confirmed event
    window.addEventListener('modal-confirmed', function(e){
        if(!e?.detail || e.detail.id !== 'add-posisi') return;
        const nameEl = document.getElementById('new-posisi-name');
        const errEl = document.getElementById('new-posisi-error');
        const name = nameEl.value.trim();
        errEl.classList.add('hidden'); errEl.innerText = '';
        if(!name){ errEl.innerText = 'Nama posisi tidak boleh kosong.'; errEl.classList.remove('hidden'); return; }

        fetch("{{ route('rekrutmen.posisi.store') }}", {
            method: 'POST', credentials: 'same-origin', headers: {
                'Content-Type':'application/json','X-CSRF-TOKEN': token,'Accept':'application/json'
            }, body: JSON.stringify({ nama_posisi: name })
        }).then(async r=>{
            const j = await r.json().catch(()=>null);
            if(r.ok && j?.success){ window.dispatchEvent(new CustomEvent('close-modal',{detail:{id:'add-posisi'}})); location.reload(); return; }
            if(r.status === 422 && j && j.errors){ errEl.innerText = (j.errors.nama_posisi||[]).join(' ') || 'Validasi gagal.'; errEl.classList.remove('hidden'); return; }
            errEl.innerText = (j && j.message) ? j.message : 'Terjadi kesalahan server.'; errEl.classList.remove('hidden');
        }).catch(err=>{ console.error(err); errEl.innerText = 'Terjadi kesalahan jaringan.'; errEl.classList.remove('hidden'); });
    });

    // Save edit via AJAX
    document.querySelectorAll('.save-posisi-btn').forEach(btn=>{
        btn.addEventListener('click', async function(){
            const id = this.dataset.id;
            const nameEl = document.getElementById('edit-posisi-name-'+id);
            const errEl = document.getElementById('edit-posisi-error-'+id);
            const name = nameEl.value.trim();
            errEl.classList.add('hidden'); errEl.innerText = '';
            if(!name){ errEl.innerText = 'Nama posisi tidak boleh kosong.'; errEl.classList.remove('hidden'); return; }
            try{
                const resp = await fetch("/rekrutmen/posisi/"+id, { method: 'PUT', credentials:'same-origin', headers:{'Content-Type':'application/json','X-CSRF-TOKEN': token,'Accept':'application/json'}, body: JSON.stringify({ nama_posisi: name }) });
                const json = await resp.json().catch(()=>null);
                if(resp.ok && json?.success){
                    // update row text
                    const td = document.querySelector('button.save-posisi-btn[data-id="'+id+'"]').closest('td').previousElementSibling; // not ideal but quick
                    // better: find the td by searching for row with edit modal id
                    const row = document.querySelector('[data-modal-id="edit-posisi-'+id+'"]')?.closest('tr');
                    if(row){ row.querySelectorAll('td')[1].innerText = json.posisi.nama_posisi; }
                    window.dispatchEvent(new CustomEvent('close-modal',{detail:{id:'edit-posisi-'+id}}));
                    return;
                }
                if(resp.status === 422 && json && json.errors){ errEl.innerText = (json.errors.nama_posisi||[]).join(' ') || 'Validasi gagal.'; errEl.classList.remove('hidden'); return; }
                alert((json && json.message) ? json.message : 'Gagal menyimpan');
            }catch(e){ console.error(e); alert('Gagal menyimpan'); }
        });
    });

    // Delete flow with confirm modal
    let _deleteTargetId = null;
    document.querySelectorAll('.delete-posisi-btn').forEach(btn=>{
        btn.addEventListener('click', function(){
            _deleteTargetId = this.dataset.id;
            const name = this.dataset.name || '';
            document.getElementById('confirm-delete-message').innerText = 'Yakin ingin menghapus posisi '+name+'?';
            window.dispatchEvent(new CustomEvent('open-modal',{detail:{id:'confirm-delete'}}));
        });
    });

    document.getElementById('confirm-delete-action').addEventListener('click', async function(){
        if(!_deleteTargetId) return;
        try{
            const r = await fetch('/rekrutmen/posisi/'+_deleteTargetId, { method: 'DELETE', credentials:'same-origin', headers:{'X-CSRF-TOKEN': token, 'Accept':'application/json'} });
            if(r.ok){ window.dispatchEvent(new CustomEvent('close-modal',{detail:{id:'confirm-delete'}})); document.querySelector('[data-id="'+_deleteTargetId+'"]').closest('tr').remove(); _deleteTargetId = null; return; }
            const j = await r.json().catch(()=>null); alert((j && j.message) ? j.message : 'Gagal menghapus');
        }catch(e){ console.error(e); alert('Gagal menghapus'); }
    });
})();
</script>

@endsection
