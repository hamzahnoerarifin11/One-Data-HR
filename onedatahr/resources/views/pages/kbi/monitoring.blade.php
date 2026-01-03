@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Monitoring KBI {{ $tahun }}</h1>
            <p class="text-gray-500 text-sm">Pantau progres pengisian penilaian karyawan.</p>
        </div>
        <a href="{{ route('kbi.index') }}" class="text-gray-500 hover:text-blue-600 text-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Menu Utama
        </a>
    </div>

    {{-- KARTU STATISTIK --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        {{-- Card 1: Total --}}
        <div class="bg-white p-5 rounded-xl shadow border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs uppercase font-bold">Total Karyawan</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $totalKaryawan }}</h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                <i class="fas fa-users"></i>
            </div>
        </div>

        {{-- Card 2: Selesai --}}
        <div class="bg-white p-5 rounded-xl shadow border border-green-100 flex items-center justify-between">
            <div>
                <p class="text-green-600 text-xs uppercase font-bold">Sudah Lengkap</p>
                <h3 class="text-2xl font-bold text-green-700">{{ $sudahSelesaiSemua }}</h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                <i class="fas fa-check-double"></i>
            </div>
        </div>

        {{-- Card 3: Belum --}}
        <div class="bg-white p-5 rounded-xl shadow border border-red-100 flex items-center justify-between">
            <div>
                <p class="text-red-600 text-xs uppercase font-bold">Belum Selesai</p>
                <h3 class="text-2xl font-bold text-red-700">{{ $belumSelesai }}</h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                <i class="fas fa-exclamation-circle"></i>
            </div>
        </div>
    </div>

    {{-- TABEL MONITORING --}}
    <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-700 uppercase text-xs font-bold">
                    <tr>
                        <th class="px-6 py-3">Karyawan</th>
                        <th class="px-6 py-3">Jabatan & Unit</th>
                        <th class="px-6 py-3 text-center">Penilaian Diri</th>
                        <th class="px-6 py-3 text-center">Feedback Atasan</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($listKaryawan as $kry)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800">{{ $kry->Nama_Lengkap_Sesuai_Ijazah }}</div>
                            <div class="text-xs text-gray-500">{{ $kry->NIK }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-700">{{ $kry->pekerjaan->nama_jabatan ?? '-' }}</div>
                            <div class="text-xs text-gray-400">{{ $kry->atasan->Nama_Lengkap_Sesuai_Ijazah ?? 'Tidak Ada Atasan' }}</div>
                        </td>
                        
                        {{-- STATUS PENILAIAN DIRI --}}
                        <td class="px-6 py-4 text-center">
                            @if($kry->status_diri)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check"></i> Selesai
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times"></i> Belum
                                </span>
                            @endif
                        </td>

                        {{-- STATUS FEEDBACK ATASAN --}}
                        <td class="px-6 py-4 text-center">
                            @if($kry->status_atasan == 'DONE')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check"></i> Selesai
                                </span>
                            @elseif($kry->status_atasan == 'NA')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                    - N/A -
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock"></i> Pending
                                </span>
                            @endif
                        </td>

                        {{-- AKSI (REMINDER WA) --}}
                        <td class="px-6 py-4 text-center">
                            @if(!$kry->status_diri || ($kry->status_atasan == 'PENDING'))
                                {{-- Ganti nomor HP sesuai kolom di DB Anda, misal $kry->no_hp --}}
                                @php 
                                    $pesan = "Halo {$kry->Nama_Lengkap_Sesuai_Ijazah}, mohon segera melengkapi penilaian KBI Anda (Penilaian Diri/Atasan) di sistem ONE DATA HR. Terima kasih.";
                                    // Pastikan nomor HP format 628xxx
                                    $linkWa = "https://wa.me/628123456789?text=" . urlencode($pesan);
                                @endphp
                                <a href="{{ $linkWa }}" target="_blank" class="text-green-600 hover:text-green-800" title="Kirim Reminder WA">
                                    <i class="fab fa-whatsapp text-lg"></i>
                                </a>
                            @else
                                <i class="fas fa-check text-gray-300"></i>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                            Data karyawan tidak ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection