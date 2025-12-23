@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Interview HR
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">
                Daftar hasil interview HR kandidat rekrutmen
            </p>
        </div>

        <a href="{{ route('rekrutmen.interview_hr.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Interview
        </a>
    </div>

    @php
        // Menyiapkan data untuk Alpine.js
        $tableData = $data->map(fn($row) => [
            'id'             => $row->id_interview_hr,
            'tanggal'        => $row->hari_tanggal,
            'nama_kandidat'  => $row->nama_kandidat,
            'posisi'         => $row->posisi_dilamar,
            'total'          => $row->total,
            'keputusan'      => $row->keputusan,
            'show_url'       => route('rekrutmen.interview_hr.show', $row->id_interview_hr),
            'edit_url'       => route('rekrutmen.interview_hr.edit', $row->id_interview_hr),
            'delete_url'     => route('rekrutmen.interview_hr.destroy', $row->id_interview_hr),
        ])->values();
    @endphp

    <div x-data="interviewTable()" class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        
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
                <span class="absolute text-gray-500 -translate-y-1/2 left-4 top-1/2">
                    <svg class="h-5 w-5 fill-current" viewBox="0 0 20 20"><path d="M9.37533 3.04199C5.87735 3.04199 3.04199 5.87693 3.04199 9.37363C3.04199 12.8703 5.87735 15.7053 9.37533 15.7053C12.8733 15.7053 15.7087 12.8703 15.7087 9.37363C15.7087 5.87693 12.8733 3.04199 9.37533 3.04199ZM1.54199 9.37363C1.54199 5.04817 5.04926 1.54199 9.37533 1.54199C13.7014 1.54199 17.2087 5.04817 17.2087 9.37363C17.2087 13.6991 13.7014 17.2053 9.37533 17.2053C5.04926 17.2053 1.54199 13.6991 1.54199 9.37363Z"/></svg>
                </span>
                <input
                    x-model="search"
                    @input="resetPage"
                    type="text"
                    placeholder="Cari kandidat..."
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-12 pr-4 text-sm text-gray-800 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:text-white/90 xl:w-[300px]"
                />
            </div>
        </div>

        <div class="max-w-full overflow-x-auto">
            <table class="w-full min-w-full">
                <thead>
                    <tr class="border-y border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th @click="sortBy('tanggal')" class="px-5 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer hover:text-blue-600">
                            Tanggal
                        </th>
                        <th @click="sortBy('nama_kandidat')" class="px-5 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer hover:text-blue-600">
                            Nama Kandidat
                        </th>
                        <th @click="sortBy('posisi')" class="px-5 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer hover:text-blue-600">
                            Posisi
                        </th>
                        <th @click="sortBy('total')" class="px-5 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer hover:text-blue-600">
                            Total Skor
                        </th>
                        <th class="px-5 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-400">
                            Keputusan
                        </th>
                        <th class="px-5 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-400">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="row in paginated" :key="row.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20 transition">
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400" x-text="row.tanggal"></td>
                            <td class="px-5 py-4 text-sm font-medium text-gray-900 dark:text-white" x-text="row.nama_kandidat"></td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400" x-text="row.posisi"></td>
                            <td class="px-5 py-4 text-center text-sm font-bold text-gray-700 dark:text-gray-300" x-text="row.total"></td>
                            <td class="px-5 py-4 text-center">
                                <template x-if="row.keputusan === 'DITERIMA'">
                                    <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">DITERIMA</span>
                                </template>
                                <template x-if="row.keputusan === 'DITOLAK'">
                                    <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-700 dark:bg-red-900/30 dark:text-red-400">DITOLAK</span>
                                </template>
                                <template x-if="row.keputusan !== 'DITERIMA' && row.keputusan !== 'DITOLAK'">
                                    <span class="inline-flex rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400" x-text="row.keputusan"></span>
                                </template>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a :href="row.show_url" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition dark:text-blue-400 dark:hover:bg-blue-900/20" title="Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a :href="row.edit_url" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition dark:text-yellow-400 dark:hover:bg-yellow-900/20" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                </div>
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
                <button @click="prevPage" :disabled="page === 1" class="rounded-lg border px-3 py-1.5 text-sm dark:text-white disabled:opacity-50">Prev</button>
                <template x-for="p in displayedPages" :key="p">
                    <button @click="goToPage(p)" :class="page === p ? 'bg-blue-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-800 dark:text-white'" class="px-3 py-1 text-sm rounded-lg" x-text="p"></button>
                </template>
                <button @click="nextPage" :disabled="page === totalPages" class="rounded-lg border px-3 py-1.5 text-sm dark:text-white disabled:opacity-50">Next</button>
            </div>
        </div>
    </div>
</div>

<script>
function interviewTable() {
    return {
        data: @json($tableData),
        search: '',
        page: 1,
        perPage: 10,
        sortCol: 'tanggal',
        sortDir: 'desc',

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
                    d.nama_kandidat.toLowerCase().includes(q) || 
                    d.posisi.toLowerCase().includes(q) ||
                    d.keputusan.toLowerCase().includes(q)
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