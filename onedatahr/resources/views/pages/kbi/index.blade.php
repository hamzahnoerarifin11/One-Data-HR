@extends('layouts.app') {{-- Sesuaikan dengan layout Anda --}}

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-6  text-gray-900 dark:text-white">PENILAIAN KBI</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- KARTU 1: PENILAIAN DIRI SENDIRI --}}
        <div class="bg-white p-6 rounded-xl shadow border border-blue-100">
            <h3 class="font-bold text-lg mb-2 text-blue-800">1. Penilaian Diri Sendiri</h3>
            <p class="text-sm text-gray-500 mb-4">Wajib dilakukan setiap semester.</p>
            
            @if($selfAssessment)
                <button disabled class="w-full bg-green-100 text-green-700 py-2 rounded font-bold">
                    <i class="fas fa-check"></i> Sudah Selesai (Skor: {{ $selfAssessment->rata_rata_akhir }})
                </button>
            @else
                <a href="{{ route('kbi.create', ['karyawan_id' => $karyawan->id_karyawan, 'tipe' => 'DIRI_SENDIRI']) }}" 
                   class="block text-center w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded font-bold">
                    Mulai Menilai
                </a>
            @endif
        </div>

        {{-- KARTU 2: PENILAIAN KE BAWAHAN (TEAM) --}}
        <div class="bg-white p-6 rounded-xl shadow border border-green-100 md:col-span-2">
            <h3 class="font-bold text-lg mb-2 text-green-800">2. Penilaian Anggota Tim (Sebagai Atasan)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-700 uppercase">
                        <tr>
                            <th class="p-3">Nama Anggota</th>
                            <th class="p-3">Jabatan</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($bawahanList as $staff)
                        <tr>
                            <td class="p-3 font-medium">{{ $staff->Nama_Lengkap_Sesuai_Ijazah }}</td>
                            <td class="p-3 text-gray-500">{{ $staff->pekerjaan->Jabatan }}</td>
                            <td class="p-3 text-center">
                                @if($staff->sudah_dinilai)
                                    <span class="text-green-600 font-bold text-xs">Selesai</span>
                                @else
                                    <a href="{{ route('kbi.create', ['karyawan_id' => $staff->id_karyawan, 'tipe' => 'ATASAN']) }}" 
                                       class="bg-green-600 text-green-300 px-3 py-1 rounded text-xs hover:bg-green-700">
                                        Nilai
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        @if($bawahanList->isEmpty())
                            <tr><td colspan="3" class="p-4 text-center text-gray-400">Tidak ada anggota tim.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- KARTU 3: PENILAIAN KE ATASAN (OPTIONAL) --}}
        @if($atasan)
        <div class="bg-white p-6 rounded-xl shadow border border-purple-100">
            <h3 class="font-bold text-lg mb-2 text-purple-800">3. Feedback ke Atasan</h3>
            <p class="text-sm text-gray-500 mb-2">Atasan: <strong>{{ $atasan->nama }}</strong></p>
            
            @if($sudahMenilaiAtasan)
                <button disabled class="w-full bg-green-100 text-green-700 py-2 rounded font-bold">
                    <i class="fas fa-check"></i> Sudah Selesai
                </button>
            @else
                <a href="{{ route('kbi.create', ['karyawan_id' => $atasan->id_karyawan, 'tipe' => 'BAWAHAN']) }}" 
                   class="block text-center w-full bg-purple-600 hover:bg-purple-700 text-white py-2 rounded font-bold">
                    Beri Masukan
                </a>
            @endif
        </div>
        @endif

    </div>
</div>
@endsection