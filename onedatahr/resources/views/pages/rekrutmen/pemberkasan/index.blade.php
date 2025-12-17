@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl p-4">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold">Pemberkasan</h3>
        <a href="{{ route('rekrutmen.pemberkasan.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary px-3 py-2 text-sm text-white">Tambah</a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y">
            <thead class="bg-gray-50">
                <tr class="text-left text-sm text-gray-600">
                    <th class="px-4 py-3">Kandidat</th>
                    <th class="px-4 py-3">Posisi</th>
                    <th class="px-4 py-3">Selesai Recruitment</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($data as $d)
                <tr class="bg-white">
                    <td class="px-4 py-3">{{ $d->kandidat->nama ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $d->kandidat->posisi->nama_posisi ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $d->selesai_recruitment ? $d->selesai_recruitment->format('Y-m-d') : '-' }}</td>
                    <td class="px-4 py-3"><a href="{{ route('rekrutmen.pemberkasan.edit', $d->id_pemberkasan) }}" class="inline-flex items-center gap-2 rounded-lg border border-yellow-100 px-3 py-1 text-sm text-yellow-600 hover:bg-yellow-50">Edit</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $data->links() }}</div>
</div>
@endsection
