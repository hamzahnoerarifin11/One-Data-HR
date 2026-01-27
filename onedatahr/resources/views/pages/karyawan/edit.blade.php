@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <!-- Breadcrumb & Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Data Karyawan</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Perbarui data karyawan menggunakan formulir multi-step</p>
        </div>
        <a href="{{ route('karyawan.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.05] transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali
        </a>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] p-6 md:p-8">

        @if($errors->any())
            <div class="mb-6 flex rounded-lg border border-red-200 bg-red-50 p-4 text-red-800 dark:border-red-900 dark:bg-red-900/20 dark:text-red-400">
                <div class="mr-4 flex items-center">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 5.223A8.001 8.001 0 006.777 18H18V5.223zM2 10a8 8 0 1115.707 3.707L9.293 2.293A7.963 7.963 0 002 10z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-medium">Terjadi Kesalahan!</h3>
                    <ul class="mt-2 space-y-1 text-sm">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif



        <form action="{{ route('karyawan.update', $karyawan->id_karyawan) }}" method="POST" x-data="karyawanForm()" @submit.prevent="submit">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <nav class="flex space-x-2 text-sm">
                    <template x-for="(s, i) in steps" :key="i">
                        <button type="button" class="px-3 py-1 rounded" :class="currentStep===i ? 'bg-primary-600 dark:text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'" x-text="s" @click="go(i)"></button>
                    </template>
                </nav>
            </div>

            <!-- STEP 0: Data Karyawan -->
            <div x-show="currentStep===0" class="space-y-6">

                <div class="grid grid-cols-2 gap-4">

                    <!-- NIK -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">NIK</label>
                        <input name="NIK" placeholder="Masukkan NIK Karyawan" value="{{ old('NIK', $karyawan->NIK) }}"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                            dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden
                            dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
                         <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                            <select id="status" name="Status"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                                <option value="">-- Pilih Status --</option>
                                <option value="1" {{ old('Status', (string)$karyawan->Status) == '1' ? 'selected' : '' }}>1</option>
                                <option value="0" {{ old('Status', (string)$karyawan->Status) == '0' ? 'selected' : '' }}>0</option>
                            </select>
                            <span
                                class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- Kode -->
                   <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Kode</label>
                        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                        <select id="kode" name="Kode"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">

                            <option value="">-- Pilih Kode --</option>
                            <option value="Aktif" {{ old('Kode', $karyawan->Kode) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Non Aktif" {{ old('Kode', $karyawan->Kode) == 'Non Aktif' ? 'selected' : '' }}>Non Aktif</option>
                        </select>
                        <span
                                class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                    </div>


                    <!-- Nama KTP -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Nama (Sesuai KTP)</label>
                        <input name="Nama_Sesuai_KTP" placeholder="Contoh: Budi Santoso" required value="{{ old('Nama_Sesuai_KTP', $karyawan->Nama_Sesuai_KTP) }}"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                            dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>

                    <!-- NIK KTP -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">NIK KTP</label>
                        <input name="NIK_KTP" placeholder="Masukkan NIK (16 digit)" required value="{{ old('NIK_KTP', $karyawan->NIK_KTP) }}"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                            dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>

                    <!-- Nama Ijazah -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Lengkap (Sesuai Ijazah)</label>
                        <input name="Nama_Lengkap_Sesuai_Ijazah" placeholder="Contoh: Budi Santoso" required value="{{ old('Nama_Lengkap_Sesuai_Ijazah', $karyawan->Nama_Lengkap_Sesuai_Ijazah) }}"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                            dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>

                    <!-- Tempat Lahir -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Tempat Lahir</label>
                        <input name="Tempat_Lahir_Karyawan" placeholder="Contoh: Pati" value="{{ old('Tempat_Lahir_Karyawan', $karyawan->Tempat_Lahir_Karyawan) }}"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                            dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Tanggal Lahir</label>
                         <div class="relative">
                            <input id="tanggal_lahir" name="Tanggal_Lahir_Karyawan" type="date"
                            value="{{ old('Tanggal_Lahir_Karyawan', $karyawan->Tanggal_Lahir_Karyawan) }}"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" onclick="this.showPicker()" />
                            <span class="absolute top-1/2 right-3.5 -translate-y-1/2 pointer-events-none">
                                <svg class="fill-gray-700 dark:fill-gray-400" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.33317 0.0830078C4.74738 0.0830078 5.08317 0.418794 5.08317 0.833008V1.24967H8.9165V0.833008C8.9165 0.418794 9.25229 0.0830078 9.6665 0.0830078C10.0807 0.0830078 10.4165 0.418794 10.4165 0.833008V1.24967L11.3332 1.24967C12.2997 1.24967 13.0832 2.03318 13.0832 2.99967V4.99967V11.6663C13.0832 12.6328 12.2997 13.4163 11.3332 13.4163H2.6665C1.70001 13.4163 0.916504 12.6328 0.916504 11.6663V4.99967V2.99967C0.916504 2.03318 1.70001 1.24967 2.6665 1.24967L3.58317 1.24967V0.833008C3.58317 0.418794 3.91896 0.0830078 4.33317 0.0830078ZM4.33317 2.74967H2.6665C2.52843 2.74967 2.4165 2.8616 2.4165 2.99967V4.24967H11.5832V2.99967C11.5832 2.8616 11.4712 2.74967 11.3332 2.74967H9.6665H4.33317ZM11.5832 5.74967H2.4165V11.6663C2.4165 11.8044 2.52843 11.9163 2.6665 11.9163H11.3332C11.4712 11.9163 11.5832 11.8044 11.5832 11.6663V5.74967Z" fill="" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- Umur -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Umur</label>
                        <input id="umur" name="Umur_Karyawan" placeholder="Otomatis  Terhitung" value="{{ old('Umur_Karyawan', $karyawan->Umur_Karyawan) }}"
                            readonly
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>

                    <!-- Jenis Kelamin -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Jenis Kelamin</label>
                        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                            <select name="Jenis_Kelamin_Karyawan"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                                <option value="">-- Pilih --</option>
                                <option value="L" {{ old('Jenis_Kelamin_Karyawan', $karyawan->Jenis_Kelamin_Karyawan)=='L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('Jenis_Kelamin_Karyawan', $karyawan->Jenis_Kelamin_Karyawan)=='P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            <span
                                class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                    </div>

                   <!-- Status Pernikahan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Status Pernikahan</label>
                        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                        <select name="Status_Pernikahan"
                           class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                             :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                            <option value="">-- Pilih Status --</option>
                            <option value="Belum Menikah" {{ old('Status_Pernikahan', $karyawan->Status_Pernikahan) == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                            <option value="Menikah" {{ old('Status_Pernikahan', $karyawan->Status_Pernikahan) == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                            <option value="Cerai Hidup" {{ old('Status_Pernikahan', $karyawan->Status_Pernikahan) == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                            <option value="Cerai Mati (Duda/Janda)" {{ old('Status_Pernikahan', $karyawan->Status_Pernikahan) == 'Cerai Mati (Duda/Janda)' ? 'selected' : '' }}>Cerai Mati (Duda/Janda)</option>
                        </select>
                        <span
                            class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        </div>
                    </div>

                    <!-- Golongan Darah -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Golongan Darah</label>
                        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                        <select name="Golongan_Darah"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">

                            <option value="">-- Pilih Golongan Darah --</option>
                            <option value="Tidak Tahu" {{ old('Golongan_Darah', $karyawan->Golongan_Darah)=='Tidak Tahu' ? 'selected' : '' }}>Tidak Tahu</option>
                            <option value="A" {{ old('Golongan_Darah', $karyawan->Golongan_Darah)=='A' ? 'selected' : '' }}>A</option>
                            <option value="B" {{ old('Golongan_Darah', $karyawan->Golongan_Darah)=='B' ? 'selected' : '' }}>B</option>
                            <option value="O" {{ old('Golongan_Darah', $karyawan->Golongan_Darah)=='O' ? 'selected' : '' }}>O</option>
                            <option value="AB" {{ old('Golongan_Darah', $karyawan->Golongan_Darah)=='AB' ? 'selected' : '' }}>AB</option>
                        </select>
                        <span
                                class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- Nomor Telepon -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Nomor Telepon</label>
                        <input name="Nomor_Telepon_Aktif_Karyawan" placeholder="Contoh: 081234567890" value="{{ old('Nomor_Telepon_Aktif_Karyawan', $karyawan->Nomor_Telepon_Aktif_Karyawan) }}"
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Email</label>
                        <input name="Email" placeholder="Contoh: email@gmail.com" type="email" value="{{ old('Email', $karyawan->Email) }}"
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>

                    <!-- Alamat KTP -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Alamat KTP</label>
                        <textarea id="Alamat_KTP" placeholder="Contoh: Jl Mawar No 21 RT 01 RW 02, Desa Bumirejo" name="Alamat_KTP" rows="2"
                            class="dark:bg-dark-900 shadow-theme-xs w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('Alamat_KTP', $karyawan->Alamat_KTP) }}</textarea>
                    </div>

                    <!-- RT / RW -->
                    <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">RT</label>
                        <input id="RT" name="RT" placeholder="Contoh: 01" value="{{ old('RT', $karyawan->RT) }}"
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">RW</label>
                        <input id="RW" name="RW" placeholder="Contoh: 02" value="{{ old('RW', $karyawan->RW) }}"
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>
                    </div>
                    <!-- Provinsi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Provinsi</label>
                        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                        <select id="Provinsi" name="Provinsi"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                            <option value="">-- Pilih Provinsi --</option>
                            <option value="{{ old('Provinsi', $karyawan->Provinsi) }}" selected>{{ old('Provinsi', $karyawan->Provinsi) }}</option>
                        </select>
                        <span
                                class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- Kabupaten/Kota -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Kabupaten/Kota</label>
                        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                        <select id="Kabupaten_Kota" name="Kabupaten_Kota"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                            <option value="">-- Pilih Kabupaten/Kota --</option>
                            <option value="{{ old('Kabupaten_Kota', $karyawan->Kabupaten_Kota) }}" selected>{{ old('Kabupaten_Kota', $karyawan->Kabupaten_Kota) }}</option>
                        </select>
                        <span
                                class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- Kecamatan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Kecamatan</label>
                        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                        <select id="Kecamatan" name="Kecamatan"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                            <option value="">-- Pilih Kecamatan --</option>
                            <option value="{{ old('Kecamatan', $karyawan->Kecamatan) }}" selected>{{ old('Kecamatan', $karyawan->Kecamatan) }}</option>

                        </select>
                        <span
                                class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- Desa/Kelurahan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Kelurahan/Desa</label>
                        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                        <select id="Kelurahan_Desa" name="Kelurahan_Desa"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                            <option value="">-- Pilih Desa/Kelurahan --</option>
                            <option value="{{ old('Kelurahan_Desa', $karyawan->Kelurahan_Desa) }}" selected>{{ old('Kelurahan_Desa', $karyawan->Kelurahan_Desa) }}</option>
                        </select>
                        <span
                                class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                    </div>


                    <!--  CHECKBOX SAMA DENGAN KTP -->
                    <!-- ========================= -->
                    <div class="col-span-2 flex items-center gap-2 mt-2">
                        <div x-data="{ checkboxToggle: false }">
                        <label for="sameAsKTP"
                            class="flex cursor-pointer items-center text-sm font-medium text-gray-700 select-none dark:text-gray-400">
                            <div class="relative">
                                <input type="checkbox" id="sameAsKTP" class="sr-only"
                                    @change="checkboxToggle = !checkboxToggle" />
                                <div :class="checkboxToggle ? 'border-brand-500 bg-brand-500' :
                                    'bg-transparent border-gray-300 dark:border-gray-700'"
                                    class="f hover:border-brand-500 dark:hover:border-brand-500 mr-3 flex h-5 w-5 items-center justify-center rounded-md border-[1.25px]">
                                    <span :class="checkboxToggle ? '' : 'opacity-0'">
                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M11.6666 3.5L5.24992 9.91667L2.33325 7" stroke="white" stroke-width="1.94437"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            Checklist Jika Domisili Sama dengan Alamat KTP
                        </label>
                        </div>
                    </div>

                    <!--  ALAMAT DOMISILI -->

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Alamat Domisili</label>
                        <textarea id="Alamat_Domisili" name="Alamat_Domisili" placeholder="Contoh: Jl Melati No 15 RT 03 RW 04" rows="2"
                            class="dark:bg-dark-900 shadow-theme-xs w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('Alamat_Domisili', $karyawan->Alamat_Domisili) }}</textarea>
                    </div>

                    <!-- RT -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">RT Domisili</label>
                        <input id="RT_Sesuai_Domisili" name="RT_Sesuai_Domisili" placeholder="Contoh: 03" value="{{ old('RT_Sesuai_Domisili', $karyawan->RT_Sesuai_Domisili) }}"
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>

                    <!-- RW -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">RW Domisili</label>
                        <input id="RW_Sesuai_Domisili" name="RW_Sesuai_Domisili" placeholder="Contoh: 04" value="{{ old('RW_Sesuai_Domisili', $karyawan->RW_Sesuai_Domisili) }}"
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>

                    <!-- Provinsi Domisili -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Provinsi Domisili</label>
                        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                        <select id="Provinsi_Sesuai_Domisili" name="Provinsi_Sesuai_Domisili"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                            <option value="">-- Pilih Provinsi --</option>
                            <option value="{{ old('Provinsi_Sesuai_Domisili', $karyawan->Provinsi_Sesuai_Domisili) }}" selected>{{ old('Provinsi_Sesuai_Domisili', $karyawan->Provinsi_Sesuai_Domisili) }}</option>
                        </select>
                        <span
                                class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- Kabupaten Domisili -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Kabupaten/Kota Domisili</label>
                        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                        <select id="Kabupaten_Kota_Sesuai_Domisili" name="Kabupaten_Kota_Sesuai_Domisili"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                            <option value="">-- Pilih Kabupaten/Kota --</option>
                            <option value="{{ old('Kabupaten_Kota_Sesuai_Domisili', $karyawan->Kabupaten_Kota_Sesuai_Domisili) }}" selected>{{ old('Kabupaten_Kota_Sesuai_Domisili', $karyawan->Kabupaten_Kota_Sesuai_Domisili) }}</option>
                        </select>
                        <span
                                class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- Kecamatan Domisili -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Kecamatan Domisili</label>
                        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                        <select id="Kecamatan_Sesuai_Domisili" name="Kecamatan_Sesuai_Domisili"
                           class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                            <option value="">-- Pilih Kecamatan --</option>
                            <option value="{{ old('Kecamatan_Sesuai_Domisili', $karyawan->Kecamatan_Sesuai_Domisili) }}" selected>{{ old('Kecamatan_Sesuai_Domisili', $karyawan->Kecamatan_Sesuai_Domisili) }}</option>
                        </select>
                        <span
                                class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- Kelurahan Domisili -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Kelurahan/Desa Domisili</label>
                        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                        <select id="Kelurahan_Desa_Domisili" name="Kelurahan_Desa_Domisili"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                            <option value="">-- Pilih Desa/Kelurahan --</option>
                            <option value="{{ old('Kelurahan_Desa_Domisili', $karyawan->Kelurahan_Desa_Domisili) }}" selected>{{ old('Kelurahan_Desa_Domisili', $karyawan->Kelurahan_Desa_Domisili) }}</option>
                        </select>
                         <span
                                class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                    </div>



                    <!-- Alamat Lengkap -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Alamat Lengkap</label>
                        <textarea id="Alamat_Lengkap" name="Alamat_Lengkap" placeholder="Contoh: Jl Melati No 15 RT 03 RW 04, Kel Sukamaju, Kec Sukoharjo" rows="3"
                            class="dark:bg-dark-900 shadow-theme-xs w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('Alamat_Lengkap', $karyawan->Alamat_Lengkap) }}</textarea>
                    </div>
                </div>
            </div>


           <!-- ================= STEP 1: Data Keluarga ================= -->
            <div x-show="currentStep===1" x-transition class="space-y-6">
                <div class="grid grid-cols-2 gap-4">

                    <!-- Nama Ayah -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Ayah Kandung</label>
                        <input name="Nama_Ayah_Kandung"
                            placeholder="Contoh: Ahmad Sudirman"
                            value="{{ old('Nama_Ayah_Kandung', optional($karyawan->keluarga)->Nama_Ayah_Kandung) }}"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                            dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden
                            dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    </div>

                    <!-- Nama Ibu -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Ibu Kandung</label>
                        <input name="Nama_Ibu_Kandung"
                            placeholder="Contoh: Siti Rahmawati"
                            value="{{ old('Nama_Ibu_Kandung', optional($karyawan->keluarga)->Nama_Ibu_Kandung) }}"
                             class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                            dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden
                            dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    </div>

                    <!-- Nama Suami/Istri -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Suami/Istri</label>
                        <input name="Nama_Lengkap_Suami_Istri"
                            placeholder="Contoh: Budi Prasetyo"
                            value="{{ old('Nama_Lengkap_Suami_Istri', optional($karyawan->keluarga)->Nama_Lengkap_Suami_Istri) }}"
                             class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                            dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden
                            dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    </div>

                    <!-- NIK Suami/Istri -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">NIK KTP Suami/Istri</label>
                        <input name="NIK_KTP_Suami_Istri"
                            placeholder="Masukkan NIK (16 digit)"
                            value="{{ old('NIK_KTP_Suami_Istri', optional($karyawan->keluarga)->NIK_KTP_Suami_Istri) }}"
                             class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                            dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden
                            dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                            maxlength="16" />
                    </div>

                    <!-- Tempat Lahir Suami/Istri -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Tempat Lahir Suami/Istri</label>
                        <input name="Tempat_Lahir_Suami_Istri"
                            placeholder="Contoh: Bandung"
                            value="{{ old('Tempat_Lahir_Suami_Istri', optional($karyawan->keluarga)->Tempat_Lahir_Suami_Istri) }}"
                             class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                            dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden
                            dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    </div>

                    <!-- Tanggal Lahir Suami/Istri -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Tanggal Lahir Suami/Istri</label>
                        <div class="relative">
                        <input name="Tanggal_Lahir_Suami_Istri"
                            type="date"
                            value="{{ old('Tanggal_Lahir_Suami_Istri', optional($karyawan->keluarga)->Tanggal_Lahir_Suami_Istri) }}"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" onclick="this.showPicker()" />
                            <span class="absolute top-1/2 right-3.5 -translate-y-1/2 pointer-events-none">
                                <svg class="fill-gray-700 dark:fill-gray-400" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.33317 0.0830078C4.74738 0.0830078 5.08317 0.418794 5.08317 0.833008V1.24967H8.9165V0.833008C8.9165 0.418794 9.25229 0.0830078 9.6665 0.0830078C10.0807 0.0830078 10.4165 0.418794 10.4165 0.833008V1.24967L11.3332 1.24967C12.2997 1.24967 13.0832 2.03318 13.0832 2.99967V4.99967V11.6663C13.0832 12.6328 12.2997 13.4163 11.3332 13.4163H2.6665C1.70001 13.4163 0.916504 12.6328 0.916504 11.6663V4.99967V2.99967C0.916504 2.03318 1.70001 1.24967 2.6665 1.24967L3.58317 1.24967V0.833008C3.58317 0.418794 3.91896 0.0830078 4.33317 0.0830078ZM4.33317 2.74967H2.6665C2.52843 2.74967 2.4165 2.8616 2.4165 2.99967V4.24967H11.5832V2.99967C11.5832 2.8616 11.4712 2.74967 11.3332 2.74967H9.6665H4.33317ZM11.5832 5.74967H2.4165V11.6663C2.4165 11.8044 2.52843 11.9163 2.6665 11.9163H11.3332C11.4712 11.9163 11.5832 11.8044 11.5832 11.6663V5.74967Z" fill="" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- Telepon Suami/Istri -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Nomor Telepon Suami/Istri</label>
                        <input name="Nomor_Telepon_Suami_Istri"
                            placeholder="Contoh: 081234567890"
                            value="{{ old('Nomor_Telepon_Suami_Istri', optional($karyawan->keluarga)->Nomor_Telepon_Suami_Istri) }}"
                             class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                            dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden
                            dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    </div>

                    <!-- Pendidikan Suami/Istri -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Pendidikan Terakhir Suami/Istri</label>
                        <input name="Pendidikan_Terakhir_Suami_Istri"
                            placeholder="Contoh: S1"
                            value="{{ old('Pendidikan_Terakhir_Suami_Istri', optional($karyawan->keluarga)->Pendidikan_Terakhir_Suami_Istri) }}"
                             class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                            dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden
                            dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    </div>

                    <!-- =================== Anak Pertama =================== -->
                     <!-- ===== DATA ANAK (EDIT MODE - JSON) ===== -->
                    <div class="col-span-2 space-y-4"
                        x-data="{
                            anak: {{ old('anak')
                                ? json_encode(old('anak'))
                                : json_encode(optional($karyawan->keluarga)->anak ?? []) }}
                        }">

                        <!-- Jika belum ada anak -->
                        <template x-if="anak.length === 0">
                            <div class="rounded-lg border border-dashed border-gray-300 p-4 text-center
                                        text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                Belum ada data anak
                            </div>
                        </template>

                        <!-- Loop Anak -->
                        <template x-for="(item, index) in anak" :key="index">
                            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-theme-sm
                                        dark:border-gray-800 dark:bg-gray-900">

                                <!-- Header -->
                                <div class="mb-3 flex items-center justify-between">
                                    <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">
                                        Data Anak <span x-text="index + 1"></span>
                                    </h4>

                                    <button type="button"
                                            @click="anak.splice(index, 1)"
                                            class="text-xs text-red-600 hover:underline">
                                        Hapus
                                    </button>
                                </div>

                                <div class="grid grid-cols-3 gap-3">

                                    <!-- Nama Anak -->
                                    <div>
                                        <label class="block text-sm dark:text-gray-400">Nama Anak</label>
                                        <input
                                            :name="'anak['+index+'][nama]'"
                                            x-model="item.nama"
                                            placeholder="Nama lengkap anak"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                                                dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent
                                                px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden
                                                dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                                    </div>

                                    <!-- Tempat Lahir -->
                                    <div>
                                        <label class="block text-sm dark:text-gray-400">Tempat Lahir</label>
                                        <input
                                            :name="'anak['+index+'][tempat_lahir]'"
                                            x-model="item.tempat_lahir"
                                            placeholder="Kota lahir"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                                                dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent
                                                px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden
                                                dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                                    </div>

                                    <!-- Tanggal Lahir -->
                                    <div>
                                        <label class="block text-sm dark:text-gray-400">Tanggal Lahir</label>
                                        <div class="relative">
                                        <input type="date"
                                            :name="'anak['+index+'][tanggal_lahir]'"
                                            x-model="item.tanggal_lahir"
                                             class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" onclick="this.showPicker()" />
                                            <span class="absolute top-1/2 right-3.5 -translate-y-1/2 pointer-events-none">
                                                <svg class="fill-gray-700 dark:fill-gray-400" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.33317 0.0830078C4.74738 0.0830078 5.08317 0.418794 5.08317 0.833008V1.24967H8.9165V0.833008C8.9165 0.418794 9.25229 0.0830078 9.6665 0.0830078C10.0807 0.0830078 10.4165 0.418794 10.4165 0.833008V1.24967L11.3332 1.24967C12.2997 1.24967 13.0832 2.03318 13.0832 2.99967V4.99967V11.6663C13.0832 12.6328 12.2997 13.4163 11.3332 13.4163H2.6665C1.70001 13.4163 0.916504 12.6328 0.916504 11.6663V4.99967V2.99967C0.916504 2.03318 1.70001 1.24967 2.6665 1.24967L3.58317 1.24967V0.833008C3.58317 0.418794 3.91896 0.0830078 4.33317 0.0830078ZM4.33317 2.74967H2.6665C2.52843 2.74967 2.4165 2.8616 2.4165 2.99967V4.24967H11.5832V2.99967C11.5832 2.8616 11.4712 2.74967 11.3332 2.74967H9.6665H4.33317ZM11.5832 5.74967H2.4165V11.6663C2.4165 11.8044 2.52843 11.9163 2.6665 11.9163H11.3332C11.4712 11.9163 11.5832 11.8044 11.5832 11.6663V5.74967Z" fill="" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Jenis Kelamin -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                            Jenis Kelamin
                                        </label>
                                        <div x-data="{ isOptionSelected: item.jenis_kelamin ? true : false }"
                                            class="relative z-20 bg-transparent">
                                            <select
                                                :name="'anak['+index+'][jenis_kelamin]'"
                                                x-model="item.jenis_kelamin"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                                                    dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg
                                                    border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm
                                                    text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden
                                                    dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
                                                    dark:placeholder:text-white/30"
                                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                                                @change="isOptionSelected = true">
                                                <option value="">-- Pilih --</option>
                                                <option value="L">Laki-laki</option>
                                                <option value="P">Perempuan</option>
                                            </select>

                                            <span
                                                class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2
                                                    text-gray-700 dark:text-gray-400">
                                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396"
                                                        stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Pendidikan -->
                                    <div class="col-span-2">
                                        <label class="block text-sm dark:text-gray-400">
                                            Pendidikan Terakhir
                                        </label>
                                        <input
                                            :name="'anak['+index+'][pendidikan]'"
                                            x-model="item.pendidikan"
                                            placeholder="TK / SD / SMP / SMA"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                                                dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300
                                                bg-transparent px-4 py-2.5 text-sm text-gray-800
                                                placeholder:text-gray-400 focus:ring-3 focus:outline-hidden
                                                dark:border-gray-700 dark:bg-gray-900
                                                dark:text-white/90 dark:placeholder:text-white/30">
                                    </div>

                                </div>
                            </div>
                        </template>

                        <!-- Tambah Anak -->
                        <button type="button"
                                @click="anak.push({
                                    nama: '',
                                    tempat_lahir: '',
                                    tanggal_lahir: '',
                                    jenis_kelamin: '',
                                    pendidikan: ''
                                })"
                                class="flex items-center gap-2 rounded-lg border border-brand-300
                                    bg-brand-50 px-4 py-2 text-sm font-medium text-brand-600
                                    hover:bg-brand-100 dark:border-brand-800 dark:bg-brand-900/30
                                    dark:text-brand-400">
                            <svg width="16" height="16" fill="none" viewBox="0 0 16 16">
                                <path d="M8 3.333V12.667M3.333 8H12.667"
                                    stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" />
                            </svg>
                            Tambah Anak
                        </button>
                    </div>
               </div>
            </div>
                    <!-- ===== END DATA ANAK ===== -->

            <!-- ================= STEP 2: Pekerjaan & Perusahaan ================= -->
            <div x-show="currentStep===2" x-transition class="space-y-6">
                <div class="grid grid-cols-2 gap-4">

                    <!-- PERUSAHAAN -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Perusahaan
                        </label>

                        <div class="relative z-20">
                            <select id="company" name="company_id"
                                class="h-11 w-full appearance-none rounded-lg border border-gray-300 px-4 pr-11 text-sm
                                    shadow-theme-xs bg-transparent
                                    focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10
                                    dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">

                                <option value="">-- Pilih Perusahaan --</option>

                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}"
                                        {{ old('company_id', optional($karyawan->pekerjaan->first())->company_id) == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Arrow -->
                            <span class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-gray-500">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M4.8 7.4L10 12.6L15.2 7.4"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- DIVISI -->
                     <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Divisi
                        </label>

                        <div class="relative z-20">
                            <select id="division" name="division_id"
                                class="h-11 w-full appearance-none rounded-lg border border-gray-300 px-4 pr-11 text-sm
                                    shadow-theme-xs bg-transparent
                                    focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10
                                    dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">

                                <option value="">-- Pilih Divisi --</option>
                            </select>

                            <!-- Arrow -->
                            <span class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-gray-500">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M4.8 7.4L10 12.6L15.2 7.4"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- DEPARTEMENT -->
                     <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Departement
                        </label>

                        <div class="relative z-20">
                            <select id="department" name="department_id"
                                class="h-11 w-full appearance-none rounded-lg border border-gray-300 px-4 pr-11 text-sm
                                    shadow-theme-xs bg-transparent
                                    focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10
                                    dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">

                                <option value="">-- Pilih Departement --</option>
                            </select>

                            <!-- Arrow -->
                            <span class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-gray-500">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M4.8 7.4L10 12.6L15.2 7.4"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- UNIT -->
                     <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Unit
                        </label>

                        <div class="relative z-20">
                            <select id="unit" name="unit_id"
                                class="h-11 w-full appearance-none rounded-lg border border-gray-300 px-4 pr-11 text-sm
                                    shadow-theme-xs bg-transparent
                                    focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10
                                    dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">

                                <option value="">-- Pilih Unit --</option>
                            </select>

                            <!-- Arrow -->
                            <span class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-gray-500">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M4.8 7.4L10 12.6L15.2 7.4"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- JABATAN -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Level Jabatan
                        </label>

                        <div class="relative z-20">
                            <select id="position" name="position_id"
                                class="h-11 w-full appearance-none rounded-lg border border-gray-300 px-4 pr-11 text-sm
                                    shadow-theme-xs bg-transparent
                                    focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10
                                    dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">

                                <option value="">-- Pilih Level Jabatan --</option>
                            </select>

                            <!-- Arrow -->
                            <span class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-gray-500">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M4.8 7.4L10 12.6L15.2 7.4"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </div>
                    </div>


                    <!-- BAGIAN -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Jabatan</label>
                        <input name="Jabatan"
                            placeholder="Contoh: Administrasi / Operasional"
                            value="{{ old('Jabatan', optional($karyawan->pekerjaan->first())->Jabatan) }}"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                            dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden
                            dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    </div>

                    {{-- <!-- DEPARTEMENT (ENUM) -->
                     <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Departement
                        </label>

                        <div class="relative z-20">
                            <select name="Departement"
                                class="h-11 w-full appearance-none rounded-lg border border-gray-300 px-4 pr-11 text-sm
                                    shadow-theme-xs bg-transparent
                                    focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10
                                    dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">

                                <option value="">-- Pilih Departement --</option>

                                @foreach ($departementOptions as $departement)
                                    <option value="{{ $departement }}"
                                        {{ old('Departement', optional($karyawan->pekerjaan->first())->Departement) === $departement ? 'selected' : '' }}>

                                        {{ $departement }}
                                    </option>
                                        {{ old('Departement', optional($karyawan->pekerjaan->first())->Departement) === $departement ? 'selected' : '' }}>
                                        {{ $departement }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Arrow -->
                            <span class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-gray-500">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M4.8 7.4L10 12.6L15.2 7.4"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </div>
                    </div> --}}



                    <!-- JENIS KONTRAK & PERJANJIAN (DEPENDENT DROPDOWN) -->
                    <div class="grid grid-cols-2 gap-4">

                    <!-- JENIS KONTRAK -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Jenis Kontrak
                        </label>

                        <div class="relative z-20 bg-transparent">
                            <select
                                name="Jenis_Kontrak"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                                    dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg
                                    border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm
                                    text-gray-800 focus:ring-3 focus:outline-hidden
                                    dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                            >
                                <option value="">-- Pilih Jenis Kontrak --</option>
                                <option value="PKWT" {{ old('Jenis_Kontrak', optional($karyawan->pekerjaan->first())->Jenis_Kontrak) == 'PKWT' ? 'selected' : '' }}>
                                    PKWT
                                </option>
                                <option value="PKWTT" {{ old('Jenis_Kontrak', optional($karyawan->pekerjaan->first())->Jenis_Kontrak) == 'PKWTT' ? 'selected' : '' }}>
                                    PKWTT
                                </option>
                            </select>

                            <span class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-gray-500">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M4.8 7.4L10 12.6L15.2 7.4"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- PERJANJIAN -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Perjanjian
                        </label>

                        <div class="relative z-20 bg-transparent">
                            <select
                                name="Perjanjian"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                                    dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg
                                    border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm
                                    text-gray-800 focus:ring-3 focus:outline-hidden
                                    dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                            >
                                <option value="">-- Pilih Perjanjian --</option>
                                <option value="Harian Lepas" {{ old('Perjanjian', optional($karyawan->pekerjaan->first())->Perjanjian) == 'Harian Lepas' ? 'selected' : '' }}>
                                    Harian Lepas
                                </option>
                                <option value="Kontrak" {{ old('Perjanjian', optional($karyawan->pekerjaan->first())->Perjanjian) == 'Kontrak' ? 'selected' : '' }}>
                                    Kontrak
                                </option>
                                <option value="Tetap" {{ old('Perjanjian', optional($karyawan->pekerjaan->first())->Perjanjian) == 'Tetap' ? 'selected' : '' }}>
                                    Tetap
                                </option>
                            </select>

                            <span class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-gray-500">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M4.8 7.4L10 12.6L15.2 7.4"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </div>
                    </div>

                </div>



                    <!-- LOKASI KERJA -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Lokasi Kerja
                        </label>

                        <div class="relative z-20">
                            <select name="Lokasi_Kerja"
                                class="h-11 w-full appearance-none rounded-lg border border-gray-300 px-4 pr-11 text-sm
                                    shadow-theme-xs bg-transparent
                                    focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10
                                    dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">

                                <option value="">-- Pilih Lokasi Kerja --</option>

                                @foreach ($lokasikerjaOptions as $lokasikerja)
                                    <option value="{{ $lokasikerja }}"
                                        {{ old('Lokasi_Kerja', optional($karyawan->pekerjaan->first())->Lokasi_Kerja) === $lokasikerja ? 'selected' : '' }}>
                                        {{ $lokasikerja }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Arrow -->
                            <span class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-gray-500">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M4.8 7.4L10 12.6L15.2 7.4"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </div>
                    </div>

                </div>
            </div>

           <!-- ================= STEP 3: Pendidikan (dynamic) ================= -->
            <div x-show="currentStep===3" x-transition class="space-y-6">
                <!-- <template x-for="(pd, idx) in pendidikan" :key="idx"> -->

                    <!-- <div class="col-span-2 border rounded-lg p-3 space-y-4"> -->
                        <!-- Header -->
                        <!-- <div class="flex justify-between items-center">
                            <div class="text-sm font-medium text-gray-700 dark:text-white">
                                Pendidikan ke-<span x-text="idx+1"></span>
                            </div>

                            <button type="button"
                                class="px-2 py-1.5 text-sm rounded bg-red-500 text-white shadow-theme-xs hover:bg-red-600 disabled:bg-red-300"
                                @click="removePendidikan(idx)">
                                Hapus
                            </button>
                        </div> -->

                        <!-- Form Fields -->
                        <div class="grid grid-cols-3 gap-3">

                            <!-- Pendidikan Terakhir -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Pendidikan Terakhir
                                </label>
                                <div class="relative z-20">
                                    <select name="Pendidikan_Terakhir"
                                        class="h-11 w-full appearance-none rounded-lg border border-gray-300 px-4 pr-11 text-sm
                                            shadow-theme-xs bg-transparent
                                            focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10
                                            dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">

                                        <option value="">-- Pilih Pendidikan Terakhir --</option>

                                        @foreach ($pendidikanOptions as $pendidikan)
                                            <option value="{{ $pendidikan }}"
                                                {{ old('Pendidikan_Terakhir', optional($karyawan->pendidikan)->Pendidikan_Terakhir) === $pendidikan ? 'selected' : '' }}>
                                                {{ $pendidikan }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <!-- Arrow -->
                                    <span class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-gray-500">
                                        <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <path d="M4.8 7.4L10 12.6L15.2 7.4"
                                                stroke-width="1.5"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>

                            <!-- Nama Institusi -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Nama Institusi
                                </label>
                                <input name="Nama_Lengkap_Tempat_Pendidikan_Terakhir"
                                    placeholder="Contoh: Universitas Indonesia"
                                    value="{{ old('Nama_Lengkap_Tempat_Pendidikan_Terakhir', optional($karyawan->pendidikan)->Nama_Lengkap_Tempat_Pendidikan_Terakhir) }}"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300
                                    focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full
                                    rounded-lg border border-gray-300 bg-transparent px-4 py-2.5
                                    text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3
                                    focus:outline-hidden dark:border-gray-700 dark:bg-gray-900
                                    dark:text-white/90 dark:placeholder:text-white/30"
                                />
                            </div>

                            <!-- Jurusan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Jurusan
                                </label>
                                <input
                                    name="Jurusan"
                                    placeholder="Contoh: Teknik Informatika"
                                    value="{{ old('Jurusan', optional($karyawan->pendidikan)->Jurusan) }}"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300
                                    focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full
                                    rounded-lg border border-gray-300 bg-transparent px-4 py-2.5
                                    text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3
                                    focus:outline-hidden dark:border-gray-700 dark:bg-gray-900
                                    dark:text-white/90 dark:placeholder:text-white/30"
                                />
                            </div>
                    </div>

                <!-- </template> -->

                <!-- Button Add -->
                <!-- <div>
                    <button type="button"
                        @click="addPendidikan()"
                        class="px-4 py-2 bg-gray-100 dark:bg-gray-800
                        text-sm rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 dark:text-white">
                        + Tambah Pendidikan
                    </button>
                </div> -->
            </div>

          <!-- ================= STEP 4: Kontrak (dynamic) ================= -->
            <div x-show="currentStep===4" x-transition class="space-y-6" x-data="{
                    tanggalMulai: '{{ old(
                                        'Tanggal_Mulai_Tugas',
                                        optional($karyawan->kontrak)->Tanggal_Mulai_Tugas
                                            ? \Carbon\Carbon::parse(optional($karyawan->kontrak)->Tanggal_Mulai_Tugas)->translatedFormat('Y-m-d')
                                            : ''
                                    ) }}',
                    masaKerja: '{{ old('Masa_Kerja', optional($karyawan->kontrak)->Masa_Kerja) }}',
                    hitungMasaKerja() {
                        if (!this.tanggalMulai) {
                            this.masaKerja = ''
                            return
                        }

                        const start = new Date(this.tanggalMulai)
                        const today = new Date()

                        let years = today.getFullYear() - start.getFullYear()
                        let months = today.getMonth() - start.getMonth()
                        let days = today.getDate() - start.getDate()

                        if (days < 0) {
                            months--
                            const lastMonth = new Date(today.getFullYear(), today.getMonth(), 0)
                            days += lastMonth.getDate()
                        }

                        if (months < 0) {
                            years--
                            months += 12
                        }

                        this.masaKerja = `${years} Tahun ${months} Bulan ${days} Hari`
                    }
                }"
                x-init="hitungMasaKerja()"
            >
                <!-- <template x-for="(kn, idx) in kontrak" :key="idx"> -->
                        <!-- ROW 1 -->
                        <div class="grid grid-cols-3 gap-3">

                            <!-- Tanggal Mulai Tugas -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Tanggal Mulai Tugas
                                </label>
                                <div class="relative">
                                <input
                                    type="date"
                                    name="Tanggal_Mulai_Tugas"
                                    x-model="tanggalMulai"
                                    @change="hitungMasaKerja()"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" onclick="this.showPicker()"/>
                                    <span class="absolute top-1/2 right-3.5 -translate-y-1/2 pointer-events-none">
                                        <svg class="fill-gray-700 dark:fill-gray-400" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.33317 0.0830078C4.74738 0.0830078 5.08317 0.418794 5.08317 0.833008V1.24967H8.9165V0.833008C8.9165 0.418794 9.25229 0.0830078 9.6665 0.0830078C10.0807 0.0830078 10.4165 0.418794 10.4165 0.833008V1.24967L11.3332 1.24967C12.2997 1.24967 13.0832 2.03318 13.0832 2.99967V4.99967V11.6663C13.0832 12.6328 12.2997 13.4163 11.3332 13.4163H2.6665C1.70001 13.4163 0.916504 12.6328 0.916504 11.6663V4.99967V2.99967C0.916504 2.03318 1.70001 1.24967 2.6665 1.24967L3.58317 1.24967V0.833008C3.58317 0.418794 3.91896 0.0830078 4.33317 0.0830078ZM4.33317 2.74967H2.6665C2.52843 2.74967 2.4165 2.8616 2.4165 2.99967V4.24967H11.5832V2.99967C11.5832 2.8616 11.4712 2.74967 11.3332 2.74967H9.6665H4.33317ZM11.5832 5.74967H2.4165V11.6663C2.4165 11.8044 2.52843 11.9163 2.6665 11.9163H11.3332C11.4712 11.9163 11.5832 11.8044 11.5832 11.6663V5.74967Z" fill="" />
                                        </svg>
                                    </span>
                                </div>
                            </div>

                            <!-- PKWT Berakhir -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    PKWT Berakhir
                                </label>
                                <div class="relative">
                                <input
                                    type="date"
                                    name="PKWT_Berakhir"
                                    value="{{ old('PKWT_Berakhir', optional($karyawan->kontrak)->PKWT_Berakhir) }}"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" onclick="this.showPicker()"/>
                                    <span class="absolute top-1/2 right-3.5 -translate-y-1/2 pointer-events-none">
                                        <svg class="fill-gray-700 dark:fill-gray-400" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.33317 0.0830078C4.74738 0.0830078 5.08317 0.418794 5.08317 0.833008V1.24967H8.9165V0.833008C8.9165 0.418794 9.25229 0.0830078 9.6665 0.0830078C10.0807 0.0830078 10.4165 0.418794 10.4165 0.833008V1.24967L11.3332 1.24967C12.2997 1.24967 13.0832 2.03318 13.0832 2.99967V4.99967V11.6663C13.0832 12.6328 12.2997 13.4163 11.3332 13.4163H2.6665C1.70001 13.4163 0.916504 12.6328 0.916504 11.6663V4.99967V2.99967C0.916504 2.03318 1.70001 1.24967 2.6665 1.24967L3.58317 1.24967V0.833008C3.58317 0.418794 3.91896 0.0830078 4.33317 0.0830078ZM4.33317 2.74967H2.6665C2.52843 2.74967 2.4165 2.8616 2.4165 2.99967V4.24967H11.5832V2.99967C11.5832 2.8616 11.4712 2.74967 11.3332 2.74967H9.6665H4.33317ZM11.5832 5.74967H2.4165V11.6663C2.4165 11.8044 2.52843 11.9163 2.6665 11.9163H11.3332C11.4712 11.9163 11.5832 11.8044 11.5832 11.6663V5.74967Z" fill="" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- ROW 2 -->
                        <div class="grid grid-cols-3 gap-3">

                            <!-- Tanggal Diangkat Tetap -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Tanggal Diangkat Menjadi Tetap
                                </label>
                                <div class="relative">
                                <input
                                    type="date"
                                    name="Tanggal_Diangkat_Menjadi_Karyawan_Tetap"
                                    value="{{ old('Tanggal_Diangkat_Menjadi_Karyawan_Tetap', optional($karyawan->kontrak)->Tanggal_Diangkat_Menjadi_Karyawan_Tetap) }}"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" onclick="this.showPicker()"/>
                                    <span class="absolute top-1/2 right-3.5 -translate-y-1/2 pointer-events-none">
                                    <svg class="fill-gray-700 dark:fill-gray-400" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M4.33317 0.0830078C4.74738 0.0830078 5.08317 0.418794 5.08317 0.833008V1.24967H8.9165V0.833008C8.9165 0.418794 9.25229 0.0830078 9.6665 0.0830078C10.0807 0.0830078 10.4165 0.418794 10.4165 0.833008V1.24967L11.3332 1.24967C12.2997 1.24967 13.0832 2.03318 13.0832 2.99967V4.99967V11.6663C13.0832 12.6328 12.2997 13.4163 11.3332 13.4163H2.6665C1.70001 13.4163 0.916504 12.6328 0.916504 11.6663V4.99967V2.99967C0.916504 2.03318 1.70001 1.24967 2.6665 1.24967L3.58317 1.24967V0.833008C3.58317 0.418794 3.91896 0.0830078 4.33317 0.0830078ZM4.33317 2.74967H2.6665C2.52843 2.74967 2.4165 2.8616 2.4165 2.99967V4.24967H11.5832V2.99967C11.5832 2.8616 11.4712 2.74967 11.3332 2.74967H9.6665H4.33317ZM11.5832 5.74967H2.4165V11.6663C2.4165 11.8044 2.52843 11.9163 2.6665 11.9163H11.3332C11.4712 11.9163 11.5832 11.8044 11.5832 11.6663V5.74967Z" fill="" />
                                    </svg>
                                </span>
                                </div>
                            </div>

                            <!-- Riwayat Penempatan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Riwayat Penempatan
                                </label>
                                <input
                                    name="Riwayat_Penempatan"
                                    value="{{ old('Riwayat_Penempatan', optional($karyawan->kontrak)->Riwayat_Penempatan) }}"
                                    placeholder="Contoh: Admin Gudang"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent
                px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3
                focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
                dark:placeholder:text-white/30;"
                                />
                            </div>

                            <!-- Tanggal Penempatan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Tanggal Riwayat Penempatan
                                </label>
                                <div class="relative">
                                <input
                                    type="date"
                                    name="Tanggal_Riwayat_Penempatan"
                                    value="{{ old('Tanggal_Riwayat_Penempatan', optional($karyawan->kontrak)->Tanggal_Riwayat_Penempatan) }}"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" onclick="this.showPicker()"/>
                                    <span class="absolute top-1/2 right-3.5 -translate-y-1/2 pointer-events-none">
                                    <svg class="fill-gray-700 dark:fill-gray-400" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M4.33317 0.0830078C4.74738 0.0830078 5.08317 0.418794 5.08317 0.833008V1.24967H8.9165V0.833008C8.9165 0.418794 9.25229 0.0830078 9.6665 0.0830078C10.0807 0.0830078 10.4165 0.418794 10.4165 0.833008V1.24967L11.3332 1.24967C12.2997 1.24967 13.0832 2.03318 13.0832 2.99967V4.99967V11.6663C13.0832 12.6328 12.2997 13.4163 11.3332 13.4163H2.6665C1.70001 13.4163 0.916504 12.6328 0.916504 11.6663V4.99967V2.99967C0.916504 2.03318 1.70001 1.24967 2.6665 1.24967L3.58317 1.24967V0.833008C3.58317 0.418794 3.91896 0.0830078 4.33317 0.0830078ZM4.33317 2.74967H2.6665C2.52843 2.74967 2.4165 2.8616 2.4165 2.99967V4.24967H11.5832V2.99967C11.5832 2.8616 11.4712 2.74967 11.3332 2.74967H9.6665H4.33317ZM11.5832 5.74967H2.4165V11.6663C2.4165 11.8044 2.52843 11.9163 2.6665 11.9163H11.3332C11.4712 11.9163 11.5832 11.8044 11.5832 11.6663V5.74967Z" fill="" />
                                    </svg>
                                </span>
                                </div>
                            </div>

                        </div>

                        <!-- ROW 3 -->
                        <div class="grid grid-cols-3 gap-3">

                            <!-- Mutasi/Promosi/Demosi -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Mutasi / Promosi / Demosi
                                </label>
                                <input
                                    name="Mutasi_Promosi_Demosi"
                                    value="{{ old('Mutasi_Promosi_Demosi', optional($karyawan->kontrak)->Mutasi_Promosi_Demosi) }}"
                                    placeholder="Contoh: Mutasi ke Divisi Baru"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent
                px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3
                focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
                dark:placeholder:text-white/30;"
                                />
                            </div>

                            <!-- Tanggal Mutasi -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Tanggal Mutasi / Promosi / Demosi
                                </label>
                                <div class="relative">
                                <input
                                    type="date"
                                    name="Tanggal_Mutasi_Promosi_Demosi"
                                    value="{{ old('Tanggal_Mutasi_Promosi_Demosi', optional($karyawan->kontrak)->Tanggal_Mutasi_Promosi_Demosi) }}"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" onclick="this.showPicker()"/>
                                    <span class="absolute top-1/2 right-3.5 -translate-y-1/2 pointer-events-none">
                                    <svg class="fill-gray-700 dark:fill-gray-400" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M4.33317 0.0830078C4.74738 0.0830078 5.08317 0.418794 5.08317 0.833008V1.24967H8.9165V0.833008C8.9165 0.418794 9.25229 0.0830078 9.6665 0.0830078C10.0807 0.0830078 10.4165 0.418794 10.4165 0.833008V1.24967L11.3332 1.24967C12.2997 1.24967 13.0832 2.03318 13.0832 2.99967V4.99967V11.6663C13.0832 12.6328 12.2997 13.4163 11.3332 13.4163H2.6665C1.70001 13.4163 0.916504 12.6328 0.916504 11.6663V4.99967V2.99967C0.916504 2.03318 1.70001 1.24967 2.6665 1.24967L3.58317 1.24967V0.833008C3.58317 0.418794 3.91896 0.0830078 4.33317 0.0830078ZM4.33317 2.74967H2.6665C2.52843 2.74967 2.4165 2.8616 2.4165 2.99967V4.24967H11.5832V2.99967C11.5832 2.8616 11.4712 2.74967 11.3332 2.74967H9.6665H4.33317ZM11.5832 5.74967H2.4165V11.6663C2.4165 11.8044 2.52843 11.9163 2.6665 11.9163H11.3332C11.4712 11.9163 11.5832 11.8044 11.5832 11.6663V5.74967Z" fill="" />
                                    </svg>
                                </span>
                                </div>
                            </div>

                            <!-- Masa Kerja -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Masa Kerja
                                </label>
                                <!-- name="Masa_Kerja" -->
                                <input
                                    x-model="masaKerja"
                                    readonly
                                    placeholder="Otomatis terhitung"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300
                                    focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full
                                    rounded-lg border border-gray-300 bg-transparent px-4 py-2.5
                                    text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3
                                    focus:outline-hidden dark:border-gray-700 dark:bg-gray-900
                                    dark:text-white/90 dark:placeholder:text-white/30"
                                />
                            </div>

                        </div>

                        <!-- ROW 4 -->
                        <div class="grid grid-cols-3 gap-3">
                             <!-- Nomor PKWT Pertama -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Nomor PKWT Pertama
                                </label>
                                <input
                                    name="NO_PKWT_PERTAMA"
                                    value="{{ old('NO_PKWT_PERTAMA', optional($karyawan->kontrak)->NO_PKWT_PERTAMA) }}"
                                    placeholder="Masukkan Nomor PKWT Pertama"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                                    dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent
                                    px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3
                                    focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
                                    dark:placeholder:text-white/30;"
                                />
                            </div>

                            <!-- NO SK PERTAMA -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Nomor SK Pertama
                                </label>
                                <input
                                    name="NO_SK_PERTAMA"
                                    value="{{ old('NO_SK_PERTAMA', optional($karyawan->kontrak)->NO_SK_PERTAMA) }}"
                                    placeholder="Masukkan Nomor SK Pertama"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                                    dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent
                                    px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3
                                    focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
                                    dark:placeholder:text-white/30;"
                                />
                            </div>

                        </div>
                    </div>


            <!-- ================= STEP 5: Status & BPJS ================= -->
            <div x-show="currentStep===5" x-transition class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Tanggal Non Aktif</label>
                        <div class="relative">
                        <input name="Tanggal_Non_Aktif" type="date" value="{{ old('Tanggal_Non_Aktif', optional($karyawan->status)->Tanggal_Non_Aktif) }}"
                         class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" onclick="this.showPicker()"/>
                                    <span class="absolute top-1/2 right-3.5 -translate-y-1/2 pointer-events-none">
                                    <svg class="fill-gray-700 dark:fill-gray-400" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M4.33317 0.0830078C4.74738 0.0830078 5.08317 0.418794 5.08317 0.833008V1.24967H8.9165V0.833008C8.9165 0.418794 9.25229 0.0830078 9.6665 0.0830078C10.0807 0.0830078 10.4165 0.418794 10.4165 0.833008V1.24967L11.3332 1.24967C12.2997 1.24967 13.0832 2.03318 13.0832 2.99967V4.99967V11.6663C13.0832 12.6328 12.2997 13.4163 11.3332 13.4163H2.6665C1.70001 13.4163 0.916504 12.6328 0.916504 11.6663V4.99967V2.99967C0.916504 2.03318 1.70001 1.24967 2.6665 1.24967L3.58317 1.24967V0.833008C3.58317 0.418794 3.91896 0.0830078 4.33317 0.0830078ZM4.33317 2.74967H2.6665C2.52843 2.74967 2.4165 2.8616 2.4165 2.99967V4.24967H11.5832V2.99967C11.5832 2.8616 11.4712 2.74967 11.3332 2.74967H9.6665H4.33317ZM11.5832 5.74967H2.4165V11.6663C2.4165 11.8044 2.52843 11.9163 2.6665 11.9163H11.3332C11.4712 11.9163 11.5832 11.8044 11.5832 11.6663V5.74967Z" fill="" />
                                    </svg>
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Alasan Non Aktif</label>
                        <input name="Alasan_Non_Aktif" value="{{ old('Alasan_Non_Aktif', optional($karyawan->status)->Alasan_Non_Aktif) }}"
                         class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                            dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden
                            dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Ijazah Dikembalikan</label>
                        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                            <select name="Ijazah_Dikembalikan"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                                <option value="">-- Pilih --</option>
                                <option value="Ya" {{ old('Ijazah_Dikembalikan', optional($karyawan->status)->Ijazah_Dikembalikan) == 'Ya' ? 'selected' : '' }}>Ya</option>
                                <option value="Tidak" {{ old('Ijazah_Dikembalikan', optional($karyawan->status)->Ijazah_Dikembalikan) == 'Tidak' ? 'selected' : '' }}>Tidak</option>
                            </select>
                            <span
                                class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Bulan (status)</label>
                        <input name="Bulan" type="number" min="0" step="1" value="{{ old('Bulan') }}" placeholder="Jumlah bulan (angka)" class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm" />
                    </div> -->
                </div>
            </div>

            <!-- ================= STEP 6: Status BPJS ================= -->
            <div x-show="currentStep===6" x-transition class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Status BPJS Ketenagakerjaan</label>
                        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                            <select name="Status_BPJS_KT"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                                <option value="">-- Pilih Status BPJS --</option>
                                <option value="Aktif" {{ old('Status_BPJS_KT', optional($karyawan->bpjs)->Status_BPJS_KT) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Tidak Aktif" {{ old('Status_BPJS_KT', optional($karyawan->bpjs)->Status_BPJS_KT) == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            <span
                                class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Status BPJS Kesehatan</label>
                        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                            <select name="Status_BPJS_KS"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                                <option value="">-- Pilih Status BPJS --</option>
                                <option value="Aktif" {{ old('Status_BPJS_KS', optional($karyawan->bpjs)->Status_BPJS_KS) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Tidak Aktif" {{ old('Status_BPJS_KS', optional($karyawan->bpjs)->Status_BPJS_KS) == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            <span
                                class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <div>
                    <button type="button" @click="prev" x-show="currentStep>0" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">Kembali
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-gray-500 dark:text-gray-400" x-text="'Step ' + (currentStep+1) + ' / ' + steps.length"></span>
                    <button type="button" @click="next" x-show="currentStep < steps.length-1" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 dark:text-white hover:bg-primary-700">Selanjutnya
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <button type="submit" x-show="currentStep===steps.length-1" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 dark:text-white hover:bg-emerald-700">Simpan
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function karyawanForm() {
    return {
        steps: ['Data Karyawan','Data Keluarga','Pekerjaan','Pendidikan','Kontrak','Status Non Aktif','BPJS'],
        currentStep: 0,
        // pendidikan: [{Pendidikan_Terakhir:'', Nama_Lengkap_Tempat_Pendidikan_Terakhir:'', Jurusan:''}],
        // kontrak: [{Tanggal_Mulai_Tugas:'', PKWT_berakhir:'', Tanggal_Diangkat_Menjadi_Karyawan_Tetap:'', Riwayat_Penempatan:'', Tanggal_Riwayat_Penempatan:'',
        //             Mutasi_Promosi_Demosi:'', Tanggal_Mutasi_Promosi_Demosi:'', Masa_Kerja:'', NO_PKWT_PERTAMA:'', NO_SK_PERTAMA:'',}],
        go(i){ this.currentStep = i; window.scrollTo(0,0); },
        next(){ if(this.currentStep < this.steps.length-1) this.currentStep++; window.scrollTo(0,0); },
        prev(){ if(this.currentStep > 0) this.currentStep--; window.scrollTo(0,0); },
        // addPendidikan(){ this.pendidikan.push({Pendidikan_Terakhir:'', Nama_Lengkap_Tempat_Pendidikan_Terakhir:'', Jurusan:''}); },
        // removePendidikan(i){ this.pendidikan.splice(i,1); },
        // addKontrak(){ this.kontrak.push({Tanggal_Mulai_Tugas:'', PKWT_berakhir:'', Tanggal_Diangkat_Menjadi_Karyawan_Tetap:'', Riwayat_Penempatan:'', Tanggal_Riwayat_Penempatan:'',
        //             Mutasi_Promosi_Demosi:'', Tanggal_Mutasi_Promosi_Demosi:'', Masa_Kerja:'', NO_PKWT_PERTAMA:'', NO_SK_PERTAMA:'',}); },
        // removeKontrak(i){ this.kontrak.splice(i,1); },
        calculateMasaKerja(idx) {
            const start = this.kontrak[idx].Tanggal_Mulai_Tugas;
            console.log('calculateMasaKerja called for idx', idx, 'start=', start);
            if (!start) {
                this.kontrak[idx].Masa_Kerja = '';
                return;
            }

            const startDate = new Date(start);
            const today = new Date();

            let years = today.getFullYear() - startDate.getFullYear();
            let months = today.getMonth() - startDate.getMonth();
            let days = today.getDate() - startDate.getDate();

            // Koreksi bulan negatif
            if (months < 0) {
                years--;
                months += 12;
            }

            // Koreksi hari negatif
            if (days < 0) {
                months--;
                const prevMonth = new Date(today.getFullYear(), today.getMonth(), 0).getDate();
                days += prevMonth;

                if (months < 0) {
                    months = 11;
                    years--;
                }
            }

            this.kontrak[idx].Masa_Kerja = `${years} Tahun ${months} Bulan ${days} Hari`;
        },
        submit(e){
            // submit native form
            $el = document.querySelector('form[x-data]');
            $el.submit();
        }
    }
}
</script>
<script>
    document.getElementById('status').addEventListener('change', function () {
        const kodeField = document.getElementById('kode');
        const statusValue = this.value;

        if (statusValue === "1") {
            kodeField.value = "Aktif";
        } else if (statusValue === "0") {
            kodeField.value = "Non Aktif";
        } else {
            kodeField.value = "";
        }
    });
</script>
<script>
    document.getElementById('tanggal_lahir').addEventListener('change', function () {
        const lahir = new Date(this.value);
        const today = new Date();

        let umur = today.getFullYear() - lahir.getFullYear();
        const monthDiff = today.getMonth() - lahir.getMonth();

        // Jika bulan belum lewat atau belum ulang tahun bulan ini → kurangi 1
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < lahir.getDate())) {
            umur--;
        }

        // Set hasil ke input umur
        document.getElementById('umur').value = umur + " Tahun";
    });
</script>
<script>
    // Load Provinsi
    fetch("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json")
        .then(res => res.json())
        .then(data => {
            let provinsi = document.getElementById("Provinsi");
            data.forEach(item => {
                provinsi.innerHTML += `<option value="${item.name}" data-id="${item.id}">${item.name}</option>`;
            });
        });

    // Ketika Provinsi dipilih → Load Kabupaten
    document.getElementById("Provinsi").addEventListener("change", function() {
        let id = this.options[this.selectedIndex].dataset.id;

        fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${id}.json`)
            .then(res => res.json())
            .then(data => {
                let kab = document.getElementById("Kabupaten_Kota");
                kab.innerHTML = `<option value="">-- Pilih Kabupaten/Kota --</option>`;
                document.getElementById("Kecamatan").innerHTML = `<option value="">-- Pilih Kecamatan --</option>`;
                document.getElementById("Kelurahan_Desa").innerHTML = `<option value="">-- Pilih Desa/Kelurahan --</option>`;

                data.forEach(item => {
                    kab.innerHTML += `<option value="${item.name}" data-id="${item.id}">${item.name}</option>`;
                });
            });
    });

    // Ketika Kabupaten dipilih → Load Kecamatan
    document.getElementById("Kabupaten_Kota").addEventListener("change", function() {
        let id = this.options[this.selectedIndex].dataset.id;

        fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${id}.json`)
            .then(res => res.json())
            .then(data => {
                let kec = document.getElementById("Kecamatan");
                kec.innerHTML = `<option value="">-- Pilih Kecamatan --</option>`;
                document.getElementById("Kelurahan_Desa").innerHTML = `<option value="">-- Pilih Desa/Kelurahan --</option>`;

                data.forEach(item => {
                    kec.innerHTML += `<option value="${item.name}" data-id="${item.id}">${item.name}</option>`;
                });
            });
    });

    // Ketika Kecamatan dipilih → Load Desa
    document.getElementById("Kecamatan").addEventListener("change", function() {
        let id = this.options[this.selectedIndex].dataset.id;

        fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${id}.json`)
            .then(res => res.json())
            .then(data => {
                let desa = document.getElementById("Kelurahan_Desa");
                desa.innerHTML = `<option value="">-- Pilih Desa/Kelurahan --</option>`;

                data.forEach(item => {
                    desa.innerHTML += `<option value="${item.name}">${item.name}</option>`;
                });
            });
    });
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    /* ===============================
       FUNGSI GLOBAL
    =============================== */

    const sleep = (ms) => new Promise(r => setTimeout(r, ms));

    const provDom = document.getElementById("Provinsi_Sesuai_Domisili");
    const kabDom  = document.getElementById("Kabupaten_Kota_Sesuai_Domisili");
    const kecDom  = document.getElementById("Kecamatan_Sesuai_Domisili");
    const kelDom  = document.getElementById("Kelurahan_Desa_Domisili");

    /* ===============================
       LOAD PROVINSI DOMISILI
    =============================== */

    let provinsiData = [];
    let kabupatenData = [];
    let kecamatanData = [];

    fetch("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json")
        .then(r => r.json())
        .then(data => {
            provinsiData = data;
            data.forEach(p => {
                provDom.innerHTML += `<option value="${p.name}" data-id="${p.id}">${p.name}</option>`;
            });
        });


    /* ===============================
       LOAD KABUPATEN DOMISILI
    =============================== */
    provDom.addEventListener("change", function () {
        let provId = this.options[this.selectedIndex].dataset.id;

        kabDom.innerHTML = `<option value="">Pilih Kabupaten/Kota</option>`;
        kecDom.innerHTML = `<option value="">Pilih Kecamatan</option>`;
        kelDom.innerHTML = `<option value="">Pilih Kelurahan</option>`;

        fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provId}.json`)
            .then(r => r.json())
            .then(data => {
                kabupatenData = data;
                data.forEach(k => {
                    kabDom.innerHTML += `<option value="${k.name}" data-id="${k.id}">${k.name}</option>`;
                });
            });
    });


    /* ===============================
       LOAD KECAMATAN DOMISILI
    =============================== */
    kabDom.addEventListener("change", function () {
        let id = this.options[this.selectedIndex].dataset.id;

        kecDom.innerHTML = `<option value="">Pilih Kecamatan</option>`;
        kelDom.innerHTML = `<option value="">Pilih Kelurahan</option>`;

        fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${id}.json`)
            .then(r => r.json())
            .then(data => {
                kecamatanData = data;
                data.forEach(d => {
                    kecDom.innerHTML += `<option value="${d.name}" data-id="${d.id}">${d.name}</option>`;
                });
            });
    });


    /* ===============================
       LOAD KELURAHAN DOMISILI
    =============================== */
    kecDom.addEventListener("change", function () {
        let id = this.options[this.selectedIndex].dataset.id;

        kelDom.innerHTML = `<option value="">Pilih Kelurahan</option>`;

        fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${id}.json`)
            .then(r => r.json())
            .then(data => {
                data.forEach(v => {
                    kelDom.innerHTML += `<option value="${v.name}">${v.name}</option>`;
                });
            });
    });


    /* ==========================================================
       AUTO-COPY DENGAN MENCOCOKKAN NAMA → ID WILAYAH
    ========================================================== */

    document.getElementById("sameAsKTP").addEventListener("change", async function () {

        const isChecked = this.checked;

        if (!isChecked) {
            // RESET
            provDom.value = "";
            kabDom.innerHTML = `<option value="">Pilih Kabupaten/Kota</option>`;
            kecDom.innerHTML = `<option value="">Pilih Kecamatan</option>`;
            kelDom.innerHTML = `<option value="">Pilih Kelurahan</option>`;

            document.getElementById("Alamat_Domisili").value = "";
            document.getElementById("RT_Sesuai_Domisili").value = "";
            document.getElementById("RW_Sesuai_Domisili").value = "";
            document.getElementById("Alamat_Lengkap").value = "";

            return;
        }

        /* -------------------------------
           SALIN TEXT
        ------------------------------- */
        document.getElementById("Alamat_Domisili").value =
            document.getElementById("Alamat_KTP").value;

        document.getElementById("RT_Sesuai_Domisili").value =
            document.getElementById("RT").value;

        document.getElementById("RW_Sesuai_Domisili").value =
            document.getElementById("RW").value;

        document.getElementById("Alamat_Lengkap").value =
            document.getElementById("Alamat_KTP").value;

        /* -------------------------------
           MATCH PROVINSI (by NAME)
        ------------------------------- */

        let fromProvName = document.getElementById("Provinsi").value;
        let provMatch    = provinsiData.find(p => p.name == fromProvName);

        if (!provMatch) return;

        provDom.value = provMatch.name;

        // LOAD KABUPATEN
        let kabRes = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provMatch.id}.json`);
        kabupatenData = await kabRes.json();

        kabDom.innerHTML = `<option value="">Pilih Kabupaten/Kota</option>`;
        kabupatenData.forEach(k => {
            kabDom.innerHTML += `<option value="${k.name}" data-id="${k.id}">${k.name}</option>`;
        });

        /* -------------------------------
           MATCH KABUPATEN (by NAME)
        ------------------------------- */
        let fromKabName = document.getElementById("Kabupaten_Kota").value;
        let kabMatch    = kabupatenData.find(k => k.name == fromKabName);

        if (!kabMatch) return;

        kabDom.value = kabMatch.name;


        // LOAD KECAMATAN
        let kecRes = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${kabMatch.id}.json`);
        kecamatanData = await kecRes.json();

        kecDom.innerHTML = `<option value="">Pilih Kecamatan</option>`;
        kecamatanData.forEach(k => {
            kecDom.innerHTML += `<option value="${k.name}" data-id="${k.id}">${k.name}</option>`;
        });

        /* -------------------------------
           MATCH KECAMATAN (by NAME)
        ------------------------------- */
        let fromKecName = document.getElementById("Kecamatan").value;
        let kecMatch    = kecamatanData.find(k => k.name == fromKecName);

        if (!kecMatch) return;

        kecDom.value = kecMatch.name;

        // LOAD KELURAHAN
        let kelRes = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${kecMatch.id}.json`);
        let kelurahanData = await kelRes.json();

        kelDom.innerHTML = `<option value="">Pilih Kelurahan</option>`;
        kelurahanData.forEach(v => {
            kelDom.innerHTML += `<option value="${v.name}">${v.name}</option>`;
        });

        /* -------------------------------
           MATCH KELURAHAN (by NAME)
        ------------------------------- */
        let fromKelName = document.getElementById("Kelurahan_Desa").value;
        kelDom.value = fromKelName;
    });

});
</script>
<!-- <script>
function kontrakForm() {
  return {

    kontrak: [],

    existingKontrak: window.existingKontrak ? window.existingKontrak : null,

    init() {
      if (Array.isArray(this.existingKontrak) && this.existingKontrak.length > 0) {
        this.kontrak = JSON.parse(JSON.stringify(this.existingKontrak));
      } else {
        this.kontrak = [ this.emptyKontrak() ];
      }

      // hitung semua masa kerja setelah load
      for (let i = 0; i < this.kontrak.length; i++) {
        this.calculateMasaKerja(i);
      }
    },

    emptyKontrak() {
      return {
        Tanggal_Mulai_Tugas: "",
        PKWT_berakhir: "",
        NO_PKWT_PERTAMA: "",
        Tanggal_Diangkat_Menjadi_Karyawan_Tetap: "",
        Riwayat_Penempatan: "",
        Tanggal_Riwayat_Penempatan: "",
        Mutasi_Promosi_Demosi: "",
        Tanggal_Mutasi_Promosi_Demosi: "",
        Masa_Kerja: "",
        NO_SK_PERTAMA: ""
      };
    },

    addKontrak() {
      this.kontrak.push(this.emptyKontrak());
    },

    removeKontrak(index) {
      this.kontrak.splice(index, 1);
    },

    validDate(str) {
      if (!str || str === "0000-00-00") return false;
      var d = new Date(str);
      return d instanceof Date && !isNaN(d);
    },

    calculateMasaKerja(idx) {
      var startStr = this.kontrak[idx].Tanggal_Mulai_Tugas;

      if (!this.validDate(startStr)) {
        this.kontrak[idx].Masa_Kerja = "";
        return;
      }

      var start = new Date(startStr);
      var end = new Date();

      if (start > end) {
        this.kontrak[idx].Masa_Kerja = "";
        return;
      }

      var years = end.getFullYear() - start.getFullYear();
      var months = end.getMonth() - start.getMonth();
      var days = end.getDate() - start.getDate();

      if (days < 0) {
        months -= 1;
        days += new Date(end.getFullYear(), end.getMonth(), 0).getDate();
      }

      if (months < 0) {
        years -= 1;
        months += 12;
      }

      this.kontrak[idx].Masa_Kerja =
        years + " Tahun " + months + " Bulan " + days + " Hari";
    }

  };
}
</script> -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const companySelect = document.getElementById('company');
    const divisionSelect = document.getElementById('division');
    const departmentSelect = document.getElementById('department');
    const unitSelect = document.getElementById('unit');
    const positionSelect = document.getElementById('position');

    // Function to populate select options
    function populateSelect(selectElement, data, placeholder) {
        console.log('Populating', selectElement.id, 'with data:', data);
        selectElement.innerHTML = `<option value="">${placeholder}</option>`;
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.name;
            selectElement.appendChild(option);
        });
    }

    // Company change event
    companySelect.addEventListener('change', function() {
        const companyId = this.value;
        console.log('Company changed to:', companyId);
        if (companyId) {
            console.log('Fetching divisions for company:', companyId);
            fetch(`/karyawan/divisions/${companyId}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Fetched divisions data:', data);
                    populateSelect(divisionSelect, data, '-- Pilih Divisi --');
                    // Reset dependent selects
                    departmentSelect.innerHTML = '<option value="">-- Pilih Departement --</option>';
                    unitSelect.innerHTML = '<option value="">-- Pilih Unit --</option>';
                    positionSelect.innerHTML = '<option value="">-- Pilih Level Jabatan --</option>';
                })
                .catch(error => console.error('Error fetching divisions:', error));
        } else {
            divisionSelect.innerHTML = '<option value="">-- Pilih Divisi --</option>';
            departmentSelect.innerHTML = '<option value="">-- Pilih Departement --</option>';
            unitSelect.innerHTML = '<option value="">-- Pilih Unit --</option>';
            positionSelect.innerHTML = '<option value="">-- Pilih Level Jabatan --</option>';
        }
    });

    // Division change event
    divisionSelect.addEventListener('change', function() {
        const divisionId = this.value;
        if (divisionId) {
            fetch(`/karyawan/departments/${divisionId}`)
                .then(response => response.json())
                .then(data => {
                    populateSelect(departmentSelect, data, '-- Pilih Departement --');
                    // Reset dependent selects
                    unitSelect.innerHTML = '<option value="">-- Pilih Unit --</option>';
                    positionSelect.innerHTML = '<option value="">-- Pilih Level Jabatan --</option>';
                });
        } else {
            departmentSelect.innerHTML = '<option value="">-- Pilih Departement --</option>';
            unitSelect.innerHTML = '<option value="">-- Pilih Unit --</option>';
            positionSelect.innerHTML = '<option value="">-- Pilih Level Jabatan --</option>';
        }
    });

    // Department change event
    departmentSelect.addEventListener('change', function() {
        const departmentId = this.value;
        if (departmentId) {
            fetch(`/karyawan/units/${departmentId}`)
                .then(response => response.json())
                .then(data => {
                    populateSelect(unitSelect, data, '-- Pilih Unit --');
                    // Reset dependent select
                    positionSelect.innerHTML = '<option value="">-- Pilih Level Jabatan --</option>';
                });
        } else {
            unitSelect.innerHTML = '<option value="">-- Pilih Unit --</option>';
            positionSelect.innerHTML = '<option value="">-- Pilih Level Jabatan --</option>';
        }
    });

    // Unit change event
    unitSelect.addEventListener('change', function() {
        const unitId = this.value;
        if (unitId) {
            fetch(`/karyawan/positions/${unitId}`)
                .then(response => response.json())
                .then(data => {
                    populateSelect(positionSelect, data, '-- Pilih Level Jabatan --');
                });
        } else {
            positionSelect.innerHTML = '<option value="">-- Pilih Level Jabatan --</option>';
        }
    });

    // Load initial data on page load
    const initialCompanyId = companySelect.value;
    const initialDivisionId = '{{ optional($karyawan->pekerjaan->first())->division_id }}';
    const initialDepartmentId = '{{ optional($karyawan->pekerjaan->first())->department_id }}';
    const initialUnitId = '{{ optional($karyawan->pekerjaan->first())->unit_id }}';
    const initialPositionId = '{{ optional($karyawan->pekerjaan->first())->position_id }}';

    if (initialCompanyId) {
        fetch(`/karyawan/divisions/${initialCompanyId}`)
            .then(response => response.json())
            .then(data => {
                populateSelect(divisionSelect, data, '-- Pilih Divisi --');
                // Set selected division
                if (initialDivisionId) {
                    divisionSelect.value = initialDivisionId;

                    // Load departments for selected division
                    fetch(`/karyawan/departments/${initialDivisionId}`)
                        .then(response => response.json())
                        .then(data => {
                            populateSelect(departmentSelect, data, '-- Pilih Departement --');
                            if (initialDepartmentId) {
                                departmentSelect.value = initialDepartmentId;

                                // Load units for selected department
                                fetch(`/karyawan/units/${initialDepartmentId}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        populateSelect(unitSelect, data, '-- Pilih Unit --');
                                        if (initialUnitId) {
                                            unitSelect.value = initialUnitId;

                                            // Load positions for selected unit
                                            fetch(`/karyawan/positions/${initialUnitId}`)
                                                .then(response => response.json())
                                                .then(data => {
                                                    populateSelect(positionSelect, data, '-- Pilih Level Jabatan --');
                                                    if (initialPositionId) {
                                                        positionSelect.value = initialPositionId;
                                                    }
                                                });
                                        }
                                    });
                            }
                        });
                }
            });
    }
});
</script>
@endpush
