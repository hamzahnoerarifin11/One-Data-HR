@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Detail Kelompok TEMPA</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Informasi lengkap kelompok TEMPA</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('tempa.kelompok.edit', $kelompok->id_kelompok) }}" class="inline-flex items-center gap-2 rounded-lg bg-yellow-500 px-5 py-2.5 text-center text-white font-medium hover:bg-yellow-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </a>
            <button type="button" aria-label="Hapus Kelompok" data-modal-id="delete-confirm" data-modal-target="deleteForm" data-modal-title="Hapus Kelompok" data-modal-message="{{ e('Yakin ingin menghapus kelompok: ' . $kelompok->nama_kelompok . '?') }}" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-5 py-2.5 text-center text-white font-medium hover:bg-red-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Hapus
            </button>
            <a href="{{ route('tempa.kelompok.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.05] transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Informasi Kelompok -->
    <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            Informasi Kelompok
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nama Kelompok</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $kelompok->nama_kelompok ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nama Mentor</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $kelompok->nama_mentor ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Ketua TEMPA</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $kelompok->ketuaTempa->name ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Lokasi</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">
                    @if($kelompok->tempat === 'pusat')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                            Pusat
                        </span>
                    @elseif($kelompok->tempat === 'cabang')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                            Cabang{{ $kelompok->keterangan_cabang ? ' - ' . $kelompok->keterangan_cabang : '' }}
                        </span>
                    @else
                        -
                    @endif
                </p>
            </div>

            <!-- <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">TEMPA</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $kelompok->tempa->nama_tempa ?? '-' }}</p>
            </div> -->

            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Jumlah Peserta</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $kelompok->pesertas->count() }} orang</p>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 flex justify-between items-center text-xs text-gray-500">
            <p>Dibuat pada: {{ $kelompok->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</p>
            <p>Terakhir diperbarui: {{ $kelompok->updated_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- Daftar Peserta -->
    <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            Daftar Peserta ({{ $kelompok->pesertas->count() }} orang)
        </h2>

        @if($kelompok->pesertas->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama Peserta</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">NIK</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($kelompok->pesertas as $index => $peserta)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $index + 1 }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $peserta->nama_peserta }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $peserta->nik_karyawan }}</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($peserta->status_peserta == 1)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                        Aktif
                                    </span>
                                @elseif($peserta->status_peserta == 2)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                        Pindah
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                        Keluar
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('tempa.peserta.show', $peserta->id_peserta) }}"
                                    class="inline-flex items-center justify-center rounded-lg bg-blue-50 p-2 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/40 transition" title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Belum ada peserta</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelompok ini belum memiliki peserta yang terdaftar.</p>
                <div class="mt-6">
                    <a href="{{ route('tempa.peserta.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Tambah Peserta
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteForm" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
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
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Hapus Kelompok</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" id="modal-message">Yakin ingin menghapus kelompok ini?</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <form action="{{ route('tempa.kelompok.destroy', $kelompok->id_kelompok) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Hapus
                    </button>
                </form>
                <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Modal functionality
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function closeModal() {
    const modals = document.querySelectorAll('[id$="Form"]');
    modals.forEach(modal => modal.classList.add('hidden'));
}

// Event listeners for modal triggers
document.addEventListener('DOMContentLoaded', function() {
    const modalTriggers = document.querySelectorAll('[data-modal-target]');
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal-target');
            const modalTitle = this.getAttribute('data-modal-title');
            const modalMessage = this.getAttribute('data-modal-message');

            if (modalTitle) {
                document.getElementById('modal-title').textContent = modalTitle;
            }
            if (modalMessage) {
                document.getElementById('modal-message').textContent = modalMessage;
            }

            openModal(modalId);
        });
    });
});
</script>
@endsection
