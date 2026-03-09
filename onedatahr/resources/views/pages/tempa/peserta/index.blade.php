@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Manajemen Peserta TEMPA
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">
                Kelola data peserta TEMPA secara efisien
            </p>
        </div>

        <div class="flex items-center gap-2">
            @can('createTempaPeserta')
            <button @click="$dispatch('open-import-modal')"
               class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-green-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import Excel
            </button>

            <a href="{{ route('tempa.peserta.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Peserta
            </a>

            @can('deleteTempaPeserta')
            <form action="{{ route('tempa.peserta.bulk-delete') }}" method="POST" id="bulkDeleteForm" class="inline-block"
                  x-data="{ hasSelection: false }"
                  @update-selection.window="hasSelection = $event.detail.length > 0">
                @csrf
                <!-- Input hidden diisi dari js alpine -->
                <div id="hidden-inputs-container"></div>
                
                <button type="submit" x-show="hasSelection" x-cloak
                        onclick="return confirm('Apakah Anda yakin ingin menghapus peserta yang dipilih?')"
                        class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-red-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0H7m3-3h4a1 1 0 011 1v1H9V5a1 1 0 011-1z"/>
                    </svg>
                    Hapus Terpilih (<span x-text="$store.selection ? $store.selection.length : 0"></span>)
                </button>
            </form>
            @endcan
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800 dark:border-green-900 dark:bg-green-900/20 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800 dark:border-red-900 dark:bg-red-900/20 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    @php
        // Menyiapkan data untuk Alpine.js agar fitur search & sort berjalan client-side
        $tableData = $pesertas->map(fn($row) => [
            'id'             => $row->id_peserta,
            'nama_peserta'   => $row->nama_peserta,
            'nik'            => $row->nik_karyawan,
            'status_val'     => $row->status_peserta, // untuk filter/sort
            'status_label'   => $row->status_peserta == 1 ? 'Aktif' : ($row->status_peserta == 2 ? 'Pindah' : 'Keluar'),
            'keterangan_pindah' => $row->keterangan_pindah ?? '-',
            'kelompok'       => $row->kelompok->nama_kelompok ?? '-',
            'mentor'         => $row->kelompok->nama_mentor ?? '-',
            'show_url'       => route('tempa.peserta.show', ['peserta' => $row->id_peserta]),
            'edit_url'       => route('tempa.peserta.edit', ['peserta' => $row->id_peserta]),
            'delete_url'     => route('tempa.peserta.destroy', ['peserta' => $row->id_peserta]),
            'can_edit'       => auth()->user()->can('editTempaPeserta'),
            'can_delete'     => auth()->user()->can('deleteTempaPeserta'),
        ])->values();
    @endphp

    <div x-data="pesertaTable()" class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

        <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4">
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">Show</span>
                <div class="relative z-20">
                    <select
                        x-model.number="perPage"
                        @change="resetPage"
                        class="h-11 w-20 appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-8 text-sm text-gray-800 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    >
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500">
                        <svg class="fill-current" width="18" height="18" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" /></svg>
                    </span>
                </div>
                <span class="text-sm text-gray-500 dark:text-gray-400">entries</span>
            </div>

            <div class="relative">
                <button class="absolute text-gray-500 -translate-y-1/2 left-4 top-1/2">
                    <svg class="h-5 w-5 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.04199 9.37363C3.04199 5.87693 5.87735 3.04199 9.37533 3.04199C12.8733 3.04199 15.7087 5.87693 15.7087 9.37363C15.7087 12.8703 12.8733 15.7053 9.37533 15.7053C5.87735 15.7053 3.04199 12.8703 3.04199 9.37363ZM9.37533 1.54199C5.04926 1.54199 1.54199 5.04817 1.54199 9.37363C1.54199 13.6991 5.04926 17.2053 9.37533 17.2053C11.2676 17.2053 13.0032 16.5344 14.3572 15.4176L17.1773 18.238C17.4702 18.5309 17.945 18.5309 18.2379 18.238C18.5308 17.9451 18.5309 17.4703 18.238 17.1773L15.4182 14.3573C16.5367 13.0033 17.2087 11.2669 17.2087 9.37363C17.2087 5.04817 13.7014 1.54199 9.37533 1.54199Z"/></svg>
                </button>
                <input
                    x-model="search"
                    @input="resetPage"
                    type="text"
                    placeholder="Cari nama, NIK, atau kelompok..."
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-12 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 xl:w-[300px]"
                />
            </div>
        </div>

        <div class="max-w-full overflow-x-auto">
            <table class="w-full min-w-full border-collapse">
                <thead>
                    <tr class="border-y border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th class="px-5 py-3 text-left w-12">
                            <input type="checkbox" @change="toggleSelectAll($event)" :checked="isAllSelected" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800">
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 w-12">#</th>
                        <th @click="sortBy('nama_peserta')" class="px-6 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer hover:text-blue-600 transition">
                            <div class="flex items-center gap-1">
                                Nama Peserta
                                <svg :class="sortCol === 'nama_peserta' ? (sortDir === 'asc' ? 'rotate-0' : 'rotate-180') : 'opacity-20'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </div>
                        </th>
                        <th @click="sortBy('nik')" class="px-6 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer hover:text-blue-600">
                            <div class="flex items-center gap-1">
                                NIK
                                <svg :class="sortCol === 'nik' ? (sortDir === 'asc' ? 'rotate-0' : 'rotate-180') : 'opacity-20'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </div>
                        </th>
                        <th @click="sortBy('status_label')" class="px-6 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer hover:text-blue-600">
                            <div class="flex items-center gap-1">
                                Status
                                <svg :class="sortCol === 'status_label' ? (sortDir === 'asc' ? 'rotate-0' : 'rotate-180') : 'opacity-20'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </div>
                        </th>
                        <th @click="sortBy('kelompok')" class="px-6 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer hover:text-blue-600">
                            <div class="flex items-center gap-1">
                                Kelompok
                                <svg :class="sortCol === 'kelompok' ? (sortDir === 'asc' ? 'rotate-0' : 'rotate-180') : 'opacity-20'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </div>
                        </th>
                        <th @click="sortBy('mentor')" class="px-6 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer hover:text-blue-600">
                            <div class="flex items-center gap-1">
                                Mentor
                                <svg :class="sortCol === 'mentor' ? (sortDir === 'asc' ? 'rotate-0' : 'rotate-180') : 'opacity-20'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-right text-sm font-medium text-gray-600 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="(row, index) in paginated" :key="row.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20 transition">
                            <td class="px-5 py-4 text-center">
                                <input type="checkbox" :value="row.id" x-model="selectedItems" @change="updateSelection" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800">
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400" x-text="(page - 1) * perPage + index + 1"></td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white" x-text="row.nama_peserta"></td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300" x-text="row.nik"></td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex flex-col gap-1.5">
                                    <div>
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold tracking-wide uppercase"
                                            :class="{
                                                'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': row.status_val == 1,
                                                'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400': row.status_val == 2,
                                                'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': row.status_val != 1 && row.status_val != 2
                                            }"
                                            x-text="row.status_label">
                                        </span>
                                    </div>
                                    <template x-if="row.status_val == 2 && row.keterangan_pindah && row.keterangan_pindah !== '-'">
                                        <div class="flex items-start gap-1 text-gray-500 dark:text-gray-400">
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-[11px] leading-tight italic" x-text="row.keterangan_pindah"></span>
                                        </div>
                                    </template>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300" x-text="row.kelompok"></td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300" x-text="row.mentor"></td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a :href="row.show_url" class="inline-flex items-center justify-center rounded-lg bg-blue-50 p-2 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/40 transition" title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    <template x-if="row.can_edit">
                                        <a :href="row.edit_url" class="inline-flex items-center justify-center rounded-lg bg-yellow-50 p-2 text-yellow-600 hover:bg-yellow-100 dark:bg-yellow-900/20 dark:text-yellow-400 dark:hover:bg-yellow-900/40 transition" title="Edit">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                    </template>

                                    <template x-if="row.can_delete">
                                        <form :action="row.delete_url" method="POST" @submit.prevent="if(confirm('Apakah Anda yakin ingin menghapus peserta ini?')) $el.submit()">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-red-50 p-2 text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 transition" title="Hapus">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filtered.length === 0">
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada data peserta ditemukan.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Showing <span x-text="startItem"></span> to <span x-text="endItem"></span> of <span x-text="filtered.length"></span> entries
            </div>

            <div class="flex items-center gap-2">
                <button @click="prevPage" :disabled="page === 1" class="rounded-lg border px-3 py-1.5 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white disabled:opacity-50 transition">Prev</button>
                <template x-for="p in displayedPages" :key="p">
                    <button @click="goToPage(p)" :class="page === p ? 'bg-blue-600 text-white' : 'hover:bg-blue-500/[0.08] hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-500'" class="px-3 py-1 text-sm rounded-lg transition" x-text="p"></button>
                </template>
                <button @click="nextPage" :disabled="page === totalPages" class="rounded-lg border px-3 py-1.5 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white disabled:opacity-50 transition">Next</button>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal using Alpine.js -->
<div x-data="{ open: false }"
     @open-import-modal.window="open = true"
     @keydown.escape.window="open = false"
     class="relative z-50"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true"
     x-show="open"
     x-cloak>

    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.away="open = false"
                 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg dark:bg-gray-800 border border-gray-100 dark:border-gray-700">

                <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4 max-h-[80vh] overflow-y-auto">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10 dark:bg-green-900/30">
                            <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                            <h3 class="text-xl font-semibold leading-6 text-gray-900 dark:text-white" id="modal-title">Import Data Peserta</h3>
                            <div class="mt-4 space-y-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Upload file Excel untuk import data peserta. Pastikan format kolom sesuai template.
                                </p>

                                <!-- Guideline Box -->
                                <div class="rounded-lg bg-blue-50/50 p-4 border border-blue-100 dark:bg-blue-900/20 dark:border-blue-800">
                                    <h4 class="flex items-center gap-2 text-sm font-semibold text-blue-800 dark:text-blue-300 mb-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Poin Penting
                                    </h4>
                                    <ul class="text-xs text-blue-700 dark:text-blue-400 space-y-1.5 list-disc list-inside ml-1">
                                        <li><span class="font-medium text-blue-900 dark:text-blue-200">Nama Kelompok:</span> Wajib SAMA PERSIS dengan di sistem.</li>
                                        <li><span class="font-medium text-blue-900 dark:text-blue-200">NIK & Nama:</span> Wajib diisi.</li>
                                        <li><span class="font-medium text-blue-900 dark:text-blue-200">Status:</span> "Aktif", "Pindah", atau "Keluar".</li>
                                    </ul>
                                    <div class="mt-3 pt-3 border-t border-blue-200 dark:border-blue-800">
                                        <a href="{{ route('tempa.peserta.import-template') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            Download Template Peserta.xlsx
                                        </a>
                                    </div>
                                </div>

                                <form action="{{ route('tempa.peserta.import') }}" method="POST" enctype="multipart/form-data" class="mt-4">
                                    @csrf
                                    <div class="space-y-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            File Excel
                                        </label>
                                        <div class="relative">
                                            <input type="file" name="file" accept=".xlsx, .xls, .csv" required
                                                   class="block w-full text-sm text-gray-500
                                                          file:mr-4 file:py-2.5 file:px-4
                                                          file:rounded-lg file:border-0
                                                          file:text-sm file:font-semibold
                                                          file:bg-blue-50 file:text-blue-700
                                                          hover:file:bg-blue-100
                                                          dark:file:bg-blue-900/30 dark:file:text-blue-400
                                                          border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                                        </div>
                                    </div>
                                    <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                                        <button type="button" @click="open = false" class="inline-flex w-full justify-center rounded-lg bg-gray-50 px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-100 sm:w-auto dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:hover:bg-gray-600 transition">
                                            Batal
                                        </button>
                                        <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:w-auto transition">
                                            Import Data
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('selection', []);
});

