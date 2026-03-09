@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Detail Kandidat Lanjut User
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Informasi lengkap kandidat yang telah melalui tahapan Interview HR & User
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if(auth()->user() && auth()->user()->role === 'admin')
                <a href="{{ route('rekrutmen.kandidat_lanjut_user.edit', $data->id_kandidat_lanjut_user) }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-yellow-500 px-5 py-2.5 text-center text-white font-medium hover:bg-yellow-600 transition shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Data
                </a>
            @endif
            <a href="{{ route('rekrutmen.kandidat_lanjut_user.index') }}"
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

        <div class="mb-8">
            <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <span class="w-1.5 h-6 bg-brand-600 rounded-full"></span>
                Identitas Kandidat
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Nama Kandidat</label>
                    <div class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">
                        {{ $data->kandidat->nama ?? '-' }}
                    </div>
                </div>

                <div>
                    <label class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Posisi Dilamar</label>
                    <div class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">
                        {{ $data->kandidat->posisi->nama_posisi ?? '-' }}
                    </div>
                </div>

                <div>
                    <label class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Tanggal Interview HR</label>
                    <div class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">
                        {{ $data->tanggal_interview_hr ? \Carbon\Carbon::parse($data->tanggal_interview_hr)->translatedFormat('d F Y') : '-' }}
                    </div>
                </div>

                <div>
                    <label class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Tanggal Penyerahan ke User</label>
                    <div class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">
                        {{ $data->tanggal_penyerahan ? \Carbon\Carbon::parse($data->tanggal_penyerahan)->translatedFormat('d F Y') : '-' }}
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-8 border-gray-100 dark:border-gray-800">

        <div class="mb-8">
            <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <span class="w-1.5 h-6 bg-brand-600 rounded-full"></span>
                Tahapan Interview User
            </h3>

            <div class="grid grid-cols-1 gap-4">
                @php
                    $interviews = is_array($data->detail_interview) ? $data->detail_interview : json_decode($data->detail_interview, true);
                @endphp

                @if(!empty($interviews) && count($interviews) > 0)
                    @foreach($interviews as $index => $item)
                        <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-5 dark:border-gray-800 dark:bg-gray-900/50">
                            <div class="mb-4 flex items-center border-b border-gray-200 pb-2 dark:border-gray-800">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-brand-600 text-[10px] font-bold text-white mr-2">
                                    {{ $index + 1 }}
                                </span>
                                <h4 class="text-sm font-bold text-gray-800 dark:text-white">
                                    Tahap Interview Ke-{{ $index + 1 }}
                                </h4>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="text-xs text-gray-500 dark:text-gray-400 block">User / Interviewer</label>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $item['nama_user'] ?? '-' }}</span>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 dark:text-gray-400 block">Tanggal Interview</label>
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        {{ !empty($item['tanggal']) ? \Carbon\Carbon::parse($item['tanggal'])->translatedFormat('d F Y') : '-' }}
                                    </span>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 dark:text-gray-400 block mb-1">Status Hasil</label>
                                    @php $hasil = $item['hasil'] ?? ''; @endphp
                                    @if($hasil === 'Lolos')
                                        <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                            LOLOS
                                        </span>
                                    @elseif($hasil === 'Tidak Lolos')
                                        <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                            TIDAK LOLOS
                                        </span>
                                    @elseif($hasil === 'Pending')
                                        <span class="inline-flex rounded-full bg-yellow-100 px-3 py-1 text-xs font-bold text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                            PENDING
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400 italic">Belum ditentukan</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="rounded-xl border border-dashed border-gray-300 p-8 text-center dark:border-gray-700">
                        <p class="text-sm text-gray-500">Belum ada data tahapan interview user yang dicatat.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="mb-8">
            <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white uppercase tracking-wider">
                Catatan Tambahan
            </h3>
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm leading-relaxed text-gray-800 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                {!! nl2br(e($data->catatan ?? 'Tidak ada catatan tambahan.')) !!}
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 flex justify-between items-center text-xs text-gray-500">

                <p>Dibuat pada: {{ $data->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</p>

                <p>Terakhir diperbarui: {{ $data->updated_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</p>

        </div>

            <!-- <div class="text-[11px] font-medium text-brand-600 dark:text-brand-400">
                ID Dokumen: #{{ str_pad($data->id_kandidat_lanjut_user, 5, '0', STR_PAD_LEFT) }}
            </div> -->

    </div>
</div>
@endsection
