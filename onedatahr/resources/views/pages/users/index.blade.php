@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <!-- HEADER -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                User Management
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">
                Kelola seluruh data pengguna sistem
            </p>
        </div>

        <a href="{{ route('users.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4v16m8-8H4"/>
            </svg>
            Add New User
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

    @php
        $tableData = $users->map(function($u) {
            return [
                'id'         => $u->id,
                'name'       => $u->name ?? '',
                'nik'        => $u->nik ?? '-',
                'email'      => $u->email ?? '-',
                'jabatan'    => $u->jabatan ?? '-',
                'role'       => ucfirst($u->role ?? 'staff'),
                'created_at' => $u->created_at ? $u->created_at->format('d M Y') : '-',
            ];
        })->values()->toArray(); // Pastikan jadi array murni
    @endphp

    <!-- TABLE -->
    <div x-data="usersTable()" class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

        <!-- TOP BAR -->
        <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4">
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">Show</span>

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

                <span class="text-sm text-gray-500 dark:text-gray-400">entries</span>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <button
                    x-show="selected.length > 0"
                    @click="confirmBatchDelete"
                    class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-5 py-2.5 text-center text-white font-medium hover:bg-red-700 transition shadow-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete (<span x-text="selected.length"></span>)
                </button>
                <div class="relative">
                    <button class="absolute text-gray-500 -translate-y-1/2 left-4 top-1/2 dark:text-gray-400">
                        <svg class="h-5 w-5 fill-current" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M3.04199 9.37363C3.04199 5.87693 5.87735 3.04199 9.37533 3.04199C12.8733 3.04199 15.7087 5.87693 15.7087 9.37363C15.7087 12.8703 12.8733 15.7053 9.37533 15.7053C5.87735 15.7053 3.04199 12.8703 3.04199 9.37363ZM9.37533 1.54199C5.04926 1.54199 1.54199 5.04817 1.54199 9.37363C1.54199 13.6991 5.04926 17.2053 9.37533 17.2053C11.2676 17.2053 13.0032 16.5344 14.3572 15.4176L17.1773 18.238C17.4702 18.5309 17.945 18.5309 18.2379 18.238C18.5308 17.9451 18.5309 17.4703 18.238 17.1773L15.4182 14.3573C16.5367 13.0033 17.2087 11.2669 17.2087 9.37363C17.2087 5.04817 13.7014 1.54199 9.37533 1.54199Z"/>
                        </svg>
                    </button>
                    <!-- Input -->
                    <input
                        x-model="search"
                        @input="resetPage"
                        type="text"
                        placeholder="Search..."
                        class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-12 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 xl:w-[300px]"
                    />
                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="max-w-full overflow-x-auto">
            <table class="w-full min-w-full">
                <thead>
                    <tr class="border-y border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th class="px-4 py-3 text-center w-10">
                            <input type="checkbox" @change="toggleAll" :checked="isAllSelected" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </th>
                        <th @click="sortBy('name')" class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer hover:text-blue-600 transition">
                            <div class="flex items-center gap-1">
                                Name
                                <svg :class="sortCol === 'name' ? (sortDir === 'asc' ? 'rotate-0' : 'rotate-180') : 'opacity-20'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </div>
                        </th>
                        <th @click="sortBy('nik')" class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer hover:text-blue-600 transition">
                            <div class="flex items-center gap-1">
                                NIK
                                <svg :class="sortCol === 'nik' ? (sortDir === 'asc' ? 'rotate-0' : 'rotate-180') : 'opacity-20'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </div>
                        </th>
                        <th @click="sortBy('email')" class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer hover:text-blue-600 transition">
                            <div class="flex items-center gap-1">
                                Email
                                <svg :class="sortCol === 'email' ? (sortDir === 'asc' ? 'rotate-0' : 'rotate-180') : 'opacity-20'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </div>
                        </th>
                        <th @click="sortBy('jabatan')" class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer hover:text-blue-600 transition">
                            <div class="flex items-center gap-1">
                                Jabatan
                                <svg :class="sortCol === 'jabatan' ? (sortDir === 'asc' ? 'rotate-0' : 'rotate-180') : 'opacity-20'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </div>
                        </th>
                        <th @click="sortBy('role')" class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer hover:text-blue-600 transition">
                            <div class="flex items-center gap-1">
                                Role
                                <svg :class="sortCol === 'role' ? (sortDir === 'asc' ? 'rotate-0' : 'rotate-180') : 'opacity-20'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </div>
                        </th>
                        <th @click="sortBy('created_at')" class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer hover:text-blue-600 transition">
                            <div class="flex items-center gap-1">
                                Created At
                                <svg :class="sortCol === 'created_at' ? (sortDir === 'asc' ? 'rotate-0' : 'rotate-180') : 'opacity-20'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </div>
                        </th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="row in paginated" :key="row.id">
                        <tr>
                            <td class="px-4 py-3 text-center">
                                <input type="checkbox" :value="row.id" x-model="selected" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="shrink-0 w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-sm font-semibold text-gray-700 dark:text-white">
                                        <span x-text="(row.name || 'U').split(' ')[0].charAt(0)"></span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white" x-text="row.name"></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400" x-text="row.email || 'no email'"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400" x-text="row.nik || '-' "></td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400" x-text="row.email || '-' "></td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400" x-text="row.jabatan || '-' "></td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400" x-text="row.role || '-' "></td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400" x-text="row.created_at || '-' "></td>
                            <td class="px-4 py-3 text-sm font-medium text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a :href="'{{ route('users.index') }}/' + row.id"
                                    class="inline-flex items-center justify-center rounded-lg bg-blue-50 p-2 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/40 transition">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>

                                    <a :href="'{{ route('users.index') }}/' + row.id + '/edit'"
                                    class="inline-flex items-center justify-center rounded-lg bg-yellow-50 p-2 text-yellow-600 hover:bg-yellow-100 dark:bg-yellow-900/20 dark:text-yellow-400 dark:hover:bg-yellow-900/40 transition">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    <form :action="'{{ route('users.index') }}/' + row.id" method="POST" class="inline" :id="'delete-form-' + row.id">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                @click="if(confirm('Are you sure?')) $el.closest('form').submit()"
                                                class="inline-flex items-center justify-center rounded-lg bg-red-50 p-2 text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 transition">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <tr x-show="paginated.length === 0">
                        <td colspan="8" class="py-6 text-center text-gray-500">
                            No users found
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="flex items-center justify-between px-6 py-4">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Showing <span x-text="startItem"></span> to <span x-text="endItem"></span> of <span x-text="filtered.length"></span> entries
            </div>

            <div class="flex items-center gap-2">
                <button @click="prevPage" :disabled="page === 1" class="rounded-lg border px-3 py-2 text-sm dark:text-white disabled:opacity-50">Prev</button>

                <template x-for="p in displayedPages" :key="p">
                    <button x-show="p !== '...'" @click="goToPage(p)" :class="page === p ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-blue-500/[0.08] hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-500'" class="flex h-8 w-8 items-center justify-center rounded-lg text-theme-sm font-medium" x-text="p"></button>
                    <span x-show="p === '...'" class="flex h-8 w-8 items-center justify-center text-gray-500">...</span>
                </template>

                <button @click="nextPage" :disabled="page === totalPages" class="rounded-lg border px-3 py-2 text-sm dark:text-white disabled:opacity-50">Next</button>
            </div>
        </div>
    </div>

    <!-- Global delete confirmation modal (reusable) -->
    <x-modal id="delete-confirm" size="sm" title="Konfirmasi Hapus" closeLabel="Batal" confirmLabel="Hapus">
        <p class="text-sm text-gray-600">Gunakan tombol <strong>Hapus</strong> untuk mengonfirmasi penghapusan user yang dipilih.</p>
    </x-modal>