function pesertaTable() {
    return {
        data: @json($tableData),
        search: '',
        page: 1,
        perPage: 10,
        sortCol: 'nama_peserta',
        sortDir: 'asc',
        selectedItems: [],

        init() {
            this.$watch('search', value => {
                this.resetPage();
                this.selectedItems = []; // Reset selesksi saat search
                this.updateSelection();
            });
            this.$watch('page', value => {
                // Opsional: reset selection saat ganti halaman. 
                // Jika ingin persist, comment baris di bawah.
                // this.selectedItems = [];
                // this.updateSelection();
            });
        },

        resetPage() { this.page = 1; },
        sortBy(column) {
            if (this.sortCol === column) {
                this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortCol = column;
                this.sortDir = 'asc';
            }
        },
        get filtered() {
            let filteredData = this.data;
            if (this.search) {
                const q = this.search.toLowerCase();
                filteredData = filteredData.filter(d =>
                    d.nama_peserta.toLowerCase().includes(q) ||
                    d.nik.toLowerCase().includes(q) ||
                    d.kelompok.toLowerCase().includes(q) ||
                    d.mentor.toLowerCase().includes(q) ||
                    d.status_label.toLowerCase().includes(q)
                );
            }
            return filteredData.sort((a, b) => {
                let aVal = a[this.sortCol], bVal = b[this.sortCol];
                if (typeof aVal === 'string') aVal = aVal.toLowerCase();
                if (aVal < bVal) return this.sortDir === 'asc' ? -1 : 1;
                if (aVal > bVal) return this.sortDir === 'asc' ? 1 : -1;
                return 0;
            });
        },
        get totalPages() { return Math.max(1, Math.ceil(this.filtered.length / this.perPage)); },
        get paginated() {
            const start = (this.page - 1) * this.perPage;
            return this.filtered.slice(start, start + this.perPage);
        },
        prevPage() { if (this.page > 1) this.page--; },
        nextPage() { if (this.page < this.totalPages) this.page++; },
        goToPage(p) { if(typeof p === 'number') this.page = p; },
        get displayedPages() {
            let pages = [];
            for (let i = 1; i <= this.totalPages; i++) pages.push(i);
            return pages;
        },
        get startItem() { return this.filtered.length === 0 ? 0 : (this.page - 1) * this.perPage + 1; },
        get endItem() { return Math.min(this.page * this.perPage, this.filtered.length); },

        // Checkbox Logic
        get isAllSelected() {
            if (this.paginated.length === 0) return false;
            return this.paginated.every(item => this.selectedItems.includes(item.id.toString()) || this.selectedItems.includes(item.id));
        },
        toggleSelectAll(event) {
            const isChecked = event.target.checked;
            if (isChecked) {
                const newSelections = this.paginated.map(item => item.id);
                this.selectedItems = [...new Set([...this.selectedItems, ...newSelections])];
            } else {
                const currentIds = this.paginated.map(item => item.id);
                this.selectedItems = this.selectedItems.filter(id => !currentIds.includes(parseInt(id)) && !currentIds.includes(id));
            }
            this.updateSelection();
        },
        updateSelection() {
            Alpine.store('selection', this.selectedItems);
            this.$dispatch('update-selection', this.selectedItems);
            
            // Update hidden inputs for mass delete
            const container = document.getElementById('hidden-inputs-container');
            if (container) {
                container.innerHTML = '';
                this.selectedItems.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    container.appendChild(input);
                });
            }
        }
    }
}
</script>
@endsection
