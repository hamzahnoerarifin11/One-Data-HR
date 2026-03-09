@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <!-- HEADER -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Data Onboarding Karyawan
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">
                Monitoring progres onboarding karyawan baru
            </p>
        </div>

        <a href="{{ route('onboarding.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2
                  text-sm font-medium text-white shadow hover:bg-blue-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Data
        </a>
    </div>

    <!-- ALERT -->
    @if(session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4
                    text-green-800 dark:border-green-900
                    dark:bg-green-900/20 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4
                    text-red-800 dark:border-red-900
                    dark:bg-red-900/20 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    @php
        $tableData = $onboardings->map(fn($row) => [
            'id'           => $row->id_onboarding,
            'nama'         => $row->kandidat?->nama ?? '-',
            'posisi'       => $row->posisi?->nama_posisi ?? '-',
            'jadwal_ttd'   => $row->jadwal_ttd_kontrak
                                ? date('d/m/Y', strtotime($row->jadwal_ttd_kontrak))
                                : '-',
            'status'       => strtoupper($row->status_onboarding),
            'show_url'     => route('onboarding.show', $row->id_onboarding),
            'edit_url'     => route('onboarding.edit', $row->id_onboarding),
            'delete_url'   => route('onboarding.destroy', $row->id_onboarding),
        ])->values();
    @endphp

    <!-- TABLE CARD -->
    <div x-data="onboardingTable()"
         class="rounded-xl border border-gray-200 bg-white
                dark:border-gray-800 dark:bg-white/[0.03]">

        <!-- FILTER -->
        <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4">
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">Show</span>
                <div class="relative z-20">
                    <select x-model.number="perPage" @change="resetPage"
                        class="h-11 w-20 appearance-none rounded-lg border
                               border-gray-300 bg-transparent px-4 py-2.5 pr-8
                               text-sm text-gray-800 outline-none
                               focus:border-blue-600 focus:ring-1 focus:ring-blue-600
                               dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500">
                        <svg class="fill-current" width="18" height="18" viewBox="0 0 20 20">
                            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293
                                     a1 1 0 111.414 1.414l-4 4a1 1 0
                                     01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                        </svg>
                    </span>
                </div>
                <span class="text-sm text-gray-500 dark:text-gray-400">entries</span>
            </div>

            <div class="relative">
                <button class="absolute text-gray-500 -translate-y-1/2 left-4 top-1/2">
                    <svg class="h-5 w-5 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.04199 9.37363C3.04199 5.87693 5.87735 3.04199 9.37533 3.04199C12.8733 3.04199 15.7087 5.87693 15.7087 9.37363C15.7087 12.8703 12.8733 15.7053 9.37533 15.7053C5.87735 15.7053 3.04199 12.8703 3.04199 9.37363ZM9.37533 1.54199C5.04926 1.54199 1.54199 5.04817 1.54199 9.37363C1.54199 13.6991 5.04926 17.2053 9.37533 17.2053C11.2676 17.2053 13.0032 16.5344 14.3572 15.4176L17.1773 18.238C17.4702 18.5309 17.945 18.5309 18.2379 18.238C18.5308 17.9451 18.5309 17.4703 18.238 17.1773L15.4182 14.3573C16.5367 13.0033 17.2087 11.2669 17.2087 9.37363C17.2087 5.04817 13.7014 1.54199 9.37533 1.54199Z"/></svg>
                </button>
                <input x-model="search" @input="resetPage" type="text"
                    placeholder="Cari kandidat / posisi / status..."
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent
                           py-2.5 pl-12 pr-4 text-sm text-gray-800
                           focus:border-blue-600 focus:ring-1 focus:ring-blue-600
                           dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
                           xl:w-[300px]">
            </div>
        </div>

        <!-- TABLE -->
        <div class="max-w-full overflow-x-auto">
            <table class="w-full min-w-full text-left">
                <thead>
                    <tr class="border-y border-gray-200 bg-gray-50
                               dark:border-gray-700 dark:bg-gray-900">
                        <th @click="sortBy('nama')"
                            class="px-5 py-3 text-sm font-medium text-gray-600
                                   dark:text-gray-400 cursor-pointer hover:text-blue-600 transition">
                            <div class="flex items-center gap-1">
                                Nama Karyawan
                                <svg :class="sortCol === 'nama'
                                    ? (sortDir === 'asc' ? 'rotate-0' : 'rotate-180')
                                    : 'opacity-20'"
                                    class="w-4 h-4 transition-transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2" d="M5 15l7-7 7 7"/>
                                </svg>
                            </div>
                        </th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-600 dark:text-gray-400">
                            Posisi
                        </th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-600
                                   dark:text-gray-400 text-center">
                            Jadwal TTD Kontrak
                        </th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-600
                                   dark:text-gray-400 text-center">
                            Status Onboarding
                        </th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-600
                                   dark:text-gray-400 text-center">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="row in paginated" :key="row.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20 transition">
                            <td class="px-5 py-4">
                                <p class="text-sm font-medium text-gray-900 dark:text-white"
                                   x-text="row.nama"></p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400"
                                   x-text="row.posisi"></p>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400"
                                      x-text="row.jadwal_ttd"></span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span :class="{
                                    'bg-gray-50 text-gray-600 dark:bg-gray-800 dark:text-gray-400': row.status === 'DRAFT',
                                    'bg-yellow-50 text-yellow-600 dark:bg-yellow-900/20 dark:text-yellow-400': row.status === 'PROGRESS',
                                    'bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400': row.status === 'SELESAI'
                                }"
                                class="inline-flex rounded-lg px-2 py-1 text-xs font-bold"
                                x-text="row.status"></span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a :href="row.show_url"
                                      class="inline-flex items-center justify-center rounded-lg bg-blue-50 p-2 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/40 transition" title="Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a :href="row.edit_url"
                                      class="inline-flex items-center justify-center rounded-lg bg-yellow-50 p-2 text-yellow-600 hover:bg-yellow-100 dark:bg-yellow-900/20 dark:text-yellow-400 dark:hover:bg-yellow-900/40 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form :action="row.delete_url" method="POST"
                                          @submit.prevent="confirm('Hapus data onboarding ini?') && $el.submit()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center justify-center rounded-lg bg-red-50 p-2 text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0H7m3-3h4a1 1 0 011 1v1H9V5a1 1 0 011-1z"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <template x-if="filtered.length === 0">
                        <tr>
                            <td colspan="5"
                                class="px-5 py-10 text-center text-sm
                                       text-gray-500 dark:text-gray-400">
                                Tidak ada data onboarding ditemukan.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="flex items-center justify-between px-6 py-4
                    border-t border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Showing <span x-text="startItem"></span>
                to <span x-text="endItem"></span>
                of <span x-text="filtered.length"></span> entries
            </div>

            <div class="flex items-center gap-2">
                <button @click="prevPage" :disabled="page === 1"
                    class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-100 dark:hover:bg-gray-700
                           dark:border-gray-700 dark:text-white disabled:opacity-50">
                    Prev
                </button>

                <template x-for="p in displayedPages" :key="p">
                    <button @click="goToPage(p)"
                        :class="page === p
                            ? 'bg-blue-600 text-white border-blue-600'
                            : 'border-gray-300 dark:border-gray-700 hover:bg-blue-500/[0.08] hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-500'"
                        class="px-3 py-1 text-sm rounded-lg border transition"
                        x-text="p"></button>
                </template>

                <button @click="nextPage" :disabled="page === totalPages"
                    class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-100 dark:hover:bg-gray-700
                           dark:border-gray-700 dark:text-white disabled:opacity-50">
                    Next
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function onboardingTable() {
    return {
        data: @json($tableData),
        search: '',
        page: 1,
        perPage: 10,
        sortCol: 'nama',
        sortDir: 'asc',

        resetPage() { this.page = 1 },
        sortBy(col) {
            this.sortCol === col
                ? this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc'
                : (this.sortCol = col, this.sortDir = 'asc')
        },

        get filtered() {
            let d = this.data
            if (this.search) {
                const q = this.search.toLowerCase()
                d = d.filter(r =>
                    r.nama.toLowerCase().includes(q) ||
                    r.posisi.toLowerCase().includes(q) ||
                    r.status.toLowerCase().includes(q)
                )
            }
            return d.sort((a,b) => {
                let x=a[this.sortCol], y=b[this.sortCol]
                return this.sortDir === 'asc'
                    ? x.localeCompare(y)
                    : y.localeCompare(x)
            })
        },

        get totalPages() { return Math.max(1, Math.ceil(this.filtered.length / this.perPage)) },
        get paginated() {
            const s=(this.page-1)*this.perPage
            return this.filtered.slice(s, s+this.perPage)
        },
        prevPage() { if(this.page>1) this.page-- },
        nextPage() { if(this.page<this.totalPages) this.page++ },
        goToPage(p) { this.page=p },

        get displayedPages() {
            return Array.from({length:this.totalPages},(_,i)=>i+1)
        },
        get startItem() {
            return this.filtered.length ? (this.page-1)*this.perPage+1 : 0
        },
        get endItem() {
            return Math.min(this.page*this.perPage, this.filtered.length)
        }
    }
}
</script>
@endsection
