<div id="importModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
    <div onclick="closeImportModal()" class="absolute inset-0 bg-black/50 dark:bg-black/80"></div>
    
    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-md w-full mx-4 p-6">
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Import Data Karyawan</h2>
            <button type="button" onclick="closeImportModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form action="{{ route('karyawan.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                <p class="text-sm text-blue-800 dark:text-blue-300 mb-2">
                    <strong>Petunjuk:</strong>
                </p>
                <ul class="list-disc list-inside text-sm text-blue-700 dark:text-blue-400 space-y-1">
                    <li>Unduh template terlebih dahulu.</li>
                    <li>Isi data sesuai format template.</li>
                    <li>Pastikan NIK unik dan belum terdaftar.</li>
                    <li>Kolom dengan tanda * wajib diisi.</li>
                </ul>
                <div class="mt-3">
                    <a href="{{ route('karyawan.import-template') }}" class="inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download Template Excel
                    </a>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Upload File (Excel/CSV)
                </label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                    class="block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold
                    file:bg-blue-50 file:text-blue-700
                    hover:file:bg-blue-100
                    dark:file:bg-blue-900/30 dark:file:text-blue-400 text-gray-900 dark:text-gray-300">
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeImportModal()" class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    Batal
                </button>
                <button type="submit" class="flex-1 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Import
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }
</script>
