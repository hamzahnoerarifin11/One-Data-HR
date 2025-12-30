@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Manajemen Kandidat
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">
                Kelola daftar pelamar dan status tahapan rekrutmen
            </p>
        </div>

        <button
            @click="window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'add-kandidat' } }))"
            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kandidat
        </button>
    </div>

    <div x-data="kandidatTable()" class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

        <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4">
           <div class="flex items-center gap-2">
                <label class="text-sm text-gray-500 dark:text-gray-400">Show</label>
                <div class="relative z-20 w-20">
                    <select x-model.number="perPage" @change="resetPage" class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-9 text-sm text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="pointer-events-none absolute top-1/2 right-3 z-30 -translate-y-1/2 text-gray-500">
                        <svg class="stroke-current" width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </span>
                </div>
                <span class="text-sm text-gray-500 dark:text-gray-400">entries</span>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="relative">
                    <button class="absolute text-gray-500 -translate-y-1/2 left-4 top-1/2">
                        <svg class="h-5 w-5 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.04199 9.37363C3.04199 5.87693 5.87735 3.04199 9.37533 3.04199C12.8733 3.04199 15.7087 5.87693 15.7087 9.37363C15.7087 12.8703 12.8733 15.7053 9.37533 15.7053C5.87735 15.7053 3.04199 12.8703 3.04199 9.37363ZM9.37533 1.54199C5.04926 1.54199 1.54199 5.04817 1.54199 9.37363C1.54199 13.6991 5.04926 17.2053 9.37533 17.2053C11.2676 17.2053 13.0032 16.5344 14.3572 15.4176L17.1773 18.238C17.4702 18.5309 17.945 18.5309 18.2379 18.238C18.5308 17.9451 18.5309 17.4703 18.238 17.1773L15.4182 14.3573C16.5367 13.0033 17.2087 11.2669 17.2087 9.37363C17.2087 5.04817 13.7014 1.54199 9.37533 1.54199Z"/></svg>
                    </button>
                    <input x-model="search" @input="resetPage" type="text" placeholder="Cari nama atau posisi..." class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-12 pr-4 text-sm text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 xl:w-[300px]" />
                </div>
            </div>
        </div>

        <div class="max-w-full overflow-x-auto">
            <table class="w-full min-w-full border-collapse">
                <thead>
                    <tr class="border-y border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">#</th>
                        <th @click="sort('nama')" class="cursor-pointer px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400 hover:text-blue-600 transition">
                            <div class="flex items-center gap-1">Nama Kandidat <svg class="h-4 w-4" :class="sortCol === 'nama' ? (sortAsc ? '' : 'rotate-180') : 'opacity-20'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg></div>
                        </th>
                        <th @click="sort('posisi_id')" class="cursor-pointer px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400"><div class="flex items-center gap-1">Posisi <svg class="h-4 w-4" :class="sortCol === 'posisi_id' ? (sortAsc ? '' : 'rotate-180') : 'opacity-20'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg></div>
                        </th>
                        <th @click="sort('status_akhir')" class="cursor-pointer px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400"><div class="flex items-center gap-1">Status Tahapan <svg class="h-4 w-4" :class="sortCol === 'status_akhir' ? (sortAsc ? '' : 'rotate-180') : 'opacity-20'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg></div>
                        </th>
                        <th class="px-6 py-3 text-center text-md font-medium text-gray-600 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="(row, index) in paginated" :key="row.id_kandidat">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20 transition">
                            <td class="px-6 py-4 text-md text-gray-500" x-text="startItem + index"></td>
                            <td class="px-6 py-4">
                                <div class="text-md font-medium text-gray-900 dark:text-white" x-text="row.nama"></div>
                                <div class="text-xs text-gray-500" x-text="'Sumber: ' + (row.sumber || '-')"></div>
                            </td>
                            <td class="px-6 py-4 text-md text-gray-600 dark:text-gray-300" x-text="row.posisi ? row.posisi.nama_posisi : '-'"></td>
                            <td class="px-6 py-4">
                                <span :class="getStatusBadgeClass(row.status_akhir)" class="inline-flex rounded-full px-3 py-1 text-xs font-medium" x-text="row.status_akhir"></span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="openShowModal(row)" class="p-2 text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 dark:bg-blue-900/20 transition" title="Detail">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button @click="openEditModal(row)" class="p-2 text-yellow-600 bg-yellow-50 rounded-lg hover:bg-yellow-100 dark:bg-yellow-900/20 transition" title="Edit">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button @click="confirmDelete(row)" class="p-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 dark:bg-red-900/20 transition" title="Hapus">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filtered.length === 0">
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada data kandidat ditemukan.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Showing <span x-text="startItem"></span> to <span x-text="endItem"></span> of <span x-text="filtered.length"></span> entries
            </div>
            <div class="flex items-center gap-2">
                <button @click="prevPage" :disabled="page === 1" class="rounded-lg border px-3 py-2 text-sm disabled:opacity-50 dark:border-gray-700 dark:text-white">Prev</button>
                <template x-for="p in displayedPages" :key="p">
                    <button x-show="p !== '...'" @click="goToPage(p)" :class="page === p ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-blue-50 dark:text-gray-400'" class="flex h-8 w-8 items-center justify-center rounded-lg text-sm font-medium" x-text="p"></button>
                    <span x-show="p === '...'" class="flex h-8 w-8 items-center justify-center text-gray-500">...</span>
                </template>
                <button @click="nextPage" :disabled="page === totalPages" class="rounded-lg border px-3 py-2 text-sm disabled:opacity-50 dark:border-gray-700 dark:text-white">Next</button>
            </div>
        </div>
    </div>
