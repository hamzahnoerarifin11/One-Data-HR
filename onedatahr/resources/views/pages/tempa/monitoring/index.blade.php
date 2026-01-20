@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Monitoring TEMPA</h1>
        <p class="mt-1 text-gray-600 dark:text-gray-400">Rekapitulasi dan monitoring program TEMPA secara lengkap dan mudah</p>
    </div>

    <!-- Filter Dinamis -->
    <form method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun</label>
            <select name="tahun" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:bg-gray-900 dark:text-white">
                @for($y = date('Y')-2; $y <= date('Y')+1; $y++)
                    <option value="{{ $y }}" {{ request('tahun', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kelompok</label>
            <select name="kelompok" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:bg-gray-900 dark:text-white">
                <option value="">Semua</option>
                @foreach($listKelompok ?? [] as $kel)
                    <option value="{{ $kel->id_kelompok }}" {{ request('kelompok') == $kel->id_kelompok ? 'selected' : '' }}>{{ $kel->nama_kelompok }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
            <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:bg-gray-900 dark:text-white">
                <option value="">Semua</option>
                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Pindah</option>
                <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Keluar</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari Peserta</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama/NIK..." class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:bg-gray-900 dark:text-white">
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full rounded-lg bg-blue-600 text-white px-4 py-2 font-semibold hover:bg-blue-700">Filter</button>
        </div>
    </form>

    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-5">
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Total Peserta</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Semua status</p>
                </div>
                <div class="text-3xl font-bold text-blue-600">{{ $pesertas ? $pesertas->count() : 0 }}</div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Peserta Aktif</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Status aktif</p>
                </div>
                <div class="text-3xl font-bold text-green-600">{{ $pesertas ? $pesertas->where('status_peserta', 1)->count() : 0 }}</div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Peserta Pindah</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Status pindah</p>
                </div>
                <div class="text-3xl font-bold text-yellow-600">{{ $pesertas ? $pesertas->where('status_peserta', 2)->count() : 0 }}</div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Peserta Keluar</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Status keluar</p>
                </div>
                <div class="text-3xl font-bold text-red-600">{{ $pesertas ? $pesertas->where('status_peserta', 3)->count() : 0 }}</div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Persentase Nasional</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Rata-rata kehadiran</p>
                </div>
                <div class="text-3xl font-bold text-green-600">{{ number_format($persentaseNasional ?? 0, 1) }}%</div>
            </div>
        </div>
    </div>

    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Statistik Per Kelompok</h2>
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="max-w-full overflow-x-auto">
                <table class="w-full min-w-full border-collapse">
                    <thead>
                        <tr class="border-y border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                            <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">Kelompok</th>
                            <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">Ketua TEMPA</th>
                            <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">Peserta Aktif</th>
                            <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">Total Peserta</th>
                            <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">Persentase Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapKelompok ?? [] as $stat)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20 border-b border-gray-200 dark:border-gray-700">
                            <td class="px-6 py-4 text-md font-medium text-gray-900 dark:text-white">{{ $stat['nama_kelompok'] }}</td>
                            <td class="px-6 py-4 text-md text-gray-600 dark:text-gray-300">{{ $stat['nama_mentor'] }}</td>
                            <td class="px-6 py-4 text-md text-gray-600 dark:text-gray-300">{{ $stat['jumlah_peserta_aktif'] }}</td>
                            <td class="px-6 py-4 text-md text-gray-600 dark:text-gray-300">{{ $stat['total_peserta'] }}</td>
                            <td class="px-6 py-4 text-md">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-medium
                                    @if($stat['persentase'] >= 80) bg-green-100 text-green-700
                                    @elseif($stat['persentase'] >= 60) bg-yellow-100 text-yellow-700
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ number_format($stat['persentase'], 1) }}%
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Data kelompok tidak tersedia</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div>
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Detail Peserta</h2>
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="max-w-full overflow-x-auto">
                <table class="w-full min-w-full border-collapse">
                    <thead>
                        <tr class="border-y border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                            <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">NO</th>
                            <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">STATUS</th>
                            <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">PESERTA</th>
                            <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">KELOMPOK</th>
                            <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">MENTOR</th>
                            <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">UNIT</th>
                            <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">SHIFT</th>
                            <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">TOTAL</th>
                            <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">JUMLAH</th>
                            <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">PERSEN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pesertas ?? [] as $index => $peserta)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20 border-b border-gray-200 dark:border-gray-700">
                            <td class="px-6 py-4 text-md text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-md">
                                <span class="inline-flex flex-col rounded-full px-3 py-1 text-xs font-medium
                                    @if($peserta->status_peserta == 1) bg-green-100 text-green-700
                                    @elseif($peserta->status_peserta == 2) bg-yellow-100 text-yellow-700
                                    @else bg-red-100 text-red-700 @endif">
                                    <span>{{ $peserta->status_label }}</span>
                                    @if($peserta->status_peserta == 2 && !empty($peserta->keterangan_pindah))
                                        <span class="block text-[11px] font-normal text-yellow-700 mt-0.5">{{ $peserta->keterangan_pindah }}</span>
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-md font-medium text-gray-900 dark:text-white">{{ $peserta->nama_peserta }}</td>
                            <td class="px-6 py-4 text-md text-gray-600 dark:text-gray-300">{{ $peserta->kelompok->nama_kelompok ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-md text-gray-600 dark:text-gray-300">{{ $peserta->kelompok->nama_mentor ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-md text-gray-600 dark:text-gray-300">{{ $peserta->unit ?? '-' }}</td>
                            <td class="px-6 py-4 text-md text-gray-600 dark:text-gray-300">{{ $peserta->shift ?? '-' }}</td>
                            <td class="px-6 py-4 text-md font-semibold text-gray-900 dark:text-white">{{ $peserta->total_hadir }}</td>
                            <td class="px-6 py-4 text-md text-gray-600 dark:text-gray-300">{{ $peserta->total_pertemuan }}</td>
                            <td class="px-6 py-4 text-md font-semibold text-gray-900 dark:text-white">{{ number_format($peserta->persentase_kehadiran, 1) }}%</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-6 py-4 text-center text-gray-500">Data peserta tidak tersedia</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
