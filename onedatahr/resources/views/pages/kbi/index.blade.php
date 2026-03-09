@extends('layouts.app')

@section('content')

{{-- STYLE TAMBAHAN (MODIFIKASI AGAR SUPPORT DARK MODE) --}}
<style>
    /* 1. WARNA HIJAU (Tombol Cari & Nilai) */
    .bg-green-600 { background-color: #16a34a !important; color: white !important; }
    .hover\:bg-green-700:hover { background-color: #15803d !important; }
    .dark .bg-green-600 { background-color: #166534 !important; color: #e2e8f0 !important; }
    .dark .hover\:bg-green-700:hover { background-color: #14532d !important; }

    /* 2. WARNA BIRU (Tombol Mulai Menilai) */
    .bg-blue-600 { background-color: #2563eb !important; color: white !important; }
    .hover\:bg-blue-700:hover { background-color: #1d4ed8 !important; }
    .dark .bg-blue-600 { background-color: #1e40af !important; color: #e2e8f0 !important; }
    .dark .hover\:bg-blue-700:hover { background-color: #1e3a8a !important; }

    /* 3. WARNA UNGU (Feedback Atasan) */
    .bg-purple-600 { background-color: #9810fa !important; color: white !important; }
    .hover\:bg-purple-700:hover { background-color: #7e22ce !important; }
    .dark .bg-purple-600 { background-color: #7e22ce !important; color: #e2e8f0 !important; }
    .dark .hover\:bg-purple-700:hover { background-color: #6b21a8 !important; }

    /* 4. PERBAIKAN TEKS PUTIH */
    button.bg-green-600, a.bg-green-600, a.bg-blue-600, a.bg-purple-600 { color: #ffffff !important; }
    .dark button.bg-green-600, .dark a.bg-green-600, .dark a.bg-blue-600, .dark a.bg-purple-600 { color: #f1f5f9 !important; }
</style>

<div class="p-4 sm:p-6">
    <div class="bg-white dark:bg-gray-800 mb-6 flex flex-col lg:flex-row justify-between items-start lg:items-center p-4 md:p-6 rounded-xl shadow-sm gap-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            PENILAIAN KBI
        </h1>

        {{-- FILTER TAHUN --}}
        <form method="GET" class="flex justify-end">
            <div class="flex flex-col items-end gap-1">
                <div class="flex items-center gap-2">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        <i class="fas fa-calendar-alt mr-1 text-blue-600"></i>Pilih Tahun:
                    </label>
                    <select name="tahun" onchange="this.form.submit()" class="px-3 py-2 border border-blue-300 dark:border-blue-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm cursor-pointer">
                        @for($y = date('Y'); $y >= date('Y')-5; $y--)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-400 italic">
                    Menampilkan tahun <strong class="text-blue-600 dark:text-blue-400">{{ $tahun }}</strong>
                </p>
            </div>
        </form>
    </div>

    {{-- DEBUG: Tampilkan hanya untuk Level 1-5 (Direktur s.d. Staff) --}}
    @if(in_array($userLevel, [1, 2, 3, 4, 5]))
    <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative">
        <strong class="font-bold">DEBUG:</strong>
        <span class="block sm:inline">
            Level User: {{ $userLevel }} | ID Divisi: {{ $karyawan->pekerjaan->first()?->division_id }}
        </span>
    </div>
    @endif

    {{-- LAYOUT RESPONSIF: Kartu horizontal --}}
    <div class="grid grid-cols-1 gap-4 sm:gap-6">

        {{-- KARTU-KARTU PENILAIAN (HORIZONTAL) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
            {{-- KARTU 1: PENILAIAN DIRI --}}
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow border border-blue-100 dark:border-gray-700 flex flex-col h-full">

                <div class="flex items-center gap-3 mb-3 w-full">
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <i class="fas fa-user text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <h3 class="font-bold text-xl text-blue-800 dark:text-blue-400">
                        Penilaian Diri
                    </h3>
                </div>

                <p class="text-md text-gray-500 dark:text-gray-400 mb-4">
                    Wajib tiap semester
                </p>

                <div class="mt-auto">
                    @if($selfAssessment)
                        <div class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-sm px-3 py-2 rounded font-semibold text-center border border-green-200 dark:border-green-800">
                            <i class="fas fa-check-circle mr-1"></i>Selesai ({{ $selfAssessment->rata_rata_akhir }})
                        </div>
                    @else
                        <a href="{{ route('kbi.create', ['karyawan_id' => $karyawan->id_karyawan, 'tipe' => 'DIRI_SENDIRI']) }}"
                        class="block text-center bg-blue-600 hover:bg-blue-700 text-white py-2.5 px-4 rounded-lg font-semibold text-sm transition-all duration-200 hover:shadow-md">
                            <i class="fas fa-pen-to-square mr-1"></i>Mulai Menilai
                        </a>
                    @endif
                </div>
            </div>

            {{-- KARTU 2: FEEDBACK KE ATASAN --}}
            <div class="bg-white dark:bg-gray-800 p-5 sm:p-6 rounded-xl shadow border border-purple-100 dark:border-gray-700 relative overflow-hidden transition-transform hover:shadow-lg">

                {{-- Dekorasi Blob --}}
                <div class="absolute top-0 right-0 -mt-2 -mr-2 w-16 h-16 bg-purple-50 dark:bg-purple-900/20 rounded-full blur-xl opacity-50 pointer-events-none"></div>

                <div class="flex items-center gap-3 mb-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <i class="fas fa-star text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <h3 class="font-bold text-base text-purple-800 dark:text-purple-400">
                        Feedback ke Atasan
                    </h3>
                </div>

                @if($atasan)
                    {{-- === KONDISI A: SUDAH PUNYA ATASAN === --}}
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                        Berikan masukan untuk atasan langsung Anda.
                    </p>
                    @if(!$sudahMenilaiAtasan)
                        <form action="{{ route('kbi.reset-atasan') }}" method="POST" class="absolute top-6 right-4" onsubmit="return confirm('Reset atasan? Anda harus memilih atasan baru lagi.')">
                            @csrf
                            <input type="hidden" name="karyawan_id" value="{{ $karyawan->id_karyawan }}">
                            <button type="submit" title="Ubah Atasan"
                                    class="text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 text-sm transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                        </form>
                    @endif

                    <div class="m-auto mb-6 flex items-center gap-4">
                        {{-- Avatar Inisial --}}
                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/50 flex items-center justify-center text-purple-700 dark:text-purple-300 font-bold text-sm border border-purple-200 dark:border-purple-700">
                            {{ substr($atasan->Nama_Lengkap_Sesuai_Ijazah ?? $atasan->Nama_Sesuai_KTP ?? 'A', 0, 1) }}
                        </div>

                        {{-- Info Nama & Jabatan --}}
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-gray-800 dark:text-white text-md truncate">
                                {{ $atasan->Nama_Lengkap_Sesuai_Ijazah ?? $atasan->Nama_Sesuai_KTP }}
                            </h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ $atasan->pekerjaan->first()?->Jabatan ?? 'Atasan Langsung' }}
                            </p>
                        </div>
                    </div>

                    @if($sudahMenilaiAtasan)
                        <div class="w-full bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm py-2 rounded-lg font-semibold text-center flex items-center justify-center gap-2">
                            <i class="fas fa-check-circle"></i> Selesai
                        </div>
                    @else
                        <a href="{{ route('kbi.create', ['karyawan_id' => $atasan->id_karyawan, 'tipe' => 'BAWAHAN']) }}"
                        class="block w-full text-center bg-purple-600 hover:bg-purple-700 text-white py-2.5 px-4 rounded-lg font-semibold text-sm transition-all duration-200 shadow-sm hover:shadow-md group">
                        <span class="group-hover:scale-105 inline-block transition-transform duration-200">
                                <i class="fas fa-pen-to-square mr-1"></i> Mulai Menilai
                        </span>
                        </a>
                    @endif

                @else
                    {{-- === KONDISI B: BELUM PUNYA ATASAN === --}}
                    {{-- Hanya tampilkan pilihan atasan jika user bukan direktur (User Level > 1) --}}
                    @if(isset($userLevel) && $userLevel > 1)
                        <p class="text-xs text-red-500 dark:text-red-400 mb-4 italic">
                            *Data atasan belum disetting. Silakan pilih atasan langsung Anda:
                        </p>

                        <form action="{{ route('kbi.update-atasan') }}" method="POST">
                            @csrf
                            <input type="hidden" name="karyawan_id" value="{{ $karyawan->id_karyawan }}">

                            {{-- Dropdown Pilih Atasan --}}
                            <div class="mb-3">
                                <select name="atasan_id" required
                                        class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded px-3 py-2 focus:outline-none focus:border-purple-500 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="">-- Pilih Nama Atasan --</option>
                                    @foreach($listCalonAtasan as $calon)
                                        <option value="{{ $calon->id_karyawan }}">
                                            {{ $calon->Nama_Lengkap_Sesuai_Ijazah }}
                                            ({{ $calon->pekerjaan->first()?->level->name ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Tombol Simpan --}}
                            <button type="submit"
                                    class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition-all duration-200">
                                <i class="fas fa-save mr-1"></i> Simpan Atasan
                            </button>
                        </form>
                    @else
                        <p class="text-xs text-gray-500 mb-4">Anda berada di level tertinggi (Direktur) atau tidak dapat memilih atasan.</p>
                    @endif
                @endif
            </div>
        </div>

        {{-- KONTEN UTAMA: DAFTAR KARYAWAN --}}
        <div class="bg-white dark:bg-gray-800 p-5 sm:p-6 rounded-xl shadow border border-green-100 dark:border-gray-700">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                <h3 class="font-bold text-lg text-green-800 dark:text-green-400 flex items-center gap-2">
                    <i class="fas fa-users"></i>
                    @if($bawahanList->total() > 0)
                        Daftar Tim Divisi ({{ $bawahanList->total() }})
                    @else
                        Belum ada anggota tim
                    @endif
                </h3>
            </div>

            {{-- INFO BOX ATURAN PENILAIAN --}}
            <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-5">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mt-0.5"></i>
                    <div class="flex-1">
                        <h4 class="font-semibold text-blue-800 dark:text-blue-300 text-sm mb-1">Aturan Penilaian</h4>
                        <p class="text-blue-700 dark:text-blue-400 text-sm">
                            Menampilkan semua karyawan dalam satu divisi.
                            <br>
                            <strong>Anda hanya dapat menilai karyawan yang posisinya tepat 1 tingkat dibawah Anda.</strong>
                        </p>
                    </div>
                </div>
            </div>

            {{-- SEARCH & FILTER --}}
            <form action="{{ route('kbi.index') }}" method="GET" class="mb-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    {{-- SEARCH --}}
                    <div>
                        <input type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari Nama / NIK..."
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200 dark:focus:ring-green-800 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 transition"
                        >
                    </div>

                    {{-- FILTER COMPANIES --}}
                    <div>
                        <select name="filter_company" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200 dark:focus:ring-green-800 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">Semua Perusahaan</option>
                            @foreach($listCompanies as $company)
                                <option value="{{ $company }}" {{ request('filter_company') == $company ? 'selected' : '' }}>
                                    {{ $company }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- TOMBOL ACTION --}}
                    <div class="flex gap-2">
                        <button class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-lg font-semibold text-sm transition-all duration-200 hover:shadow-md">
                            <i class="fas fa-search mr-1"></i>Terapkan
                        </button>

                        @if(request('search') || request('filter_company'))
                            <a href="{{ route('kbi.index') }}"
                               class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2.5 rounded-lg font-semibold text-sm text-center transition-all duration-200 hover:shadow-md">
                                <i class="fas fa-redo mr-1"></i>Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            {{-- TABLE --}}
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="min-w-full w-full text-sm">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 text-gray-700 dark:text-gray-300 uppercase text-xs font-semibold">
                        <tr>
                            <th class="p-4 text-left">Nama</th>
                            <th class="p-4 text-center">Perusahaan</th>
                            <th class="p-4 text-center ">Jabatan</th>
                            <th class="p-4 text-center">Keterangan</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
    @forelse($bawahanList as $staff)
    <tr>
        {{-- KOLOM 1: NAMA --}}
        <td class="p-4 whitespace-nowrap">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10">
                    <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500 font-semibold">
                        {{ substr($staff->Nama_Lengkap_Sesuai_Ijazah ?? $staff->Nama_Sesuai_KTP ?? '?', 0, 1) }}
                    </div>
                </div>
                <div class="ml-4">
                    <div class="font-medium text-gray-900 dark:text-white">
                        {{ $staff->Nama_Lengkap_Sesuai_Ijazah ?? $staff->Nama_Sesuai_KTP }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $staff->NIK }}
                    </div>
                </div>
            </div>
        </td>
        {{-- KOLOM 2: NIK --}}
        <td class="p-4 text-center text-gray-700 dark:text-gray-300">
            {{ $staff->pekerjaan->first()?->company?->name ?? 'N/A'}}
        </td>
        {{-- KOLOM 3: JABATAN --}}
        <td class="p-4 text-center text-gray-700 dark:text-gray-300">
            {{ $staff->pekerjaan->first()?->level?->name ?? 'N/A' }} 
            <br>
            <!-- <span class="text-xs text-gray-500">(Level: {{ $staff->calculated_level }})</span> -->
        </td>
        
        {{-- KOLOM 4: KETERANGAN STATUS --}}
        <td class="p-4 text-center">
            @if($staff->can_assess)
                <span class="inline-flex items-center gap-1 text-green-700 dark:text-green-400 bg-green-100 dark:bg-green-900/30 px-2 py-1 rounded text-xs font-medium">
                    <i class="fas fa-check"></i> Dapat dinilai
                </span>
            @else
                <span class="inline-flex items-center gap-1 text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded text-xs font-medium" title="{{ $staff->lock_reason }}">
                    <i class="fas fa-lock"></i> Locked
                </span>
            @endif
        </td>

        {{-- KOLOM 5: TOMBOL AKSI --}}
        <td class="p-4 text-center">
            @if($staff->sudah_dinilai)
                {{-- JIKA SUDAH DINILAI --}}
                <span class="inline-flex items-center gap-1 text-green-700 dark:text-green-400 bg-green-100 dark:bg-green-900/30 border border-green-200 dark:border-green-800 px-3 py-1.5 rounded-lg text-xs font-semibold">
                    <i class="fas fa-check-circle"></i> Selesai
                </span>
            @else
                {{-- JIKA BELUM DINILAI --}}
                @if($staff->can_assess)
                    <a href="{{ route('kbi.create', ['karyawan_id' => $staff->id_karyawan, 'tipe' => 'ATASAN']) }}"
                        class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white px-4 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 hover:shadow-md">
                        <i class="fas fa-pen-to-square"></i> Nilai
                    </a>
                @else
                    <button disabled title="{{ $staff->lock_reason }}" class="inline-flex items-center gap-1 bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 px-4 py-1.5 rounded-lg text-xs font-semibold cursor-not-allowed">
                        <i class="fas fa-ban"></i> Nilai
                    </button>
                @endif
            @endif
        </td>
    </tr>
    @empty
        <tr>
            <td colspan="5" class="p-8 text-center text-gray-400 dark:text-gray-500">
                <div class="flex flex-col items-center justify-center">
                    <i class="fas fa-inbox text-4xl mb-3 opacity-30"></i>
                    <p class="text-sm">
                        Belum ada anggota tim yang sesuai kriteria.
                    </p>
                </div>
            </td>
        </tr>
    @endforelse
</tbody>
                </table>
            </div>
             {{-- PAGINATION --}}
             <div class="mt-6 flex justify-end">
                {{ $bawahanList->links('components.pagination-custom') }}
            </div>

        </div>
    </div>
</div>
@endsection
