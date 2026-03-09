@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Detail Peserta TEMPA</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Informasi lengkap peserta TEMPA</p>
        </div>
        <div class="flex items-center gap-2">
            @can('editTempaPeserta')
            <a href="{{ route('tempa.peserta.edit', $pesertaModel->id_peserta) }}" class="inline-flex items-center gap-2 rounded-lg bg-yellow-500 px-5 py-2.5 text-center text-white font-medium hover:bg-yellow-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </a>
            @endcan
            @can('deleteTempaPeserta')
            <button type="button" aria-label="Hapus Peserta" data-modal-id="delete-confirm" data-modal-target="deleteForm" data-modal-title="Hapus Peserta" data-modal-message="{{ e('Yakin ingin menghapus peserta: ' . $pesertaModel->nama_peserta . '?') }}" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-5 py-2.5 text-center text-white font-medium hover:bg-red-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Hapus
            </button>
            @endcan
            <a href="{{ route('tempa.peserta.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.05] transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Informasi Peserta -->
    <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            Informasi Peserta
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nama Peserta</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $pesertaModel->nama_peserta ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">NIK Karyawan</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $pesertaModel->nik_karyawan ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Status Peserta</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">
                    @if($pesertaModel->status_peserta == 1)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                            Aktif
                        </span>
                    @elseif($pesertaModel->status_peserta == 2)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                            Pindah
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                            Keluar
                        </span>
                    @endif
                </p>
            </div>

            @if($pesertaModel->status_peserta == 2 && $pesertaModel->keterangan_pindah)
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Keterangan Pindah</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $pesertaModel->keterangan_pindah }}</p>
            </div>
            @endif

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nama Kelompok</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $pesertaModel->kelompok->nama_kelompok ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nama Mentor</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $pesertaModel->kelompok->nama_mentor ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Ketua TEMPA</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $pesertaModel->kelompok->ketuaTempa->name ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Lokasi Kelompok</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">
                    @if($pesertaModel->kelompok->tempat === 'pusat')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                            Pusat
                        </span>
                    @elseif($pesertaModel->kelompok->tempat === 'cabang')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                            Cabang{{ $pesertaModel->kelompok->keterangan_cabang ? ' - ' . $pesertaModel->kelompok->keterangan_cabang : '' }}
                        </span>
                    @else
                        -
                    @endif
                </p>
            </div>

            <!-- <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">TEMPA</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $pesertaModel->tempa->nama_tempa ?? '-' }}</p>
            </div> -->
        </div>

        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 flex justify-between items-center text-xs text-gray-500">
            <p>Dibuat pada: {{ $pesertaModel->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</p>
            <p>Terakhir diperbarui: {{ $pesertaModel->updated_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- Modal Delete -->
    @can('deleteTempaPeserta')
    <div id="delete-confirm" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Hapus Peserta</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" id="delete-message">Yakin ingin menghapus peserta ini?</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <form id="deleteForm" method="POST" action="{{ route('tempa.peserta.destroy', $pesertaModel->id_peserta) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Hapus
                        </button>
                    </form>
                    <button type="button" data-modal-close="delete-confirm" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endcan
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal functionality
    const modalButtons = document.querySelectorAll('[data-modal-target]');
    const modalCloseButtons = document.querySelectorAll('[data-modal-close]');

    modalButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal-id');
            const modal = document.getElementById(modalId);
            const title = this.getAttribute('data-modal-title');
            const message = this.getAttribute('data-modal-message');

            if (modal) {
                // Update modal content
                const titleElement = modal.querySelector('#modal-title');
                const messageElement = modal.querySelector('#delete-message');

                if (titleElement) titleElement.textContent = title;
                if (messageElement) messageElement.textContent = message;

                modal.classList.remove('hidden');
            }
        });
    });

    modalCloseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal-close');
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
            }
        });
    });

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('fixed')) {
            event.target.classList.add('hidden');
        }
    });
});
</script>
@endsection
