@extends('layouts.app')

@section('title','Manajemen Posisi')

@section('content')
<div class="px-4 py-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold">Daftar Posisi</h2>
        <button class="btn btn-primary" data-modal-id="add-posisi" data-modal-title="Tambah Posisi">Tambah Posisi</button>
    </div>

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
                        <form action="{{ route('rekrutmen.posisi.destroy', $p->id_posisi) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm ml-2" data-modal-id="confirm-delete" data-modal-title="Hapus Posisi" data-modal-message="Yakin ingin menghapus posisi {{ $p->nama_posisi }}?">Hapus</button>
                        </form>

                        {{-- edit modal per posisi --}}
                        <x-modal id="edit-posisi-{{ $p->id_posisi }}" title="Edit Posisi">
                            <form id="edit-posisi-form-{{ $p->id_posisi }}" method="POST" action="{{ route('rekrutmen.posisi.update', $p->id_posisi) }}">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="block text-sm">Nama Posisi</label>
                                    <input type="text" name="nama_posisi" value="{{ old('nama_posisi', $p->nama_posisi) }}" class="mt-1 block w-full rounded border px-3 py-2" />
                                </div>
                                <div class="flex justify-end">
                                    <button type="button" class="btn btn-secondary mr-2" data-modal-id="edit-posisi-{{ $p->id_posisi }}">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </x-modal>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
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

@endsection
