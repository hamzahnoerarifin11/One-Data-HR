@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Data Level Jabatan
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">
                Kelola data level/jenjang jabatan perusahaan
            </p>
        </div>

        <button
            onclick="openAddLevelModal()"
            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Level Jabatan
        </button>
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

    <div x-data="levelTable()" class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

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
                    placeholder="Cari level jabatan..."
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-12 pr-4 text-sm text-gray-800 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 xl:w-[300px]"
                />
            </div>
        </div>

        <div class="max-w-full overflow-x-auto">
            <table class="w-full min-w-full">
                <thead>
                    <tr class="border-y border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th @click="sortBy('id')" class="px-5 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer hover:text-blue-600">
                            <div class="flex items-center gap-1">
                                No
                                <svg :class="sortCol === 'id' ? (sortDir === 'asc' ? 'rotate-0' : 'rotate-180') : 'opacity-20'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </div>
                        </th>
                        <th @click="sortBy('name')" class="px-5 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer hover:text-blue-600">
                            <div class="flex items-center gap-1">
                                Nama Level Jabatan
                                <svg :class="sortCol === 'name' ? (sortDir === 'asc' ? 'rotate-0' : 'rotate-180') : 'opacity-20'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </div>
                        </th>
                        <th @click="sortBy('level_order')" class="px-5 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer hover:text-blue-600">
                            <div class="flex items-center gap-1">
                                Urutan
                                <svg :class="sortCol === 'level_order' ? (sortDir === 'asc' ? 'rotate-0' : 'rotate-180') : 'opacity-20'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </div>
                        </th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400">
                            Karyawan
                        </th>
                        <th @click="sortBy('created_at')" class="px-5 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer hover:text-blue-600">
                            <div class="flex items-center gap-1">
                                Tanggal Dibuat
                                <svg :class="sortCol === 'created_at' ? (sortDir === 'asc' ? 'rotate-0' : 'rotate-180') : 'opacity-20'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </div>
                        </th>
                        <th class="px-5 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-400">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="(row, index) in paginated" :key="row.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20 transition">
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400" x-text="startItem + index"></td>
                            <td class="px-5 py-4 text-sm font-medium text-gray-900 dark:text-white" x-text="row.name"></td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/20 dark:text-blue-400" x-text="row.level_order"></span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700 dark:bg-green-900/20 dark:text-green-400" x-text="row.pekerjaan_count + ' karyawan'"></span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400" x-text="row.created_at"></td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="openEditLevelModal(this)"
                                        :data-id="row.id"
                                        :data-name="row.name"
                                        :data-level-order="row.level_order"
                                        class="inline-flex items-center justify-center rounded-lg bg-yellow-50 p-2 text-yellow-600 hover:bg-yellow-100 dark:bg-yellow-900/20 dark:text-yellow-400 dark:hover:bg-yellow-900/40 transition" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <button onclick="openDeleteLevelModal(this)"
                                        :data-id="row.id"
                                        :data-name="row.name"
                                        :data-count="row.pekerjaan_count"
                                        class="inline-flex items-center justify-center rounded-lg bg-red-50 p-2 text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 transition"
                                        title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0H7m3-3h4a1 1 0 011 1v1H9V5a1 1 0 011-1z"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filtered.length === 0">
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                Belum ada data level jabatan ditemukan.
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
                    <button @click="goToPage(p)"
                            x-show="p !== '...'"
                            :class="page === p ? 'bg-blue-600 text-white' : 'hover:bg-blue-500/[0.08] hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-500'"
                            class="px-3 py-1 text-sm rounded-lg transition"
                            x-text="p"></button>
                    <span x-show="p === '...'" class="px-2 text-gray-400">...</span>
                </template>

                <button @click="nextPage" :disabled="page === totalPages" class="rounded-lg border px-3 py-1.5 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white disabled:opacity-50 transition">Next</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add/Edit -->
