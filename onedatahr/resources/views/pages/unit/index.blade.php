@extends('layouts.app')

@section('content')

<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <!-- HEADER -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Data Unit
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">
                Kelola data unit
            </p>
        </div>

        <a href="{{ route('unit.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Unit
        </a>

    </div>

    <!-- SUCCESS ALERT -->
    @if(session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800 dark:border-green-900 dark:bg-green-900/20 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    <!-- ERROR ALERT -->
    @if(session('error'))
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800 dark:border-red-900 dark:bg-red-900/20 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    <!-- TABLE -->
    <div x-data="unitTable()" class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

        <!-- TOP BAR -->
        <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4">
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">Tampilkan</span>

                <div x-data="{ isOptionSelected: false }" class="relative z-20">
                    <select
                        x-model.number="perPage"
                        @change="resetPage(); isOptionSelected = true"
                        class="h-11 w-20 appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-8 text-sm text-gray-800 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                        :class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                    >
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>

                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500 dark:text-gray-400">
                        <svg class="fill-current" width="18" height="18" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                        </svg>
                    </span>
                </div>

                <span class="text-sm text-gray-500 dark:text-gray-400">entri</span>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="relative">
                    <button class="absolute text-gray-500 -translate-y-1/2 left-4 top-1/2 dark:text-gray-400">
                        <svg class="h-5 w-5 fill-current" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <input
                        x-model="search"
                        type="text"
                        placeholder="Cari unit..."
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-12 pr-4 text-sm text-gray-800 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 lg:w-80"
                    />
                </div>
            </div>
        </div>

        <!-- TABLE CONTENT -->
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">
                            <div class="flex items-center gap-2">
                                <span>ID</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">
                            <div class="flex items-center gap-2">
                                <span>Nama Unit</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">
                            <div class="flex items-center gap-2">
                                <span>Departemen</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">
                            <div class="flex items-center gap-2">
                                <span>Divisi</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">
                            <div class="flex items-center gap-2">
                                <span>Perusahaan</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">
                            <div class="flex items-center gap-2">
                                <span>Dibuat</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="(row, index) in filtered.slice((page - 1) * perPage, page * perPage)" :key="row.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white" x-text="row.id"></td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white" x-text="row.name"></td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400" x-text="row.department_name"></td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400" x-text="row.division_name"></td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400" x-text="row.company_name"></td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400" x-text="row.created_at"></td>
                            <td class="px-6 py-4 text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <a :href="'/unit/' + row.id + '/edit'"
                                       class="inline-flex items-center gap-1 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                    <form :action="'/unit/' + row.id" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700 transition"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus unit ini?')">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="flex items-center justify-between px-6 py-4">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Menampilkan <span class="font-medium" x-text="startItem"></span> sampai <span class="font-medium" x-text="endItem"></span> dari <span class="font-medium" x-text="filtered.length"></span> entri
            </div>

            <div class="flex items-center gap-2">
                <button @click="prevPage" :disabled="page === 1" class="rounded-lg border border-gray-300 px-3 py-2 text-sm hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.05] disabled:opacity-50 disabled:cursor-not-allowed transition">
                    Sebelumnya
                </button>

                <template x-for="p in displayedPages" :key="p">
                    <button x-show="p !== '...'" @click="goToPage(p)" :class="page === p ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-blue-500/[0.08] hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-500'" class="flex h-8 w-8 items-center justify-center rounded-lg text-sm font-medium transition" x-text="p"></button>
                    <span x-show="p === '...'" class="flex h-8 w-8 items-center justify-center text-gray-500">...</span>
                </template>

                <button @click="nextPage" :disabled="page === totalPages" class="rounded-lg border border-gray-300 px-3 py-2 text-sm hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.05] disabled:opacity-50 disabled:cursor-not-allowed transition">
                    Selanjutnya
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function unitTable() {
    return {
        data: @json($units->map(fn($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'department_name' => $u->department->name ?? '-',
            'division_name' => $u->department?->division?->name ?? '-',
            'company_name' => $u->department?->division?->company?->name ?? '-',
            'created_at' => $u->created_at->format('d/m/Y')
        ])),
        search: '',
        sortField: 'name',
        sortDirection: 'asc',
        page: 1,
        perPage: 10,

        get filtered() {
            let filtered = this.data.filter(item => {
                return item.name.toLowerCase().includes(this.search.toLowerCase()) ||
                       item.department_name.toLowerCase().includes(this.search.toLowerCase()) ||
                       item.division_name.toLowerCase().includes(this.search.toLowerCase()) ||
                       item.company_name.toLowerCase().includes(this.search.toLowerCase());
            });

            // Sort data
            filtered.sort((a, b) => {
                let aVal = a[this.sortField];
                let bVal = b[this.sortField];

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
            this.page = 1;
        }
    }
}
</script>

@endsection
