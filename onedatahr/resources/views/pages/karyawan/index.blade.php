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

        <div class="flex gap-2">
            <button onclick="openImportModal()"
               class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-green-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                Import
            </button>
            <a href="{{ route('karyawan.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Karyawan
            </a>
        </div>

    </div>

    <!-- SUCCESS ALERT -->
    @if(session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800 dark:border-green-900 dark:bg-green-900/20 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    <!-- USER CREDENTIALS MODAL -->
    @if(session('user_created') && session('user_credentials'))
        <div id="credentialsModal" class="fixed inset-0 z-50 flex items-center justify-center">
            <div onclick="closeCredentialsModal()" class="absolute inset-0 bg-black/50 dark:bg-black/80"></div>

            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-md w-full mx-4 p-6">
                <div class="mb-6">
                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/20 mb-4">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">User Berhasil Dibuat!</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Akun user telah otomatis dibuat untuk karyawan baru</p>
                </div>

                <div class="space-y-4 mb-6 bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400">NAMA</label>
                        <p class="text-sm font-medium text-gray-900 dark:text-white mt-1" id="credName">{{ session('user_credentials')['name'] ?? '' }}</p>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400">EMAIL</label>
                        <div class="flex items-center gap-2 mt-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white flex-1 break-all" id="credEmail">{{ session('user_credentials')['email'] ?? '' }}</p>
                            <button type="button" onclick="copyToClipboard('credEmail')" class="text-blue-600 hover:text-blue-700 dark:text-blue-400" title="Copy">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400">PASSWORD SEMENTARA</label>
                        <div class="flex items-center gap-2 mt-1">
                            <input type="password" id="credPassword" value="{{ session('user_credentials')['password'] ?? '' }}" readonly class="text-sm font-medium bg-white dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 rounded px-2 py-1 flex-1" />
                            <button type="button" onclick="togglePasswordVisibility()" class="text-gray-600 hover:text-gray-700 dark:text-gray-400" title="Toggle">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            <button type="button" onclick="copyToClipboard('credPassword')" class="text-blue-600 hover:text-blue-700 dark:text-blue-400" title="Copy">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400">ROLE</label>
                        <div class="flex gap-2 mt-1 flex-wrap">
                            @foreach(session('user_credentials')['roles'] ?? [] as $role)
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/20 dark:text-blue-400">
                                    {{ ucfirst($role) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 mb-6">
                    <p class="text-xs text-blue-800 dark:text-blue-400">
                        <strong>Catatan:</strong> Pastikan user segera mengubah password saat login pertama kali. Password sementara ini hanya berlaku sekali.
                    </p>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeCredentialsModal()" class="flex-1 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                        Tutup
                    </button>
                    <button type="button" onclick="copyAllCredentials()" class="flex-1 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition">
                        Salin Semua
                    </button>
                </div>
            </div>
        </div>

        <script>
        function closeCredentialsModal() {
            document.getElementById('credentialsModal').style.display = 'none';
        }

        function togglePasswordVisibility() {
            const input = document.getElementById('credPassword');
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        function copyToClipboard(elementId) {
            const element = document.getElementById(elementId);
            const text = element.textContent || element.value;

            navigator.clipboard.writeText(text).then(() => {
                const btn = event.target.closest('button');
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';

                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                }, 2000);
            });
        }

        function copyAllCredentials() {
            const name = document.getElementById('credName').textContent;
            const email = document.getElementById('credEmail').textContent;
            const password = document.getElementById('credPassword').value;

            const text = `
Nama: ${name}
Email: ${email}
Password: ${password}
            `.trim();

            navigator.clipboard.writeText(text).then(() => {
                alert('Semua kredensial berhasil disalin!');
            });
        }

        // Auto-close modal after 30 seconds
        setTimeout(() => {
            closeCredentialsModal();
        }, 30000);
        </script>
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

        <form method="GET" action="{{ route('karyawan.index') }}" class="flex flex-col gap-4 md:flex-row md:items-end" id="filter-form">
            <div class="flex-1">
                <!-- <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pencarian Karyawan</label> -->
                <div class="relative">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari nama, NIK, jabatan, lokasi kerja, divisi, perusahaan..."
                           class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-4 pr-10 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:placeholder-gray-400">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 transition focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Cari
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
    <form
        id="batch-delete-form"
        action="{{ route('karyawan.batchDelete') }}"
        method="POST"
    >
        @csrf

        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

            <!-- TOP BAR -->
            <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4">
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Menampilkan {{ $karyawans->firstItem() ?? 0 }} sampai {{ $karyawans->lastItem() ?? 0 }} dari {{ $karyawans->total() }} data</span>
                </div>
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
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        id="batch-delete-btn"
                        onclick="batchDelete()"
                        class="hidden inline-flex items-center gap-2 rounded-lg bg-red-600 px-5 py-2.5 text-white font-medium hover:bg-red-700 transition shadow-sm"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus Terpilih
                    </button>

                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" @click.away="open = false" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 hover:bg-gray-50 focus:outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-50 border border-gray-200 dark:border-gray-700" style="display: none;">
                            <a href="{{ route('karyawan.export', ['type' => 'csv']) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Export CSV</a>
                            <a href="{{ route('karyawan.export', ['type' => 'excel']) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Export Excel</a>
                            <a href="{{ route('karyawan.export', ['type' => 'pdf']) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Export PDF</a>
                        </div>
                    </div>
                </div>
            </div>

        <!-- TABLE -->
        <div class="max-w-full overflow-x-auto">
            <table class="w-full min-w-full">
                <thead>
                    <tr class="border-y border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th class="px-4 py-3 text-center w-10">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400">Nama</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400">NIK</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400">Nomor Telepon</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400">Level Jabatan</th>
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
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                @if ($karyawan->pekerjaan->first() && $karyawan->pekerjaan->first()->level)
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/20 dark:text-blue-400">
                                        {{ $karyawan->pekerjaan->first()->level->name }}
                                    </span>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $karyawan->pekerjaan->first()->Lokasi_Kerja ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $karyawan->pekerjaan->first()->division->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                {{ $karyawan->pekerjaan->first()?->company->name ?? $karyawan->pekerjaan->first()?->holding->name ?? '-' }}
                            </td>
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
                                    <button type="button"
                                            onclick="deleteKaryawan('{{ $karyawan->id_karyawan }}', '{{ addslashes($karyawan->Nama_Sesuai_KTP) }}')"
                                            class="inline-flex items-center justify-center rounded-lg bg-red-50 p-2 text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 transition" title="Hapus">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
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

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('input[name="selected_karyawan[]"]');
    const deleteBtn = document.getElementById('batch-delete-btn');

    function updateButton() {
        const checked = document.querySelectorAll('input[name="selected_karyawan[]"]:checked');

        if (checked.length > 0) {
            deleteBtn.classList.remove('hidden');
            deleteBtn.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Hapus (${checked.length})
            `;
        } else {
            deleteBtn.classList.add('hidden');
        }

        // Update select all state
        selectAll.checked = checked.length === checkboxes.length && checkboxes.length > 0;
        selectAll.indeterminate = checked.length > 0 && checked.length < checkboxes.length;
    }

    // Add event listeners to individual checkboxes
    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateButton);
    });

    // Add event listener to select all checkbox
    selectAll.addEventListener('change', function () {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateButton();
    });

    // Initialize button state
    updateButton();

    // Global batch delete function - using fetch API
    window.batchDelete = function () {
        const checked = document.querySelectorAll('input[name="selected_karyawan[]"]:checked');

        if (checked.length === 0) {
            alert('Pilih data yang ingin dihapus terlebih dahulu');
            return;
        }

        if (!confirm(`Yakin ingin menghapus ${checked.length} karyawan terpilih?`)) {
            return;
        }

        // Get selected IDs
        const selectedIds = Array.from(checked).map(cb => cb.value);
        
        // Create form data
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        selectedIds.forEach(id => {
            formData.append('selected_karyawan[]', id);
        });

        // Show loading state
        deleteBtn.disabled = true;
        deleteBtn.innerHTML = `
            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Menghapus...
        `;

        // Send request
        fetch('{{ route("karyawan.batchDelete") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
            } else {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus data');
            deleteBtn.disabled = false;
            updateButton();
        });
    };

    // Individual delete function - creates a temporary form outside the batch form
    window.deleteKaryawan = function(id, name) {
        if (!confirm(`Yakin ingin menghapus karyawan "${name}"?`)) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ url('karyawan') }}/${id}`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(form);
        form.submit();
    };
});
</script>
@include('pages.karyawan.components.import-modal')
@endsection

