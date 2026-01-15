@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Input Absensi TEMPA</h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Silakan pilih peserta untuk melakukan input kehadiran.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 flex w-full border-l-6 border-[#28a745] bg-[#28a745]/[0.1] px-7 py-4 shadow-md dark:bg-[#1b1b24]">
        <div class="mr-5 flex h-9 w-full max-w-[36px] items-center justify-center rounded-lg bg-[#28a745]">
            <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15.2984 0.826822L15.2859 0.814322C15.1109 0.639322 14.8234 0.639322 14.6484 0.814322L5.19721 10.2656L1.35147 6.41982C1.17647 6.24482 0.888972 6.24482 0.713972 6.41982L0.701472 6.43232C0.526472 6.60732 0.526472 6.89482 0.701472 7.06982L5.19721 11.5656L15.2984 1.47807C15.4734 1.30307 15.4734 1.01557 15.2984 0.840572V0.826822Z" fill="white" />
            </svg>
        </div>
        <div class="w-full">
            <h5 class="text-lg font-semibold text-[#28a745]">Berhasil</h5>
            <p class="text-base text-[#28a745]">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white shadow-default dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-50 text-left dark:bg-gray-900">
                        <th class="px-6 py-4 font-medium text-gray-900 dark:text-white">Nama Peserta</th>
                        <th class="px-6 py-4 font-medium text-gray-900 dark:text-white">Kelompok</th>
                        <th class="px-6 py-4 font-medium text-gray-900 dark:text-white text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesertas as $peserta)
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <td class="px-6 py-4">
                            <p class="text-gray-900 dark:text-white font-medium">{{ $peserta->nama_peserta }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-gray-600 dark:text-gray-400">{{ $peserta->kelompok->nama_kelompok ?? 'Tanpa Kelompok' }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="openModal('{{ $peserta->id }}', '{{ $peserta->nama_peserta }}')"
                                class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-center font-medium text-white hover:bg-opacity-90">
                                Input Absensi
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-10 text-center text-gray-500">Tidak ada peserta yang terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalAbsensi" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50">
    <div class="flex min-h-screen items-center justify-center p-4 text-center">
        <div class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all dark:bg-gray-900">
            <h3 class="text-xl font-bold leading-6 text-gray-900 dark:text-white mb-4">
                Absensi: <span id="modal_nama_peserta"></span>
            </h3>

            <form action="{{ route('tempa.absensi.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id_peserta" id="modal_id_peserta">

                <div class="mb-4 text-gray-900 dark:text-white">
                    <label class="mb-2.5 block font-medium">Bulan</label>
                    <select name="bulan" required class="w-full rounded border-[1.5px] border-gray-200 bg-transparent py-3 px-5 outline-none transition focus:border-blue-600 dark:border-gray-700 dark:bg-gray-800">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4 text-gray-900 dark:text-white">
                    <label class="mb-2.5 block font-medium">Pertemuan Ke-</label>
                    <select name="pertemuan_ke" required class="w-full rounded border-[1.5px] border-gray-200 bg-transparent py-3 px-5 outline-none transition focus:border-blue-600 dark:border-gray-700 dark:bg-gray-800">
                        <option value="1">Pertemuan 1</option>
                        <option value="2">Pertemuan 2</option>
                        <option value="3">Pertemuan 3</option>
                        <option value="4">Pertemuan 4</option>
                        <option value="5">Pertemuan 5 (Opsional)</option>
                    </select>
                </div>

                <div class="mb-4 text-gray-900 dark:text-white">
                    <label class="mb-2.5 block font-medium">Status Kehadiran</label>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="status_hadir" value="1" checked class="mr-2"> Hadir
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="status_hadir" value="0" class="mr-2"> Tidak Hadir
                        </label>
                    </div>
                </div>

                <div class="mb-6 text-gray-900 dark:text-white">
                    <label class="mb-2.5 block font-medium">Foto Bukti</label>
                    <input type="file" name="foto" required accept="image/*" class="w-full rounded border-[1.5px] border-gray-200 bg-transparent py-3 px-5 outline-none transition focus:border-blue-600 dark:border-gray-700 dark:bg-gray-800">
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeModal()" class="flex-1 rounded border border-gray-200 py-3 text-center font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 rounded bg-blue-600 py-3 text-center font-medium text-white hover:bg-opacity-90">
                        Simpan Absen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal(id, nama) {
        document.getElementById('modal_id_peserta').value = id;
        document.getElementById('modal_nama_peserta').innerText = nama;
        document.getElementById('modalAbsensi').classList.remove('hidden');
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'modalAbsensi' } }));
    }

    function closeModal() {
        document.getElementById('modalAbsensi').classList.add('hidden');
    }
</script>
@endsection
