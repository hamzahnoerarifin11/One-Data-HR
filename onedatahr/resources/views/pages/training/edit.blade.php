@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Edit Data Training
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Perbarui jadwal dan hasil evaluasi training untuk kandidat.
            </p>
        </div>

        <a href="{{ route('training.index') }}"
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

    @if ($errors->any())
    <div class="mb-4 rounded-lg bg-red-100 p-4 text-sm text-red-700 dark:bg-red-200 dark:text-red-800">
        <strong>Error!</strong> Mohon periksa kembali inputan Anda:
        <ul class="mt-2 list-inside list-disc">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-lg
                dark:border-gray-800 dark:bg-white/[0.03]">

        <form method="POST" action="{{ route('training.update', $training->id_training) }}">
            @csrf
            @method('PUT')

            <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">
                Informasi Kandidat & Posisi
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Nama Kandidat
                    </label>
                    <div x-data="{ isOptionSelected: true }" class="relative z-20 bg-transparent">
                        <select id="kandidat_select" name="kandidat_id" required
                           class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                           :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                            <option value="">-- Pilih Kandidat --</option>
                            @foreach($kandidat as $item)
                                <option value="{{ $item->id_kandidat }}"
                                        data-posisi-id="{{ $item->posisi_id }}"
                                        data-posisi-nama="{{ $item->posisi->nama_posisi }}"
                                        {{ $training->kandidat_id == $item->id_kandidat ? 'selected' : '' }}>
                                    {{ $item->nama }}
                                </option>
                            @endforeach
                        </select>
                        <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Posisi
                    </label>
                    <input type="hidden" name="posisi_id" id="posisi_id" value="{{ $training->posisi_id }}">
                    <input id="posisi_nama" placeholder="Otomatis Terisi" readonly
                        value="{{ $training->posisi->nama_posisi ?? '' }}"
                        class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg
                               border border-gray-300 bg-gray-50 px-4 py-2.5
                               text-sm text-gray-800 dark:border-gray-700
                               dark:bg-gray-900 dark:text-white/90 cursor-not-allowed">
                </div>
            </div>

            <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">
                Timeline & Hasil Training
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Tanggal Mulai</label>
                    <div class="relative">
                        <input type="date" name="tanggal_mulai"
                               value="{{ $training->tanggal_mulai }}"
                               class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" onclick="this.showPicker()" />
                     <span class="absolute top-1/2 right-3.5 -translate-y-1/2 pointer-events-none">
                                <svg class="fill-gray-700 dark:fill-gray-400" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.33317 0.0830078C4.74738 0.0830078 5.08317 0.418794 5.08317 0.833008V1.24967H8.9165V0.833008C8.9165 0.418794 9.25229 0.0830078 9.6665 0.0830078C10.0807 0.0830078 10.4165 0.418794 10.4165 0.833008V1.24967L11.3332 1.24967C12.2997 1.24967 13.0832 2.03318 13.0832 2.99967V4.99967V11.6663C13.0832 12.6328 12.2997 13.4163 11.3332 13.4163H2.6665C1.70001 13.4163 0.916504 12.6328 0.916504 11.6663V4.99967V2.99967C0.916504 2.03318 1.70001 1.24967 2.6665 1.24967L3.58317 1.24967V0.833008C3.58317 0.418794 3.91896 0.0830078 4.33317 0.0830078ZM4.33317 2.74967H2.6665C2.52843 2.74967 2.4165 2.8616 2.4165 2.99967V4.24967H11.5832V2.99967C11.5832 2.8616 11.4712 2.74967 11.3332 2.74967H9.6665H4.33317ZM11.5832 5.74967H2.4165V11.6663C2.4165 11.8044 2.52843 11.9163 2.6665 11.9163H11.3332C11.4712 11.9163 11.5832 11.8044 11.5832 11.6663V5.74967Z" fill="" />
                                </svg>
                            </span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Tanggal Selesai</label>
                    <div class="relative">
                        <input type="date" name="tanggal_selesai"
                               value="{{ $training->tanggal_selesai }}"
                               class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" onclick="this.showPicker()" />
                    <span class="absolute top-1/2 right-3.5 -translate-y-1/2 pointer-events-none">
                                <svg class="fill-gray-700 dark:fill-gray-400" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.33317 0.0830078C4.74738 0.0830078 5.08317 0.418794 5.08317 0.833008V1.24967H8.9165V0.833008C8.9165 0.418794 9.25229 0.0830078 9.6665 0.0830078C10.0807 0.0830078 10.4165 0.418794 10.4165 0.833008V1.24967L11.3332 1.24967C12.2997 1.24967 13.0832 2.03318 13.0832 2.99967V4.99967V11.6663C13.0832 12.6328 12.2997 13.4163 11.3332 13.4163H2.6665C1.70001 13.4163 0.916504 12.6328 0.916504 11.6663V4.99967V2.99967C0.916504 2.03318 1.70001 1.24967 2.6665 1.24967L3.58317 1.24967V0.833008C3.58317 0.418794 3.91896 0.0830078 4.33317 0.0830078ZM4.33317 2.74967H2.6665C2.52843 2.74967 2.4165 2.8616 2.4165 2.99967V4.24967H11.5832V2.99967C11.5832 2.8616 11.4712 2.74967 11.3332 2.74967H9.6665H4.33317ZM11.5832 5.74967H2.4165V11.6663C2.4165 11.8044 2.52843 11.9163 2.6665 11.9163H11.3332C11.4712 11.9163 11.5832 11.8044 11.5832 11.6663V5.74967Z" fill="" />
                                </svg>
                            </span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-brand-600 dark:text-brand-400 font-bold">Jadwal TTD Kontrak</label>
                    <div class="relative">
                        <input type="date" name="jadwal_ttd_kontrak"
                               value="{{ $training->jadwal_ttd_kontrak }}"
                               class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" onclick="this.showPicker()" />
                     <span class="absolute top-1/2 right-3.5 -translate-y-1/2 pointer-events-none">
                                <svg class="fill-gray-700 dark:fill-gray-400" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.33317 0.0830078C4.74738 0.0830078 5.08317 0.418794 5.08317 0.833008V1.24967H8.9165V0.833008C8.9165 0.418794 9.25229 0.0830078 9.6665 0.0830078C10.0807 0.0830078 10.4165 0.418794 10.4165 0.833008V1.24967L11.3332 1.24967C12.2997 1.24967 13.0832 2.03318 13.0832 2.99967V4.99967V11.6663C13.0832 12.6328 12.2997 13.4163 11.3332 13.4163H2.6665C1.70001 13.4163 0.916504 12.6328 0.916504 11.6663V4.99967V2.99967C0.916504 2.03318 1.70001 1.24967 2.6665 1.24967L3.58317 1.24967V0.833008C3.58317 0.418794 3.91896 0.0830078 4.33317 0.0830078ZM4.33317 2.74967H2.6665C2.52843 2.74967 2.4165 2.8616 2.4165 2.99967V4.24967H11.5832V2.99967C11.5832 2.8616 11.4712 2.74967 11.3332 2.74967H9.6665H4.33317ZM11.5832 5.74967H2.4165V11.6663C2.4165 11.8044 2.52843 11.9163 2.6665 11.9163H11.3332C11.4712 11.9163 11.5832 11.8044 11.5832 11.6663V5.74967Z" fill="" />
                                </svg>
                            </span>
                    </div>
                </div>

                <div class="col-span-1 md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                        Hasil Evaluasi
                    </label>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="hasil_evaluasi" value="LULUS TRAINING"
                                   {{ $training->hasil_evaluasi == 'LULUS TRAINING' ? 'checked' : '' }}
                                   class="w-4 h-4 text-brand-600">
                            <span class="text-sm text-gray-800 dark:text-gray-300">LULUS TRAINING</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="hasil_evaluasi" value="TIDAK LULUS TRAINING"
                                   {{ $training->hasil_evaluasi == 'TIDAK LULUS TRAINING' ? 'checked' : '' }}
                                   class="w-4 h-4 text-brand-600">
                            <span class="text-sm text-gray-800 dark:text-gray-300">TIDAK LULUS TRAINING</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="hasil_evaluasi" value="MENGUNDURKAN DIRI"
                                   {{ $training->hasil_evaluasi == 'MENGUNDURKAN DIRI' ? 'checked' : '' }}
                                   class="w-4 h-4 text-brand-600">
                            <span class="text-sm text-gray-800 dark:text-gray-300">MENGUNDURKAN DIRI</span>
                        </label>
                    </div>
                </div>

                <div class="col-span-1 md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Keterangan Tambahan
                    </label>
                    <textarea name="keterangan_tambahan" rows="3"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                        placeholder="Catatan selama masa training...">{{ old('keterangan_tambahan', $training->keterangan_tambahan) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-800">
                <a href="{{ route('training.index') }}"
                   class="rounded-lg border border-gray-300 px-4 py-2 text-sm
                          text-gray-700 hover:bg-gray-50 dark:border-gray-700
                          dark:text-gray-300 dark:hover:bg-white/[0.05] transition">
                    Batal
                </a>
                <button type="submit" class="rounded-lg bg-brand-600 px-6 py-2 text-sm text-white hover:bg-brand-700 transition font-medium shadow-md">
                    Update Data Training
                </button>
            </div>

        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectKandidat = document.getElementById('kandidat_select');
    const inputPosisiId = document.getElementById('posisi_id');
    const inputPosisiNama = document.getElementById('posisi_nama');

    selectKandidat.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];

        // Mengambil data-posisi dari option
        const posisiId = selectedOption.dataset.posisiId || '';
        const posisiNama = selectedOption.dataset.posisiNama || '';

        // Mengisi value input
        inputPosisiId.value = posisiId;
        inputPosisiNama.value = posisiNama;
    });
});
</script>
@endpush
