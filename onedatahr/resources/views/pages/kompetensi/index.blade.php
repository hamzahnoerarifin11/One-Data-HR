@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Monitoring Kompetensi (LMS)" />

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-5 sm:p-6 shadow-sm">
        <!-- Header / Title -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <i class="fas fa-trophy text-orange-500 text-2xl"></i> Top 10 Skor Tertinggi
            </h3>
            
            <form action="{{ route('kompetensi.monitoring') }}" method="GET" class="w-full sm:w-auto flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex items-center bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 shadow-xs hidden sm:flex">
                    <i class="far fa-calendar-alt text-gray-500 mr-2"></i>
                    <select name="period" class="bg-transparent border-none p-0 text-sm text-gray-700 dark:text-gray-300 focus:ring-0 cursor-pointer outline-none">
                        <option value="6_bulan">6 Bulan</option>
                        <option value="1_tahun">1 Tahun</option>
                    </select>
                </div>

                <div class="relative w-full sm:w-auto">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                        <i class="fas fa-search text-sm"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Pegawai..."
                        class="h-10 w-full rounded-lg border border-gray-300 bg-white py-2 pl-9 pr-3 text-sm text-gray-800 shadow-xs placeholder-gray-400 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white xl:w-[250px]" />
                </div>

                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2 text-sm font-medium text-gray-800 hover:bg-gray-50 shadow-xs dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-700 transition">
                    Terapkan
                </button>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap text-left text-sm text-gray-700 dark:text-gray-300">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th scope="col" class="px-5 py-4 font-semibold">Peringkat</th>
                        <th scope="col" class="px-5 py-4 font-semibold">Nama</th>
                        <th scope="col" class="px-5 py-4 font-semibold">NIP</th>
                        <th scope="col" class="px-5 py-4 font-semibold">Kelas</th>
                        <th scope="col" class="px-5 py-4 font-semibold">Kompetensi Kelas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800/50">
                    @forelse ($kompetensiList as $index => $karyawan)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20 transition">
                            <td class="px-5 py-4">
                                @php
                                    $rank = $kompetensiList->firstItem() + $index;
                                    $rankClass = 'bg-gray-400 text-white'; // default
                                    if($rank == 1) $rankClass = 'bg-yellow-500 text-white';
                                    elseif($rank == 2) $rankClass = 'bg-gray-400 text-white';
                                    elseif($rank == 3) $rankClass = 'bg-orange-500 text-white';
                                @endphp
                                <div class="flex items-center justify-center w-8 h-8 rounded-full font-bold {{ $rankClass }}">
                                    {{ $rank }}
                                </div>
                            </td>
                            <td class="px-5 py-4 font-semibold text-gray-900 dark:text-white">
                                {{ $karyawan->Nama_Lengkap_Sesuai_Ijazah ?? '-' }}
                            </td>
                            <td class="px-5 py-4 text-gray-500">
                                {{ $karyawan->NIK ?? '-' }}
                            </td>
                            <td class="px-5 py-4 text-gray-800 dark:text-gray-200">
                                @php
                                    $job = $karyawan->pekerjaanTerkini()->first() ?? $karyawan->pekerjaan()->first();
                                    $kelas = $job->department->name ?? '-';
                                @endphp
                                {{ $kelas }}
                            </td>
                            <td class="px-5 py-4 text-gray-600 dark:text-gray-400">
                                {{ $karyawan->pegawaiKompetensi->pluck('nama_kompetensi')->unique()->implode(', ') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">
                                Belum ada data kompetensi pegawai yang diselesaikan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kompetensiList->hasPages())
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                {{ $kompetensiList->links() }}
            </div>
        @endif
    </div>
@endsection
