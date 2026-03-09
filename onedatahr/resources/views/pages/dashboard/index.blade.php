@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-8 max-w-7xl mx-auto space-y-8">

    {{-- HEADER & DATE FILTER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        {{-- Welcome Banner --}}
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 text-white shadow-lg w-full md:w-2/3 relative overflow-hidden">
            <div class="relative z-10">
                <h1 class="text-2xl font-bold">Halo, {{ $karyawan->Nama_Lengkap_Sesuai_Ijazah }}! 👋</h1>
                <p class="opacity-90 mt-1">Dashboard Kinerja Tahun <strong>{{ $tahun }}</strong></p>
                <div class="mt-4 flex gap-2">
                    <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-semibold border border-white/30">
                        {{ $isManagerOrSpv ? $roleTitle : 'Staff' }}
                    </span>
                    <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-semibold border border-white/30">
                        {{ $karyawan->pekerjaan->first()?->position?->name ?? 'Karyawan' }}
                    </span>
                </div>
            </div>
            {{-- Decorative Circles --}}
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
            <div class="absolute bottom-0 right-10 w-24 h-24 bg-white/10 rounded-full -mb-12"></div>
        </div>

        {{-- Date Filter --}}
        <div class="w-full md:w-1/3 flex justify-end">
            <form method="GET" class="w-full md:w-auto bg-white dark:bg-gray-800 p-4 rounded-xl shadow border border-gray-100 dark:border-gray-700">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Periode Penilaian</label>
                <div class="flex gap-2">
                    <select name="tahun" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white transition">
                        @for($y = date('Y'); $y >= date('Y')-5; $y--)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </form>
        </div>
    </div>

    {{-- SECTION A: PERSONAL STATS (MY PERFORMANCE) --}}
    <div>
        <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-user-circle text-blue-600"></i> Kinerja Saya
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- CARD 1: KPI (Key Performance Indicator) --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-md transition group">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-base font-bold text-gray-700 dark:text-gray-200">KPI (Kinerja)</h3>
                            <p class="text-xs text-gray-400 mt-1">Key Performance Indicator</p>
                        </div>
                        <div class="bg-green-100 text-green-600 p-2 rounded-lg group-hover:scale-110 transition"><i class="fas fa-chart-line text-lg"></i></div>
                    </div>

                    @if($myKpi)
                        <div class="my-4">
                            @if($myKpi->status == 'FINAL')
                                <div class="flex items-end gap-2">
                                    <span class="text-4xl font-extrabold text-green-600">{{ $myKpi->total_skor_akhir }}</span>
                                    <span class="text-sm font-bold text-gray-500 mb-1">Skor Akhir</span>
                                </div>
                                <!-- Progress Bar Visual -->
                                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 mt-3 mb-2">
                                    <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ min($myKpi->total_skor_akhir, 100) }}%"></div>
                                </div>
                                <div class="mt-2 flex justify-between items-center">
                                    <div>
                                        <span class="inline-block px-2 py-0.5 bg-green-100 text-green-800 text-xs rounded font-bold border border-green-200">GRADE {{ $myKpi->grade }}</span>
                                        <span class="inline-block px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded font-bold border border-blue-200 ml-1">FINAL</span>
                                    </div>
                                    <span class="text-xs text-gray-500 font-medium">{{ min($myKpi->total_skor_akhir, 100) }}/100</span>
                                </div>
                            @elseif($myKpi->status == 'SUBMITTED')
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-center">
                                    <span class="block text-yellow-600 font-bold text-sm mb-1"><i class="fas fa-clock"></i> Menunggu Approval</span>
                                    <p class="text-xs text-gray-500">Dokumen sedang direview atasan</p>
                                </div>
                            @else
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-center">
                                    <span class="block text-gray-600 font-bold text-sm mb-1"><i class="fas fa-pencil-alt"></i> Draft</span>
                                    <p class="text-xs text-gray-500">Silakan lengkapi dokumen KPI Anda</p>
                                </div>
                            @endif
                        </div>
                        
                        <a href="{{ route('kpi.show', ['karyawan_id' => $karyawan->id_karyawan, 'tahun' => $tahun]) }}" 
                           class="flex w-full justify-center items-center gap-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:text-blue-600 font-medium py-2 rounded-lg transition text-sm shadow-sm">
                            <i class="fas fa-external-link-alt"></i> Buka Dokumen KPI
                        </a>
                    @else
                        <div class="text-center py-6">
                            <p class="text-sm text-gray-500 mb-4">Anda belum membuat KPI tahun {{ $tahun }}.</p>
                            @if($isManagerOrSpv)
                                {{-- Manager/Supervisor: Arahkan ke KPI Dashboard untuk mengelola/membuat --}}
                                <a href="{{ route('kpi.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow transition text-sm">
                                    <i class="fas fa-tachometer-alt"></i> Ke KPI Dashboard
                                </a>
                            @else
                                {{-- Staff: Form Create Langsung --}}
                                <form action="{{ route('kpi.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="karyawan_id" value="{{ $karyawan->id_karyawan }}">
                                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow transition text-sm">
                                        <i class="fas fa-plus-circle"></i> Buat KPI Baru
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- CARD 2: KBI (Key Behavioral Indicator) --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-md transition group">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-base font-bold text-gray-700 dark:text-gray-200">KBI (Perilaku)</h3>
                            <p class="text-xs text-gray-400 mt-1">Key Behavioral Indicator (Self Assessment)</p>
                        </div>
                        <div class="bg-purple-100 text-purple-600 p-2 rounded-lg group-hover:scale-110 transition"><i class="fas fa-user-check text-lg"></i></div>
                    </div>

                    @if($myKbi)
                        <div class="my-4">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                                <span class="block text-green-600 font-bold text-sm mb-1"><i class="fas fa-check-circle"></i> Sudah Dinilai</span>
                                <p class="text-xs text-gray-500">Terima kasih telah melakukan self-assessment.</p>
                            </div>
                        </div>
                        <button class="w-full bg-gray-100 text-gray-400 font-medium py-2 rounded-lg cursor-not-allowed text-sm">
                            Selesai
                        </button>
                    @else
                        <div class="text-center py-6">
                            <p class="text-sm text-gray-500 mb-4">Silakan isi penilaian perilaku diri sendiri.</p>
                            <a href="{{ route('kbi.index') }}" class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded-lg shadow transition text-sm">
                                Mulai Penilaian Diri
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION B: TEAM MONITORING (MANAGER & SPV ONLY) --}}
    @if($isManagerOrSpv)
        <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                <i class="fas fa-users-cog text-indigo-600"></i> Monitoring Tim ({{ $roleTitle }})
            </h2>

            {{-- Action Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Card Butuh Approval -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 p-4 rounded shadow-sm">
                    <div class="flex justify-between">
                        <div>
                            <p class="text-xs font-bold text-yellow-600 dark:text-yellow-400 uppercase">Menunggu Approval KPI</p>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $butuhApprovalKPI }}</h3>
                            <p class="text-xs text-gray-500">Permintaan dari anggota tim</p>
                        </div>
                        <div class="text-yellow-500 text-2xl opacity-50"><i class="fas fa-clipboard-check"></i></div>
                    </div>
                </div>

                <!-- Card Belum Dinilai -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 rounded shadow-sm">
                    <div class="flex justify-between">
                        <div>
                            <p class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase">Belum Dinilai (KBI)</p>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $belumDinilaiKBI }}</h3>
                            <p class="text-xs text-gray-500">Dari {{ $totalTim }} anggota tim</p>
                        </div>
                        <div class="text-blue-500 text-2xl opacity-50"><i class="fas fa-user-edit"></i></div>
                    </div>
                </div>

                <!-- Card Total Tim -->
                <div class="bg-white dark:bg-gray-800 border-l-4 border-gray-500 p-4 rounded shadow-sm">
                    <div class="flex justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase">Total Tim</p>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $totalTim }}</h3>
                            <p class="text-xs text-gray-500">Orang</p>
                        </div>
                        <div class="text-gray-400 text-2xl opacity-50"><i class="fas fa-users"></i></div>
                    </div>
                </div>
            </div>

            {{-- Monitoring Table --}}
            <div x-data="{ filterStatus: 'all' }" class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h3 class="font-bold text-gray-700 dark:text-white text-sm">Daftar Anggota Tim</h3>
                    {{-- Tab Filter Alpine.js --}}
                    <div class="flex bg-gray-200/50 dark:bg-gray-900/50 rounded-lg p-1 text-xs">
                        <button @click="filterStatus = 'all'" :class="{ 'bg-white dark:bg-gray-700 shadow text-blue-600 font-bold': filterStatus === 'all', 'text-gray-500 hover:text-gray-700': filterStatus !== 'all' }" class="px-3 py-1.5 rounded-md transition-all">Semua</button>
                        <button @click="filterStatus = 'needs_approval'" :class="{ 'bg-white dark:bg-gray-700 shadow text-yellow-600 font-bold': filterStatus === 'needs_approval', 'text-gray-500 hover:text-gray-700': filterStatus !== 'needs_approval' }" class="px-3 py-1.5 rounded-md transition-all">Butuh Approval</button>
                        <button @click="filterStatus = 'draft'" :class="{ 'bg-white dark:bg-gray-700 shadow text-gray-800 dark:text-gray-200 font-bold': filterStatus === 'draft', 'text-gray-500 hover:text-gray-700': filterStatus !== 'draft' }" class="px-3 py-1.5 rounded-md transition-all">Draft KPI/KBI</button>
                        <button @click="filterStatus = 'done'" :class="{ 'bg-white dark:bg-gray-700 shadow text-green-600 font-bold': filterStatus === 'done', 'text-gray-500 hover:text-gray-700': filterStatus !== 'done' }" class="px-3 py-1.5 rounded-md transition-all">Selesai</button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                            <tr>
                                <th class="px-6 py-3">Nama Karyawan</th>
                                <th class="px-6 py-3 text-center">Status KPI</th>
                                <th class="px-6 py-3 text-center">Status KBI (Appraisal)</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teamMonitoring as $member)
                                @php 
                                    $kpi = $member->kpiAssessment; 
                                    $kbi = $member->kbiAssessment;
                                    $kpiStatus = $kpi ? strtoupper($kpi->status) : 'BELUM BUAT';
                                    $kbiDone = ($kbi && $kbi->isNotEmpty());

                                    $rowCategory = 'draft';
                                    if ($kpiStatus == 'SUBMITTED') {
                                        $rowCategory = 'needs_approval';
                                    } elseif (in_array($kpiStatus, ['FINAL', 'APPROVED', 'DONE']) && $kbiDone) {
                                        $rowCategory = 'done';
                                    }
                                    // if KPI is done but KBI is pending, we can classify it into a specific bucket or leave as 'draft' so they know work is needed
                                @endphp
                                <tr x-show="filterStatus === 'all' || filterStatus === '{{ $rowCategory }}'" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                                                {{ substr($member->Nama_Lengkap_Sesuai_Ijazah, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $member->Nama_Lengkap_Sesuai_Ijazah }}</div>
                                                <div class="text-xs text-gray-500">{{ $member->pekerjaan->first()?->unit?->name ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    {{-- STATUS KPI --}}
                                    <td class="px-6 py-4 text-center">
                                        @if($kpi)
                                            @php $status = strtoupper($kpi->status); @endphp
                                            @if(in_array($status, ['FINAL', 'APPROVED', 'DONE']))
                                                <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-bold">
                                                    <i class="fas fa-check mr-1"></i> {{ $kpi->total_skor_akhir }}
                                                </span>
                                            @elseif($status == 'SUBMITTED')
                                                <span class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full font-bold animate-pulse cursor-pointer" title="Butuh Approval">
                                                    <i class="fas fa-exclamation-circle mr-1"></i> Review
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full font-bold">
                                                    Draft
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-xs text-gray-400 italic">Belum buat</span>
                                        @endif
                                    </td>

                                    {{-- STATUS KBI --}}
                                    <td class="px-6 py-4 text-center">
                                        @if($kbi && $kbi->isNotEmpty())
                                            <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-bold">
                                                <i class="fas fa-check-double mr-1"></i> Done
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full font-bold">
                                                <i class="fas fa-times mr-1"></i> Pending
                                            </span>
                                        @endif
                                    </td>

                                    {{-- AKSI --}}
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            @if($kpi)
                                                @if(strtoupper($kpi->status) == 'SUBMITTED')
                                                    <a href="{{ route('kpi.show', ['karyawan_id' => $member->id_karyawan, 'tahun' => $tahun]) }}" 
                                                       class="text-yellow-700 hover:text-yellow-900 bg-yellow-100 hover:bg-yellow-200 px-3 py-1.5 rounded text-xs font-bold transition whitespace-nowrap">
                                                        <i class="fas fa-search mr-1"></i> Review KPI
                                                    </a>
                                                @else
                                                    <a href="{{ route('kpi.show', ['karyawan_id' => $member->id_karyawan, 'tahun' => $tahun]) }}" 
                                                       class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded text-xs font-semibold transition whitespace-nowrap">
                                                        View KPI
                                                    </a>
                                                @endif
                                            @endif

                                            @if(!$kbi || $kbi->isEmpty())
                                                <a href="{{ route('kbi.create', ['karyawan_id' => $member->id_karyawan, 'tipe' => 'ATASAN']) }}" 
                                                   class="text-white bg-purple-600 hover:bg-purple-700 px-3 py-1.5 rounded text-xs font-semibold shadow transition whitespace-nowrap">
                                                    <i class="fas fa-edit mr-1"></i> Nilai KBI
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-users-slash text-2xl mb-2 text-gray-300"></i>
                                            <p>Tidak ada anggota tim yang ditemukan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 border-t dark:border-gray-700">
                    {{ $teamMonitoring->links() }}
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
