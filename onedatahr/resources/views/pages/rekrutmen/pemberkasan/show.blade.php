@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Detail Pemberkasan
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Informasi administrasi dan timeline pemberkasan kandidat
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if(auth()->user() && auth()->user()->role === 'admin')
                <a href="{{ route('rekrutmen.pemberkasan.edit', $pemberkasan->id_pemberkasan) }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-yellow-500 px-5 py-2.5 text-center text-white font-medium hover:bg-yellow-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
            @endif
            <a href="{{ route('rekrutmen.pemberkasan.index') }}"
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
            Informasi Kandidat
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <label class="text-sm text-gray-500">Nama Kandidat</label>
                <div class="mt-1 font-semibold text-gray-900 dark:text-white">
                    {{ $pemberkasan->kandidat->nama ?? '-' }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">Posisi</label>
                <div class="mt-1 font-semibold text-gray-900 dark:text-white">
                    {{ $pemberkasan->kandidat->posisi->nama_posisi ?? '-' }}
                </div>
            </div>

            <div class="col-span-1 md:col-span-2">
                <label class="text-sm text-gray-500">Follow Up</label>
                <div class="mt-1 font-semibold text-blue-600 dark:text-blue-400">
                    {{ $pemberkasan->follow_up ? \Carbon\Carbon::parse($pemberkasan->follow_up)->translatedFormat('d M Y') : '-' }}
                </div>
            </div>
        </div>

        <hr class="my-8 border-gray-100 dark:border-gray-800" />

        <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
            <span class="w-1 h-6 bg-red-600 rounded-full"></span>
            Timeline & Status Berkas
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">

            <div>
                <label class="text-sm text-gray-500">Kandidat Kirim Berkas</label>
                <div class="mt-1 font-medium text-gray-900 dark:text-white">
                    {{ $pemberkasan->kandidat_kirim_berkas ? \Carbon\Carbon::parse($pemberkasan->kandidat_kirim_berkas)->translatedFormat('d F Y') : '-' }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">Selesai Recruitment</label>
                <div class="mt-1 font-medium text-gray-900 dark:text-white">
                    {{ $pemberkasan->selesai_recruitment ? \Carbon\Carbon::parse($pemberkasan->selesai_recruitment)->translatedFormat('d F Y') : '-' }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">Selesai SKGK Finance</label>
                <div class="mt-1 font-medium text-gray-900 dark:text-white">
                    {{ $pemberkasan->selesai_skgk_finance ? \Carbon\Carbon::parse($pemberkasan->selesai_skgk_finance)->translatedFormat('d F Y') : '-' }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">Selesai TTD Manager HRD</label>
                <div class="mt-1 font-medium text-gray-900 dark:text-white">
                    {{ $pemberkasan->selesai_ttd_manager_hrd ? \Carbon\Carbon::parse($pemberkasan->selesai_ttd_manager_hrd)->translatedFormat('d F Y') : '-' }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">Selesai TTD User</label>
                <div class="mt-1 font-medium text-gray-900 dark:text-white">
                    {{ $pemberkasan->selesai_ttd_user ? \Carbon\Carbon::parse($pemberkasan->selesai_ttd_user)->translatedFormat('d F Y') : '-' }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">Selesai TTD Direktur</label>
                <div class="mt-1 font-medium text-gray-900 dark:text-white">
                    {{ $pemberkasan->selesai_ttd_direktur ? \Carbon\Carbon::parse($pemberkasan->selesai_ttd_direktur)->translatedFormat('d F Y') : '-' }}
                </div>
            </div>

            <div class="p-4 rounded-lg bg-gray-50 dark:bg-white/[0.03] border border-gray-200 dark:border-gray-800">
                <label class="text-sm font-bold text-brand-600 dark:text-brand-400">Jadwal TTD Kontrak</label>
                <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $pemberkasan->jadwal_ttd_kontrak ? \Carbon\Carbon::parse($pemberkasan->jadwal_ttd_kontrak)->translatedFormat('d F Y') : 'Belum Terjadwal' }}
                </div>
            </div>

            <div class="p-4 rounded-lg bg-gray-50 dark:bg-white/[0.03] border border-gray-200 dark:border-gray-800">
                <label class="text-sm text-gray-500">Background Checking</label>
                <div class="mt-1 font-semibold text-gray-900 dark:text-white">
                    {{ $pemberkasan->background_checking ?? 'N/A' }}
                </div>
            </div>

        </div>

        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 flex justify-between items-center text-xs text-gray-500">
            <p>Dibuat pada: {{ $pemberkasan->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</p>
            <p>Terakhir diperbarui: {{ $pemberkasan->updated_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</p>
        </div>

    </div>
</div>
@endsection
