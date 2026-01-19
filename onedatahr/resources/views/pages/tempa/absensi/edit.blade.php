@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Absensi TEMPA</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Perbarui data absensi peserta TEMPA</p>
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

            <a href="{{ route('tempa.absensi.show', $absensiModel->id_absensi) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.05] transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('tempa.absensi.update', $absensiModel->id_absensi) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Peserta Selection -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Informasi Peserta</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label for="id_peserta" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Pilih Peserta <span class="text-red-500">*</span>
                    </label>
                    <select name="id_peserta" id="id_peserta" required
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500">
                        <option value="">Pilih Peserta...</option>
                        @foreach($pesertas as $peserta)
                            <option value="{{ $peserta->id_peserta }}"
                                    {{ $absensiModel->id_peserta == $peserta->id_peserta ? 'selected' : '' }}
                                    data-nik="{{ $peserta->nik_karyawan }}"
                                    data-status="{{ $peserta->status_peserta }}"
                                    data-kelompok="{{ $peserta->kelompok->nama_kelompok ?? '' }}"
                                    data-mentor="{{ $peserta->kelompok->nama_mentor ?? '' }}"
                                    data-lokasi="{{ $peserta->kelompok->tempat ?? '' }}"
                                    data-cabang="{{ $peserta->kelompok->keterangan_cabang ?? '' }}">
                                {{ $peserta->nama_peserta }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">NIK Karyawan</label>
                    <input type="text" id="nik_display" readonly
                           class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                           value="{{ $absensiModel->peserta->nik_karyawan ?? '' }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status Peserta</label>
                    <input type="text" id="status_display" readonly
                           class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                           value="{{ $absensiModel->peserta->status_peserta == 1 ? 'Aktif' : ($absensiModel->peserta->status_peserta == 2 ? 'Pindah' : 'Keluar') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Kelompok</label>
                    <input type="text" id="kelompok_display" readonly
                           class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                           value="{{ $absensiModel->peserta->kelompok->nama_kelompok ?? '' }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Mentor</label>
                    <input type="text" id="mentor_display" readonly
                           class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                           value="{{ $absensiModel->peserta->kelompok->nama_mentor ?? '' }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lokasi Kelompok</label>
                    <input type="text" id="lokasi_display" readonly
                           class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                           value="{{ $absensiModel->peserta->kelompok->tempat === 'pusat' ? 'Pusat' : ($absensiModel->peserta->kelompok->tempat === 'cabang' ? 'Cabang' . ($absensiModel->peserta->kelompok->keterangan_cabang ? ' - ' . $absensiModel->peserta->kelompok->keterangan_cabang : '') : '') }}">
                </div>

                <div>
                    <label for="tahun_absensi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tahun Absensi <span class="text-red-500">*</span>
                    </label>
                    <select name="tahun_absensi" id="tahun_absensi" required
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500">
                        @for($year = date('Y') - 2; $year <= date('Y') + 2; $year++)
                            <option value="{{ $year }}" {{ $absensiModel->tahun_absensi == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>

        <!-- Absensi Bulanan -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Data Absensi Bulanan</h2>

            <div class="overflow-x-auto">
                <table class="w-full {{ request('bulan') ? 'min-w-[600px]' : 'min-w-[800px]' }}">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Bulan
                            </th>
                            @for($week = 1; $week <= 5; $week++)
                                <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[60px]">
                                    Minggu {{ $week }}
                                </th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @php
                            $namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            $selectedMonth = request('bulan');
                            $monthsToShow = $selectedMonth ? [$selectedMonth => $namaBulan[$selectedMonth - 1]] : [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                        @endphp

                        @foreach($monthsToShow as $monthIndex => $monthName)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $monthName }}
                                </td>
                                @for($week = 1; $week <= 5; $week++)
                                    @php
                                        $currentStatus = $absensiModel->getAbsensiBulan($monthIndex)[$week] ?? '';
                                    @endphp
                                    <td class="px-2 py-4 whitespace-nowrap text-center">
                                        <select name="absensi[{{ $monthIndex }}][{{ $week }}]"
                                                class="w-full text-sm rounded-lg border border-gray-300 px-2 py-1 text-gray-900 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                            <option value="" {{ $currentStatus === '' ? 'selected' : '' }}>Kosong</option>
                                            <option value="hadir" {{ $currentStatus === 'hadir' ? 'selected' : '' }}>Hadir</option>
                                            <option value="tidak_hadir" {{ $currentStatus === 'tidak_hadir' ? 'selected' : '' }}>Tidak Hadir</option>
                                        </select>
                                    </td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bukti Foto -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Bukti Foto Absensi</h2>

            <div class="space-y-4">
                @if($absensiModel->bukti_foto)
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0">
                            <img src="{{ Storage::url($absensiModel->bukti_foto) }}"
                                 alt="Bukti Foto Absensi"
                                 class="w-24 h-24 object-cover rounded-lg border border-gray-200 dark:border-gray-700">
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Foto bukti absensi saat ini. Upload foto baru untuk mengganti.
                            </p>
                        </div>
                    </div>
                @endif

                <div>
                    <label for="bukti_foto" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Upload Bukti Foto Baru (Opsional)
                    </label>
                    <input type="file"
                           name="bukti_foto"
                           id="bukti_foto"
                           accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Format: JPG, PNG, atau GIF. Maksimal 5MB.
                    </p>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('tempa.absensi.show', $absensiModel->id_absensi) }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-6 py-2.5 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.05] transition">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-center text-white font-medium hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Simpan Perubahan
            </button>
        </div>
    </form>
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

document.addEventListener('DOMContentLoaded', function() {
    const pesertaSelect = document.getElementById('id_peserta');
    const nikDisplay = document.getElementById('nik_display');
    const statusDisplay = document.getElementById('status_display');
    const kelompokDisplay = document.getElementById('kelompok_display');
    const mentorDisplay = document.getElementById('mentor_display');
    const lokasiDisplay = document.getElementById('lokasi_display');

    function updatePesertaInfo() {
        const selectedOption = pesertaSelect.options[pesertaSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            nikDisplay.value = selectedOption.getAttribute('data-nik') || '';
            statusDisplay.value = selectedOption.getAttribute('data-status') == '1' ? 'Aktif' :
                                selectedOption.getAttribute('data-status') == '2' ? 'Pindah' : 'Keluar';
            kelompokDisplay.value = selectedOption.getAttribute('data-kelompok') || '';
            mentorDisplay.value = selectedOption.getAttribute('data-mentor') || '';
            const tempat = selectedOption.getAttribute('data-lokasi');
            const cabang = selectedOption.getAttribute('data-cabang');
            lokasiDisplay.value = tempat === 'pusat' ? 'Pusat' :
                                tempat === 'cabang' ? 'Cabang' + (cabang ? ' - ' + cabang : '') : '';
        } else {
            nikDisplay.value = '';
            statusDisplay.value = '';
            kelompokDisplay.value = '';
            mentorDisplay.value = '';
            lokasiDisplay.value = '';
        }
    }

    // Initial update
    updatePesertaInfo();

    // Update on change
    pesertaSelect.addEventListener('change', updatePesertaInfo);
});
</script>
@endsection