</div>
<x-modal id="show-kandidat" title="Detail Informasi Kandidat" :showFooter="false">
    <div class="p-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Nama Lengkap</label>
                <p id="show-nama" class="mt-1 text-lg font-medium text-gray-900 dark:text-white">-</p>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Status Saat Ini</label>
                <div class="mt-1">
                    <span id="show-status-badge" class="inline-flex rounded-full px-3 py-1 text-xs font-medium"></span>
                </div>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Posisi yang Dilamar</label>
                <p id="show-posisi" class="mt-1 text-gray-700 dark:text-gray-300">-</p>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tanggal Melamar</label>
                <p id="show-tanggal" class="mt-1 text-gray-700 dark:text-gray-300">-</p>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Sumber Informasi</label>
                <p id="show-sumber" class="mt-1 text-gray-700 dark:text-gray-300">-</p>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">ID Kandidat</label>
                <p id="show-id" class="mt-1 font-mono text-sm text-gray-500"># -</p>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <button type="button"
                @click="window.dispatchEvent(new CustomEvent('close-modal', { detail: { id: 'show-kandidat' } }))"
                class="rounded-lg bg-gray-100 px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                Tutup
            </button>
        </div>
        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row justify-between items-center text-xs text-gray-500 gap-2">
            <p>Dibuat pada: <span id="show-created-at">-</span></p>
            <p>Terakhir diperbarui: <span id="show-updated-at">-</span></p>
        </div>
    </div>
</x-modal>

<x-modal id="add-kandidat" title="Tambah Kandidat Baru" :showFooter="false">
    <div class="p-6">
        <div class="space-y-4">
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Nama Lengkap</label>
                <input type="text" id="add-nama" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" placeholder="Nama kandidat" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Posisi</label>
                    <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                    <select id="add-posisi_id" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                            <option value="">-- Pilih Posisi --</option>
                        @foreach($posisis as $posisi)
                            <option value="{{ $posisi->id_posisi }}">{{ $posisi->nama_posisi }}</option>
                        @endforeach
                    </select>
                    <span
                                    class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                                    <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                        </span>
                    </div>
                </div>
                <div>
                    <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Tanggal Melamar</label>
                    <div class="relative">
                    <input type="date" id="add-tanggal_melamar" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" onclick="this.showPicker()" />
                            <span class="absolute top-1/2 right-3.5 -translate-y-1/2 pointer-events-none">
                                <svg class="fill-gray-700 dark:fill-gray-400" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.33317 0.0830078C4.74738 0.0830078 5.08317 0.418794 5.08317 0.833008V1.24967H8.9165V0.833008C8.9165 0.418794 9.25229 0.0830078 9.6665 0.0830078C10.0807 0.0830078 10.4165 0.418794 10.4165 0.833008V1.24967L11.3332 1.24967C12.2997 1.24967 13.0832 2.03318 13.0832 2.99967V4.99967V11.6663C13.0832 12.6328 12.2997 13.4163 11.3332 13.4163H2.6665C1.70001 13.4163 0.916504 12.6328 0.916504 11.6663V4.99967V2.99967C0.916504 2.03318 1.70001 1.24967 2.6665 1.24967L3.58317 1.24967V0.833008C3.58317 0.418794 3.91896 0.0830078 4.33317 0.0830078ZM4.33317 2.74967H2.6665C2.52843 2.74967 2.4165 2.8616 2.4165 2.99967V4.24967H11.5832V2.99967C11.5832 2.8616 11.4712 2.74967 11.3332 2.74967H9.6665H4.33317ZM11.5832 5.74967H2.4165V11.6663C2.4165 11.8044 2.52843 11.9163 2.6665 11.9163H11.3332C11.4712 11.9163 11.5832 11.8044 11.5832 11.6663V5.74967Z" fill="" />
                                </svg>
                            </span>
                    </div>
                </div>
            </div>
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Sumber Info</label>
                <input type="text" id="add-sumber" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" placeholder="LinkedIn, Glints, dll" />
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-8">
            <button type="button" @click="window.dispatchEvent(new CustomEvent('close-modal', { detail: { id: 'add-kandidat' } }))" class="rounded-lg border border-gray-300 px-5 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400">Batal</button>
            <button id="save-add" class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-medium text-white hover:bg-blue-700">Simpan Kandidat</button>
        </div>
    </div>
