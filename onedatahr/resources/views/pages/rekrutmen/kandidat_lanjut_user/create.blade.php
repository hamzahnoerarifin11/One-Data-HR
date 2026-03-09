@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Tambah Kandidat Lanjut User
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Kandidat yang telah dinyatakan DITERIMA pada Interview HR
            </p>
        </div>

        <a href="{{ route('rekrutmen.kandidat_lanjut_user.index') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2
                  text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300
                  dark:hover:bg-white/[0.05] transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
    </div>

    <!-- Card -->
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-lg
                dark:border-gray-800 dark:bg-white/[0.03]">

        <form method="POST" action="{{ route('rekrutmen.kandidat_lanjut_user.store') }}">
            @csrf

            <!-- ================= IDENTITAS ================= -->
            <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">
                Identitas Kandidat
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">

                <!-- Kandidat -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Nama Kandidat
                    </label>
                    <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                    <select id="kandidat_select" name="kandidat_id" required
                       class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                        <option value="">-- Pilih Kandidat --</option>
                        @foreach($kandidat as $k)
                            <option value="{{ $k->kandidat_id }}"
                                data-posisi="{{ $k->kandidat->posisi?->nama_posisi }}"
                                data-tanggal="{{ $k->hari_tanggal }}">
                                {{ $k->kandidat->nama }}
                            </option>
                        @endforeach
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

                <!-- Posisi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Posisi Dilamar
                    </label>
                    <input id="posisi_dilamar" placeholder="Otomatis" readonly
                        class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg
                               border border-gray-300 bg-transparent px-4 py-2.5
                               text-sm text-gray-800 dark:border-gray-700
                               dark:bg-gray-900 dark:text-white/90">
                </div>

                <!-- Tanggal Interview HR -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Tanggal Interview HR
                    </label>
                    <input id="tanggal_interview_hr" name="tanggal_interview_hr" placeholder="Otomatis" readonly
                        class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg
                               border border-gray-300 bg-transparent px-4 py-2.5
                               text-sm text-gray-800 dark:border-gray-700
                               dark:bg-gray-900 dark:text-white/90">
                </div>

                <!-- Tanggal Penyerahan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Tanggal Penyerahan
                    </label>
                    <div class="relative">
                    <input type="date" name="tanggal_penyerahan"
                        value="{{ date('Y-m-d') }}"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" onclick="this.showPicker()" />
                            <span class="absolute top-1/2 right-3.5 -translate-y-1/2 pointer-events-none">
                                <svg class="fill-gray-700 dark:fill-gray-400" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.33317 0.0830078C4.74738 0.0830078 5.08317 0.418794 5.08317 0.833008V1.24967H8.9165V0.833008C8.9165 0.418794 9.25229 0.0830078 9.6665 0.0830078C10.0807 0.0830078 10.4165 0.418794 10.4165 0.833008V1.24967L11.3332 1.24967C12.2997 1.24967 13.0832 2.03318 13.0832 2.99967V4.99967V11.6663C13.0832 12.6328 12.2997 13.4163 11.3332 13.4163H2.6665C1.70001 13.4163 0.916504 12.6328 0.916504 11.6663V4.99967V2.99967C0.916504 2.03318 1.70001 1.24967 2.6665 1.24967L3.58317 1.24967V0.833008C3.58317 0.418794 3.91896 0.0830078 4.33317 0.0830078ZM4.33317 2.74967H2.6665C2.52843 2.74967 2.4165 2.8616 2.4165 2.99967V4.24967H11.5832V2.99967C11.5832 2.8616 11.4712 2.74967 11.3332 2.74967H9.6665H4.33317ZM11.5832 5.74967H2.4165V11.6663C2.4165 11.8044 2.52843 11.9163 2.6665 11.9163H11.3332C11.4712 11.9163 11.5832 11.8044 11.5832 11.6663V5.74967Z" fill="" />
                                </svg>
                            </span>
                        </div>
                </div>

                <!-- User Terkait -->
                <!-- <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                        User Terkait
                    </label>
                    <input name="user_terkait" placeholder="Nama User" required
                        class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg
                               border border-gray-300 bg-transparent px-4 py-2.5
                               text-sm text-gray-800 dark:border-gray-700
                               dark:bg-gray-900 dark:text-white/90">
                </div> -->

            </div>

            <!-- ================= INTERVIEW USER ================= -->
            <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">
                Interview User
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <div class="col-span-2 space-y-4"
                    x-data="{
                        interviews: {{ old('detail_interview')
                            ? json_encode(old('detail_interview'))
                            : json_encode($kandidatLanjut->detail_interview ?? []) }}
                    }">

                    <template x-for="(item, index) in interviews" :key="index">
                        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">

                            <div class="mb-3 flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">
                                    Interview User Ke-<span x-text="index + 1"></span>
                                </h4>
                                <button type="button" @click="interviews.splice(index, 1)" class="text-xs text-red-600 hover:underline">
                                    Hapus
                                </button>
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-sm dark:text-gray-400">Nama User / Interviewer</label>
                                    <input type="text"
                                        :name="'detail_interview['+index+'][nama_user]'"
                                        x-model="item.nama_user"
                                        placeholder="Contoh: Bpk. Budi (IT Manager)"
                                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm dark:border-gray-700 dark:text-white">
                                </div>

                                <div>
                                    <label class="block text-sm dark:text-gray-400">Tanggal Interview</label>
                                    <div class="relative">
                                    <input type="date"
                                        :name="'detail_interview['+index+'][tanggal]'"
                                        x-model="item.tanggal"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" onclick="this.showPicker()" />
                                        <span class="absolute top-1/2 right-3.5 -translate-y-1/2 pointer-events-none">
                                            <svg class="fill-gray-700 dark:fill-gray-400" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.33317 0.0830078C4.74738 0.0830078 5.08317 0.418794 5.08317 0.833008V1.24967H8.9165V0.833008C8.9165 0.418794 9.25229 0.0830078 9.6665 0.0830078C10.0807 0.0830078 10.4165 0.418794 10.4165 0.833008V1.24967L11.3332 1.24967C12.2997 1.24967 13.0832 2.03318 13.0832 2.99967V4.99967V11.6663C13.0832 12.6328 12.2997 13.4163 11.3332 13.4163H2.6665C1.70001 13.4163 0.916504 12.6328 0.916504 11.6663V4.99967V2.99967C0.916504 2.03318 1.70001 1.24967 2.6665 1.24967L3.58317 1.24967V0.833008C3.58317 0.418794 3.91896 0.0830078 4.33317 0.0830078ZM4.33317 2.74967H2.6665C2.52843 2.74967 2.4165 2.8616 2.4165 2.99967V4.24967H11.5832V2.99967C11.5832 2.8616 11.4712 2.74967 11.3332 2.74967H9.6665H4.33317ZM11.5832 5.74967H2.4165V11.6663C2.4165 11.8044 2.52843 11.9163 2.6665 11.9163H11.3332C11.4712 11.9163 11.5832 11.8044 11.5832 11.6663V5.74967Z" fill="" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm dark:text-gray-400">Hasil</label>
                                    <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                                    <select :name="'detail_interview['+index+'][hasil]'"
                                        x-model="item.hasil"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                        :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                                        <option value="">-- Pilih Hasil --</option>
                                        <option value="Lolos">Lolos</option>
                                        <option value="Tidak Lolos">Tidak Lolos</option>
                                        <option value="Pending">Pending</option>
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
                    </template>

                    <button type="button"
                            @click="interviews.push({
                                nama_user: '',
                                tanggal: '',
                                hasil: ''
                            })"
                            class="flex items-center gap-2 rounded-lg border border-brand-300 bg-brand-50 px-4 py-2 text-sm font-medium text-brand-600 hover:bg-brand-100 dark:border-brand-800 dark:bg-brand-900/30">
                        <svg width="16" height="16" fill="none" viewBox="0 0 16 16">
                            <path d="M8 3.333V12.667M3.333 8H12.667" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                        Tambah Tahap Interview User
                    </button>
                </div>

            </div>

            <!-- Catatan -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Catatan
                </label>
                <textarea name="catatan" placeholder="Catatan Tambahan (Pemberkasan/Alasan/Lain-lain...)" rows="3"
                    class="dark:bg-dark-900 shadow-theme-xs w-full rounded-lg
                           border border-gray-300 bg-transparent px-4 py-2.5
                           text-sm text-gray-800 dark:border-gray-700
                           dark:bg-gray-900 dark:text-white/90"></textarea>
            </div>

            <!-- ACTION -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('rekrutmen.kandidat_lanjut_user.index') }}"
                   class="rounded-lg border border-gray-300 px-4 py-2 text-sm
                          text-gray-700 hover:bg-gray-50 dark:border-gray-700
                          dark:text-gray-300 dark:hover:bg-white/[0.05] transition">
                    Batal
                </a>
                <button class="rounded-lg bg-brand-600 px-6 py-2 text-sm text-white">
                    Simpan
                </button>
            </div>

        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('kandidat_select');
    const posisi = document.getElementById('posisi_dilamar');
    const tanggal = document.getElementById('tanggal_interview_hr');

    select.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        posisi.value = opt.dataset.posisi || '';
        tanggal.value = opt.dataset.tanggal || '';
    });
});
</script>
@endpush
