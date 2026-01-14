@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Edit Interview HR
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Perbarui hasil wawancara kandidat
            </p>
        </div>

        <a href="{{ route('rekrutmen.interview_hr.index') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.05] transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali
        </a>
    </div>

    <!-- Error Alert -->
    @if($errors->any())
        <div class="mb-6 flex rounded-lg border border-red-200 bg-red-50 p-4 text-red-800
                    dark:border-red-900 dark:bg-red-900/20 dark:text-red-400">
            <div class="mr-4">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                          d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zM9 4h2v6H9V4zm0 8h2v2H9v-2z"
                          clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="font-medium">Terjadi Kesalahan</h3>
                <ul class="mt-2 list-disc pl-5 text-sm">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Card -->

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-lg
                dark:border-gray-800 dark:bg-white/[0.03]">

        <form action="{{ route('rekrutmen.interview_hr.update', $interview->id_interview_hr) }}" method="POST">
        @csrf
        @method('PUT')

            <!-- ================= IDENTITAS ================= -->
            <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">
                Identitas Interview
            </h3>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 mb-8">

                <!-- Kandidat -->
                 <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Kandidat</label>
                    <div class="relative z-20 bg-transparent">
                    <select id="kandidat_select" name="kandidat_id" required
                           class="dark:bg-dark-900 shadow-theme-xs h-11 w-full appearance-none rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 pr-11 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 cursor-not-allowed" disabled>
                            <option value="{{ $interview->kandidat_id }}">
                                {{ $interview->kandidat->nama }}
                            </option>
                        </select>
                    <input type="hidden" name="kandidat_id"
                        class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 cursor-not-allowed"
                        value="{{ $interview->kandidat_id }}"
                        readonly>
                    </div>
                </div>


                <!-- Posisi -->
                 <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Posisi Dilamar</label>
                    <input type="text"
                        class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 cursor-not-allowed"
                        value="{{ $interview->kandidat?->posisi?->nama_posisi ?? '-' }}"
                        readonly>
                </div>

                <!-- Tanggal -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Tanggal Interview</label>
                    <div class="relative">
                    <input type="date" name="hari_tanggal" value="{{ $interview->hari_tanggal }}"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" onclick="this.showPicker()" />
                            <span class="absolute top-1/2 right-3.5 -translate-y-1/2 pointer-events-none">
                                <svg class="fill-gray-700 dark:fill-gray-400" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.33317 0.0830078C4.74738 0.0830078 5.08317 0.418794 5.08317 0.833008V1.24967H8.9165V0.833008C8.9165 0.418794 9.25229 0.0830078 9.6665 0.0830078C10.0807 0.0830078 10.4165 0.418794 10.4165 0.833008V1.24967L11.3332 1.24967C12.2997 1.24967 13.0832 2.03318 13.0832 2.99967V4.99967V11.6663C13.0832 12.6328 12.2997 13.4163 11.3332 13.4163H2.6665C1.70001 13.4163 0.916504 12.6328 0.916504 11.6663V4.99967V2.99967C0.916504 2.03318 1.70001 1.24967 2.6665 1.24967L3.58317 1.24967V0.833008C3.58317 0.418794 3.91896 0.0830078 4.33317 0.0830078ZM4.33317 2.74967H2.6665C2.52843 2.74967 2.4165 2.8616 2.4165 2.99967V4.24967H11.5832V2.99967C11.5832 2.8616 11.4712 2.74967 11.3332 2.74967H9.6665H4.33317ZM11.5832 5.74967H2.4165V11.6663C2.4165 11.8044 2.52843 11.9163 2.6665 11.9163H11.3332C11.4712 11.9163 11.5832 11.8044 11.5832 11.6663V5.74967Z" fill="" />
                                </svg>
                            </span>
                        </div>
                </div>

                <!-- Interviewer -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Interviewer</label>
                    <input name="nama_interviewer" placeholder="HR" value="{{ $interview->nama_interviewer }}"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                            dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden
                            dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                </div>

                <!-- Model -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Model Wawancara</label>
                    <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                    <select name="model_wawancara"
                       class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                        <!-- <option value="">-- Pilih Model --</option> -->
                        <option value="Online" {{ $interview->model_wawancara=='Online'?'selected':'' }}>Online</option>
                        <option value="Offline" {{ $interview->model_wawancara=='Offline'?'selected':'' }}>Offline</option>
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

           <!-- ================= PENILAIAN ================= -->
            <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">
                📊 Aspek Penilaian
            </h3>

            @php
                $aspek = [
                    'profesional'     => 'Profesional',
                    'spiritual'       => 'Spiritual',
                    'learning'        => 'Learning Ability',
                    'initiative'      => 'Initiative',
                    'komunikasi'      => 'Komunikasi',
                    'problem_solving' => 'Problem Solving',
                    'teamwork'        => 'Teamwork'
                ];
            @endphp

            <div class="space-y-4 mb-8">
                @foreach($aspek as $key => $label)
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">

                        <!-- LABEL -->
                        <div class="md:col-span-4 font-medium text-gray-800 dark:text-white">
                            {{ $label }}
                        </div>

                        <!-- SKOR -->
                        <div class="md:col-span-2">
                            <div x-data="{ isOptionSelected: true }" class="relative z-20 bg-transparent">
                                <select name="skor_{{ $key }}"
                                    class="skor-interview dark:bg-dark-900 shadow-theme-xs focus:border-brand-300
                                    focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none
                                    rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11
                                    text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">

                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}"
                                            {{ (int)($interview->{'skor_'.$key} ?? 0) === $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>

                                <span class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                        <path d="M4.8 7.4L10 12.6L15.2 7.4" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <!-- CATATAN -->
                        <div class="md:col-span-6">
                            <textarea name="catatan_{{ $key }}" placeholder="Catatan"
                                class="dark:bg-dark-900 shadow-theme-xs w-full rounded-lg border border-gray-300 bg-transparent
                                px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ $interview->{'catatan_'.$key} }}</textarea>
                        </div>

                    </div>
                @endforeach
            </div>


            <!-- ================= SUMMARY ================= -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div>
                    <label class="md:col-span-4 font-medium capitalize text-gray-800 dark:text-white">Total Skor</label>
                    <input id="total_skor" readonly
                        class="w-full bg-transparent text-2xl font-bold border-none focus:ring-0 dark:text-white">
                </div>
                <div>
                    <label class="md:col-span-4 font-medium capitalize text-gray-800 dark:text-white">Rata-rata</label>
                    <input id="rata_rata" readonly
                        class="w-full bg-transparent text-2xl font-bold border-none focus:ring-0 dark:text-white">
                </div>
                <div>
                    <label class="md:col-span-4 font-medium capitalize text-gray-800 dark:text-white">Kategori</label>
                    <input id="kategori_nilai" readonly
                        class="w-full bg-transparent text-xl font-semibold border-none focus:ring-0 dark:text-white">
                </div>
            </div>

            <!-- ================= KEPUTUSAN ================= -->
            <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">
                ✅ Keputusan Akhir
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Keputusan</label>
                <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                <select name="keputusan"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                    <!-- <option value="">-- Pilih Keputusan --</option> -->
                    <option value="DITERIMA" {{ $interview->keputusan=='DITERIMA'?'selected':'' }}>DITERIMA</option>
                    <option value="DITOLAK" {{ $interview->keputusan=='DITOLAK'?'selected':'' }}>DITOLAK</option>
                    <option value="MENGUNDURKAN DIRI" {{ $interview->keputusan=='MENGUNDURKAN DIRI'?'selected':'' }}>MENGUNDURKAN DIRI</option>
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
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Hasil Akhir</label>
                <input name="hasil_akhir" placeholder="Status Proses" value="{{ $interview->hasil_akhir }}"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                            dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden
                            dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                </div>
            </div>
            <!-- ================= CATATAN TAMBAHAN ================= -->
            <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Catatan Tambahan</label>
            <textarea name="catatan_tambahan" rows="3" placeholder="Kesimpulan akhir"
                class="dark:bg-dark-900 shadow-theme-xs w-full rounded-lg border border-gray-300 bg-transparent
                            px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 mb-6">{{ $interview->catatan_tambahan }}</textarea>
            </div>
            <!-- ACTION -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('rekrutmen.interview_hr.index') }}"
                   class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.05] transition">Batal</a>
                <button type="submit" class="rounded-lg bg-brand-600 px-6 py-2 text-sm text-white">
                    Update
                </button>
            </div>

        </form>
    </div>
</div>
@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // const kandidatSelect = document.getElementById('kandidat_select');
        // const posisiInput = document.getElementById('posisi_dilamar');

        // 1. AUTO-FILL POSISI BERDASARKAN KANDIDAT
        // kandidatSelect.addEventListener('change', function() {
        //     const selectedOption = this.options[this.selectedIndex];
        //     const namaPosisi = selectedOption.dataset.posisi;

        //     if (namaPosisi) {
        //         posisiInput.value = namaPosisi;
        //     } else {
        //         posisiInput.value = '';
        //     }
        //     // const posisi = selectedOption.getAttribute('data-posisi');
        //     // posisiInput.value = posisi || '';

        //     // // Efek Highlight
        //     // posisiInput.classList.add('bg-blue-50');
        //     // setTimeout(() => posisiInput.classList.remove('bg-blue-50'), 500);
        // });

        // 2. FUNGSI HITUNG NILAI OTOMATIS
        function hitungNilaiInterview() {
            let total = 0;
            let jumlah = 0;
            const skorElements = document.querySelectorAll('.skor-interview');

            skorElements.forEach(function(el) {
                const nilai = parseInt(el.value);
                if (!isNaN(nilai)) {
                    total += nilai;
                    jumlah++;
                }
            });

            const rata = jumlah > 0 ? (total / jumlah).toFixed(2) : 0;

            document.getElementById('total_skor').value = total;
            document.getElementById('rata_rata').value = rata;

            // Visual Kategori & Warna
            let kategori = '-';
            let colorClass = 'text-gray-500';

            if (rata >= 4.5) {
                kategori = 'Sangat Baik';
                colorClass = 'text-green-600';
            } else if (rata >= 3.5) {
                kategori = 'Baik';
                colorClass = 'text-blue-600';
            } else if (rata >= 2.5) {
                kategori = 'Cukup';
                colorClass = 'text-yellow-600';
            } else if (rata > 0) {
                kategori = 'Kurang';
                colorClass = 'text-red-600';
            }

            const katInput = document.getElementById('kategori_nilai');
            katInput.value = kategori;
            katInput.className = `w-full border-none bg-transparent text-xl font-bold p-0 focus:ring-0 ${colorClass}`;
        }

        // Jalankan saat ada perubahan skor
        document.querySelectorAll('.skor-interview').forEach(el => {
            el.addEventListener('change', hitungNilaiInterview);
        });

        // Jalankan saat halaman pertama kali dimuat
        hitungNilaiInterview();
    });
</script>
@endpush
