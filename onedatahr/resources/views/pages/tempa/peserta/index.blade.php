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

        @can('createTempaPeserta')
        <a href="{{ route('tempa.peserta.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Peserta
        </a>
        @endcan
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
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-12 pr-4 text-sm text-gray-800 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:text-white/90 xl:w-[300px]"
                />
            </div>
        </div>

        <div class="max-w-full overflow-x-auto">
            <table class="w-full min-w-full border-collapse">
                <thead>
                    <tr class="border-y border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
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
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400" x-text="(page - 1) * perPage + index + 1"></td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white" x-text="row.nama_peserta"></td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300" x-text="row.nik"></td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-medium"
                                    :class="{
                                        'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': row.status_val == 1,
                                        'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400': row.status_val == 2,
                                        'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': row.status_val != 1 && row.status_val != 2
                                    }"
                                    x-text="row.status_label">
                                </span>
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
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
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

<script>
function pesertaTable() {
    return {
        data: @json($tableData),
        search: '',
        page: 1,
        perPage: 10,
        sortCol: 'nama_peserta',
        sortDir: 'asc',

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
        get endItem() { return Math.min(this.page * this.perPage, this.filtered.length); }
    }
}
</script>
@endsection
