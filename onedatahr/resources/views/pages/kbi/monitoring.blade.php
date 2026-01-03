@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6">
    
    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Monitoring KBI {{ $tahun }}</h1>
            <p class="text-gray-500 text-sm">Pantau progres pengisian penilaian karyawan.</p>
        </div>
        
        {{-- Tombol Back --}}
        <a href="{{ route('kbi.index') }}" class="text-gray-500 hover:text-blue-600 text-sm font-medium flex items-center gap-2 bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Menu Utama
        </a>
    </div>

    {{-- BAR FILTER --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6">
        <form action="{{ route('kbi.monitoring') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                
                {{-- 1. Cari Nama --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Cari Nama / NIK</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: Budi..." 
                           class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                {{-- 2. Filter Jabatan --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jabatan</label>
                    <select name="jabatan" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <option value="">- Semua Jabatan -</option>
                        @foreach($listJabatan as $jab)
                            <option value="{{ $jab }}" {{ request('jabatan') == $jab ? 'selected' : '' }}>
                                {{ $jab }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 3. Filter Status --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status Pengerjaan</label>
                    <select name="status" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <option value="">- Semua Status -</option>
                        <option value="sudah" {{ request('status') == 'sudah' ? 'selected' : '' }}>Sudah Lengkap (Selesai)</option>
                        <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>Belum Lengkap (Pending)</option>
                    </select>
                </div>

                {{-- 4. Tombol Aksi --}}
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'jabatan', 'status']))
                        <a href="{{ route('kbi.monitoring') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2 px-3 rounded-lg text-sm transition" title="Reset Filter">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </div>

            </div>
        </form>
    </div>

    {{-- KARTU STATISTIK (Angkanya akan berubah mengikuti Filter di atas) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-blue-500 flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-xs uppercase font-bold">Total Data</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $totalKaryawan }}</h3>
            </div>
            <div class="text-blue-200 text-3xl"><i class="fas fa-users"></i></div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-green-500 flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-xs uppercase font-bold">Sudah Lengkap</p>
                <h3 class="text-2xl font-bold text-green-600">{{ $sudahSelesaiSemua }}</h3>
            </div>
            <div class="text-green-200 text-3xl"><i class="fas fa-check-circle"></i></div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-red-500 flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-xs uppercase font-bold">Belum Selesai</p>
                <h3 class="text-2xl font-bold text-red-600">{{ $belumSelesai }}</h3>
            </div>
            <div class="text-red-200 text-3xl"><i class="fas fa-clock"></i></div>
        </div>
    </div>

    {{-- TABEL MONITORING --}}
    <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-700 uppercase text-xs font-bold border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 w-10">No</th>
                        <th class="px-6 py-3">Karyawan</th>
                        <th class="px-6 py-3">Jabatan</th>
                        <th class="px-6 py-3 text-center">Penilaian Diri</th>
                        <th class="px-6 py-3 text-center">Feedback Atasan</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($listKaryawan as $index => $kry)
                    <tr class="hover:bg-blue-50/30 transition">
                        <td class="px-6 py-4 text-center text-gray-400 text-xs">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800">{{ $kry->Nama_Lengkap_Sesuai_Ijazah }}</div>
                            <div class="text-xs text-gray-500">{{ $kry->NIK }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-700 text-xs font-semibold bg-gray-100 px-2 py-1 rounded inline-block">
                                {{ $kry->pekerjaan->Jabatan ?? '-' }}
                            </div>
                            <div class="text-xs text-gray-400 mt-1">
                                Atasan: {{ $kry->atasan->Nama_Lengkap_Sesuai_Ijazah ?? 'Tidak Ada' }}
                            </div>
                        </td>
                        
                        {{-- STATUS DIRI --}}
                        <td class="px-6 py-4 text-center">
                            @if($kry->status_diri)
                                <span class="text-green-600 bg-green-100 px-2 py-1 rounded-full text-xs font-bold">
                                    <i class="fas fa-check mr-1"></i> Selesai
                                </span>
                            @else
                                <span class="text-red-600 bg-red-100 px-2 py-1 rounded-full text-xs font-bold animate-pulse">
                                    <i class="fas fa-times mr-1"></i> Belum
                                </span>
                            @endif
                        </td>

                        {{-- STATUS ATASAN --}}
                        <td class="px-6 py-4 text-center">
                            @if($kry->status_atasan == 'DONE')
                                <span class="text-green-600 bg-green-100 px-2 py-1 rounded-full text-xs font-bold">
                                    <i class="fas fa-check mr-1"></i> Selesai
                                </span>
                            @elseif($kry->status_atasan == 'NA')
                                <span class="text-gray-400 text-xs italic">- Tidak Perlu -</span>
                            @else
                                <span class="text-orange-600 bg-orange-100 px-2 py-1 rounded-full text-xs font-bold">
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
                                <a href="{{ $linkWa }}" target="_blank" class="text-green-600 hover:text-green-800 hover:bg-green-100 p-2 rounded-full transition" title="Kirim Reminder via WA">
                                    <i class="fab fa-whatsapp text-xl"></i>
                                </a>
                            @else
                                <span class="text-gray-300"><i class="fas fa-check-double"></i></span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-search text-4xl mb-3 text-gray-300"></i>
                                <p>Tidak ada data karyawan yang sesuai filter.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection