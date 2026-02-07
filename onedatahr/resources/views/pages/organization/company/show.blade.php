@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <nav class="mb-6">
        <ol class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
            <li>
                <a href="{{ route('dashboard.index') }}" class="hover:text-blue-600 transition">Dashboard</a>
            </li>
            <li class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 01-1.414 1.414L7.293 14.707z" clip-rule="evenodd"/>
                </svg>
                <a href="{{ route('organization.company.index') }}" class="hover:text-blue-600 transition">Data Perusahaan</a>
            </li>
            <li class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 01-1.414 1.414L7.293 14.707z" clip-rule="evenodd"/>
                </svg>
                <span class="text-gray-900 dark:text-white">Detail Perusahaan</span>
            </li>
        </ol>
    </nav>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ $company->name }}
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">
                Informasi detail perusahaan dan struktur divisi terkait.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('organization.company.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow hover:bg-gray-50 transition dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
               <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
               Kembali
            </a>
            <!-- <a href="{{ route('organization.company.edit', $company->id) }}"
               class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Perusahaan
            </a> -->
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6">
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 italic">Informasi Umum</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Nama Perusahaan</label>
                    <p class="mt-1 text-base font-semibold text-gray-900 dark:text-white">{{ $company->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Holding Naungan</label>
                    <p class="mt-1 text-base text-gray-900 dark:text-white">
                        @if($company->holding)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                {{ $company->holding->name }}
                            </span>
                        @else
                            <span class="text-gray-400 italic text-sm">Tanpa Holding</span>
                        @endif
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Terdaftar Sejak</label>
                    <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $company->created_at->format('d F Y') }}</p>
                </div>
            </div>
        </div>

        <div x-data="divisionList()" class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar Divisi</h2>

                <div class="flex items-center gap-3">
                    <div class="relative">
                        <button class="absolute text-gray-500 -translate-y-1/2 left-4 top-1/2">
                            <svg class="h-5 w-5 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.04199 9.37363C3.04199 5.87693 5.87735 3.04199 9.37533 3.04199C12.8733 3.04199 15.7087 5.87693 15.7087 9.37363C15.7087 12.8703 12.8733 15.7053 9.37533 15.7053C5.87735 15.7053 3.04199 12.8703 3.04199 9.37363ZM9.37533 1.54199C5.04926 1.54199 1.54199 5.04817 1.54199 9.37363C1.54199 13.6991 5.04926 17.2053 9.37533 17.2053C11.2676 17.2053 13.0032 16.5344 14.3572 15.4176L17.1773 18.238C17.4702 18.5309 17.945 18.5309 18.2379 18.238C18.5308 17.9451 18.5309 17.4703 18.238 17.1773L15.4182 14.3573C16.5367 13.0033 17.2087 11.2669 17.2087 9.37363C17.2087 5.04817 13.7014 1.54199 9.37533 1.54199Z"/></svg>
                        </button>
                        <input
                            x-model="search"
                            type="text"
                            placeholder="Cari divisi..."
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-12 pr-4 text-sm text-gray-800 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 xl:w-[300px]"
                        />
                    </div>

                    <a href="{{ route('organization.division.create', ['company_id' => $company->id]) }}"
                       class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Divisi
                    </a>
                </div>
            </div>

            <div class="max-w-full overflow-x-auto">
                <table class="w-full min-w-full">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50 text-left">
                            <th class="px-5 py-3 text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 w-16">No</th>
                            <th @click="sortBy('name')" class="px-5 py-3 text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 cursor-pointer hover:text-blue-600 transition">
                                <div class="flex items-center gap-1">
                                    Nama Divisi
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5"/></svg>
                                </div>
                            </th>
                            <th class="px-5 py-3 text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-5 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="(division, index) in filtered" :key="division.id">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20 transition">
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400" x-text="index + 1"></td>
                                <td class="px-5 py-4 text-sm font-medium text-gray-900 dark:text-white" x-text="division.name"></td>
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-900/20 dark:text-green-400">
                                        Aktif
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <a :href="'{{ url('organization/division') }}/' + division.id" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        </template>

                        <template x-if="filtered.length === 0">
                            <tr>
                                <td colspan="4" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Belum ada divisi untuk perusahaan ini.
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="'Menampilkan ' + filtered.length + ' divisi'"></p>
            </div>
        </div>
    </div>
</div>

<script>
function divisionList() {
    return {
        // Mengambil data divisi dari relasi company
        data: @json($company->divisions ?? []),
        search: '',
        sortCol: 'name',
        sortDir: 'asc',

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
        }
    }
}
</script>
@endsection
