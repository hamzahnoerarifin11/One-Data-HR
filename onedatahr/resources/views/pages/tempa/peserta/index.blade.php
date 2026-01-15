@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Manajemen Peserta TEMPA
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">
                Kelola peserta TEMPA
            </p>
        </div>

        @can('createTempaPeserta')
        <a href="{{ route('tempa.peserta.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Peserta
        </a>
        @endcan
    </div>

    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

        <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4">
           <div class="flex items-center gap-2">
            <label class="text-sm text-gray-500 dark:text-gray-400">Show</label>

                <div class="relative z-20 w-20">
                    <select class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-9 text-sm text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>

                    <span class="pointer-events-none absolute top-1/2 right-3 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                        <svg class="stroke-current" width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </div>

                <span class="text-sm text-gray-500 dark:text-gray-400">entries</span>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="relative">
                    <button class="absolute text-gray-500 -translate-y-1/2 left-4 top-1/2 dark:text-gray-400">
                        <svg class="h-5 w-5 fill-current" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.04199 9.37363C3.04199 5.87693 5.87735 3.04199 9.37533 3.04199C12.8733 3.04199 15.7087 5.87693 15.7087 9.37363C15.7087 12.8703 12.8733 15.7053 9.37533 15.7053C5.87735 15.7053 3.04199 12.8703 3.04199 9.37363ZM9.37533 1.54199C5.04926 1.54199 1.54199 5.04817 1.54199 9.37363C1.54199 13.6991 5.04926 17.2053 9.37533 17.2053C11.2676 17.2053 13.0032 16.5344 14.3572 15.4176L17.1773 18.238C17.4702 18.5309 17.945 18.5309 18.2379 18.238C18.5308 17.9451 18.5309 17.4703 18.238 17.1773L15.4182 14.3573C16.5367 13.0033 17.2087 11.2669 17.2087 9.37363C17.2087 5.04817 13.7014 1.54199 9.37533 1.54199Z"/>
                        </svg>
                    </button>
                    <input type="text" placeholder="Search peserta..." class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-12 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 xl:w-[300px]">
                </div>
            </div>
        </div>

        <div class="max-w-full overflow-x-auto">
            <table class="w-full min-w-full border-collapse">
                <thead>
                    <tr class="border-y border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">#</th>
                        <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">Nama Peserta</th>
                        <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">NIK</th>
                        <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">Status</th>
                        <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">Kelompok</th>
                        <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">Mentor</th>
                        <th class="px-6 py-3 text-right text-md font-medium text-gray-600 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pesertas as $index => $peserta)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20 transition">
                        <td class="px-6 py-4 text-md text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-md font-medium text-gray-900 dark:text-white">{{ $peserta->nama_peserta }}</td>
                        <td class="px-6 py-4 text-md text-gray-600 dark:text-gray-300">{{ $peserta->nik_karyawan }}</td>
                        <td class="px-6 py-4 text-md">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-medium
                                @if($peserta->status_peserta == 1) bg-green-100 text-green-700
                                @elseif($peserta->status_peserta == 2) bg-yellow-100 text-yellow-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ $peserta->status_peserta == 1 ? 'Aktif' : ($peserta->status_peserta == 2 ? 'Pindah' : 'Keluar') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-md text-gray-600 dark:text-gray-300">{{ $peserta->kelompok->nama_kelompok ?? '-' }}</td>
                        <td class="px-6 py-4 text-md text-gray-600 dark:text-gray-300">{{ $peserta->mentor->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">
                            @can('editTempaPeserta')
                            <a href="{{ route('tempa.peserta.edit', $peserta->id) }}" class="inline-flex items-center justify-center rounded-lg bg-yellow-50 p-2 text-yellow-600 hover:bg-yellow-100 dark:bg-yellow-900/20 dark:text-yellow-400 dark:hover:bg-yellow-900/40 transition" title="Edit">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            @endcan
                            @can('deleteTempaPeserta')
                            <form action="{{ route('tempa.peserta.destroy', $peserta->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-red-50 p-2 text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 transition" title="Hapus">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Showing 1 to {{ $pesertas->count() }} of {{ $pesertas->count() }} entries
            </div>

            <div class="flex items-center gap-2">
                <!-- Pagination placeholder -->
            </div>
        </div>
    </div>
</div>
@endsection