</div>

<script type="application/json" id="users-data">
    {!! json_encode($tableData) !!}
</script>

<script>
function usersTable() {
    return {
        data: JSON.parse(document.getElementById('users-data').textContent),
        search: '',
        page: 1,
        perPage: 10,
        sortCol: 'name',
        sortDir: 'asc',
        selected: [],

        resetPage() {
            this.page = 1;
            this.selected = [];
        },

        toggleAll() {
            if (this.isAllSelected) {
                this.selected = [];
            } else {
                this.selected = this.paginated.map(row => row.id);
            }
        },

        get isAllSelected() {
            return this.paginated.length > 0 && this.paginated.every(row => this.selected.includes(row.id));
        },

        confirmBatchDelete() {
            if (confirm(`Delete ${this.selected.length} selected users?`)) {
                let form = document.createElement('form');
                form.action = "{{ route('users.batchDelete') }}";
                form.method = 'POST';
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="ids" value="${this.selected.join(',')}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        },

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
                    Object.values(d).join(' ').toLowerCase().includes(q)
                );
            }

            return filteredData.sort((a, b) => {
                let aVal = a[this.sortCol] || '';
                let bVal = b[this.sortCol] || '';

                if (typeof aVal === 'string') aVal = aVal.toLowerCase();
                if (typeof bVal === 'string') bVal = bVal.toLowerCase();

                if (aVal < bVal) return this.sortDir === 'asc' ? -1 : 1;
                if (aVal > bVal) return this.sortDir === 'asc' ? 1 : -1;
                return 0;
            });
        },

        get totalPages() {
            return Math.max(1, Math.ceil(this.filtered.length / this.perPage));
        },

        get paginated() {
            const start = (this.page - 1) * this.perPage;
            return this.filtered.slice(start, start + this.perPage);
        },

        prevPage() { if (this.page > 1) this.page--; },
        nextPage() { if (this.page < this.totalPages) this.page++; },
        goToPage(p) { if (typeof p === 'number' && p >= 1 && p <= this.totalPages) this.page = p; },

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

        get startItem() {
            if (this.filtered.length === 0) return 0;
            return (this.page - 1) * this.perPage + 1;
        },

        get endItem() {
            return Math.min(this.page * this.perPage, this.filtered.length);
        }
    }
}
</script>
@endsection