<div id="levelModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
    <div onclick="closeLevelModal()" class="absolute inset-0 bg-black/50 dark:bg-black/80"></div>

    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-md w-full mx-4 p-6">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white" id="levelModalTitle">Tambah Level Jabatan</h2>
        </div>

        <div id="levelModalAlert" class="mb-4 hidden rounded-lg border p-3 text-sm"></div>

        <form onsubmit="submitLevelForm(event)" class="space-y-4">
            <input type="hidden" id="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="levelId">

            <div>
                <label for="levelName" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Nama Level Jabatan <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="levelName"
                    placeholder="Misal: Manager, Supervisor, Staff"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    required
                />
                <div id="levelNameError" class="text-xs text-red-600 dark:text-red-400 mt-1 hidden"></div>
            </div>

            <div>
                <label for="levelOrder" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Urutan Level <span class="text-red-500">*</span>
                </label>
                <input
                    type="number"
                    id="levelOrder"
                    placeholder="Misal: 1, 2, 3"
                    min="1"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    required
                />
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Urutan digunakan untuk menentukan hierarki level (1 = tertinggi)</p>
                <div id="levelOrderError" class="text-xs text-red-600 dark:text-red-400 mt-1 hidden"></div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeLevelModal()" class="flex-1 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                    Batal
                </button>
                <button type="submit" id="submitLevelBtn" class="flex-1 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-blue-500 dark:hover:bg-blue-600 transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Delete Confirmation -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
    <div onclick="closeDeleteModal()" class="absolute inset-0 bg-black/50 dark:bg-black/80"></div>

    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-sm w-full mx-4 p-6">
        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/20">
            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0H7m3-3h4a1 1 0 011 1v1H9V5a1 1 0 011-1z"/>
            </svg>
        </div>

        <h3 class="mb-2 text-lg font-bold text-gray-900 dark:text-white">Hapus Level Jabatan</h3>
        <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
            Apakah Anda yakin ingin menghapus level jabatan <strong id="deleteModalName"></strong>? Tindakan ini tidak dapat dibatalkan.
        </p>

        <div id="deleteWarning" class="mb-4 hidden rounded-lg border border-yellow-200 bg-yellow-50 p-3 text-sm text-yellow-800 dark:border-yellow-900 dark:bg-yellow-900/20 dark:text-yellow-400">
            <p><strong>Perhatian:</strong> Level ini sedang digunakan oleh karyawan dan tidak dapat dihapus.</p>
        </div>

        <div class="flex gap-3">
            <button type="button" onclick="closeDeleteModal()" class="flex-1 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                Batal
            </button>
            <button type="button" id="confirmDeleteBtn" onclick="confirmDeleteLevel()" class="flex-1 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-red-500 dark:hover:bg-red-600 transition">
                Hapus
            </button>
        </div>
    </div>
</div>

<script>
// Table Data & Logic
function levelTable() {
    return {
        data: @json($levels),
        search: '',
        page: 1,
        perPage: 10,
        sortCol: 'level_order',
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
                    d.name.toLowerCase().includes(q)
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

        get paginated() {
            const start = (this.page - 1) * this.perPage;
            return this.filtered.slice(start, start + this.perPage);
        },

        get startItem() { return this.filtered.length === 0 ? 0 : (this.page - 1) * this.perPage + 1; },
        get endItem() { return Math.min(this.page * this.perPage, this.filtered.length); },
        get totalPages() { return Math.ceil(this.filtered.length / this.perPage); },

        get displayedPages() {
            const pages = [];
            const maxPages = 5;
            if (this.totalPages <= maxPages) {
                for (let i = 1; i <= this.totalPages; i++) pages.push(i);
            } else {
                if (this.page <= 3) {
                    for (let i = 1; i <= maxPages - 1; i++) pages.push(i);
                    pages.push('...');
                    pages.push(this.totalPages);
                } else if (this.page >= this.totalPages - 2) {
                    pages.push(1);
                    pages.push('...');
                    for (let i = this.totalPages - (maxPages - 2); i <= this.totalPages; i++) pages.push(i);
                } else {
                    pages.push(1);
                    pages.push('...');
                    for (let i = this.page - 1; i <= this.page + 1; i++) pages.push(i);
                    pages.push('...');
                    pages.push(this.totalPages);
                }
            }
            return pages;
        },

        prevPage() { if (this.page > 1) this.page--; },
        nextPage() { if (this.page < this.totalPages) this.page++; },
        goToPage(page) { if (typeof page === 'number') this.page = page; },
    };
}

// Modal Functions
function openAddLevelModal() {
    document.getElementById('levelId').value = '';
    document.getElementById('levelName').value = '';
    document.getElementById('levelOrder').value = '';
    document.getElementById('levelModalTitle').textContent = 'Tambah Level Jabatan';
    document.getElementById('levelModalAlert').classList.add('hidden');
    document.getElementById('levelNameError').classList.add('hidden');
    document.getElementById('levelOrderError').classList.add('hidden');
    document.getElementById('levelModal').classList.remove('hidden');
    document.getElementById('levelName').focus();
}

function openEditLevelModal(btn) {
    const levelId = btn.getAttribute('data-id');
    const levelName = btn.getAttribute('data-name');
    const levelOrder = btn.getAttribute('data-level-order');

    document.getElementById('levelId').value = levelId;
    document.getElementById('levelName').value = levelName;
    document.getElementById('levelOrder').value = levelOrder;
    document.getElementById('levelModalTitle').textContent = 'Edit Level Jabatan';
    document.getElementById('levelModalAlert').classList.add('hidden');
    document.getElementById('levelNameError').classList.add('hidden');
    document.getElementById('levelOrderError').classList.add('hidden');
    document.getElementById('levelModal').classList.remove('hidden');
    document.getElementById('levelName').focus();
}

