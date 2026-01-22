@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Materi TEMPA
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">
                Kelola dan unduh materi TEMPA
            </p>
        </div>

        @can('createTempaMateri')
        <a href="{{ route('tempa.materi.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Upload Materi
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

    {{-- Tabel Materi --}}
    @php
        // Menyiapkan data untuk Alpine.js
        $tableData = $materis->map(fn($row) => [
            'id' => $row->id_materi,
            'judul' => $row->judul_materi,
            'uploader' => $row->uploader->name ?? 'Unknown',
            'tanggal_upload' => $row->created_at->format('d M Y'),
            'file_path' => $row->file_materi,
            'download_url' => route('tempa.materi.download', $row->id_materi),
            'edit_url' => route('tempa.materi.edit', $row->id_materi),
            'destroy_url' => route('tempa.materi.destroy', $row->id_materi),
        ])->values();
    @endphp

    <div x-data="materiTable()" class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

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
                    placeholder="Cari Materi..."
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-12 pr-4 text-sm text-gray-800 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:text-white/90 xl:w-[300px]"
                />
            </div>
        </div>

        <div class="max-w-full overflow-x-auto">
            <table class="w-full min-w-full border-collapse">
                <thead>
                    <tr class="border-y border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">#</th>
                        <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">
                            <span class="inline-flex items-center gap-2">
                                Judul
                                <button @click="sortBy('judul')" class="hover:bg-gray-200 dark:hover:bg-gray-700 rounded p-1">
                                    <svg class="w-4 h-4" :class="sortField === 'judul' && sortDirection === 'asc' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                    </svg>
                                </button>
                            </span>
                        </th>
                        <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">
                            <span class="inline-flex items-center gap-2">
                                Uploaded By
                                <button @click="sortBy('uploader')" class="hover:bg-gray-200 dark:hover:bg-gray-700 rounded p-1">
                                    <svg class="w-4 h-4" :class="sortField === 'uploader' && sortDirection === 'asc' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                    </svg>
                                </button>
                            </span>
                        </th>
                        <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">
                            <span class="inline-flex items-center gap-2">
                                Tanggal Upload
                                <button @click="sortBy('tanggal_upload')" class="hover:bg-gray-200 dark:hover:bg-gray-700 rounded p-1">
                                    <svg class="w-4 h-4" :class="sortField === 'tanggal_upload' && sortDirection === 'asc' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                    </svg>
                                </button>
                            </span>
                        </th>
                        <th class="px-6 py-3 text-right text-md font-medium text-gray-600 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(row, index) in filtered.slice((page - 1) * perPage, page * perPage)" :key="row.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20">
                            <td class="px-6 py-4 text-md text-gray-500 dark:text-gray-400" x-text="getRowNumber(index)"></td>
                            <td class="px-6 py-4 text-md font-medium text-gray-900 dark:text-white" x-text="row.judul"></td>
                            <td class="px-6 py-4 text-md text-gray-600 dark:text-gray-300" x-text="row.uploader"></td>
                            <td class="px-6 py-4 text-md text-gray-600 dark:text-gray-300" x-text="row.tanggal_upload"></td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a :href="row.download_url"
                                       class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-3 py-1.5 text-xs font-medium text-white shadow hover:bg-green-700 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Download
                                    </a>
                                    @can('createTempaMateri')
                                    <a x-bind:href="row.edit_url"
                                       class="inline-flex items-center justify-center rounded-lg bg-yellow-50 p-2 text-yellow-600 hover:bg-yellow-100 dark:bg-yellow-900/20 dark:text-yellow-400 dark:hover:bg-yellow-900/40 transition" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" x-bind:action="row.destroy_url" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus materi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                    class="inline-flex items-center justify-center rounded-lg bg-red-50 p-2 text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 transition"
                                                    title="Hapus">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0H7m3-3h4a1 1 0 011 1v1H9V5a1 1 0 011-1z"/>
                                                </svg>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-between px-6 py-4">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Showing <span x-text="startItem"></span> to <span x-text="endItem"></span> of <span x-text="filtered.length"></span> entries
            </div>

            <div class="flex items-center gap-2">
                <button @click="prevPage" :disabled="page === 1" class="rounded-lg border px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white disabled:opacity-50">Prev</button>

                <template x-for="p in displayedPages" :key="p">
                    <button x-show="p !== '...'" @click="goToPage(p)" :class="page === p ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-blue-500/[0.08] hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-500'" class="flex h-8 w-8 items-center justify-center rounded-lg text-theme-sm font-medium" x-text="p"></button>
                    <span x-show="p === '...'" class="flex h-8 w-8 items-center justify-center text-gray-500">...</span>
                </template>

                <button @click="nextPage" :disabled="page === totalPages" class="rounded-lg border px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white disabled:opacity-50">Next</button>
            </div>
        </div>
    </div>
</div>

<script>
function materiTable() {
    return {
        data: @json($tableData),
        search: '',
        sortField: 'judul',
        sortDirection: 'asc',
        page: 1,
        perPage: 10,

        get filtered() {
            let filtered = this.data.filter(item => {
                return item.judul.toLowerCase().includes(this.search.toLowerCase()) ||
                       item.uploader.toLowerCase().includes(this.search.toLowerCase()) ||
                       item.tanggal_upload.toLowerCase().includes(this.search.toLowerCase());
            });

            // Sort data
            filtered.sort((a, b) => {
                let aVal = a[this.sortField];
                let bVal = b[this.sortField];

                if (this.sortField === 'tanggal_upload') {
                    aVal = new Date(aVal.split(' ').reverse().join('-'));
                    bVal = new Date(bVal.split(' ').reverse().join('-'));
                }

                if (aVal < bVal) return this.sortDirection === 'asc' ? -1 : 1;
                if (aVal > bVal) return this.sortDirection === 'asc' ? 1 : -1;
                return 0;
            });

            return filtered;
        },

        get totalPages() {
            return Math.max(1, Math.ceil(this.filtered.length / this.perPage));
        },

        get startItem() {
            return this.filtered.length === 0 ? 0 : (this.page - 1) * this.perPage + 1;
        },

        get endItem() {
            return Math.min(this.page * this.perPage, this.filtered.length);
        },

        get displayedPages() {
            const total = this.totalPages;
            const current = this.page;
            let pages = [];

            if (total <= 7) {
                for (let i = 1; i <= total; i++) pages.push(i);
            } else {
                pages.push(1);
                if (current > 4) pages.push('...');

                const start = Math.max(2, current - 1);
                const end = Math.min(total - 1, current + 1);
                for (let i = start; i <= end; i++) pages.push(i);

                if (current < total - 3) pages.push('...');
                pages.push(total);
            }

            return pages;
        },

        prevPage() {
            if (this.page > 1) this.page--;
        },

        nextPage() {
            if (this.page < this.totalPages) this.page++;
        },

        goToPage(p) {
            if (typeof p === 'number' && p >= 1 && p <= this.totalPages) this.page = p;
        },

        sortBy(field) {
            if (this.sortField === field) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortField = field;
                this.sortDirection = 'asc';
            }
            this.page = 1; // Reset to first page on sort
        },

        getRowNumber(index) {
            return (this.page - 1) * this.perPage + index + 1;
        }
    }
}
</script>
@endsection
