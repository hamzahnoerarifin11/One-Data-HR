@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Tambah Data Absensi TEMPA</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Input data absensi peserta TEMPA untuk satu tahun</p>
        </div>
        <a href="{{ route('tempa.absensi.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.05] transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali
        </a>
    </div>

    <form action="{{ route('tempa.absensi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Informasi Dasar -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Informasi Dasar</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Peserta -->
                <div>
                    <label for="id_peserta" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Pilih Peserta <span class="text-red-500">*</span>
                    </label>
                    <select id="id_peserta" name="id_peserta"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                            required>
                        <option value="">Pilih Peserta</option>
                        @foreach($pesertas as $peserta)
                            <option value="{{ $peserta->id_peserta }}"
                                    data-kelompok="{{ $peserta->kelompok->nama_kelompok ?? '-' }}"
                                    data-mentor="{{ $peserta->kelompok->nama_mentor ?? '-' }}"
                                    data-lokasi="{{ $peserta->kelompok->tempat ?? '-' }}"
                                    data-keterangan="{{ $peserta->kelompok->keterangan_cabang ?? '' }}">
                                {{ $peserta->nama_peserta }} - {{ $peserta->nik_karyawan }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_peserta')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tahun Absensi -->
                <div>
                    <label for="tahun_absensi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tahun Absensi <span class="text-red-500">*</span>
                    </label>
                    <select id="tahun_absensi" name="tahun_absensi"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                            required>
                        @for($year = date('Y') - 1; $year <= date('Y') + 1; $year++)
                            <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                    @error('tahun_absensi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Info Peserta Terpilih -->
            <div id="peserta-info" class="mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg hidden">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Informasi Peserta Terpilih</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-600 dark:text-gray-400">Kelompok:</span>
                        <span id="info-kelompok" class="ml-2 text-gray-900 dark:text-white">-</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-600 dark:text-gray-400">Mentor:</span>
                        <span id="info-mentor" class="ml-2 text-gray-900 dark:text-white">-</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-600 dark:text-gray-400">Lokasi:</span>
                        <span id="info-lokasi" class="ml-2 text-gray-900 dark:text-white">-</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Absensi Bulanan -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Data Absensi Bulanan</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Pilih status kehadiran untuk setiap minggu (1-5 minggu per bulan). Kosongkan jika tidak ada pertemuan TEMPA.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Januari -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b pb-2">Januari</h3>
                    @for($i = 1; $i <= 5; $i++)
                        <div class="flex items-center justify-between">
                            <label for="absensi_jan_{{ $i }}" class="text-sm text-gray-700 dark:text-gray-300">
                                Minggu {{ $i }}
                            </label>
                            <select id="absensi_jan_{{ $i }}" name="absensi[jan][{{ $i }}]"
                                    class="ml-2 w-32 text-sm rounded-lg border border-gray-300 px-2 py-1 text-gray-900 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="">Kosong</option>
                                <option value="hadir">Hadir</option>
                                <option value="tidak_hadir">Tidak Hadir</option>
                            </select>
                        </div>
                    @endfor
                </div>

                <!-- Februari -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b pb-2">Februari</h3>
                    @for($i = 1; $i <= 5; $i++)
                        <div class="flex items-center justify-between">
                            <label for="absensi_feb_{{ $i }}" class="text-sm text-gray-700 dark:text-gray-300">
                                Minggu {{ $i }}
                            </label>
                            <select id="absensi_feb_{{ $i }}" name="absensi[feb][{{ $i }}]"
                                    class="ml-2 w-32 text-sm rounded-lg border border-gray-300 px-2 py-1 text-gray-900 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="">Kosong</option>
                                <option value="hadir">Hadir</option>
                                <option value="tidak_hadir">Tidak Hadir</option>
                            </select>
                        </div>
                    @endfor
                </div>

                <!-- Maret -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b pb-2">Maret</h3>
                    @for($i = 1; $i <= 5; $i++)
                        <div class="flex items-center justify-between">
                            <label for="absensi_mar_{{ $i }}" class="text-sm text-gray-700 dark:text-gray-300">
                                Minggu {{ $i }}
                            </label>
                            <select id="absensi_mar_{{ $i }}" name="absensi[mar][{{ $i }}]"
                                    class="ml-2 w-32 text-sm rounded-lg border border-gray-300 px-2 py-1 text-gray-900 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="">Kosong</option>
                                <option value="hadir">Hadir</option>
                                <option value="tidak_hadir">Tidak Hadir</option>
                            </select>
                        </div>
                    @endfor
                </div>

                <!-- April -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b pb-2">April</h3>
                    @for($i = 1; $i <= 5; $i++)
                        <div class="flex items-center justify-between">
                            <label for="absensi_apr_{{ $i }}" class="text-sm text-gray-700 dark:text-gray-300">
                                Minggu {{ $i }}
                            </label>
                            <select id="absensi_apr_{{ $i }}" name="absensi[apr][{{ $i }}]"
                                    class="ml-2 w-32 text-sm rounded-lg border border-gray-300 px-2 py-1 text-gray-900 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="">Kosong</option>
                                <option value="hadir">Hadir</option>
                                <option value="tidak_hadir">Tidak Hadir</option>
                            </select>
                        </div>
                    @endfor
                </div>

                <!-- Mei -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b pb-2">Mei</h3>
                    @for($i = 1; $i <= 5; $i++)
                        <div class="flex items-center justify-between">
                            <label for="absensi_mei_{{ $i }}" class="text-sm text-gray-700 dark:text-gray-300">
                                Minggu {{ $i }}
                            </label>
                            <select id="absensi_mei_{{ $i }}" name="absensi[mei][{{ $i }}]"
                                    class="ml-2 w-32 text-sm rounded-lg border border-gray-300 px-2 py-1 text-gray-900 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="">Kosong</option>
                                <option value="hadir">Hadir</option>
                                <option value="tidak_hadir">Tidak Hadir</option>
                            </select>
                        </div>
                    @endfor
                </div>

                <!-- Juni -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b pb-2">Juni</h3>
                    @for($i = 1; $i <= 5; $i++)
                        <div class="flex items-center justify-between">
                            <label for="absensi_jun_{{ $i }}" class="text-sm text-gray-700 dark:text-gray-300">
                                Minggu {{ $i }}
                            </label>
                            <select id="absensi_jun_{{ $i }}" name="absensi[jun][{{ $i }}]"
                                    class="ml-2 w-32 text-sm rounded-lg border border-gray-300 px-2 py-1 text-gray-900 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="">Kosong</option>
                                <option value="hadir">Hadir</option>
                                <option value="tidak_hadir">Tidak Hadir</option>
                            </select>
                        </div>
                    @endfor
                </div>

                <!-- Juli -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b pb-2">Juli</h3>
                    @for($i = 1; $i <= 5; $i++)
                        <div class="flex items-center justify-between">
                            <label for="absensi_jul_{{ $i }}" class="text-sm text-gray-700 dark:text-gray-300">
                                Minggu {{ $i }}
                            </label>
                            <select id="absensi_jul_{{ $i }}" name="absensi[jul][{{ $i }}]"
                                    class="ml-2 w-32 text-sm rounded-lg border border-gray-300 px-2 py-1 text-gray-900 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="">Kosong</option>
                                <option value="hadir">Hadir</option>
                                <option value="tidak_hadir">Tidak Hadir</option>
                            </select>
                        </div>
                    @endfor
                </div>

                <!-- Agustus -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b pb-2">Agustus</h3>
                    @for($i = 1; $i <= 5; $i++)
                        <div class="flex items-center justify-between">
                            <label for="absensi_agu_{{ $i }}" class="text-sm text-gray-700 dark:text-gray-300">
                                Minggu {{ $i }}
                            </label>
                            <select id="absensi_agu_{{ $i }}" name="absensi[agu][{{ $i }}]"
                                    class="ml-2 w-32 text-sm rounded-lg border border-gray-300 px-2 py-1 text-gray-900 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="">Kosong</option>
                                <option value="hadir">Hadir</option>
                                <option value="tidak_hadir">Tidak Hadir</option>
                            </select>
                        </div>
                    @endfor
                </div>

                <!-- September -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b pb-2">September</h3>
                    @for($i = 1; $i <= 5; $i++)
                        <div class="flex items-center justify-between">
                            <label for="absensi_sep_{{ $i }}" class="text-sm text-gray-700 dark:text-gray-300">
                                Minggu {{ $i }}
                            </label>
                            <select id="absensi_sep_{{ $i }}" name="absensi[sep][{{ $i }}]"
                                    class="ml-2 w-32 text-sm rounded-lg border border-gray-300 px-2 py-1 text-gray-900 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="">Kosong</option>
                                <option value="hadir">Hadir</option>
                                <option value="tidak_hadir">Tidak Hadir</option>
                            </select>
                        </div>
                    @endfor
                </div>

                <!-- Oktober -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b pb-2">Oktober</h3>
                    @for($i = 1; $i <= 5; $i++)
                        <div class="flex items-center justify-between">
                            <label for="absensi_okt_{{ $i }}" class="text-sm text-gray-700 dark:text-gray-300">
                                Minggu {{ $i }}
                            </label>
                            <select id="absensi_okt_{{ $i }}" name="absensi[okt][{{ $i }}]"
                                    class="ml-2 w-32 text-sm rounded-lg border border-gray-300 px-2 py-1 text-gray-900 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="">Kosong</option>
                                <option value="hadir">Hadir</option>
                                <option value="tidak_hadir">Tidak Hadir</option>
                            </select>
                        </div>
                    @endfor
                </div>

                <!-- November -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b pb-2">November</h3>
                    @for($i = 1; $i <= 5; $i++)
                        <div class="flex items-center justify-between">
                            <label for="absensi_nov_{{ $i }}" class="text-sm text-gray-700 dark:text-gray-300">
                                Minggu {{ $i }}
                            </label>
                            <select id="absensi_nov_{{ $i }}" name="absensi[nov][{{ $i }}]"
                                    class="ml-2 w-32 text-sm rounded-lg border border-gray-300 px-2 py-1 text-gray-900 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="">Kosong</option>
                                <option value="hadir">Hadir</option>
                                <option value="tidak_hadir">Tidak Hadir</option>
                            </select>
                        </div>
                    @endfor
                </div>

                <!-- Desember -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b pb-2">Desember</h3>
                    @for($i = 1; $i <= 5; $i++)
                        <div class="flex items-center justify-between">
                            <label for="absensi_des_{{ $i }}" class="text-sm text-gray-700 dark:text-gray-300">
                                Minggu {{ $i }}
                            </label>
                            <select id="absensi_des_{{ $i }}" name="absensi[des][{{ $i }}]"
                                    class="ml-2 w-32 text-sm rounded-lg border border-gray-300 px-2 py-1 text-gray-900 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="">Kosong</option>
                                <option value="hadir">Hadir</option>
                                <option value="tidak_hadir">Tidak Hadir</option>
                            </select>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Upload Bukti Foto -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Bukti Foto</h2>

            <div>
                <label for="bukti_foto" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Upload Bukti Foto Absensi
                </label>
                <input type="file" id="bukti_foto" name="bukti_foto"
                       class="w-full text-gray-900 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white"
                       accept="image/*">
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Upload foto bukti absensi (format: JPG, PNG, maksimal 2MB)
                </p>
                @error('bukti_foto')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('tempa.absensi.index') }}"
               class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                Batal
            </a>
            <button type="submit"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">
                Simpan Data Absensi
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('id_peserta').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const pesertaInfo = document.getElementById('peserta-info');

    if (this.value) {
        document.getElementById('info-kelompok').textContent = selectedOption.getAttribute('data-kelompok') || '-';
        document.getElementById('info-mentor').textContent = selectedOption.getAttribute('data-mentor') || '-';

        const tempat = selectedOption.getAttribute('data-lokasi') || '-';
        const keterangan = selectedOption.getAttribute('data-keterangan') || '';
        const lokasiText = tempat === 'cabang' && keterangan ? `Cabang - ${keterangan}` : tempat.charAt(0).toUpperCase() + tempat.slice(1);
        document.getElementById('info-lokasi').textContent = lokasiText;

        pesertaInfo.classList.remove('hidden');
    } else {
        pesertaInfo.classList.add('hidden');
    }
});
</script>
@endsection
