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
                    <h3 class="font-bold text-base text-blue-800 dark:text-blue-400">
                        Penilaian Diri
                    </h3>
                </div>

                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
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
                            <h4 class="font-bold text-gray-800 dark:text-white text-sm truncate">
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
                                        ({{ $calon->pekerjaan->first()?->Jabatan ?? '-' }})
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
                @endif
            </div>
        </div>

        {{-- KONTEN UTAMA: DAFTAR KARYAWAN --}}
        {{-- Tampilkan table hanya jika role bukan staff --}}
        @if(auth()->user() && auth()->user()->hasRole(['admin', 'manager', 'superadmin']))
        <div class="bg-white dark:bg-gray-800 p-5 sm:p-6 rounded-xl shadow border border-green-100 dark:border-gray-700">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                <h3 class="font-bold text-lg text-green-800 dark:text-green-400 flex items-center gap-2">
                    {{-- Ganti Judul Agar Lebih Relevan untuk Manager --}}
                    <i class="fas fa-users"></i>
                    @if(auth()->user()->hasRole('manager'))
                        Daftar Tim Saya ({{ $bawahanList->total() }})
                    @else
                        belum memiliki bawahan yang terdaftar
                    @endif
                </h3>
            </div>

            {{-- SEARCH --}}
            <form action="{{ route('kbi.index') }}" method="GET" class="mb-5">
                <div class="flex flex-col sm:flex-row gap-2">
                    <input type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari Nama / NIK..."
                        class="flex-1 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200 dark:focus:ring-green-800 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 transition"
                    >
                    {{-- TOMBOL CARI --}}
                    <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-lg font-semibold text-sm transition-all duration-200 hover:shadow-md whitespace-nowrap">
                        <i class="fas fa-search mr-1"></i>Cari
                    </button>

                    @if(request('search'))
                        <a href="{{ route('kbi.index') }}"
                           class="bg-gray-400 hover:bg-gray-500 text-white px-6 py-2.5 rounded-lg font-semibold text-sm text-center transition-all duration-200 hover:shadow-md whitespace-nowrap">
                            <i class="fas fa-redo mr-1"></i>Reset
                        </a>
                    @endif
                </div>
            </form>

            {{-- TABLE --}}
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="min-w-full w-full text-sm">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 text-gray-700 dark:text-gray-300 uppercase text-xs font-semibold">
                        <tr>
                            <th class="p-4 text-left">Nama</th>
                            <th class="p-4 text-center">NIK</th>
                            <th class="p-4 text-center hidden sm:table-cell">Jabatan</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($bawahanList as $staff)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <td class="p-4 font-medium text-gray-900 dark:text-white">
                                {{ $staff->Nama_Lengkap_Sesuai_Ijazah ?? $staff->Nama_Sesuai_KTP }}
                            </td>
                            <td class="p-4 text-gray-600 dark:text-gray-400 text-center font-mono text-xs">{{ $staff->NIK }}</td>
                            <td class="p-4 text-gray-600 dark:text-gray-400 text-center hidden sm:table-cell">
                                {{ $staff->pekerjaan->first()?->Jabatan ?? '-' }}
                            </td>
                            <td class="p-4 text-center">
                                @if($staff->sudah_dinilai)
                                    <span class="inline-flex items-center gap-1 text-green-700 dark:text-green-400 bg-green-100 dark:bg-green-900/30 border border-green-200 dark:border-green-800 px-3 py-1.5 rounded-lg text-xs font-semibold">
                                        <i class="fas fa-check-circle"></i> Selesai
                                    </span>
                                @else
                                    {{-- TOMBOL NILAI --}}
                                    <a href="{{ route('kbi.create', ['karyawan_id' => $staff->id_karyawan, 'tipe' => 'ATASAN']) }}"
                                       class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white px-4 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 hover:shadow-md">
                                        <i class="fas fa-pen-to-square"></i>Nilai
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-400 dark:text-gray-500">
                                <i class="fas fa-inbox text-3xl mb-3 opacity-50"></i>
                                <p>
                                    @if(auth()->user()->hasRole('manager'))
                                        Belum ada anggota tim di divisi Anda.
                                    @else
                                        Data tidak ditemukan
                                    @endif
                                </p>
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
        @endif
    </div>
</div>
@endsection