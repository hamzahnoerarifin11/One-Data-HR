@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Detail Karyawan</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Informasi lengkap karyawan</p>
        </div>
        <div class="flex items-center gap-2">
            @if(auth()->user() && auth()->user()->role === 'admin')
                <a href="{{ route('karyawan.edit', $karyawan->id_karyawan) }}" class="inline-flex items-center gap-2 rounded-lg bg-orange-600 px-5 py-2.5 text-center text-white font-medium hover:bg-orange-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
                <button type="button" aria-label="Hapus Karyawan" data-modal-id="delete-confirm" data-modal-target="deleteForm" data-modal-title="Hapus Karyawan" data-modal-message="{{ e('Yakin ingin menghapus karyawan: ' . $karyawan->Nama_Sesuai_KTP . '?') }}" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-5 py-2.5 text-center text-white font-medium hover:bg-red-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Hapus
                </button>
            @endif
            <a href="{{ route('karyawan.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.05] transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- ========================= SHOW: KARYAWAN ========================= -->

    <!-- Informasi Dasar -->
    <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            <!-- <i class="fa-solid fa-user text-blue-600"></i>  -->
            Informasi Karyawan
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Left column items -->
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">NIK</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $karyawan->NIK ?? '-' }}</p>
            </div>
            <!-- <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Status & Kode</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $karyawan->Status ?? '-' }}</p>
            </div> -->
            <!-- Status / Kode badge -->
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Status & Kode</p>
                @php
                    $kode = data_get($karyawan, 'Kode') ?? data_get($karyawan,'Status') ?? null;
                @endphp
                <span class="inline-flex items-center mt-1 px-8 py-1 rounded-full text-xs font-semibold
                    @if(strtolower($kode) === 'aktif' || $kode === '1') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                    @elseif(strtolower($kode) === 'tidak aktif' || $kode === '0') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                    @else bg-gray-100 text-gray-700 dark:bg-gray-800/40 dark:text-gray-300 @endif">
                    {{ $kode ?? '-' }}
                </span>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nama (Sesuai KTP)</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $karyawan->Nama_Sesuai_KTP ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">NIK (KTP)</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $karyawan->NIK_KTP ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nama (Sesuai Ijazah)</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $karyawan->Nama_Lengkap_Sesuai_Ijazah ?? '-' }}</p>
            </div>

            <!-- <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tempat Lahir</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $karyawan->Tempat_Lahir_Karyawan ?? '-' }}</p>
            </div> -->

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tempat & Tanggal Lahir</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">
                    {{ $karyawan->Tempat_Lahir_Karyawan ?? '-' }},
                    {{ $karyawan->Tanggal_Lahir_Karyawan ? \Carbon\Carbon::parse($karyawan->Tanggal_Lahir_Karyawan)->format('d M Y') : '-' }}
                </p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Umur</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $karyawan->Umur_Karyawan ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Jenis Kelamin</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">
                    @if($karyawan->Jenis_Kelamin_Karyawan === 'L') Laki-laki
                    @elseif($karyawan->Jenis_Kelamin_Karyawan === 'P') Perempuan
                    @else - @endif
                </p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Status Pernikahan</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $karyawan->Status_Pernikahan ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Golongan Darah</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $karyawan->Golongan_Darah ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Email</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">
                    @if($karyawan->Email)
                        <a href="mailto:{{ $karyawan->Email }}" class="underline hover:text-brand-600">{{ $karyawan->Email }}</a>
                    @else
                        -
                    @endif
                </p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nomor Telepon</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $karyawan->Nomor_Telepon_Aktif_Karyawan ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Data Alamat -->
    <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Informasi Alamat</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Alamat KTP</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $karyawan->Alamat_KTP ?? '-' }}</p>
            </div>
            <!-- <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">RT / RW</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ ($karyawan->RT ?? '-') . ' / ' . ($karyawan->RW ?? '-') }}</p>
            </div> -->
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">RT/RW / Kelurahan / Kecamatan</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ ($karyawan->RT ?? '-') . ' / ' . ($karyawan->RW ?? '-') }}, {{ ($karyawan->Kelurahan_Desa ?? '-') . ', ' . ($karyawan->Kecamatan ?? '-') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kabupaten / Provinsi</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ ($karyawan->Kabupaten_Kota ?? '-') . ', ' . ($karyawan->Provinsi ?? '-') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Alamat Domisili</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $karyawan->Alamat_Domisili ?? '-' }}</p>
            </div>
            <!-- <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">RT / RW Domisili</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ ($karyawan->RT_Sesuai_Domisili ?? '-') . ' / ' . ($karyawan->RW_Sesuai_Domisili ?? '-') }}</p>
            </div> -->
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">RT/RW / Kelurahan / Kecamatan Domisili</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ ($karyawan->RT_Sesuai_Domisili ?? '-') . ' / ' . ($karyawan->RW_Sesuai_Domisili ?? '-') }}, {{ ($karyawan->Kelurahan_Desa_Domisili ?? '-') . ', ' . ($karyawan->Kecamatan_Sesuai_Domisili ?? '-') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kabupaten / Provinsi</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ ($karyawan->Kabupaten_Kota_Sesuai_Domisili ?? '-') . ', ' . ($karyawan->Provinsi_Sesuai_Domisili ?? '-') }}</p>
            </div>
            <!-- <div> -->
            <!-- <div class="col-span-3">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Alamat Lengkap</p>
                <p class="text-base text-gray-700 dark:text-gray-300 mt-1">{{ $karyawan->Alamat_Lengkap ?? '-' }}</p>
            </div> -->
        </div>
    </div>

    <!-- Data Pekerjaan -->
    @if($karyawan->pekerjaan && $karyawan->pekerjaan->count() > 0)
    <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Informasi Pekerjaan</h2>
        @if($karyawan->perusahaan)
        <div class="mb-4 p-4 rounded-lg border border-gray-100 dark:border-gray-700">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Perusahaan</p>
            <p class="text-base font-semibold text-gray-900 dark:text-white mt-1">{{ $karyawan->perusahaan->Perusahaan ?? '-' }}</p>
        </div>
        @endif
        @foreach($karyawan->pekerjaan as $p)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
            <!-- grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 -->
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Jabatan</p>
                <p class="text-base font-semibold text-gray-900 dark:text-white mt-1">{{ $p->Jabatan ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Bagian</p>
                <p class="text-base font-semibold text-gray-900 dark:text-white mt-1">{{ $p->Bagian ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Departement</p>
                <p class="text-base font-semibold text-gray-900 dark:text-white mt-1">{{ $p->Departement ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Divisi</p>
                <p class="text-base font-semibold text-gray-900 dark:text-white mt-1">{{ $p->Divisi ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Unit</p>
                <p class="text-base font-semibold text-gray-900 dark:text-white mt-1">{{ $p->Unit ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Jenis Kontrak</p>
                <p class="text-base font-semibold text-gray-900 dark:text-white mt-1">{{ $p->Jenis_Kontrak ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Perjanjian Kerja</p>
                <p class="text-base font-semibold text-gray-900 dark:text-white mt-1">{{ $p->Perjanjian ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Lokasi Kerja</p>
                <p class="text-base font-semibold text-gray-900 dark:text-white mt-1">{{ $p->Lokasi_Kerja ?? '-' }}</p>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- ================= DATA PENDIDIKAN ================= -->
    @if($karyawan->pendidikan)
    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-white/[0.03]">

        <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">
            Pendidikan Terakhir
        </h2>

        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
            <div class="flex items-start gap-4">

                <!-- Icon -->
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z"/>
                    </svg>
                </div>

                <!-- Content -->
                <div class="flex-1">
                    <p class="font-semibold text-gray-900 dark:text-white">
                        {{ $karyawan->pendidikan->Pendidikan_Terakhir ?: '-' }}
                    </p>

                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $karyawan->pendidikan->Nama_Lengkap_Tempat_Pendidikan_Terakhir ?: '-' }}
                    </p>

                    <p class="text-sm text-gray-500 dark:text-gray-500">
                        Jurusan: {{ $karyawan->pendidikan->Jurusan ?: '-' }}
                    </p>
                </div>

            </div>
        </div>

    </div>
    @endif



    <!-- Data Keluarga -->
    @if($karyawan->keluarga)
    <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Data Keluarga</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nama Ayah</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $karyawan->keluarga->Nama_Ayah_Kandung ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nama Ibu</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $karyawan->keluarga->Nama_Ibu_Kandung ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nama Suami / Istri</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $karyawan->keluarga->Nama_Lengkap_Suami_Istri ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">NIK Suami / Istri</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $karyawan->keluarga->NIK_KTP_Suami_Istri ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nomor Telepon Suami / Istri</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $karyawan->keluarga->Nomor_Telepon_Suami_Istri ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pendidikan Suami / Istri</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $karyawan->keluarga->Pendidikan_Terakhir_Suami_Istri ?? '-' }}</p>
            </div>
        </div>
            <!-- ===== DATA ANAK ===== -->
            <div class="col-span-3 mt-6">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Anak-anak</p>
                @php
                    $anak = $karyawan->keluarga->anak ?? [];
                @endphp

                @if(empty($anak))
                    <div class="rounded-lg border border-dashed border-gray-300 p-4 text-center
                                text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                        Tidak ada data anak
                    </div>
                @else
                    <div class="space-y-4 mt-2">
                        @foreach($anak as $index => $item)
                            <div class="rounded-xl border border-gray-200 p-4
                                        dark:border-gray-800 dark:bg-gray-800/40">

                                <!-- Header Anak -->
                                <div class="mb-3 flex items-center justify-between">
                                    <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">
                                        Anak ke-{{ $index + 1 }}
                                    </h4>
                                </div>

                                <!-- Detail -->
                                <div class="grid grid-cols-2 gap-4 md:grid-cols-3">

                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Nama Anak</p>
                                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                            {{ $item['nama'] ?? '-' }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Tempat Lahir</p>
                                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                            {{ $item['tempat_lahir'] ?? '-' }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Tanggal Lahir</p>
                                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                            {{ !empty($item['tanggal_lahir'])
                                                ? \Carbon\Carbon::parse($item['tanggal_lahir'])->format('d M Y')
                                                : '-' }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Jenis Kelamin</p>
                                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                            @if(($item['jenis_kelamin'] ?? '') === 'L')
                                                Laki-laki
                                            @elseif(($item['jenis_kelamin'] ?? '') === 'P')
                                                Perempuan
                                            @else
                                                -
                                            @endif
                                        </p>
                                    </div>

                                    <div class="md:col-span-2">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Pendidikan Terakhir
                                        </p>
                                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                            {{ $item['pendidikan'] ?? '-' }}
                                        </p>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
            <!-- ===== END DATA ANAK ===== -->
        </div>
    </div>
    @endif

    <!-- Data Kontrak -->
    @if($karyawan->kontrak)
    <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
            Riwayat Kontrak
        </h2>

        <div class="grid grid-cols-3 gap-4">

            <!-- Tanggal Mulai Tugas -->
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tanggal Mulai Tugas</p>
                <p class="text-base text-gray-900 dark:text-white mt-1">
                    {{ $karyawan->kontrak->Tanggal_Mulai_Tugas
                        ? date('d M Y', strtotime($karyawan->kontrak->Tanggal_Mulai_Tugas))
                        : '-' }}
                </p>
            </div>

            <!-- PKWT Berakhir -->
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">PKWT Berakhir</p>
                <p class="text-base text-gray-900 dark:text-white mt-1">
                    {{ $karyawan->kontrak->PKWT_Berakhir
                        ? date('d M Y', strtotime($karyawan->kontrak->PKWT_Berakhir))
                        : '-' }}
                </p>
            </div>

            <!-- Masa Kerja -->
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Masa Kerja</p>
                <p class="text-base text-gray-900 dark:text-white mt-1">
                    {{ $karyawan->kontrak->Masa_Kerja ?? '-' }}
                </p>
            </div>

            <!-- Tanggal Diangkat Tetap -->
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                    Tanggal Diangkat Menjadi Tetap
                </p>
                <p class="text-base text-gray-900 dark:text-white mt-1">
                    {{ $karyawan->kontrak->Tanggal_Diangkat_Menjadi_Karyawan_Tetap
                        ? date('d M Y', strtotime($karyawan->kontrak->Tanggal_Diangkat_Menjadi_Karyawan_Tetap))
                        : '-' }}
                </p>
            </div>

            <!-- Riwayat Penempatan -->
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Riwayat Penempatan</p>
                <p class="text-base text-gray-900 dark:text-white mt-1">
                    {{ $karyawan->kontrak->Riwayat_Penempatan ?? '-' }}
                </p>
            </div>

            <!-- Tanggal Riwayat Penempatan -->
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tanggal Riwayat Penempatan</p>
                <p class="text-base text-gray-900 dark:text-white mt-1">
                    {{ $karyawan->kontrak->Tanggal_Riwayat_Penempatan
                        ? date('d M Y', strtotime($karyawan->kontrak->Tanggal_Riwayat_Penempatan))
                        : '-' }}
                </p>
            </div>

            <!-- Mutasi / Promosi / Demosi -->
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                    Mutasi / Promosi / Demosi
                </p>
                <p class="text-base text-gray-900 dark:text-white mt-1">
                    {{ $karyawan->kontrak->Mutasi_Promosi_Demosi ?? '-' }}
                </p>
            </div>

            <!-- Tanggal Mutasi -->
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                    Tanggal Mutasi / Promosi / Demosi
                </p>
                <p class="text-base text-gray-900 dark:text-white mt-1">
                    {{ $karyawan->kontrak->Tanggal_Mutasi_Promosi_Demosi
                        ? date('d M Y', strtotime($karyawan->kontrak->Tanggal_Mutasi_Promosi_Demosi))
                        : '-' }}
                </p>
            </div>

            <!-- Nomor PKWT Pertama -->
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nomor PKWT Pertama</p>
                <p class="text-base text-gray-900 dark:text-white mt-1">
                    {{ $karyawan->kontrak->NO_PKWT_PERTAMA ?? '-' }}
                </p>
            </div>

            <!-- Nomor SK Pertama -->
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nomor SK Pertama</p>
                <p class="text-base text-gray-900 dark:text-white mt-1">
                    {{ $karyawan->kontrak->NO_SK_PERTAMA ?? '-' }}
                </p>
            </div>

        </div>
    </div>
    @endif

    <!-- Status & BPJS -->
    <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Status Non Aktif</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tanggal Non Aktif</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ optional($karyawan->status)->Tanggal_Non_Aktif ? date('d M Y', strtotime($karyawan->status->Tanggal_Non_Aktif)) : '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Alasan Non Aktif</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $karyawan->status->Alasan_Non_Aktif ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Ijazah Dikembalikan</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $karyawan->status->Ijazah_Dikembalikan ?? '-' }}</p>
            </div>
        </div>
    </div>
    <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Status BPJS</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Status BPJS Ketenagakerjaan</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ optional($karyawan->bpjs)->Status_BPJS_KT ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Status BPJS Kesehatan</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ optional($karyawan->bpjs)->Status_BPJS_KS ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Delete Form Hidden -->
    @if(auth()->user() && auth()->user()->role === 'admin')
    <form id="deleteForm" action="{{ route('karyawan.destroy', $karyawan->id_karyawan) }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- Global delete confirmation modal (reusable) -->
    <x-modal id="delete-confirm" size="sm" title="Konfirmasi Hapus" closeLabel="Batal" confirmLabel="Hapus">
        <p class="text-sm text-gray-600">Gunakan tombol <strong>Hapus</strong> untuk mengonfirmasi penghapusan karyawan ini.</p>
    </x-modal>
    @endif
</div>
@endsection
