@extends('layouts.app')

@section('content')

<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <!-- HEADER -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Data Karyawan
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">
                Kelola seluruh data karyawan perusahaan
            </p>
        </div>

        <a href="{{ route('karyawan.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Karyawan
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


    <!-- FILTER FORM -->
    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Filter & Pencarian Karyawan</h3>
            <button type="button" onclick="toggleFilter()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 lg:hidden">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                </svg>
            </button>
        </div>

        <form method="GET" action="{{ route('karyawan.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" id="filter-form">
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Karyawan</label>
                <div class="relative">
                    <input type="text" name="nama" id="nama" value="{{ request('nama') }}" placeholder="Masukkan nama karyawan"
                           class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-4 pr-10 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div>
                <label for="nik" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NIK</label>
                <div class="relative">
                    <input type="text" name="nik" id="nik" value="{{ request('nik') }}" placeholder="Masukkan NIK"
                           class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-4 pr-10 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div>
                <label for="jabatan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jabatan</label>
                <div class="relative">
                    <input type="text" name="jabatan" id="jabatan" value="{{ request('jabatan') }}" placeholder="Masukkan jabatan"
                           class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-4 pr-10 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m8 0V8a2 2 0 01-2 2H8a2 2 0 01-2-2V6m8 0H8"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div>
                <label for="lokasi_kerja" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lokasi Kerja</label>
                <div class="relative">
                    <input type="text" name="lokasi_kerja" id="lokasi_kerja" value="{{ request('lokasi_kerja') }}" placeholder="Masukkan lokasi kerja"
                           class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-4 pr-10 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div>
                <label for="divisi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Divisi</label>
                <div class="relative">
                    <input type="text" name="divisi" id="divisi" value="{{ request('divisi') }}" placeholder="Masukkan divisi"
                           class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-4 pr-10 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div>
                <label for="perusahaan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Perusahaan</label>
                <div class="relative">
                    <input type="text" name="perusahaan" id="perusahaan" value="{{ request('perusahaan') }}" placeholder="Masukkan perusahaan"
                           class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-4 pr-10 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" id="status"
                        class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-4 pr-10 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">Semua Status</option>
                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Non Aktif" {{ request('status') == 'Non Aktif' ? 'selected' : '' }}>Non Aktif</option>
                </select>
            </div>

            <div class="flex items-end gap-2 lg:col-span-1 xl:col-span-1">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 transition focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filter
                </button>
                <a href="{{ route('karyawan.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- TABLE -->
    <form id="batch-delete-form" action="{{ route('karyawan.batchDelete') }}" method="POST">
        @csrf
        @method('DELETE')
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

            <!-- TOP BAR -->
            <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4">
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Menampilkan {{ $karyawans->firstItem() ?? 0 }} sampai {{ $karyawans->lastItem() ?? 0 }} dari {{ $karyawans->total() }} data</span>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="batchDelete()" id="batch-delete-btn" class="hidden inline-flex items-center gap-2 rounded-lg bg-red-600 px-5 py-2.5 text-center text-white font-medium hover:bg-red-700 transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus Terpilih
                    </button>
                    <button class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2v6m0 0l-2-2m2 2l2-2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Download
                    </button>
                </div>
            </div>

        <!-- TABLE -->
        <div class="max-w-full overflow-x-auto">
            <table class="w-full min-w-full">
                <thead>
                    <tr class="border-y border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th class="px-4 py-3 text-center w-10">
                            <input type="checkbox" id="select-all" onclick="toggleAllCheckboxes()" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400">Nama</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400">NIK</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400">Nomor Telepon</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400">Jabatan</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400">Lokasi Kerja</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400">Divisi</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400">Perusahaan</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($karyawans as $karyawan)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20 transition">
                            <td class="px-4 py-3 text-center">
                                <input type="checkbox" value="{{ $karyawan->id_karyawan }}" name="selected_karyawan[]" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="shrink-0 w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-sm font-semibold text-gray-700 dark:text-white">
                                        {{ substr($karyawan->Nama_Sesuai_KTP ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $karyawan->Nama_Sesuai_KTP }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $karyawan->Nomor_Telepon_Aktif_Karyawan ?? 'no phone' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $karyawan->NIK ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $karyawan->Nomor_Telepon_Aktif_Karyawan ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $karyawan->pekerjaan->position->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $karyawan->pekerjaan->Lokasi_Kerja ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $karyawan->pekerjaan->division->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $karyawan->pekerjaan->company->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('karyawan.show', $karyawan->id_karyawan) }}" class="inline-flex items-center justify-center rounded-lg bg-blue-50 p-2 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/40 transition" title="Lihat Detail">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('karyawan.edit', $karyawan->id_karyawan) }}" class="inline-flex items-center justify-center rounded-lg bg-yellow-50 p-2 text-yellow-600 hover:bg-yellow-100 dark:bg-yellow-900/20 dark:text-yellow-400 dark:hover:bg-yellow-900/40 transition" title="Edit">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('karyawan.destroy', $karyawan->id_karyawan) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus karyawan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-red-50 p-2 text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 transition" title="Hapus">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-6 text-center text-gray-500">
                                Data tidak ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="flex items-center justify-between px-6 py-4">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Menampilkan {{ $karyawans->firstItem() ?? 0 }} sampai {{ $karyawans->lastItem() ?? 0 }} dari {{ $karyawans->total() }} data
            </div>

            <div class="flex items-center gap-2">
                @if($karyawans->hasPages())
                    {{ $karyawans->links('vendor.pagination.tailwind') }}
                @endif
            </div>
        </div>
    </div>
    </form>
</div>

<script>
function toggleFilter() {
    const form = document.getElementById('filter-form');
    form.classList.toggle('hidden');
}

function toggleAllCheckboxes() {
    const selectAllCheckbox = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('input[name="selected_karyawan[]"]');
    const batchDeleteBtn = document.getElementById('batch-delete-btn');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });

    updateBatchDeleteButton();
}

function updateBatchDeleteButton() {
    const checkboxes = document.querySelectorAll('input[name="selected_karyawan[]"]:checked');
    const batchDeleteBtn = document.getElementById('batch-delete-btn');

    if (checkboxes.length > 0) {
        batchDeleteBtn.classList.remove('hidden');
        batchDeleteBtn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg> Hapus (${checkboxes.length})`;
    } else {
        batchDeleteBtn.classList.add('hidden');
    }
}

function updateSelectAllCheckbox() {
    const selectAllCheckbox = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('input[name="selected_karyawan[]"]');
    const checkedCheckboxes = document.querySelectorAll('input[name="selected_karyawan[]"]:checked');

    selectAllCheckbox.checked = checkboxes.length === checkedCheckboxes.length && checkboxes.length > 0;
    selectAllCheckbox.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < checkboxes.length;

    updateBatchDeleteButton();
}

function batchDelete() {
    const checkboxes = document.querySelectorAll('input[name="selected_karyawan[]"]:checked');
    if (checkboxes.length === 0) return;

    if (confirm(`Hapus ${checkboxes.length} karyawan terpilih?`)) {
        document.getElementById('batch-delete-form').submit();
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateBatchDeleteButton();

    // Add event listeners to checkboxes
    document.querySelectorAll('input[name="selected_karyawan[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectAllCheckbox);
    });
});
</script>
@endsection

