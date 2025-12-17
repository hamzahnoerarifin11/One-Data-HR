@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Kandidat: {{ $kandidat->nama }}</h3>
    <div class="card p-3 mb-3">
        <h5>Detail</h5>
        <p><strong>Posisi:</strong> {{ $kandidat->posisi->nama_posisi ?? '-' }}</p>
        <p><strong>Tanggal Melamar:</strong> {{ $kandidat->tanggal_melamar? $kandidat->tanggal_melamar->format('Y-m-d') : '-' }}</p>
        <p><strong>Status Akhir:</strong> {{ $kandidat->status_akhir ?? '-' }}</p>
    </div>

    <div class="card p-3 mb-3">
        <h5>Proses Rekrutmen</h5>
        @if($kandidat->proses)
            <p>CV Lolos: {{ $kandidat->proses->cv_lolos ? 'Ya' : 'Tidak' }}</p>
            <p>Tanggal CV: {{ $kandidat->proses->tanggal_cv ?? '-' }}</p>
            <p>Psikotes Lolos: {{ $kandidat->proses->psikotes_lolos ? 'Ya' : 'Tidak' }}</p>
            <p>Tgl Psikotes: {{ $kandidat->proses->tanggal_psikotes ?? '-' }}</p>
            <a class="btn btn-sm btn-warning" href="{{ route('rekrutmen.proses.edit', $kandidat->id_kandidat) }}">Edit Proses</a>
        @else
            <p>Belum ada data proses.</p>
            <a class="btn btn-sm btn-primary" href="{{ route('rekrutmen.proses.edit', $kandidat->id_kandidat) }}">Tambah Proses</a>
        @endif
    </div>

    <div class="card p-3">
        <h5>Pemberkasan</h5>
        @if($kandidat->pemberkasan)
            <p>Selesai Recruitment: {{ $kandidat->pemberkasan->selesai_recruitment ?? '-' }}</p>
            <a class="btn btn-sm btn-warning" href="{{ route('rekrutmen.pemberkasan.edit', $kandidat->pemberkasan->id_pemberkasan) }}">Edit Pemberkasan</a>
        @else
            <p>Belum ada pemberkasan.</p>
            <a href="{{ route('rekrutmen.pemberkasan.create') }}?kandidat_id={{ $kandidat->id_kandidat }}" class="btn btn-sm btn-primary">Tambah Pemberkasan</a>
        @endif
    </div>
</div>
@endsection
