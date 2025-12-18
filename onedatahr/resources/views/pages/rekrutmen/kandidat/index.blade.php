@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl p-4">
    <x-rekrutmen.card title="Kandidat">
        <x-slot name="actions">
            <a href="{{ route('rekrutmen.kandidat.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary px-3 py-2 text-sm font-medium text-white">Tambah Kandidat</a>
        </x-slot>

        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white p-2">
            <table class="min-w-full divide-y">
                <thead class="bg-gray-50">
                    <tr class="text-left text-sm text-gray-600">
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Posisi</th>
                        <th class="px-4 py-3">Tgl Melamar</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($kandidats as $k)
                    <tr class="bg-white">
                        <td class="px-4 py-3">{{ $k->nama }}</td>
                        <td class="px-4 py-3">{{ $k->posisi->nama_posisi ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $k->tanggal_melamar ? $k->tanggal_melamar->format('Y-m-d') : '-' }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('rekrutmen.kandidat.show', $k->id_kandidat) }}" class="btn btn-sm">View</a>
                            <a href="{{ route('rekrutmen.kandidat.edit', $k->id_kandidat) }}" class="btn btn-sm ml-2">Edit</a>
                            <form id="delete-form-{{ $k->id_kandidat }}" action="{{ route('rekrutmen.kandidat.destroy', $k->id_kandidat) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" aria-label="Hapus Kandidat" data-modal-id="delete-confirm" data-modal-target="delete-form-{{ $k->id_kandidat }}" data-modal-title="Hapus Kandidat" data-modal-message="{{ e('Yakin ingin menghapus kandidat: ' . $k->nama . '?') }}" class="btn btn-sm btn-danger ml-2">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $kandidats->links() }}</div>

    </x-rekrutmen.card>

    <!-- Global delete confirmation modal (reusable) -->
    <x-modal id="delete-confirm" size="sm" title="Konfirmasi Hapus" closeLabel="Batal" confirmLabel="Hapus">
        <p class="text-sm text-gray-600">Gunakan tombol <strong>Hapus</strong> untuk mengonfirmasi penghapusan kandidat yang dipilih.</p>
    </x-modal>
</div>
@endsection
