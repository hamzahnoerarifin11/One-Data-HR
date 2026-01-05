@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Rekapitulasi Kinerja {{ $tahun }}</h1>
            <p class="text-sm text-gray-500">Gabungan Nilai KPI (70%) dan KBI (30%)</p>
        </div>
        
        {{-- Search Form --}}
        <form action="{{ route('performance.rekap') }}" method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Karyawan..." 
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                Cari
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-bold">
                <tr>
                    <th class="px-6 py-3">Karyawan</th>
                    <th class="px-6 py-3 text-center bg-blue-50">KPI (70%)</th>
                    <th class="px-6 py-3 text-center bg-purple-50">KBI (30%)</th>
                    <th class="px-6 py-3 text-center bg-gray-200">Final Score</th>
                    <th class="px-6 py-3 text-center">Grade</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($rekap as $data)
                <tr class="hover:bg-gray-50 transition">
                    {{-- Nama --}}
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-800">{{ $data->nama }}</div>
                        <div class="text-xs text-gray-500">{{ $data->jabatan }} | NIK: {{ $data->nik }}</div>
                    </td>

                    {{-- Nilai KPI --}}
                    <td class="px-6 py-4 text-center font-medium text-blue-700 bg-blue-50/30">
                        {{ $data->skor_kpi }}
                    </td>

                    {{-- Nilai KBI --}}
                    <td class="px-6 py-4 text-center bg-purple-50/30">
                        <div class="font-medium text-purple-700">{{ number_format($data->skor_kbi_asli, 2) }} <span class="text-xs text-gray-400">/ 4.0</span></div>
                        <div class="text-[10px] text-gray-400">(Konversi: {{ $data->skor_kbi_100 }})</div>
                    </td>

                    {{-- Final Score --}}
                    <td class="px-6 py-4 text-center font-bold text-lg text-gray-800 bg-gray-50">
                        {{ $data->final_score }}
                    </td>

                    {{-- Grade --}}
                    <td class="px-6 py-4 text-center">
                        @php
                            $color = 'gray';
                            if($data->grade == 'A') $color = 'green';
                            elseif($data->grade == 'B') $color = 'blue';
                            elseif($data->grade == 'C') $color = 'yellow';
                            elseif($data->grade == 'D') $color = 'orange';
                            else $color = 'red';
                        @endphp
                        <span class="px-3 py-1 rounded-full text-white font-bold text-xs bg-{{ $color }}-500 shadow-sm">
                            {{ $data->grade }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-8 text-gray-400">Belum ada data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection