@extends('layouts.app')
@section('title','Dashboard Rekrutmen')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Dashboard Rekrutmen
            </h2>
            <p class="mt-1 text-sm text-gray-500">Overview statistik funnel rekrutmen tahun {{ $year }}</p>
        </div>

        <form method="GET" action="{{ route('rekrutmen.dashboard') }}">
            <select name="year" onchange="this.form.submit()" class="w-full sm:w-32 rounded-lg border border-gray-300 bg-white px-2 py-2 text-sm focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                        Tahun {{ $y }}
                    </option>
                @endforeach
            </select>


            <select name="posisi_id" onchange="this.form.submit()" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                <option value="">-- Semua Posisi --</option>
                @foreach($posisi as $p)
                    <option value="{{ $p->id_posisi }}" {{ $posisiId == $p->id_posisi ? 'selected' : '' }}>
                        {{ $p->nama_posisi }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4">
        @foreach($data as $label => $value)
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $label }}</span>
                    <h4 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $value }}
                    </h4>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-8 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-800">
        <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Funnel Progress (%)</h3>
        <div class="space-y-4">
            @foreach($data as $label => $value)
                @if($label !== 'Total Kandidat') 
                    @php 
                        // Menghitung persen dari Total Pelamar
                        $percent = $pelamar > 0 ? round(($value / $pelamar) * 100) : 0; 
                    @endphp
                    <div>
                        <div class="mb-1 flex justify-between text-sm">
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
                            <span class="text-gray-500">{{ $percent }}% ({{ $value }}/{{ $pelamar }})</span>
                        </div>
                        <div class="h-2.5 w-full rounded-full bg-gray-100 dark:bg-gray-700">
                            <div class="h-2.5 rounded-full bg-blue-600 transition-all duration-500" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

</div>
@endsection