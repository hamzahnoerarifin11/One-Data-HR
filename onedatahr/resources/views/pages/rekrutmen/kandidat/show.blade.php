@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl p-4">
    <div class="flex items-start justify-between mb-4">
        <div>
            <h3 class="text-2xl font-semibold">Kandidat: {{ $kandidat->nama }}</h3>
            <p class="text-sm text-gray-500">Detail profil dan progres rekrutmen</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('rekrutmen.kandidat.edit', $kandidat->id_kandidat) }}" class="inline-flex items-center gap-2 rounded-lg bg-yellow-100 px-3 py-2 text-yellow-700">Edit</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div class="rounded-xl border bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <h5 class="font-semibold mb-2">Detail</h5>
            <p class="text-sm text-gray-600"><strong>Posisi:</strong> {{ $kandidat->posisi->nama_posisi ?? '-' }}</p>
            <p class="mt-1 text-sm text-gray-600"><strong>Tanggal Melamar:</strong> {{ $kandidat->tanggal_melamar? $kandidat->tanggal_melamar->format('Y-m-d') : '-' }}</p>
            <p class="mt-1 text-sm text-gray-600"><strong>Status Akhir:</strong> {{ $kandidat->status_akhir ?? '-' }}</p>
        </div>

        <div class="rounded-xl border bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <h5 class="font-semibold mb-2">Proses Rekrutmen</h5>
            @if($kandidat->proses)
                <p class="text-sm text-gray-600">CV Lolos: {{ $kandidat->proses->cv_lolos ? 'Ya' : 'Tidak' }}</p>
                <p class="mt-1 text-sm text-gray-600">Tanggal CV: {{ $kandidat->proses->tanggal_cv ?? '-' }}</p>
                <p class="mt-1 text-sm text-gray-600">Psikotes Lolos: {{ $kandidat->proses->psikotes_lolos ? 'Ya' : 'Tidak' }}</p>
                <p class="mt-1 text-sm text-gray-600">Tgl Psikotes: {{ $kandidat->proses->tanggal_psikotes ?? '-' }}</p>
                <div class="mt-3">
                    <a class="inline-flex items-center gap-2 rounded-lg border border-yellow-100 px-3 py-1 text-sm text-yellow-600 hover:bg-yellow-50" href="{{ route('rekrutmen.proses.edit', $kandidat->id_kandidat) }}">Edit Proses</a>
                </div>
            @else
                <p class="text-sm text-gray-600">Belum ada data proses.</p>
                <div class="mt-3">
                    <a class="inline-flex items-center gap-2 rounded-lg bg-primary px-3 py-1 text-sm text-white" href="{{ route('rekrutmen.proses.edit', $kandidat->id_kandidat) }}">Tambah Proses</a>
                </div>
            @endif
        </div>

        <div class="md:col-span-2 rounded-xl border bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <h5 class="font-semibold mb-2">Pemberkasan</h5>
            @if($kandidat->pemberkasan)
                <p class="text-sm text-gray-600">Selesai Recruitment: {{ $kandidat->pemberkasan->selesai_recruitment ?? '-' }}</p>
                <div class="mt-3">
                    <a class="inline-flex items-center gap-2 rounded-lg border border-yellow-100 px-3 py-1 text-sm text-yellow-600 hover:bg-yellow-50" href="{{ route('rekrutmen.pemberkasan.edit', $kandidat->pemberkasan->id_pemberkasan) }}">Edit Pemberkasan</a>
                </div>
            @else
                <p class="text-sm text-gray-600">Belum ada pemberkasan.</p>
                <div class="mt-3"><a href="{{ route('rekrutmen.pemberkasan.create') }}?kandidat_id={{ $kandidat->id_kandidat }}" class="inline-flex items-center gap-2 rounded-lg bg-primary px-3 py-1 text-sm text-white">Tambah Pemberkasan</a></div>
            @endif
        </div>
    </div>
</div>
@endsection
