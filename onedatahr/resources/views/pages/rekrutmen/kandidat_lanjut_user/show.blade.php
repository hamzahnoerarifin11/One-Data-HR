@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <!-- Header -->
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
                <a href="{{ route('rekrutmen.kandidat_lanjut_user.edit', $data->id_kandidat_lanjut_user) }}" class="inline-flex items-center gap-2 rounded-lg bg-yellow-500 px-5 py-2.5 text-center text-white font-medium hover:bg-yellow-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
        @endif
        <a href="{{ route('rekrutmen.kandidat_lanjut_user.index') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2
                  text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300
                  dark:hover:bg-white/[0.05] transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
        </div>
    </div>

    <!-- Card -->
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-lg
                dark:border-gray-800 dark:bg-white/[0.03]">

        <!-- ================= IDENTITAS ================= -->
        <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">
            Identitas Kandidat
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">

            <div>
                <label class="text-sm text-gray-500">Nama Kandidat</label>
                <div class="mt-1 font-semibold text-gray-900 dark:text-white">
                    {{ $data->kandidat->nama ?? '-' }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">Posisi Dilamar</label>
                <div class="mt-1 font-semibold text-gray-900 dark:text-white">
                    {{ $data->kandidat->posisi->nama_posisi ?? '-' }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">Tanggal Interview HR</label>
                <div class="mt-1 font-semibold text-gray-900 dark:text-white">
                    {{ $data->tanggal_interview_hr ? date('d M Y', strtotime($data->tanggal_interview_hr)) : '-' }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">Tanggal Penyerahan</label>
                <div class="mt-1 font-semibold text-gray-900 dark:text-white">
                    {{ $data->tanggal_penyerahan ? date('d M Y', strtotime($data->tanggal_penyerahan)) : '-' }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">User Terkait</label>
                <div class="mt-1 font-semibold text-gray-900 dark:text-white">
                    {{ $data->user_terkait ?? '-' }}
                </div>
            </div>

        </div>

        <!-- ================= INTERVIEW USER ================= -->
        <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">
            Interview User
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">

            <!-- ASS -->
            <div>
                <label class="text-sm text-gray-500">Tanggal Interview ASS</label>
                <div class="mt-1 font-semibold text-gray-900 dark:text-white">
                    {{ $data->tanggal_interview_user_ass ? date('d M Y', strtotime($data->tanggal_interview_user_ass)) : '-' }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">Hasil ASS</label>
                <div class="mt-1">
                    @if($data->hasil_ass === 'Lolos')
                        <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">
                            LOLOS
                        </span>
                    @elseif($data->hasil_ass === 'Tidak Lolos')
                        <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-700 dark:bg-red-900/30 dark:text-red-400">
                            TIDAK LOLOS
                        </span>
                    @else
                        <span class="text-sm text-gray-500 dark:text-gray-400">-</span>
                    @endif
                </div>
            </div>

            <!-- ASM -->
            <div>
                <label class="text-sm text-gray-500">Tanggal Interview ASM</label>
                <div class="mt-1 font-semibold text-gray-900 dark:text-white">
                    {{ $data->tanggal_interview_user_asm ? date('d M Y', strtotime($data->tanggal_interview_user_asm)) : '-' }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">Hasil ASM</label>
                <div class="mt-1">
                    @if($data->hasil_asm === 'Lolos')
                        <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">
                            LOLOS
                        </span>
                    @elseif($data->hasil_asm === 'Tidak Lolos')
                        <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-700 dark:bg-red-900/30 dark:text-red-400">
                            TIDAK LOLOS
                        </span>
                    @else
                        <span class="text-sm text-gray-500 dark:text-gray-400">-</span>
                    @endif
                </div>
            </div>

        </div>

        <!-- ================= CATATAN ================= -->
        <div class="mb-6">
            <label class="text-sm text-gray-500">Catatan</label>
            <div class="mt-2 rounded-lg border border-gray-200 bg-gray-50 p-4
                        text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                {{ $data->catatan ?? '-' }}
            </div>
        </div>

        <!-- ACTION -->
        <!-- <div class="flex justify-end gap-3">
            <a href="{{ route('rekrutmen.kandidat_lanjut_user.index') }}"
               class="rounded-lg border border-gray-300 px-4 py-2 text-sm
                      text-gray-700 hover:bg-gray-50 dark:border-gray-700
                      dark:text-gray-300 dark:hover:bg-white/[0.05] transition">
                Kembali
            </a>

            <a href="{{ route('rekrutmen.kandidat_lanjut_user.edit', $data->id_kandidat_lanjut_user) }}"
               class="rounded-lg bg-brand-600 px-6 py-2 text-sm text-white hover:bg-brand-700 transition">
                Edit Data
            </a>
        </div> -->

    </div>
</div>
@endsection
