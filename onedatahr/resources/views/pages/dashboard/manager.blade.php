@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 space-y-6">
    
    {{-- Header & Filter --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Manager Dashboard</h1>
            <p class="text-sm text-gray-500">Monitoring Kinerja Tim: <span class="font-semibold text-blue-600">{{ $manager->Nama_Lengkap_Sesuai_Ijazah }}</span></p>
        </div>
        {{-- Filter Tahun (Opsional, jika ingin lihat history) --}}
        <form method="GET" class="flex justify-end">
            <div class="flex flex-col items-end gap-1">
                <div class="flex items-center gap-2">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        <i class="fas fa-calendar-alt mr-1 text-blue-600"></i>Pilih Tahun:
                    </label>
                    <select name="tahun" onchange="this.form.submit()" class="px-3 py-2 border border-blue-300 dark:border-blue-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm">
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
    {{-- <div class="mt-1 text-xs text-gray-600 dark:text-gray-400 italic">
        Menampilkan tahun <strong class="text-blue-600 dark:text-blue-400">{{ $tahun }}</strong>
    </div> --}}

    {{-- Action Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 p-4 rounded shadow-sm">
            <div class="flex justify-between">
                <div>
                    <p class="text-xs font-bold text-yellow-600 dark:text-yellow-400 uppercase">Menunggu Approval KPI</p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $butuhApprovalKPI }}</h3>
                    <p class="text-xs text-gray-500">Bawahan sudah submit</p>
                </div>
                <div class="text-yellow-500 text-3xl opacity-50"><i class="fas fa-clipboard-check"></i></div>
            </div>
        </div>

        <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 rounded shadow-sm">
            <div class="flex justify-between">
                <div>
                    <p class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase">Belum Dinilai (KBI)</p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $belumDinilaiKBI }}</h3>
                    <p class="text-xs text-gray-500">Dari {{ $totalTim }} anggota tim</p>
                </div>
                <div class="text-blue-500 text-3xl opacity-50"><i class="fas fa-user-edit"></i></div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border-l-4 border-gray-500 p-4 rounded shadow-sm">
            <div class="flex justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase">Total Tim</p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $totalTim }}</h3>
                    <p class="text-xs text-gray-500">Orang</p>
                </div>
                <div class="text-gray-400 text-3xl opacity-50"><i class="fas fa-users"></i></div>
            </div>
        </div>
    </div>

    {{-- Monitoring Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            <h3 class="font-bold text-gray-700 dark:text-white">Monitoring Anggota Tim ({{ $tahun }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                    <tr>
                        <th class="px-6 py-3">Nama Karyawan</th>
                        <th class="px-6 py-3 text-center">Status KPI</th>
                        <th class="px-6 py-3 text-center">Status KBI (Atasan)</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teamMonitoring as $member)
                        @php 
                            $kpi = $member->kpiAssessment; 
                            $kbi = $member->kbiAssessment;
                        @endphp
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $member->Nama_Lengkap_Sesuai_Ijazah }}</div>
                                <div class="text-xs text-gray-500">{{ $member->pekerjaan->first()?->Jabatan ?? '-' }}</div>
                            </td>
                            
                            {{-- STATUS KPI --}}
                            <td class="px-6 py-4 text-center">
                                @if($kpi)
                                    @php $status = strtoupper($kpi->status); @endphp

                                    {{-- 1. FINAL / APPROVED --}}
                                    @if(in_array($status, ['FINAL', 'APPROVED', 'DONE']))
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-bold">
                                            Final: {{ $kpi->total_skor_akhir }}
                                        </span>

                                    {{-- 2. SUBMITTED (Butuh Approval Manager) --}}
                                    @elseif($status == 'SUBMITTED')
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full font-bold animate-pulse">
                                            Butuh Approval
                                        </span>

                                    {{-- 3. DRAFT (Masih diisi karyawan) --}}
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full font-bold">
                                            Draft / Proses
                                        </span>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400 italic">Belum Dibuat</span>
                                @endif
                            </td>

                            {{-- STATUS KBI --}}
                            <td class="px-6 py-4 text-center">
                                @php
                                    // 1. CARI DATA DIRI MANAGER (Current User)
                                    // Kita cari manual biar aman, tidak tergantung variabel controller
                                    $meManager = \App\Models\Karyawan::where('nik', auth()->user()->nik)->first();

                                    // 2. CARI ID USER STAFF (Penilai)
                                    // Gunakan DB Query biar tidak kena error "Class User not found" lagi
                                    $idUserStaff = \Illuminate\Support\Facades\DB::table('users')
                                                    ->where('nik', $member->NIK)
                                                    ->value('id');
                                    
                                    $statusNilaiAtasan = false;

                                    // 3. CEK DATABASE (Hanya jika data lengkap)
                                    if($meManager && $idUserStaff) {
                                        $cekKbi = \Illuminate\Support\Facades\DB::table('kbi_assessments')
                                                    ->where('penilai_id', $idUserStaff) // Penilainya si Staff
                                                    ->where('karyawan_id', $meManager->id_karyawan) // Targetnya Saya (Manager)
                                                    ->where('tipe_penilai', 'BAWAHAN') // Tipe Bawahan
                                                    ->where('tahun', $tahun)
                                                    ->exists(); // Cukup cek ada/tidak (True/False)
                                        
                                        if($cekKbi) {
                                            $statusNilaiAtasan = true;
                                        }
                                    }
                                @endphp

                                @if($statusNilaiAtasan)
                                    {{-- Jika Data KBI Ditemukan --}}
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-bold">
                                        <i class="fas fa-check-double mr-1"></i> Sudah Menilai
                                    </span>
                                @else
                                    {{-- Jika Data KBI Tidak Ada --}}
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full font-bold">
                                            <i class="fas fa-times-circle mr-1"></i> Belum Menilai
                                        </span>
                                        {{-- Opsional: Info tambahan --}}
                                        <span class="text-[10px] text-gray-400 italic">Menunggu Staff</span>
                                    </div>
                                @endif
                            </td>

                            {{-- AKSI --}}
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    {{-- Tombol Lihat KPI --}}
                                    @if($kpi)
                                        <a href="{{ route('kpi.show', ['karyawan_id' => $member->id_karyawan, 'tahun' => $tahun]) }}" 
                                           class="text-blue-600 border border-blue-600 hover:bg-blue-600 hover:text-white px-2 py-1 rounded text-xs transition" title="Lihat/Review KPI">
                                            <i class="fas fa-eye"></i> KPI
                                        </a>
                                    @endif

                                    {{-- Tombol Nilai KBI --}}
                                    @if(!$kbi)
                                        <a href="#" class="bg-blue-600 text-white hover:bg-blue-700 px-3 py-1 rounded text-xs shadow transition" title="Nilai Perilaku">
                                            <i class="fas fa-pen"></i> Nilai KBI
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada anggota tim ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $teamMonitoring->links() }}
        </div>
    </div>
</div>
@endsection