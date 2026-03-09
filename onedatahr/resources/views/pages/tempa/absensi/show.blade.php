@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Detail Absensi TEMPA</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Informasi lengkap data absensi peserta TEMPA</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Filter Bulan -->
            <div class="relative">
                <select id="bulanFilter" onchange="filterBulan(this.value)"
                        class="h-11 w-32 appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-8 text-sm text-gray-800 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="">Semua Bulan</option>
                    @php
                        $namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    @endphp
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ (request('bulan') == $i) ? 'selected' : '' }}>{{ $namaBulan[$i-1] }}</option>
                    @endfor
                </select>
                <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500">
                    <svg class="fill-current" width="18" height="18" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" /></svg>
                </span>
            </div>

            @can('editTempaAbsensi')
            <a href="{{ route('tempa.absensi.edit', $absensiModel->id_absensi) }}" class="inline-flex items-center gap-2 rounded-lg bg-yellow-500 px-5 py-2.5 text-center text-white font-medium hover:bg-yellow-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </a>
            @endcan
            @can('deleteTempaAbsensi')
            <button type="button" aria-label="Hapus Absensi" data-modal-id="delete-confirm" data-modal-target="deleteForm" data-modal-title="Hapus Data Absensi" data-modal-message="{{ e('Yakin ingin menghapus data absensi peserta: ' . $absensiModel->peserta->nama_peserta . '?') }}" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-5 py-2.5 text-center text-white font-medium hover:bg-red-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Hapus
            </button>
            @endcan
            <a href="{{ route('tempa.absensi.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.05] transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Informasi Peserta -->
    <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            Informasi Peserta
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nama Peserta</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $absensiModel->peserta->nama_peserta ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">NIK Karyawan</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $absensiModel->peserta->nik_karyawan ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Status Peserta</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">
                    @if($absensiModel->peserta->status_peserta == 1)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                            Aktif
                        </span>
                    @elseif($absensiModel->peserta->status_peserta == 2)
                        <span class="inline-flex flex-col items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                            <!-- <span>Pindah</span> -->
                            @if(!empty($absensiModel->peserta->keterangan_pindah))
                                <!-- <span class="block text-[11px] font-normal text-yellow-700 dark:text-yellow-200 mt-0.5">{{ $absensiModel->peserta->keterangan_pindah }}</span> -->
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                    Pindah{{ $absensiModel->peserta->keterangan_pindah ? ' - ' .  $absensiModel->peserta->keterangan_pindah : ''  }}
                                </span>
                            @endif
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                            Keluar
                        </span>
                    @endif
                </p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nama Kelompok</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $absensiModel->peserta->kelompok->nama_kelompok ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nama Mentor</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $absensiModel->peserta->kelompok->nama_mentor ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Lokasi Kelompok</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">
                    @php
                        $kelompok = $absensiModel->peserta?->kelompok;
                    @endphp

                    @if($kelompok?->tempat === 'pusat')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                            Pusat
                        </span>
                    @elseif($kelompok?->tempat === 'cabang')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                            Cabang{{ $kelompok?->keterangan_cabang ? ' - ' . $kelompok->keterangan_cabang : '' }}
                        </span>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif

                </p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tahun Absensi</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $absensiModel->tahun_absensi }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Hadir</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $absensiModel->total_hadir }} pertemuan</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Persentase Kehadiran</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ number_format($absensiModel->persentase, 1) }}%</p>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 flex justify-between items-center text-xs text-gray-500">
            <p>Dibuat pada: {{ $absensiModel->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</p>
            <p>Terakhir diperbarui: {{ $absensiModel->updated_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- Tabel Absensi Bulanan -->
    <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                Rekap Absensi Bulanan
                @if(request('bulan'))
                    - {{ $namaBulan[request('bulan')-1] }}
                @endif
            </h2>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Total: {{ $absensiModel->total_hadir }}/{{ $absensiModel->total_pertemuan }} pertemuan
                ({{ number_format($absensiModel->persentase, 1) }}%)
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[{{ request('bulan') ? '600px' : '800px' }}]">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Bulan
                        </th>
                        @if(request('bulan'))
                            <!-- Tampilkan hanya 5 minggu untuk bulan yang dipilih -->
                            @for($week = 1; $week <= 5; $week++)
                                <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[60px]">
                                    Minggu {{ $week }}
                                </th>
                            @endfor
                        @else
                            <!-- Tampilkan semua minggu untuk semua bulan -->
                            @for($week = 1; $week <= 5; $week++)
                                <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[60px]">
                                    Minggu {{ $week }}
                                </th>
                            @endfor
                        @endif
                        <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[80px]">
                            Total
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @php
                        $months = [
                            1 => 'Januari',
                            2 => 'Februari',
                            3 => 'Maret',
                            4 => 'April',
                            5 => 'Mei',
                            6 => 'Juni',
                            7 => 'Juli',
                            8 => 'Agustus',
                            9 => 'September',
                            10 => 'Oktober',
                            11 => 'November',
                            12 => 'Desember'
                        ];
                        $selectedBulan = request('bulan');
                    @endphp

                    @if($selectedBulan)
                        <!-- Tampilkan hanya bulan yang dipilih -->
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $months[$selectedBulan] }}
                            </td>
                            @php
                                $monthTotal = 0;
                            @endphp
                            @for($week = 1; $week <= 5; $week++)
                                @php
                                    $status = $absensiModel->getAbsensiBulan($selectedBulan)[$week] ?? null;
                                    if ($status === 'hadir') $monthTotal++;
                                @endphp
                                <td class="px-2 py-4 whitespace-nowrap text-center">
                                    @if($status === 'hadir')
                                        <div class="w-8 h-8 mx-auto bg-green-500 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    @elseif($status === 'tidak_hadir')
                                        <div class="w-8 h-8 mx-auto bg-red-500 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-8 h-8 mx-auto bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                                    @endif
                                </td>
                            @endfor
                            <td class="px-2 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900 dark:text-white">
                                {{ $monthTotal }}/5
                            </td>
                        </tr>
                    @else
                        <!-- Tampilkan semua bulan -->
                        @foreach($months as $monthNum => $monthName)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $monthName }}
                                </td>
                                @php
                                    $monthTotal = 0;
                                @endphp
                                @for($week = 1; $week <= 5; $week++)
                                    @php
                                        $status = $absensiModel->getAbsensiBulan($monthNum)[$week] ?? null;
                                        if ($status === 'hadir') $monthTotal++;
                                    @endphp
                                    <td class="px-2 py-4 whitespace-nowrap text-center">
                                        @if($status === 'hadir')
                                            <div class="w-8 h-8 mx-auto bg-green-500 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        @elseif($status === 'tidak_hadir')
                                            <div class="w-8 h-8 mx-auto bg-red-500 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-8 h-8 mx-auto bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                                        @endif
                                    </td>
                                @endfor
                                <td class="px-2 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $monthTotal }}/5
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
                @if(!request('bulan'))
                <tfoot>
                    <tr class="border-t border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                            TOTAL KESELURUHAN
                        </td>
                        <td colspan="5" class="px-4 py-3 text-center text-sm font-bold text-gray-900 dark:text-white">
                            {{ $absensiModel->total_hadir }}/{{ $absensiModel->total_pertemuan }} pertemuan
                            ({{ number_format($absensiModel->persentase, 1) }}%)
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Bukti Foto -->
    @if($absensiModel->bukti_foto)
    <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Bukti Foto Absensi</h2>

        <div class="flex justify-center">
            <a href="{{ Storage::url($absensiModel->bukti_foto) }}" target="_blank" class="block">
                <img src="{{ Storage::url($absensiModel->bukti_foto) }}"
                     alt="Bukti Foto Absensi"
                     class="max-w-md h-auto rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
            </a>
        </div>
        <p class="text-center text-sm text-gray-600 dark:text-gray-400 mt-4">
            Klik gambar untuk melihat dalam ukuran penuh
        </p>
    </div>
    @endif

    <!-- Modal Delete -->
    @can('deleteTempaAbsensi')
    <div id="delete-confirm" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Hapus Data Absensi</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" id="delete-message">Yakin ingin menghapus data absensi ini?</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <form id="deleteForm" method="POST" action="{{ route('tempa.absensi.destroy', $absensiModel->id_absensi) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Hapus
                        </button>
                    </form>
                    <button type="button" data-modal-close="delete-confirm" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endcan
</div>

<script>
function filterBulan(bulan) {
    const url = new URL(window.location);
    if (bulan) {
        url.searchParams.set('bulan', bulan);
    } else {
        url.searchParams.delete('bulan');
    }
    window.location.href = url.toString();
}
</script>
@endsection
