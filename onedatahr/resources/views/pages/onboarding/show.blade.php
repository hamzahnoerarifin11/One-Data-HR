@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <!-- HEADER -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Detail Onboarding Karyawan
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Informasi lengkap proses onboarding karyawan
            </p>
        </div>

        <a href="{{ route('onboarding.index') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2
                  text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300
                  dark:hover:bg-white/[0.05] transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
    </div>

    <!-- ================= DATA KANDIDAT ================= -->
    <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-lg
                dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
            Informasi Kandidat
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-show label="Nama Kandidat" :value="$onboarding->kandidat->nama ?? '-'" />
            <x-show label="Posisi" :value="$onboarding->posisi->nama_posisi ?? '-'" />
        </div>
    </div>

    <!-- ================= DATA PRIBADI ================= -->
    <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-lg
                dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
            Data Pribadi & Kontak
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-show label="Pendidikan Terakhir" :value="$onboarding->pendidikan_terakhir" />
            <x-show label="Nama Sekolah" :value="$onboarding->nama_sekolah" />
            <x-show label="Nomor WhatsApp" :value="$onboarding->nomor_wa" />
            <x-show label="Alamat Domisili" :value="$onboarding->alamat_domisili" class="md:col-span-2"/>
            <x-show label="No Rekening" :value="$onboarding->no_rekening" />
        </div>
    </div>

    <!-- ================= ADMINISTRASI ================= -->
    <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-lg
                dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
            Administrasi & Fasilitas
        </h2>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <x-show label="ID Card Status" :value="$onboarding->id_card_status" />
            <x-show label="ID Card Proses" :value="$onboarding->formatTanggal('id_card_proses')" />
            <x-show label="ID Card Jadi" :value="$onboarding->formatTanggal('id_card_jadi')" />
            <x-show label="ID Card Diambil" :value="$onboarding->formatTanggal('id_card_diambil')" />
            <x-show label="Fingerprint Status" :value="$onboarding->fingerprint_status" />
            <x-show label="Tanggal Fingerprint" :value="$onboarding->formatTanggal('fingerprint_sudah')" />
        </div>
    </div>

    <!-- ================= DOKUMEN & KONTRAK ================= -->
    <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-lg
                dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
            Dokumen & Kontrak
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-show label="Jadwal TTD Kontrak" :value="$onboarding->formatTanggal('jadwal_ttd_kontrak')" />
            <x-show label="Link Dikirim HR" :value="$onboarding->formatTanggal('link_data_dikirim_hr')" />
            <x-show label="Link Dilengkapi Karyawan" :value="$onboarding->formatTanggal('link_data_dilengkapi_karyawan')" />
            <x-show label="Ijazah Diterima HR" :value="$onboarding->formatTanggal('ijazah_diterima_hr')" />
            <x-show label="Kontrak TTD Pusat" :value="$onboarding->formatTanggal('kontrak_ttd_pusat')" />
        </div>
    </div>

    <!-- ================= INDUCTION ================= -->
    <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-lg
                dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
            Induction Training
        </h2>

        <x-show label="Tanggal Induction" :value="$onboarding->formatTanggal('tanggal_induction')" />

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
            @foreach([
                'visi_misi' => 'Visi & Misi',
                'wadja_philosophy' => 'Wadja Philosophy',
                'sejarah_perusahaan' => 'Sejarah Perusahaan',
                'kondisi_perizinan' => 'Kondisi Perizinan',
                'tata_tertib' => 'Tata Tertib',
                'bpjs' => 'BPJS',
                'k3' => 'K3'
            ] as $field => $label)
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full {{ $onboarding->$field ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- ================= RESIGN ================= -->
    <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-lg
                dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <h2 class="text-xl font-bold text-red-700 dark:text-red-400 mb-4">
            Informasi Resign
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-show label="Tanggal Resign" :value="$onboarding->formatTanggal('tanggal_resign')" />
            <x-show label="Alasan Resign" :value="$onboarding->alasan_resign" />
        </div>
    </div>

    {{-- Status --}}
    <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-lg
                dark:border-gray-800 dark:bg-white/[0.03] p-6">

        <!-- Header -->
        <div class="flex items-center justify-between border-b pb-3 mb-4 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Status Onboarding
            </h2>

            <span
                class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium
                {{
                    $onboarding->status_onboarding === 'selesai'
                        ? 'text-green-600 bg-green-50 dark:bg-green-900/20 dark:text-green-400'
                        : ($onboarding->status_onboarding === 'progress'
                            ? 'text-yellow-600 bg-yellow-50 dark:bg-yellow-900/20 dark:text-yellow-400'
                            : 'text-gray-500 bg-gray-50 dark:bg-gray-800 dark:text-gray-400')
                }}">
                {{ ucfirst($onboarding->status_onboarding) }}
            </span>
        </div>

        <!-- Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-show
                label="Catatan Evaluasi"
                :value="$onboarding->evaluasi"
                class="md:col-span-2"
            />
        </div>
    </div>

</div>
@endsection