function closeLevelModal() {
    document.getElementById('levelModal').classList.add('hidden');
}

async function submitLevelForm(event) {
    event.preventDefault();

    const levelId = document.getElementById('levelId').value;
    const name = document.getElementById('levelName').value.trim();
    const level_order = document.getElementById('levelOrder').value;
    const submitBtn = document.getElementById('submitLevelBtn');
    const alertDiv = document.getElementById('levelModalAlert');

    document.getElementById('levelNameError').classList.add('hidden');
    document.getElementById('levelOrderError').classList.add('hidden');

    if (!name) {
        document.getElementById('levelNameError').textContent = 'Nama level harus diisi';
        document.getElementById('levelNameError').classList.remove('hidden');
        return;
    }

    if (!level_order) {
        document.getElementById('levelOrderError').textContent = 'Urutan level harus diisi';
        document.getElementById('levelOrderError').classList.remove('hidden');
        return;
    }

    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Loading...';

    const method = levelId ? 'PUT' : 'POST';
    const url = levelId ? `/organization/level/${levelId}` : '/organization/level';

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.getElementById('_token').value,
            },
            body: JSON.stringify({ name, level_order: parseInt(level_order) }),
        });

        const data = await response.json();

        if (data.success) {
            alertDiv.className = 'mb-4 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800 dark:border-green-900 dark:bg-green-900/20 dark:text-green-400';
            alertDiv.textContent = '✓ ' + (levelId ? 'Level berhasil diperbarui!' : 'Level berhasil ditambahkan!');
            alertDiv.classList.remove('hidden');

            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            if (data.errors) {
                if (data.errors.name) {
                    document.getElementById('levelNameError').textContent = data.errors.name[0];
                    document.getElementById('levelNameError').classList.remove('hidden');
                }
                if (data.errors.level_order) {
                    document.getElementById('levelOrderError').textContent = data.errors.level_order[0];
                    document.getElementById('levelOrderError').classList.remove('hidden');
                }
            } else {
                alertDiv.className = 'mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800 dark:border-red-900 dark:bg-red-900/20 dark:text-red-400';
                alertDiv.textContent = data.message || 'Terjadi kesalahan';
                alertDiv.classList.remove('hidden');
            }
        }
    } catch (error) {
        alertDiv.className = 'mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800 dark:border-red-900 dark:bg-red-900/20 dark:text-red-400';
        alertDiv.textContent = 'Error: ' + error.message;
        alertDiv.classList.remove('hidden');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Simpan';
    }
}

function openDeleteLevelModal(btn) {
    const levelId = btn.getAttribute('data-id');
    const levelName = btn.getAttribute('data-name');
    const levelCount = parseInt(btn.getAttribute('data-count'));

    document.getElementById('deleteModalName').textContent = levelName;

    const deleteWarning = document.getElementById('deleteWarning');
    const confirmBtn = document.getElementById('confirmDeleteBtn');

    if (levelCount > 0) {
        deleteWarning.classList.remove('hidden');
        confirmBtn.disabled = true;
        confirmBtn.classList.add('disabled:opacity-50', 'disabled:cursor-not-allowed');
    } else {
        deleteWarning.classList.add('hidden');
        confirmBtn.disabled = false;
        confirmBtn.classList.remove('disabled:opacity-50', 'disabled:cursor-not-allowed');
    }

    window.currentDeleteId = levelId;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

async function confirmDeleteLevel() {
    const levelId = window.currentDeleteId;
    const confirmBtn = document.getElementById('confirmDeleteBtn');

    confirmBtn.disabled = true;
    confirmBtn.innerHTML = 'Loading...';

    try {
        const response = await fetch(`/organization/level/${levelId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.getElementById('_token').value,
            },
        });

        const data = await response.json();

        if (data.success) {
            alert('Level berhasil dihapus!');
            window.location.reload();
        } else {
            alert(data.message || 'Terjadi kesalahan saat menghapus');
        }
    } catch (error) {
        alert('Error: ' + error.message);
    } finally {
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = 'Hapus';
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.id === 'levelModal') closeLevelModal();
    if (e.target.id === 'deleteModal') closeDeleteModal();
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (!document.getElementById('levelModal').classList.contains('hidden')) closeLevelModal();
        if (!document.getElementById('deleteModal').classList.contains('hidden')) closeDeleteModal();
    }
});
</script>
@endsection