</x-modal>

<x-modal id="edit-kandidat" title="Update Status Kandidat" :showFooter="false">
    <div class="p-6">
        <input type="hidden" id="edit-id">
        <div class="space-y-4">
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Nama Lengkap</label>
                <input type="text" id="edit-nama" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Posisi</label>
                    <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                    <select id="edit-posisi_id" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                        @foreach($posisis as $posisi)
                            <option value="{{ $posisi->id_posisi }}">{{ $posisi->nama_posisi }}</option>
                        @endforeach
                    </select>
                    <span
                                    class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                                    <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                        </span>
                    </div>
                </div>
                <div>
                    <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Status Akhir</label>
                    <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                    <select id="edit-status_akhir" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                        <!-- <option value="Masuk">Masuk</option> -->
                        <option value="CV Lolos">CV Lolos</option>
                        <option value="Psikotes Lolos">Psikotes Lolos</option>
                        <option value="Tes Kompetensi Lolos">Tes Kompetensi Lolos</option>
                        <option value="Interview HR Lolos">Interview HR Lolos</option>
                        <option value="Interview User Lolos">Interview User Lolos</option>
                        <option value="Diterima">Diterima</option>
                        <option value="Tidak Lolos">Tidak Lolos</option>
                    </select>
                    <span
                                    class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                                    <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-8">
            <button type="button" @click="window.dispatchEvent(new CustomEvent('close-modal', { detail: { id: 'edit-kandidat' } }))" class="rounded-lg border border-gray-300 px-5 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400">Batal</button>
            <button id="save-edit" class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-medium text-white hover:bg-blue-700">Simpan Perubahan</button>
        </div>
    </div>
