@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-8 max-w-5xl mx-auto space-y-6">

    {{-- Welcome Banner --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 text-white shadow-lg dark:from-blue-500 dark:to-indigo-500">
        <h1 class="text-2xl font-bold">Halo, {{ $karyawan->Nama_Lengkap_Sesuai_Ijazah }}! 👋</h1>
        <p class="opacity-90 mt-1">Selamat datang di Dashboard Kinerja. Berikut adalah status penilaian Anda untuk tahun <strong>{{ $tahun }}</strong>.</p>
    </div>

    {{-- Filter Tahun (Opsional, jika ingin lihat history) --}}
    <div class="flex justify-end">
        <form method="GET">
            <select name="tahun" onchange="this.form.submit()" class="border-gray-300 rounded-lg text-sm shadow-sm">
                @for($y = date('Y'); $y >= date('Y')-2; $y--)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {{-- CARD 1: KPI (Key Performance Indicator) --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-md transition">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="bg-green-100 text-green-600 p-3 rounded-lg"><i class="fas fa-chart-line text-xl"></i></div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">KPI (Kinerja)</h3>
                </div>

                @if($myKpi)
                    <div class="mb-6">
                        <p class="text-sm text-gray-500 mb-1">Status Dokumen:</p>
                        @if($myKpi->status == 'FINAL')
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-bold">FINAL / DISETUJUI</span>
                            <div class="mt-4">
                                <p class="text-sm text-gray-500">Skor Akhir:</p>
                                <span class="text-4xl font-extrabold text-green-600">{{ $myKpi->total_skor_akhir }}</span>
                                <span class="ml-2 px-2 py-0.5 bg-gray-100 rounded text-sm font-bold border">{{ $myKpi->grade }}</span>
                            </div>
                        @elseif($myKpi->status == 'SUBMITTED')
                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-bold">MENUNGGU APPROVAL</span>
                            <p class="text-sm text-gray-400 mt-2">Menunggu review manager.</p>
                        @else
                            <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-bold">DRAFT / PROSES</span>
                        @endif
                    </div>
                    
                    <a href="{{ route('kpi.show', ['karyawan_id' => $karyawan->id_karyawan, 'tahun' => $tahun]) }}" 
                       class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded-lg transition">
                        Buka Dokumen KPI
                    </a>
                @else
                    <div class="text-center py-6">
                        <p class="text-gray-500 mb-4">Anda belum membuat KPI untuk tahun {{ $tahun }}.</p>
                        <form action="{{ route('kpi.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="karyawan_id" value="{{ $karyawan->id_karyawan }}">
                            <input type="hidden" name="tahun" value="{{ $tahun }}">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                                <i class="fas fa-plus-circle"></i> Buat KPI Baru
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        {{-- CARD 2: KBI (Key Behavioral Indicator) --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-md transition">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="bg-purple-100 text-purple-600 p-3 rounded-lg"><i class="fas fa-user-check text-xl"></i></div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">KBI (Perilaku)</h3>
                </div>

                @if($myKbi)
                    <div class="mb-6">
                        <p class="text-sm text-gray-500 mb-1">Status Self-Assessment:</p>
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-bold">SUDAH DINILAI</span>
                        <p class="text-xs text-gray-400 mt-2">Terima kasih telah melakukan penilaian diri.</p>
                    </div>
                    <button class="block w-full text-center bg-gray-200 text-gray-500 font-medium py-2 rounded-lg cursor-not-allowed">
                        Sudah Selesai
                    </button>
                @else
                    <div class="text-center py-6">
                        <p class="text-gray-500 mb-4">Silakan isi penilaian perilaku diri sendiri (Self Assessment).</p>
                        <a href="{{ route('kbi.index') }}" class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                            Mulai Penilaian Diri
                        </a>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection