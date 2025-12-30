@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Manajemen Posisi
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">
                Kelola daftar posisi rekrutmen
            </p>
        </div>

        <button
            @click="window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'add-posisi' } }))"
            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Posisi
        </button>
    </div>

    <div x-data="posisiTable()" class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

        <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4">
           <div class="flex items-center gap-2">
            <label class="text-sm text-gray-500 dark:text-gray-400">Show</label>

                <div class="relative z-20 w-20">
                    <select
                        x-model.number="perPage"
                        @change="resetPage"
                        class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-9 text-sm text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    >
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
                    <input
                        x-model="search"
                        @input="resetPage"
                        type="text"
                        placeholder="Search positions..."
                        class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-12 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 xl:w-[300px]"
                    />
                </div>
            </div>
        </div>

        <div class="max-w-full overflow-x-auto">
            <table class="w-full min-w-full border-collapse">
                <thead>
                    <tr class="border-y border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">#</th>

                        <th @click="sort('nama_posisi')" class="cursor-pointer px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400 hover:text-blue-600 transition">
                            <div class="flex items-center gap-1">
                                Nama Posisi
                                <svg class="h-4 w-4" :class="sortCol === 'nama_posisi' ? (sortAsc ? '' : 'rotate-180') : 'opacity-20'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </th>

                        <th @click="sort('status')" class="cursor-pointer px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400 hover:text-blue-600 transition">
                            <div class="flex items-center gap-1">
                                Status
                                <svg class="h-4 w-4" :class="sortCol === 'status' ? (sortAsc ? '' : 'rotate-180') : 'opacity-20'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">
                            Progress Rekrutmen
                        </th>
                        <th class="px-6 py-3 text-right text-md font-medium text-gray-600 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="(row, index) in paginated" :key="row.id_posisi">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20 transition">
                            <td class="px-6 py-4 text-md text-gray-500 dark:text-gray-400" x-text="startItem + index"></td>
                            <td class="px-6 py-4">
                                <div class="text-md font-medium text-gray-900 dark:text-white" x-text="row.nama_posisi"></div>
                            </td>
                            <td class="px-6 py-4 text-md">
                                <span :class="row.status === 'Aktif' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'"
                                      class="inline-flex rounded-full px-3 py-1 text-xs font-medium"
                                      x-text="row.status">
                                </span>
                            </td>
                            <td class="px-6 py-4 text-md">
                                <div class="flex flex-col">
                                    <span x-text="row.progress_rekrutmen"
                                        :class="{
                                            'text-blue-600 bg-blue-50 dark:bg-blue-900/20 dark:text-blue-400': row.progress_rekrutmen === 'Interview HR' || row.progress_rekrutmen === 'Interview User',
                                            'text-green-600 bg-green-50 dark:bg-green-900/20 dark:text-green-400': row.progress_rekrutmen === 'Pemberkasan',
                                            'text-gray-500 bg-gray-50 dark:bg-gray-800 dark:text-gray-400': row.progress_rekrutmen === 'Menerima Kandidat',
                                            'text-orange-600 bg-orange-50 dark:bg-orange-900/20 dark:text-orange-400': row.progress_rekrutmen === 'Pre Interview'
                                        }"
                                        class="inline-flex w-fit rounded-lg px-2 py-0.5 text-xs font-semibold">
                                    </span>
                                    <span class="mt-1 text-xs text-gray-400" x-text="row.total_pelamar + ' Pelamar Aktif'"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openEditModal(row)" class="inline-flex items-center justify-center rounded-lg bg-yellow-50 p-2 text-yellow-600 hover:bg-yellow-100 dark:bg-yellow-900/20 transition" title="Edit">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button @click="confirmDelete(row)" class="inline-flex items-center justify-center rounded-lg bg-red-50 p-2 text-red-600 hover:bg-red-100 dark:bg-red-900/20 transition" title="Hapus">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="paginated.length === 0">
                        <td colspan="5" class="py-12 text-center text-gray-500">Data tidak ditemukan</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Showing <span x-text="startItem"></span> to <span x-text="endItem"></span> of <span x-text="filtered.length"></span> entries
            </div>

            <div class="flex items-center gap-2">
                <button @click="prevPage" :disabled="page === 1" class="rounded-lg border px-3 py-2 text-sm disabled:opacity-50 hover:bg-gray-50 dark:border-gray-700 dark:text-white">Prev</button>

                <template x-for="p in displayedPages" :key="p">
                    <button x-show="p !== '...'" @click="goToPage(p)" :class="page === p ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-blue-50 dark:text-gray-400'" class="flex h-8 w-8 items-center justify-center rounded-lg text-sm font-medium" x-text="p"></button>
                    <span x-show="p === '...'" class="flex h-8 w-8 items-center justify-center text-gray-500">...</span>
                </template>

                <button @click="nextPage" :disabled="page === totalPages" class="rounded-lg border px-3 py-2 text-sm disabled:opacity-50 hover:bg-gray-50 dark:border-gray-700 dark:text-white">Next</button>
            </div>
        </div>
    </div>
</div>

<x-modal id="add-posisi" title="Tambah Posisi Baru" :showFooter="false">
    <div class="p-6">
        <div class="space-y-4">
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Nama Posisi</label>
                <input type="text" id="add-nama_posisi" placeholder="Contoh: HR Manager" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm outline-none focus:border-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
            </div>
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Status</label>
                <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                <select id="add-status" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
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
        <div class="flex justify-end gap-3 mt-8">
            <button
                type="button"
                @click="window.dispatchEvent(new CustomEvent('close-modal', { detail: { id: 'add-posisi' } }))"
                class="rounded-lg border border-gray-300 px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400">
                Batal
            </button>
            <button id="save-add" class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-medium text-white hover:bg-blue-700">Simpan</button>
        </div>
    </div>
</x-modal>

<x-modal id="edit-posisi" title="Update Posisi" :showFooter="false">
    <div class="p-6">
        <input type="hidden" id="edit-id">
        <div class="space-y-4">
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Nama Posisi</label>
                <input type="text" id="edit-nama_posisi" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm outline-none focus:border-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
            </div>
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Status</label>
                <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                <select id="edit-status" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
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
        <div class="flex justify-end gap-3 mt-8">
           <button
                type="button"
                @click="window.dispatchEvent(new CustomEvent('close-modal', { detail: { id: 'edit-posisi' } }))"
                class="rounded-lg border border-gray-300 px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400"
            >
                Batal
            </button>
            <button id="save-edit" class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-medium text-white hover:bg-blue-700">Simpan Perubahan</button>
        </div>
    </div>
</x-modal>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function posisiTable() {
    return {
        data: @json($posisis),
        search: '',
        page: 1,
        perPage: 10,
        sortCol: 'nama_posisi',
        sortAsc: true,

        resetPage() { this.page = 1; },

        get filtered() {
            let filtered = this.data.filter(d =>
                d.nama_posisi.toLowerCase().includes(this.search.toLowerCase()) ||
                d.status.toLowerCase().includes(this.search.toLowerCase())
                (d.progress_rekrutmen && d.progress_rekrutmen.toLowerCase().includes(this.search.toLowerCase()))
            );

            // Sorting logic
            filtered.sort((a, b) => {
                let valA = a[this.sortCol].toLowerCase();
                let valB = b[this.sortCol].toLowerCase();
                if (valA < valB) return this.sortAsc ? -1 : 1;
                if (valA > valB) return this.sortAsc ? 1 : -1;
                return 0;
            });

            return filtered;
        },

        sort(col) {
            if (this.sortCol === col) {
                this.sortAsc = !this.sortAsc;
            } else {
                this.sortCol = col;
                this.sortAsc = true;
            }
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

        // Modal triggers
        openEditModal(row) {
            document.getElementById('edit-id').value = row.id_posisi;
            document.getElementById('edit-nama_posisi').value = row.nama_posisi;
            document.getElementById('edit-status').value = row.status;
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'edit-posisi' } }));
        },

        confirmDelete(row) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            Swal.fire({
                title: 'Hapus Posisi?',
                text: `Yakin ingin menghapus: ${row.nama_posisi}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Hapus'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const res = await fetch(`/rekrutmen/posisi/${row.id_posisi}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': token }
                    });
                    if (res.ok) Swal.fire('Berhasil', 'Data dihapus', 'success').then(() => location.reload());
                }
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // CRUD AJAX handlers
    document.getElementById('save-add').onclick = async () => {
        const payload = {
            nama_posisi: document.getElementById('add-nama_posisi').value,
            status: document.getElementById('add-status').value
        };
        const res = await fetch("{{ route('rekrutmen.posisi.store') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify(payload)
        });
        if (res.ok) location.reload();
    };

    document.getElementById('save-edit').onclick = async () => {
        const id = document.getElementById('edit-id').value;
        const payload = {
            nama_posisi: document.getElementById('edit-nama_posisi').value,
            status: document.getElementById('edit-status').value
        };
        const res = await fetch(`/rekrutmen/posisi/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify(payload)
        });
        if (res.ok) location.reload();
    };
});
</script>
@endsection