</x-modal>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function kandidatTable() {
    return {
        // Gunakan koleksi lengkap untuk pencarian client-side yang lancar
        data: @json($kandidats instanceof \Illuminate\Pagination\LengthAwarePaginator ? $kandidats->items() : $kandidats),
        search: '',
        page: 1,
        perPage: 10,
        sortCol: 'nama',
        sortAsc: true,

        resetPage() { this.page = 1; },

        getStatusBadgeClass(status) {
            const s = status ? status.toLowerCase() : '';
            if (s.includes('diterima')) return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
            if (s.includes('tidak lolos')) return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
            if (s.includes('cv lolos')) return 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
            return 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
        },

        get filtered() {
            let filtered = this.data.filter(d =>
                (d.nama && d.nama.toLowerCase().includes(this.search.toLowerCase())) ||
                (d.posisi && d.posisi.nama_posisi.toLowerCase().includes(this.search.toLowerCase())) ||
                (d.status_akhir && d.status_akhir.toLowerCase().includes(this.search.toLowerCase()))
            );

            filtered.sort((a, b) => {
                let valA = (a[this.sortCol] || '').toString().toLowerCase();
                let valB = (b[this.sortCol] || '').toString().toLowerCase();
                if (valA < valB) return this.sortAsc ? -1 : 1;
                if (valA > valB) return this.sortAsc ? 1 : -1;
                return 0;
            });
            return filtered;
        },

        sort(col) {
            if (this.sortCol === col) { this.sortAsc = !this.sortAsc; }
            else { this.sortCol = col; this.sortAsc = true; }
        },

        get totalPages() { return Math.max(1, Math.ceil(this.filtered.length / this.perPage)); },
        get paginated() {
            const start = (this.page - 1) * this.perPage;
            return this.filtered.slice(start, start + this.perPage);
        },

        prevPage() { if (this.page > 1) this.page--; },
        nextPage() { if (this.page < this.totalPages) this.page++; },
        goToPage(p) { if (typeof p === 'number') this.page = p; },

        get displayedPages() {
            const range = [];
            for (let i = 1; i <= this.totalPages; i++) {
                if (i === 1 || i === this.totalPages || (i >= this.page - 1 && i <= this.page + 1)) {
                    range.push(i);
                } else if (range[range.length - 1] !== '...') {
                    range.push('...');
                }
            }
            return range;
        },

        get startItem() { return this.filtered.length === 0 ? 0 : (this.page - 1) * this.perPage + 1; },
        get endItem() { return Math.min(this.page * this.perPage, this.filtered.length); },

        // Letakkan di dalam fungsi kandidatTable() { return { ... } }
        openShowModal(row) {
            const formatDate = (dateString) => {
                if (!dateString) return '-';
                const date = new Date(dateString);
                return new Intl.DateTimeFormat('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false,
                    timeZone: 'Asia/Jakarta'
                }).format(date).replace(/\./g, ':'); // Mengubah titik menjadi titik dua untuk jam
            };
                    // Isi data ke elemen modal
            document.getElementById('show-id').innerText = '#' + row.id_kandidat;
            document.getElementById('show-nama').innerText = row.nama;
            document.getElementById('show-posisi').innerText = row.posisi ? row.posisi.nama_posisi : '-';
            document.getElementById('show-sumber').innerText = row.sumber || '-';
            document.getElementById('show-tanggal').innerText = row.tanggal_melamar || '-';
            document.getElementById('show-created-at').innerText = formatDate(row.created_at);
            document.getElementById('show-updated-at').innerText = formatDate(row.updated_at);

            // Set badge status
            const badge = document.getElementById('show-status-badge');
            badge.innerText = row.status_akhir || 'CV Lolos';

            // Gunakan fungsi css class yang sudah Anda buat sebelumnya
            badge.className = 'inline-flex rounded-full px-3 py-1 text-xs font-medium ' + this.getStatusBadgeClass(row.status_akhir);

            // Buka modal
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'show-kandidat' } }));
        },
        openEditModal(row) {
            document.getElementById('edit-id').value = row.id_kandidat;
            document.getElementById('edit-nama').value = row.nama;
            document.getElementById('edit-posisi_id').value = row.posisi_id;
            document.getElementById('edit-status_akhir').value = row.status_akhir || 'CV Lolos';
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'edit-kandidat' } }));
        },

        confirmDelete(row) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            Swal.fire({
                title: 'Hapus Kandidat?',
                text: `Yakin ingin menghapus: ${row.nama}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const res = await fetch(`/rekrutmen/kandidat/${row.id_kandidat}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            }
                        });
                        if (res.ok) {
                            Swal.fire('Berhasil', 'Data dihapus', 'success').then(() => location.reload());
                        } else {
                            throw new Error('Gagal menghapus');
                        }
                    } catch (error) {
                        Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                    }
                }
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Action Simpan (Tambah)
    document.getElementById('save-add').onclick = async () => {
        const payload = {
            nama: document.getElementById('add-nama').value,
            posisi_id: document.getElementById('add-posisi_id').value,
            tanggal_melamar: document.getElementById('add-tanggal_melamar').value,
            sumber: document.getElementById('add-sumber').value,
        };

        const res = await fetch("{{ route('rekrutmen.kandidat.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        if (res.ok) {
            Swal.fire('Berhasil', 'Kandidat ditambahkan', 'success').then(() => location.reload());
        } else {
            const err = await res.json();
            Swal.fire('Gagal', err.message || 'Cek kembali isian formulir', 'error');
        }
    };

    // Action Update (Edit)
    document.getElementById('save-edit').onclick = async () => {
        const id = document.getElementById('edit-id').value;
        const payload = {
            nama: document.getElementById('edit-nama').value,
            posisi_id: document.getElementById('edit-posisi_id').value,
            status_akhir: document.getElementById('edit-status_akhir').value,
        };

        const res = await fetch(`/rekrutmen/kandidat/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        if (res.ok) {
            Swal.fire('Berhasil', 'Data diperbarui', 'success').then(() => location.reload());
        } else {
            const err = await res.json();
            Swal.fire('Gagal', err.message || 'Gagal memperbarui data', 'error');
        }
    };
});
</script>
@endsection
