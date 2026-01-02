@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Detail Training
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Informasi jadwal, evaluasi, dan hasil training kandidat
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if(auth()->user() && auth()->user()->role === 'admin')
                <a href="{{ route('training.edit', $training->id_training) }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-yellow-500 px-5 py-2.5 text-center text-white font-medium hover:bg-yellow-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
            @endif
            <a href="{{ route('training.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2
                      text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300
                      dark:hover:bg-white/[0.05] transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-white/[0.03]">

        <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
            <span class="w-1 h-6 bg-brand-600 rounded-full"></span>
            Informasi Kandidat & Posisi
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <label class="text-sm text-gray-500 italic">Nama Kandidat</label>
                <div class="mt-1 font-semibold text-gray-900 dark:text-white text-lg">
                    {{ $training->kandidat->nama ?? '-' }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500 italic">Posisi Training</label>
                <div class="mt-1 font-semibold text-gray-900 dark:text-white text-lg">
                    {{ $training->posisi->nama_posisi ?? '-' }}
                </div>
            </div>
        </div>

        <hr class="my-8 border-gray-100 dark:border-gray-800" />

        <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
            <span class="w-1 h-6 bg-blue-600 rounded-full"></span>
            Timeline & Hasil Evaluasi
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">

            <div>
                <label class="text-sm text-gray-500">Tanggal Mulai</label>
                <div class="mt-1 font-medium text-gray-900 dark:text-white">
                    {{ $training->tanggal_mulai ? \Carbon\Carbon::parse($training->tanggal_mulai)->translatedFormat('d F Y') : '-' }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">Tanggal Selesai</label>
                <div class="mt-1 font-medium text-gray-900 dark:text-white">
                    {{ $training->tanggal_selesai ? \Carbon\Carbon::parse($training->tanggal_selesai)->translatedFormat('d F Y') : '-' }}
                </div>
            </div>
             <div>
                <label class="text-sm text-gray-500">Hasil Evaluasi</label>
                <div class="mt-2">
                    @php
                        $statusColor = match($training->hasil_evaluasi) {
                            'LULUS TRAINING' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                            'TIDAK LULUS TRAINING' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                            'MENGUNDURKAN DIRI' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                            default => 'bg-gray-100 text-gray-700'
                        };
                    @endphp
                    <span class="px-4 py-2 rounded-full text-sm font-bold {{ $statusColor }}">
                        {{ $training->hasil_evaluasi }}
                    </span>
                </div>
            </div>

            <div class="p-4 rounded-lg bg-gray-50 dark:bg-white/[0.03] border border-gray-200 dark:border-gray-800">
                <label class="text-sm font-bold text-blue-600 dark:text-blue-400">Jadwal TTD Kontrak</label>
                <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $training->jadwal_ttd_kontrak ? \Carbon\Carbon::parse($training->jadwal_ttd_kontrak)->translatedFormat('d F Y') : 'Belum Terjadwal' }}
                </div>
            </div>

            <div class="col-span-1 md:col-span-3">
                <label class="text-sm text-gray-500">Keterangan Tambahan</label>
                <div class="mt-2 p-4 rounded-lg bg-gray-50 dark:bg-white/[0.03] border border-gray-200 dark:border-gray-800 min-h-[100px] text-gray-700 dark:text-gray-300">
                    {!! nl2br(e($training->keterangan_tambahan ?? 'Tidak ada keterangan tambahan.')) !!}
                </div>
            </div>

        </div>

        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 flex justify-between items-center text-xs text-gray-500 italic">
            <p>Data dibuat: {{ $training->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</p>
            <p>Terakhir diperbarui: {{ $training->updated_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</p>
        </div>

    </div>
</div>
@endsection
