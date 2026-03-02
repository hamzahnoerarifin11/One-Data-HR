@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Monitoring Kompetensi (LMS)" />

    <!-- Statistik Ringkas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- Card 1 -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs text-brand-500 font-semibold uppercase">Total Diraih</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $kompetensiList->total() }}</h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center text-brand-500">
                <i class="fas fa-certificate text-lg"></i>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs text-yellow-600 font-semibold uppercase">Ahli/Advanced</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ \App\Models\PegawaiKompetensi::where('level', 'Advanced')->count() }}</h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-yellow-50 dark:bg-yellow-500/20 flex items-center justify-center text-yellow-500">
                <i class="fas fa-star text-lg"></i>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs text-blue-600 font-semibold uppercase">Menengah/Inter</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ \App\Models\PegawaiKompetensi::where('level', 'Intermediate')->count() }}</h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-500/20 flex items-center justify-center text-blue-500">
                <i class="fas fa-star-half-alt text-lg"></i>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase">Dasar/Beginner</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ \App\Models\PegawaiKompetensi::where('level', 'Beginner')->count() }}</h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500">
                <i class="far fa-star text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Tabel -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-5 py-5 sm:px-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                Data Kompetensi Pegawai
            </h3>
            
            <form action="{{ route('kompetensi.monitoring') }}" method="GET" class="w-full sm:w-auto flex flex-col sm:flex-row gap-3">
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIK, Nama, Kompetensi..."
                        class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-200 bg-transparent py-2.5 pl-12 pr-14 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 xl:w-[400px]" />
                </div>
                
                <select name="level" class="py-2 px-3 rounded-lg border border-gray-300 bg-transparent text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800">
                    <option value="">Semua Level</option>
                    @foreach($listLevel as $lvl)
                        <option value="{{ $lvl }}" {{ request('level') == $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                    @endforeach
                </select>

                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                    Filter
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap text-left text-sm text-gray-500 dark:text-gray-400">
                <thead class="bg-gray-50 uppercase text-gray-800 dark:bg-gray-800/50 dark:text-white/90 border-y border-gray-200 dark:border-gray-800">
                    <tr>
                        <th scope="col" class="px-5 py-3.5 font-medium">Pegawai</th>
                        <th scope="col" class="px-5 py-3.5 font-medium">Departemen / Divisi</th>
                        <th scope="col" class="px-5 py-3.5 font-medium">Nama Kompetensi</th>
                        <th scope="col" class="px-5 py-3.5 font-medium">Level</th>
                        <th scope="col" class="px-5 py-3.5 font-medium">Tgl Update LMS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse ($kompetensiList as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 flex-shrink-0 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800 dark:text-white/90">{{ $item->karyawan->Nama_Lengkap_Sesuai_Ijazah ?? '-' }}</p>
                                        <p class="text-xs text-gray-500">{{ $item->karyawan->NIK ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                @php
                                    $job = $item->karyawan->pekerjaanTerkini()->first() ?? $item->karyawan->pekerjaan()->first();
                                @endphp
                                <p class="text-gray-800 dark:text-white/90">{{ $job->department->name ?? '-' }}</p>
                                <p class="text-xs text-brand-500">{{ $job->division->name ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-4 font-medium text-gray-800 dark:text-white/90">
                                {{ $item->nama_kompetensi }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                                    {{ $item->level ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                {{ $item->updated_at->format('d M Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">
                                Belum ada data kompetensi. Pastikan Service API dan Scheduler penarikan LMS berjalan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kompetensiList->hasPages())
            <div class="border-t border-gray-200 px-5 py-4 dark:border-gray-800">
                {{ $kompetensiList->links() }}
            </div>
        @endif
    </div>
@endsection
