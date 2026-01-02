@extends('layouts.app')

@section('content')
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
            <div class="bg-white p-5 rounded-xl shadow border border-purple-100">
                <h3 class="font-bold text-base text-purple-800 mb-1">
                    Feedback ke Atasan
                </h3>
                <p class="text-xs text-gray-500 mb-3">
                    {{ $atasan->nama }}
                </p>

                @if($sudahMenilaiAtasan)
                    <div class="bg-green-100 text-green-700 text-sm px-3 py-2 rounded font-semibold text-center">
                        ✔ Sudah Selesai
                    </div>
                @else
                    <a href="{{ route('kbi.create', ['karyawan_id' => $atasan->id_karyawan, 'tipe' => 'BAWAHAN']) }}"
                       class="block text-center bg-purple-600 hover:bg-purple-700 text-white py-2 rounded font-semibold text-sm">
                        Beri Masukan
                    </a>
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
                            <td class="p-3 text-gray-500">{{ $staff->NIK }}</td>
                            <td class="p-3 text-gray-500">
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
