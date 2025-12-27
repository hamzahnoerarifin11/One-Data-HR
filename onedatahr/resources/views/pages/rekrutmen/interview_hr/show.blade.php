@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <!-- HEADER -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Detail Interview HR
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Informasi lengkap hasil wawancara kandidat
            </p>
        </div>
        <div class="flex items-center gap-2">
        @if(auth()->user() && auth()->user()->role === 'admin')
                <a href="{{ route('rekrutmen.interview_hr.edit', $interview->id_interview_hr) }}" class="inline-flex items-center gap-2 rounded-lg bg-yellow-500 px-5 py-2.5 text-center text-white font-medium hover:bg-yellow-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
        @endif
        <a href="{{ route('rekrutmen.interview_hr.index') }}"
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

    <!-- CARD -->
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-lg
                dark:border-gray-800 dark:bg-white/[0.03]">

        <!-- ================= IDENTITAS ================= -->
        <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">
            Identitas Interview
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <p class="text-sm text-gray-500">Nama Kandidat</p>
                <p class="font-semibold text-gray-900 dark:text-white">
                    {{ $interview->kandidat?->nama ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Posisi Dilamar</p>
                <p class="font-semibold text-gray-900 dark:text-white">
                    {{ $interview->kandidat?->posisi?->nama_posisi ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Tanggal Interview</p>
                <p class="font-semibold text-gray-900 dark:text-white">
                    {{ \Carbon\Carbon::parse($interview->hari_tanggal)->format('d M Y') }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Nama Interviewer</p>
                <p class="font-semibold text-gray-900 dark:text-white">
                    {{ $interview->nama_interviewer ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Model Wawancara</p>
                <p class="font-semibold text-gray-900 dark:text-white">
                    {{ $interview->model_wawancara ?? '-' }}
                </p>
            </div>
        </div>

        <!-- ================= PENILAIAN ================= -->
        <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">
            📊 Aspek Penilaian
        </h3>

        <div class="space-y-4 mb-8">
            @php
                $aspek = [
                    'profesional','spiritual','learning','initiative',
                    'komunikasi','problem_solving','teamwork'
                ];
            @endphp

            @foreach($aspek as $a)
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                <div class="md:col-span-4 font-medium capitalize text-gray-800 dark:text-white">
                    {{ str_replace('_',' ',$a) }}
                </div>
                <div class="md:col-span-2 text-center">
                    <span class="inline-flex items-center justify-center rounded-lg
                                 bg-blue-100 px-3 py-1 text-sm font-bold text-blue-700
                                 dark:bg-blue-900/30 dark:text-blue-400">
                        {{ $interview->{'skor_'.$a} }}
                    </span>
                </div>
                <div class="md:col-span-6">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $interview->{'catatan_'.$a} ?? '-' }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        <!-- ================= SUMMARY ================= -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div>
                <p class="text-sm text-gray-500">Total Skor</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $interview->total }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Rata-rata</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ number_format($interview->total / 7, 2) }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Kategori</p>
                @php
                    $rata = $interview->total / 7;
                    $kategori = 'Kurang';
                    $color = 'text-red-600';

                    if ($rata >= 4.5) {
                        $kategori = 'Sangat Baik'; $color = 'text-green-600';
                    } elseif ($rata >= 3.5) {
                        $kategori = 'Baik'; $color = 'text-blue-600';
                    } elseif ($rata >= 2.5) {
                        $kategori = 'Cukup'; $color = 'text-yellow-600';
                    }
                @endphp
                <p class="text-xl font-bold {{ $color }}">
                    {{ $kategori }}
                </p>
            </div>
        </div>

        <!-- ================= KEPUTUSAN ================= -->
        <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">
            ✅ Keputusan Akhir
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <p class="text-sm text-gray-500">Keputusan</p>
                @php
                    $badge = match($interview->keputusan) {
                        'DITERIMA' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                        'DITOLAK' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                        default => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400'
                    };
                @endphp
                <span class="inline-flex rounded-full px-4 py-1 text-sm font-medium {{ $badge }}">
                    {{ $interview->keputusan }}
                </span>
            </div>

            <div>
                <p class="text-sm text-gray-500">Status Proses</p>
                <p class="font-semibold text-gray-900 dark:text-white">
                    {{ $interview->hasil_akhir ?? '-' }}
                </p>
            </div>
        </div>

        <div>
            <p class="text-sm text-gray-500 mb-1">Catatan Tambahan</p>
            <p class="text-gray-700 dark:text-gray-300">
                {{ $interview->catatan_tambahan ?? '-' }}
            </p>
        </div>

        <!-- ACTION -->
        <!-- <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('rekrutmen.interview_hr.edit', $interview->id_interview_hr) }}"
               class="rounded-lg bg-yellow-500 px-6 py-2 text-sm text-white hover:bg-yellow-600 transition">
                Edit
            </a>
            <a href="{{ route('rekrutmen.interview_hr.index') }}"
               class="rounded-lg border border-gray-300 px-6 py-2 text-sm text-gray-700
                      hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300
                      dark:hover:bg-white/[0.05] transition">
                Kembali
            </a>
        </div> -->

    </div>
</div>
@endsection
