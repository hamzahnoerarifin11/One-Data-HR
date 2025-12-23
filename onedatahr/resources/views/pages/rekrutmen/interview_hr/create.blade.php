@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Tambah Interview HR
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">
                Form penilaian hasil wawancara kandidat secara sistematis
            </p>
        </div>

        <a href="{{ route('rekrutmen.interview_hr.index') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium
                  text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.05] transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>

    <form action="{{ route('rekrutmen.interview_hr.store') }}" method="POST">
        @csrf

        <div class="space-y-8">

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center gap-2 mb-6 border-b border-gray-100 pb-4 dark:border-gray-800">
                    <span class="text-xl">🧑‍💼</span>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Identitas Interview</h3>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Kandidat</label>
                        <select name="kandidat_id" id="kandidat_select" class="form-input w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500" required>
                            <option value="" disabled selected>-- Pilih Kandidat --</option>
                            @foreach($kandidat as $k)
                                {{-- Kita simpan data posisi di attribute data-posisi --}}
                                <option value="{{ $k->id_kandidat }}" data-posisi="{{ $k->posisi_dilamar }}">
                                    {{ $k->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Posisi Dilamar</label>
                        <input type="text" name="posisi_dilamar" id="posisi_dilamar" 
                               class="form-input w-full rounded-lg bg-gray-50 border-gray-300 dark:bg-gray-800/50" 
                               placeholder="Otomatis terisi..." readonly>
                        <p class="mt-1 text-xs text-gray-500 italic">*Terisi otomatis berdasarkan data kandidat</p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Interview</label>
                        <input type="date" name="hari_tanggal" class="form-input w-full rounded-lg border-gray-300" required value="{{ date('Y-m-d') }}">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Interviewer</label>
                        <input name="nama_interviewer" class="form-input w-full rounded-lg border-gray-300" placeholder="Nama HR / User" required>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Model Wawancara</label>
                        <select name="model_wawancara" class="form-input w-full rounded-lg border-gray-300">
                            <option value="Online">Online (Zoom/Meet)</option>
                            <option value="Offline">Offline (Tatap Muka)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center gap-2 mb-6 border-b border-gray-100 pb-4 dark:border-gray-800">
                    <span class="text-xl">📊</span>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Aspek Penilaian</h3>
                </div>

                @php
                $aspek = [
                    'profesional' => ['label' => 'Profesionalisme', 'desc' => 'Penampilan, ketepatan waktu, sikap'],
                    'spiritual'   => ['label' => 'Spiritualitas', 'desc' => 'Kesesuaian nilai & karakter'],
                    'learning'    => ['label' => 'Learning Agility', 'desc' => 'Kemauan belajar & adaptasi'],
                    'initiative'  => ['label' => 'Initiative', 'desc' => 'Inisiatif & kemandirian'],
                    'komunikasi'  => ['label' => 'Komunikasi', 'desc' => 'Cara bicara & penyampaian ide'],
                    'problem_solving' => ['label' => 'Problem Solving', 'desc' => 'Logika & pemecahan masalah'],
                    'teamwork'    => ['label' => 'Teamwork', 'desc' => 'Kerjasama & kolaborasi']
                ];
                @endphp

                <div class="space-y-4">
                    @foreach($aspek as $key => $data)
                    <div class="group rounded-xl border border-gray-100 p-4 transition-all hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/[0.02]">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-12 items-center">
                            <div class="md:col-span-4">
                                <span class="block font-bold text-gray-800 dark:text-white">{{ $data['label'] }}</span>
                                <span class="text-xs text-gray-500">{{ $data['desc'] }}</span>
                            </div>

                            <div class="md:col-span-2">
                                <label class="text-[10px] uppercase text-gray-400 font-bold mb-1 block md:hidden">Skor (1-5)</label>
                                <select name="skor_{{ $key }}" class="form-input skor-interview w-full rounded-lg border-gray-300">
                                    @for($i=1;$i<=5;$i++)
                                        <option value="{{ $i }}" {{ $i==3 ? 'selected':'' }}>{{ $i }} {{ $i == 5 ? '(Perfect)' : '' }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="md:col-span-6">
                                <textarea name="catatan_{{ $key }}" rows="1"
                                          class="form-input w-full rounded-lg border-gray-300 focus:h-20 transition-all"
                                          placeholder="Tambahkan catatan khusus untuk aspek {{ $data['label'] }}..."></textarea>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                
                <div class="lg:col-span-1 rounded-xl border border-brand-100 bg-brand-50/30 p-6 dark:border-brand-900/20 dark:bg-brand-900/10">
                    <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-brand-600">📈 Summary Score</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-brand-700">Total Skor</label>
                            <input type="text" id="total_skor" name="total_skor" class="w-full border-none bg-transparent text-2xl font-bold p-0 focus:ring-0" readonly>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-brand-700">Rata-rata</label>
                            <input type="text" id="rata_rata" name="rata_rata" class="w-full border-none bg-transparent text-2xl font-bold p-0 focus:ring-0" readonly>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-brand-700">Kategori</label>
                            <input type="text" id="kategori_nilai" class="w-full border-none bg-transparent text-xl font-semibold p-0 focus:ring-0" readonly>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">✅ Keputusan Akhir</h3>
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Status Keputusan</label>
                            <select name="keputusan" class="form-input w-full rounded-lg border-gray-300">
                                <option value="DITERIMA" class="text-green-600 font-bold">DITERIMA</option>
                                <option value="DITOLAK" class="text-red-600 font-bold">DITOLAK</option>
                                <option value="MENGUNDURKAN DIRI" class="text-gray-600">MENGUNDURKAN DIRI</option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Hasil Akhir (Status Proses)</label>
                            <input name="hasil_akhir" class="form-input w-full rounded-lg border-gray-300" placeholder="Contoh: Lolos ke Tahap User">
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-700">Catatan Kesimpulan</label>
                            <textarea name="catatan_tambahan" rows="3" class="form-input w-full rounded-lg border-gray-300"
                                      placeholder="Berikan kesimpulan akhir mengapa kandidat ini diterima/ditolak..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
                <p class="text-xs text-gray-500 italic">Pastikan seluruh aspek penilaian telah terisi dengan objektif.</p>
                <div class="flex gap-3">
                    <a href="{{ route('rekrutmen.interview_hr.index') }}"
                       class="rounded-lg border border-gray-300 px-8 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-all">
                        Batal
                    </a>
                    <button type="submit"
                            class="rounded-lg bg-brand-600 px-8 py-2.5 text-sm font-medium text-white shadow-lg shadow-brand-200 hover:bg-brand-700 transition-all">
                        💾 Simpan Hasil Interview
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // FUNGSI AUTO-FILL POSISI
    const kandidatSelect = document.getElementById('kandidat_select');
    const posisiInput = document.getElementById('posisi_dilamar');

    kandidatSelect.addEventListener('change', function() {
        // Ambil atribut data-posisi dari option yang dipilih
        const selectedOption = this.options[this.selectedIndex];
        const posisi = selectedOption.getAttribute('data-posisi');
        
        // Isi input posisi
        posisiInput.value = posisi || '';
        
        // Tambahkan efek visual sedikit agar user sadar ada yang berubah
        posisiInput.classList.add('bg-blue-50');
        setTimeout(() => posisiInput.classList.remove('bg-blue-50'), 500);
    });

    // FUNGSI HITUNG NILAI
    function hitungNilaiInterview() {
        let total = 0;
        let jumlah = 0;

        document.querySelectorAll('.skor-interview').forEach(function(el) {
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
        } else {
            kategori = 'Kurang';
            colorClass = 'text-red-600';
        }

        const katInput = document.getElementById('kategori_nilai');
        katInput.value = kategori;
        katInput.className = `w-full border-none bg-transparent text-xl font-bold p-0 focus:ring-0 ${colorClass}`;
    }

    // Event listeners
    document.querySelectorAll('.skor-interview').forEach(el => {
        el.addEventListener('change', hitungNilaiInterview);
    });

    document.addEventListener('DOMContentLoaded', hitungNilaiInterview);
</script>
@endpush