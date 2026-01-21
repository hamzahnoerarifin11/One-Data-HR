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
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-16 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                        <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </div>
                <span class="text-sm text-gray-500 dark:text-gray-400">entries</span>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">Search:</span>
                <input
                    type="text"
                    x-model="search"
                    @input="resetPage"
                    placeholder="Cari materi..."
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-64 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
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
                    <template x-for="(row, index) in paginatedData" :key="row.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20">
                            <td class="px-6 py-4 text-md text-gray-500 dark:text-gray-400" x-text="getRowNumber(index)"></td>
                            <td class="px-6 py-4 text-md font-medium text-gray-900 dark:text-white" x-text="row.judul"></td>
                            <td class="px-6 py-4 text-md text-gray-600 dark:text-gray-300" x-text="row.uploader"></td>
                            <td class="px-6 py-4 text-md text-gray-600 dark:text-gray-300" x-text="row.tanggal_upload"></td>
                            <td class="px-6 py-4 text-right">
                                <a :href="row.download_url"
                                   class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-3 py-1.5 text-xs font-medium text-white shadow hover:bg-green-700 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Download
                                </a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Showing <span x-text="getShowingStart()"></span> to <span x-text="getShowingEnd()"></span> of <span x-text="filteredData.length"></span> entries
            </div>

            <div class="flex items-center gap-2">
                <button
                    @click="prevPage"
                    :disabled="currentPage === 1"
                    class="disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-700">
                    Previous
                </button>

                <template x-for="page in getVisiblePages()" :key="page">
                    <button
                        @click="goToPage(page)"
                        :class="page === currentPage ? 'bg-blue-600 text-white' : 'hover:bg-gray-50 dark:hover:bg-gray-700'"
                        class="px-3 py-1 text-sm border border-gray-300 rounded dark:border-gray-600"
                        x-text="page">
                    </button>
                </template>

                <button
                    @click="nextPage"
                    :disabled="currentPage === totalPages"
                    class="disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-700">
                    Next
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function materiTable() {
    return {
        data: @json($tableData),
        search: '',
        sortField: 'tanggal_upload',
        sortDirection: 'desc',
        currentPage: 1,
        perPage: 10,

        get filteredData() {
            let filtered = this.data.filter(item => {
                return item.judul.toLowerCase().includes(this.search.toLowerCase()) ||
                       item.uploader.toLowerCase().includes(this.search.toLowerCase());
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

        get paginatedData() {
            const start = (this.currentPage - 1) * this.perPage;
            const end = start + this.perPage;
            return this.filteredData.slice(start, end);
        },

        get totalPages() {
            return Math.ceil(this.filteredData.length / this.perPage);
        },

        getVisiblePages() {
            const total = this.totalPages;
            const current = this.currentPage;
            const delta = 2;
            const range = [];
            const rangeWithDots = [];

            for (let i = Math.max(2, current - delta); i <= Math.min(total - 1, current + delta); i++) {
                range.push(i);
            }

            if (current - delta > 2) {
                rangeWithDots.push(1, '...');
            } else {
                rangeWithDots.push(1);
            }

            rangeWithDots.push(...range);

            if (current + delta < total - 1) {
                rangeWithDots.push('...', total);
            } else if (total > 1) {
                rangeWithDots.push(total);
            }

            return rangeWithDots.filter(item => item !== '...').filter((item, index, arr) => arr.indexOf(item) === index);
        },

        sortBy(field) {
            if (this.sortField === field) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortField = field;
                this.sortDirection = 'asc';
            }
            this.resetPage();
        },

        resetPage() {
            this.currentPage = 1;
        },

        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },

        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        },

        goToPage(page) {
            this.currentPage = page;
        },

        getRowNumber(index) {
            return (this.currentPage - 1) * this.perPage + index + 1;
        },

        getShowingStart() {
            return this.filteredData.length === 0 ? 0 : (this.currentPage - 1) * this.perPage + 1;
        },

        getShowingEnd() {
            return Math.min(this.currentPage * this.perPage, this.filteredData.length);
        }
    }
}
</script>
@endsection
