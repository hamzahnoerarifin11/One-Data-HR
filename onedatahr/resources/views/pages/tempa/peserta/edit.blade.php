@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Peserta TEMPA</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Perbarui informasi Peserta TEMPA
            </p>
        </div>
        <a href="{{ route('tempa.peserta.index') }}"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.05] transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali
        </a>
    </div>

    <form action="{{ route('tempa.peserta.update', ['peserta' => $peserta->id_peserta]) }}" method="POST" class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Nama Peserta</label>
                <input type="text" name="nama_peserta" value="{{ old('nama_peserta', $peserta->nama_peserta) }}" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="Contoh: Aji Santosa" required>
                @error('nama_peserta') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">NIK Karyawan</label>
                <input type="text" name="nik_karyawan" value="{{ old('nik_karyawan', $peserta->nik_karyawan) }}" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="Contoh: 12345678" required>
                @error('nik_karyawan') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Status Peserta</label>
                <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                <select name="status_peserta" id="status_peserta" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                           :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true" required>
                    <option value="1" {{ old('status_peserta', $peserta->status_peserta) == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="2" {{ old('status_peserta', $peserta->status_peserta) == '2' ? 'selected' : '' }}>Pindah</option>
                    <option value="0" {{ old('status_peserta', $peserta->status_peserta) == '0' ? 'selected' : '' }}>Keluar</option>
                </select>
                <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </div>
                @error('status_peserta') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            <div id="keterangan_pindah_field_edit" style="display: {{ old('status_peserta', $peserta->status_peserta) == '2' ? 'block' : 'none' }};">
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Keterangan Pindah</label>
                <textarea name="keterangan_pindah" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" rows="3">{{ old('keterangan_pindah', $peserta->keterangan_pindah) }}</textarea>
                @error('keterangan_pindah') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            @if($isKetuaTempa)
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Kelompok <span class="text-red-500">*</span></label>
                <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                <select name="kelompok_id" id="kelompok_id" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                           :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true; updateMentor()" required>
                    <option value="">Pilih Kelompok</option>
                    @foreach($kelompoks as $kelompok)
                    <option value="{{ $kelompok->id_kelompok }}" data-mentor="{{ $kelompok->nama_mentor }}" {{ old('kelompok_id', $peserta->id_kelompok) == $kelompok->id_kelompok ? 'selected' : '' }}>
                        {{ $kelompok->nama_kelompok }}
                    </option>
                    @endforeach
                </select>
                <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                </div>
                @error('kelompok_id') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Nama Mentor</label>
                <input type="text" id="nama_mentor" name="nama_mentor" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" value="{{ old('nama_mentor', $peserta->kelompok->nama_mentor ?? '') }}" readonly placeholder="Otomatis terisi dari kelompok">
            </div>
            @else
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Kelompok <span class="text-red-500">*</span></label>
                <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                <select name="kelompok_id" id="kelompok_id" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                           :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true" required>
                    <option value="">Pilih Kelompok</option>
                    @foreach($kelompoks as $kelompok)
                    <option value="{{ $kelompok->id_kelompok }}" data-mentor="{{ $kelompok->nama_mentor }}" data-ketua="{{ $kelompok->ketuaTempa?->name ?? '-' }}" {{ old('kelompok_id', $peserta->id_kelompok) == $kelompok->id_kelompok ? 'selected' : '' }}>
                        {{ $kelompok->nama_kelompok }} - {{ $kelompok->nama_mentor }} ({{ ucfirst($kelompok->tempat) }}{!! $kelompok->tempat == 'cabang' && $kelompok->keterangan_cabang ? ' - ' . $kelompok->keterangan_cabang : '' !!})
                    </option>
                    @endforeach
                </select>
                <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                </div>
                @error('kelompok_id') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Nama Mentor</label>
                <input type="text" id="nama_mentor" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" value="{{ $peserta->kelompok->nama_mentor ?? '' }}" disabled placeholder="Otomatis terisi dari kelompok">
            </div>
            @endif
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('tempa.peserta.index') }}"
                   class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.05] transition">Batal</a>
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700">
                Update
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Define elements for mentor update
        const kelompokSelect = document.getElementById('kelompok_id');
        const mentorInput = document.getElementById('nama_mentor');

        @if($isKetuaTempa)
        function updateMentor() {
            const selectedOption = kelompokSelect.options[kelompokSelect.selectedIndex];
            const mentorName = selectedOption.getAttribute('data-mentor');
            if (mentorName) {
                mentorInput.value = mentorName;
            } else {
                mentorInput.value = '';
            }
        }

        kelompokSelect.addEventListener('change', updateMentor);
        updateMentor(); // Initial update
        @endif

        @if(!$isKetuaTempa)
        if (kelompokSelect) {
            kelompokSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const mentor = selectedOption.getAttribute('data-mentor');
                if (mentorInput && mentor) {
                    mentorInput.value = mentor;
                }
            });
        }
        @endif

        // Trigger conditional display for keterangan_pindah
        const statusPesertaSelect = document.getElementById('status_peserta');
        const keteranganPindahField = document.getElementById('keterangan_pindah_field_edit');

        if (statusPesertaSelect) {
            statusPesertaSelect.addEventListener('change', function() {
                if (this.value === '2') {
                    keteranganPindahField.style.display = 'block';
                } else {
                    keteranganPindahField.style.display = 'none';
                }
            });
        }
    });
</script>
@endsection
