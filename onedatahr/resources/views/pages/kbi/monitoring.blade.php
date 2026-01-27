@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6">
    
    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Monitoring KBI {{ $tahun }}</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Pantau progres pengisian penilaian karyawan.</p>
        </div>
        
        {{-- Tombol Back --}}
        <a href="{{ route('kbi.index') }}" class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 text-sm font-medium flex items-center gap-2 bg-white dark:bg-gray-800 px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm transition-colors">
            <i class="fas fa-arrow-left"></i> Kembali ke Menu Utama
        </a>
    </div>

    {{-- BAR FILTER --}}
    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6 transition-colors">
        <form action="{{ route('kbi.monitoring') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                
                {{-- [BARU] 1. Filter Tahun --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Tahun</label>
                    <select name="tahun" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors">
                        @php
                            $startYear = date('Y') - 4; 
                            $endYear = date('Y') + 1;
                        @endphp
                        @for($y = $endYear; $y >= $startYear; $y--)
                            <option value="{{ $y }}" {{ (request('tahun') ?? $tahun) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- 2. Cari Nama --}}
                <div class="md:col-span-1">
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Cari Nama / NIK</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: Budi..." 
                           class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-colors">
                </div>

                {{-- 3. Filter Jabatan --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Jabatan</label>
                    <select name="jabatan" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors">
                        <option value="">- Semua Jabatan -</option>
                        @foreach($listJabatan as $jab)
                            <option value="{{ $jab }}" {{ request('Jabatan') == $jab ? 'selected' : '' }}>
                                {{ $jab }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 4. Filter Status --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Status Pengerjaan</label>
                    <select name="status" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors">
                        <option value="">- Semua Status -</option>
                        <option value="sudah" {{ request('status') == 'sudah' ? 'selected' : '' }}>Sudah Lengkap (Selesai)</option>
                        <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>Belum Lengkap (Pending)</option>
                    </select>
                </div>

                {{-- 5. Tombol Aksi --}}
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition shadow-md">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'jabatan', 'status']) || (request('tahun') && request('tahun') != date('Y')))
                        <a href="{{ route('kbi.monitoring') }}" class="bg-gray-300 hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-300 font-bold py-2 px-3 rounded-lg text-sm transition flex items-center justify-center" title="Reset Filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </a>
                    @endif
                </div>

            </div>
        </form>
    </div>

    {{-- KARTU STATISTIK (Tetap sama) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border-l-4 border-blue-500 dark:border-blue-600 flex justify-between items-center transition-colors">
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-xs uppercase font-bold">Total Data</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalKaryawan }}</h3>
            </div>
            <div class="text-blue-200 dark:text-blue-900/50 text-3xl"><i class="fas fa-users"></i></div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border-l-4 border-green-500 dark:border-green-600 flex justify-between items-center transition-colors">
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-xs uppercase font-bold">Sudah Lengkap</p>
                <h3 class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $sudahSelesaiSemua }}</h3>
            </div>
            <div class="text-green-200 dark:text-green-900/50 text-3xl"><i class="fas fa-check-circle"></i></div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border-l-4 border-red-500 dark:border-red-600 flex justify-between items-center transition-colors">
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-xs uppercase font-bold">Belum Selesai</p>
                <h3 class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $belumSelesai }}</h3>
            </div>
            <div class="text-red-200 dark:text-red-900/50 text-3xl"><i class="fas fa-clock"></i></div>
        </div>
    </div>

    {{-- TABEL MONITORING (Tetap sama) --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 uppercase text-xs font-bold border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-3 w-10">No</th>
                        <th class="px-6 py-3">Karyawan</th>
                        <th class="px-6 py-3">Jabatan</th>
                        <th class="px-6 py-3 text-center">Penilaian Diri</th>
                        <th class="px-6 py-3 text-center">Feedback Atasan</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($listKaryawan as $index => $kry)
                    <tr class="hover:bg-blue-50/30 dark:hover:bg-gray-700/50 transition duration-150">
                        <td class="px-6 py-4 text-center text-gray-400 dark:text-gray-500 text-xs">
                            {{ $listKaryawan->firstItem() + $loop->index }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800 dark:text-white">{{ $kry->Nama_Lengkap_Sesuai_Ijazah }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $kry->NIK }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-700 dark:text-gray-300 text-xs font-semibold bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded inline-block border border-gray-200 dark:border-gray-600">
                                {{ $kry->pekerjaan->first()?->position?->name ?? '-' }}
                            </div>
                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                Atasan: <span class="text-gray-600 dark:text-gray-300">{{ $kry->atasan->Nama_Lengkap_Sesuai_Ijazah ?? 'Tidak Ada' }}</span>
                            </div>
                        </td>
                        
                        {{-- STATUS DIRI --}}
                        <td class="px-6 py-4 text-center">
                            @if($kry->status_diri)
                                <span class="text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/30 border border-green-200 dark:border-green-800 px-2 py-1 rounded-full text-xs font-bold">
                                    <i class="fas fa-check mr-1"></i> Selesai
                                </span>
                            @else
                                <span class="text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 px-2 py-1 rounded-full text-xs font-bold animate-pulse">
                                    <i class="fas fa-times mr-1"></i> Belum
                                </span>
                            @endif
                        </td>

                        {{-- STATUS ATASAN --}}
                        <td class="px-6 py-4 text-center">
                            @if($kry->status_atasan == 'DONE')
                                <span class="text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/30 border border-green-200 dark:border-green-800 px-2 py-1 rounded-full text-xs font-bold">
                                    <i class="fas fa-check mr-1"></i> Selesai
                                </span>
                            @elseif($kry->status_atasan == 'NA')
                                <span class="text-gray-400 dark:text-gray-500 text-xs italic">- Tidak Perlu -</span>
                            @else
                                <span class="text-orange-600 dark:text-orange-400 bg-orange-100 dark:bg-orange-900/30 border border-orange-200 dark:border-orange-800 px-2 py-1 rounded-full text-xs font-bold">
                                    <i class="fas fa-hourglass-half mr-1"></i> Pending
                                </span>
                            @endif
                        </td>

                        {{-- AKSI --}}
                        <td class="px-6 py-4 text-center">
                            @if(!$kry->status_diri || ($kry->status_atasan == 'PENDING'))
                                @php 
                                    $pesan = "Halo {$kry->Nama_Lengkap_Sesuai_Ijazah}, mohon segera melengkapi penilaian KBI Anda di sistem HRIS. Status: " . (!$kry->status_diri ? "[Penilaian Diri Belum] " : "") . ($kry->status_atasan == 'PENDING' ? "[Feedback Atasan Belum]" : "") . ". Terima kasih.";
                                    $linkWa = "https://wa.me/628123456789?text=" . urlencode($pesan); // Ganti no HP dinamis jika ada
                                @endphp
                                <a href="{{ $linkWa }}" target="_blank" class="text-green-600 hover:text-green-800 bg-green-50 hover:bg-green-100 dark:bg-green-900/20 dark:text-green-400 dark:hover:bg-green-900/40 p-2 rounded-full transition" title="Kirim Reminder via WA">
                                    <i class="fab fa-whatsapp text-xl"></i>
                                </a>
                            @else
                                <span class="text-gray-300 dark:text-gray-600"><i class="fas fa-check-double"></i></span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-search text-4xl mb-3 text-gray-300 dark:text-gray-600"></i>
                                <p>Tidak ada data karyawan yang sesuai filter.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Footer Pagination --}}
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 dark:bg-gray-800/50 flex justify-end">
            {{ $listKaryawan->links('components.pagination-custom') }}
        </div>
    </div>
</div>
@endsection