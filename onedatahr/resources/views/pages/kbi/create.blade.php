@extends('layouts.app')

@section('content')

{{-- 
    CSS MANUAL (BACKUP STYLE)
    Ini menjamin tampilan tetap berwarna meskipun Tailwind mati / belum di-build.
--}}
<style>
    /* Tombol Simpan Biru */
    .btn-save {
        background-color: #2563eb !important;
        color: white !important;
        border: none;
    }
    .btn-save:hover { background-color: #1d4ed8 !important; }

    /* Tombol Batal Abu */
    .btn-cancel {
        background-color: #e5e7eb !important;
        color: #374151 !important;
    }
    .btn-cancel:hover { background-color: #d1d5db !important; }

    /* Card Pilihan Ganda (Radio) */
    .radio-card {
        border: 1px solid #e5e7eb;
        background-color: white;
        transition: all 0.2s;
        cursor: pointer;
    }
    .radio-card:hover {
        background-color: #f9fafb;
        border-color: #d1d5db;
    }

    /* Logika Warna Saat Dipilih (Checked) */
    /* Skor 1: Merah */
    input[type="radio"]:checked + .radio-card.skor-1 {
        background-color: #ef4444 !important; /* Red 50 */
        border-color: #ef4444 !important;     /* Red 500 */
        /* color: #b91c1c !important;            Red 700 */
    }
    /* Skor 2: Kuning/Orange */
    input[type="radio"]:checked + .radio-card.skor-2 {
        background-color: #f97316 !important; /* Orange 50 */
        border-color: #f97316 !important;     /* Orange 500 */
        /* color: #c2410c !important;            Orange 700 */
    }
    /* Skor 3: Biru */
    input[type="radio"]:checked + .radio-card.skor-3 {
        background-color: #3b82f6 !important; /* Blue 50 */
        border-color: #3b82f6 !important;     /* Blue 500 */
        /* color: #1d4ed8 !important;            Blue 700 */
    }
    /* Skor 4: Hijau */
    input[type="radio"]:checked + .radio-card.skor-4 {
        background-color: #22c55e !important; /* Green 50 */
        border-color: #22c55e !important;     /* Green 500 */
        /* color: #15803d !important;            Green 700 */
    }
</style>

<div class="p-4 sm:p-6 max-w-5xl mx-auto">
    
    {{-- HEADER: JUDUL & TOMBOL KEMBALI --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                <i class="fas fa-clipboard-check mr-2 text-blue-600"></i>Formulir Penilaian KBI
            </h1>
            <p class="text-gray-500 text-sm">
                Mohon isi penilaian kinerja perilaku di bawah ini secara objektif.
            </p>
        </div>
        <a href="{{ route('kbi.index') }}" class="btn-cancel px-4 py-2 rounded-lg text-sm font-medium transition inline-flex items-center justify-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- KARTU INFO KARYAWAN (TARGET) --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-blue-100 mb-8 relative overflow-hidden">
        {{-- Hiasan background --}}
        <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-full opacity-50"></div>

        <div class="flex items-start gap-5 relative z-10">
            {{-- Avatar --}}
            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-bold text-xl shadow-md border-2 border-white">
                {{ substr($targetKaryawan->Nama_Lengkap_Sesuai_Ijazah ?? 'X', 0, 1) }}
            </div>
            
            {{-- Detail Info --}}
            <div class="flex-1">
                <h2 class="font-bold text-xl text-gray-800 mb-1">{{ $targetKaryawan->Nama_Lengkap_Sesuai_Ijazah }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm text-gray-600">
                    <div class="flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded border border-gray-100">
                        <i class="fas fa-id-card text-blue-400"></i> 
                        <span>NIK: <strong>{{ $targetKaryawan->NIK }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded border border-gray-100">
                        <i class="fas fa-briefcase text-blue-400"></i> 
                        <span>Jabatan: <strong>{{ $targetKaryawan->pekerjaan->first()?->position?->name ?? '-' }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded border border-gray-100">
                        <i class="fas fa-user-tag text-blue-400"></i> 
                        <span>Sebagai: <strong>{{ str_replace('_', ' ', $tipe) }}</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FORM UTAMA --}}
    <form action="{{ route('kbi.store') }}" method="POST">
        @csrf
        {{-- INPUT HIDDEN PENTING --}}
        <input type="hidden" name="karyawan_id" value="{{ $targetKaryawan->id_karyawan }}">
        <input type="hidden" name="tipe_penilai" value="{{ $tipe }}">

        <div class="space-y-8">
            {{-- LOOPING KATEGORI (Komunikatif, Unggul, dll) --}}
            @foreach($daftarSoal as $kategori)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-2">
                
                {{-- HEADER KATEGORI --}}
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center gap-3">
                    <div class="w-1 h-6 bg-blue-600 rounded-full"></div>
                    <h3 class="font-bold text-lg text-gray-800 tracking-wide">{{ $kategori['kategori'] }}</h3>
                </div>

                {{-- LOOPING SOAL --}}
                <div class="divide-y divide-gray-100">
                    @foreach($kategori['soal'] as $idSoal => $pertanyaan)
                    <div class="p-6 hover:bg-gray-50/50 transition duration-150">
                        {{-- Teks Soal --}}
                        <div class="mb-4">
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs font-bold px-2 py-0.5 rounded mb-1">
                                Soal #{{ $loop->parent->iteration }}-{{ $loop->iteration }}
                            </span>
                            <p class="text-gray-800 font-medium text-base leading-relaxed">
                                {{ $pertanyaan }}
                            </p>
                        </div>
                        
                        {{-- Pilihan Ganda (1-4) --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            
                            {{-- OPSI 1: KURANG --}}
                            <label class="relative w-full">
                                <input type="radio" name="skor[{{ $idSoal }}]" value="1" class="peer sr-only" required>
                                <div class="radio-card skor-1 p-3 text-center rounded-lg h-full flex flex-col justify-center items-center">
                                    <div class="text-2xl font-bold mb-1">1</div>
                                    <div class="text-xs font-medium uppercase tracking-wider">Kurang</div>
                                </div>
                            </label>

                            {{-- OPSI 2: CUKUP --}}
                            <label class="relative w-full">
                                <input type="radio" name="skor[{{ $idSoal }}]" value="2" class="peer sr-only">
                                <div class="radio-card skor-2 p-3 text-center rounded-lg h-full flex flex-col justify-center items-center">
                                    <div class="text-2xl font-bold mb-1">2</div>
                                    <div class="text-xs font-medium uppercase tracking-wider">Cukup</div>
                                </div>
                            </label>

                            {{-- OPSI 3: BAIK --}}
                            <label class="relative w-full">
                                <input type="radio" name="skor[{{ $idSoal }}]" value="3" class="peer sr-only">
                                <div class="radio-card skor-3 p-3 text-center rounded-lg h-full flex flex-col justify-center items-center">
                                    <div class="text-2xl font-bold mb-1">3</div>
                                    <div class="text-xs font-medium uppercase tracking-wider">Baik</div>
                                </div>
                            </label>

                            {{-- OPSI 4: SANGAT BAIK --}}
                            <label class="relative w-full">
                                <input type="radio" name="skor[{{ $idSoal }}]" value="4" class="peer sr-only">
                                <div class="radio-card skor-4 p-3 text-center rounded-lg h-full flex flex-col justify-center items-center">
                                    <div class="text-2xl font-bold mb-1">4</div>
                                    <div class="text-xs font-medium uppercase tracking-wider">Sangat Baik</div>
                                </div>
                            </label>

                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        {{-- FOOTER TOMBOL SIMPAN --}}
        <div class="mt-8 mb-12 flex justify-end sticky bottom-4 z-20">
            <div class="rounded-xl shadow-lg inline-block">
                <button type="submit" class="btn-save py-3 px-8 rounded-lg font-bold text-base shadow-md transform hover:-translate-y-0.5 transition duration-200 flex items-center gap-2">
                    <i class="fas fa-save"></i> Simpan Hasil Penilaian
                </button>
            </div>
        </div>
    </form>
</div>
@endsection