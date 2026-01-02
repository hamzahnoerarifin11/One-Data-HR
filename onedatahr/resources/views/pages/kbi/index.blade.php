@extends('layouts.app')

@section('content')
<style>
    /* Paksa warna hijau muncul manual */
    .bg-green-600 { background-color: #16a34a !important; }
    .hover\:bg-green-700:hover { background-color: #15803d !important; }
    .text-white { color: #ffffff !important; }
    
    /* Paksa warna biru muncul manual */
    .bg-blue-600 { background-color: #2563eb !important; }
    .hover\:bg-blue-700:hover { background-color: #1d4ed8 !important; }
    
    /* Perbaikan tombol search yang hilang */
    button.bg-green-600 {
        background-color: #16a34a !important;
        color: white !important;
    }
</style>
<div class="p-4 sm:p-6">
    <h1 class="text-xl sm:text-2xl font-bold mb-6 text-gray-900 dark:text-white">
        PENILAIAN KBI
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- SIDEBAR --}}
        <div class="space-y-4">

            {{-- KARTU 1: PENILAIAN DIRI --}}
            <div class="bg-white p-5 rounded-xl shadow border border-blue-100">
                <h3 class="font-bold text-base text-blue-800 mb-1">
                    Penilaian Diri
                </h3>
                <p class="text-xs text-gray-500 mb-3">
                    Wajib tiap semester
                </p>

                @if($selfAssessment)
                    <div class="bg-green-100 text-green-700 text-sm px-3 py-2 rounded font-semibold text-center">
                        ✔ Selesai ({{ $selfAssessment->rata_rata_akhir }})
                    </div>
                @else
                    <a href="{{ route('kbi.create', ['karyawan_id' => $karyawan->id_karyawan, 'tipe' => 'DIRI_SENDIRI']) }}"
                       class="block text-center bg-blue-600 hover:bg-blue-700 text-white py-2 rounded font-semibold text-sm">
                        Mulai Menilai
                    </a>
                @endif
            </div>

            {{-- KARTU 3: FEEDBACK KE ATASAN --}}
            @if($atasan)

                <div class="bg-white p-5 rounded-xl shadow border border-purple-100 mt-6 relative overflow-hidden">
                    
                    {{-- Hiasan Background Tipis (Opsional, biar cantik) --}}
                    <div class="absolute top-0 right-0 -mt-2 -mr-2 w-16 h-16 bg-purple-50 rounded-full blur-xl opacity-50 pointer-events-none"></div>

                    <h3 class="font-bold text-base text-purple-800 mb-1" style="color: #6b21a8;">
                        Feedback ke Atasan
                    </h3>
                    <p class="text-xs text-gray-500 mb-4">
                        Berikan masukan untuk atasan langsung Anda.
                    </p>

                    @if($atasan)
                        <div class="flex items-start gap-3 mb-4">
                            {{-- Avatar Inisial --}}
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 font-bold text-sm border border-purple-200" style="background-color: #f3e8ff; color: #7e22ce;">
                                {{ substr($atasan->Nama_Lengkap_Sesuai_Ijazah ?? $atasan->Nama_Sesuai_KTP ?? 'A', 0, 1) }}
                            </div>
                            
                            {{-- Info Nama & Jabatan --}}
                            <div class="overflow-hidden">
                                <h4 class="font-bold text-gray-800 text-sm truncate" title="{{ $atasan->Nama_Lengkap_Sesuai_Ijazah }}">
                                    {{ $atasan->Nama_Lengkap_Sesuai_Ijazah ?? $atasan->Nama_Sesuai_KTP }}
                                </h4>
                                <p class="text-xs text-gray-500 truncate">
                                    {{ $atasan->pekerjaan->nama_jabatan ?? 'Atasan Langsung' }}
                                </p>
                                <div class="text-[10px] text-gray-400 mt-0.5">
                                    NIK: {{ $atasan->NIK ?? '-' }}
                                </div>
                            </div>
                        </div>

                        @if($sudahMenilaiAtasan)
                            {{-- STATUS: SUDAH DINILAI --}}
                            <div class="w-full bg-green-50 border border-green-200 text-green-700 text-sm py-2 rounded-lg font-semibold text-center flex items-center justify-center gap-2" style="background-color: #f0fdf4; color: #15803d; border-color: #bbf7d0;">
                                <i class="fas fa-check-circle"></i> Selesai
                            </div>
                        @else
                            {{-- TOMBOL: BERI MASUKAN --}}
                            {{-- Saya tambahkan style="background-color..." agar pasti muncul warnanya --}}
                            <a href="{{ route('kbi.create', ['karyawan_id' => $atasan->id_karyawan, 'tipe' => 'BAWAHAN']) }}"
                            class="block w-full text-center bg-purple-600 hover:bg-purple-700 text-white py-2.5 rounded-lg font-semibold text-sm transition shadow-sm hover:shadow-md group"
                            style="background-color: #9333ea; color: #ffffff;">
                            
                            <span class="group-hover:scale-105 inline-block transition-transform duration-200">
                                    <i class="fas fa-pen-to-square mr-1"></i> Mulai Menilai
                            </span>
                            </a>
                        @endif

                    @else
                        {{-- JIKA TIDAK ADA ATASAN --}}
                        <div class="text-center py-6 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                            <i class="fas fa-user-slash text-gray-300 text-2xl mb-2"></i>
                            <p class="text-xs text-gray-400">Tidak ada data atasan.</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- KONTEN UTAMA --}}
        <div class="md:col-span-2 bg-white p-5 rounded-xl shadow border border-green-100">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <h3 class="font-bold text-lg text-green-800">
                    Daftar Karyawan ({{ $karyawan->count() }})
                </h3>
            </div>

            {{-- SEARCH --}}
            <form action="{{ route('kbi.index') }}" method="GET" class="mb-4">
                <div class="flex flex-col sm:flex-row gap-2">
                    <input type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari Nama / NIK..."
                        class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:outline-none focus:border-green-500"
                    >
                    <button class="bg-green-600 text-white px-5 py-2 rounded text-sm hover:bg-green-700">
                        Cari
                    </button>

                    @if(request('search'))
                        <a href="{{ route('kbi.index') }}"
                           class="bg-gray-500 text-white px-4 py-2 rounded text-sm text-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            {{-- TABLE --}}
            <div class="overflow-x-auto">
                <table class="min-w-[640px] w-full text-sm">
                    <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                        <tr>
                            <th class="p-3">Nama</th>
                            <th class="p-3">NIK</th>
                            <th class="p-3">Jabatan</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($bawahanList as $staff)
                        <tr>
                            <td class="p-3 font-medium">
                                {{ $staff->Nama_Lengkap_Sesuai_Ijazah ?? $staff->Nama_Sesuai_KTP }}
                            </td>
                            <td class="p-3 text-gray-500 text-center">{{ $staff->NIK }}</td>
                            <td class="p-3 text-gray-500 text-center">
                                {{ $staff->pekerjaan->nama_jabatan ?? '-' }}
                            </td>
                            <td class="p-3 text-center">
                                @if($staff->sudah_dinilai)
                                    <span class="text-green-700 bg-green-100 px-2 py-1 rounded text-xs font-semibold">
                                        Selesai
                                    </span>
                                @else
                                    <a href="{{ route('kbi.create', ['karyawan_id' => $staff->id_karyawan, 'tipe' => 'ATASAN']) }}"
                                       class="inline-block bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs transition duration-200">
                                        Nilai
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-6 text-center text-gray-400">
                                Data tidak ditemukan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div class="mt-4">
                {{ $bawahanList->withQueryString()->links('components.pagination-custom') }}
            </div>

        </div>
    </div>
</div>
@endsection
