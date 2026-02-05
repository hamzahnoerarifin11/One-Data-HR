@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-[1200px] p-4 md:p-8">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-800 dark:text-white tracking-tight">Buat Template KPI Massal</h1>
        <p class="mt-2 text-gray-500 dark:text-gray-400 text-lg">
            Tetapkan indikator KPI untuk semua karyawan di tahun {{ $tahun }} sekaligus.
        </p>
    </div>

    <form action="{{ route('kpi.bulk-store') }}" method="POST" id="bulk-kpi-form" class="space-y-6">
        @csrf
        <input type="hidden" name="tahun" value="{{ $tahun }}">

        <!-- Template Info Card -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Informasi Template</h2>
            <div>
                <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Nama Template (Opsional)</label>
                <input type="text" name="template_name" 
                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:placeholder-gray-500" 
                    placeholder="Contoh: KPI Standar Tahun 2026">
                <p class="mt-1.5 text-xs text-gray-500">Nama ini membantu Anda mengidentifikasi template di masa depan.</p>
            </div>
        </div>

        <!-- Indicators Card -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white">Daftar Indikator</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tambahkan daftar indikator kinerja utama.</p>
                </div>
                <button type="button" id="add-row" 
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-50 px-4 py-2 text-sm font-semibold text-brand-700 border border-brand-100 hover:bg-brand-100 transition dark:bg-brand-900/20 dark:text-brand-400 dark:border-brand-800 dark:hover:bg-brand-900/40">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tambah Baris
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" id="items-table">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-6 py-4 font-bold text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wider w-12 text-center">#</th>
                            <th class="px-6 py-4 font-bold text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wider min-w-[200px]">Key Result Area (KRA)</th>
                            <th class="px-6 py-4 font-bold text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wider min-w-[200px]">KPI</th>
                            <th class="px-6 py-4 font-bold text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wider">Satuan</th>
                            <th class="px-6 py-4 font-bold text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wider">Polaritas</th>
                            <th class="px-6 py-4 font-bold text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wider w-24">Bobot (%)</th>
                            <th class="px-6 py-4 font-bold text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wider w-24">Target</th>
                            <th class="px-6 py-4 font-bold text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wider min-w-[150px]">Perspektif</th>
                            <th class="px-6 py-4 font-bold text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wider w-16"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                        <tr class="item-row group hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                            <td class="px-6 py-4 text-center text-gray-400 font-medium row-number">1</td>
                            <td class="px-6 py-4">
                                <input type="text" name="items[0][key_result_area]" required
                                    class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 outline-none transition dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900" 
                                    placeholder="Area Kinerja Utama">
                            </td>
                            <td class="px-6 py-4">
                                <input type="text" name="items[0][key_performance_indicator]" required
                                    class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 outline-none transition dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900" 
                                    placeholder="Indikator Kinerja">
                            </td>
                            <td class="px-6 py-4">
                                <input type="text" name="items[0][units]" 
                                    class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 outline-none transition dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900" 
                                    placeholder="Contoh: %">
                            </td>
                            <td class="px-2 py-4">
                                <select name="items[0][polaritas]" 
                                    class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-left text-gray-800 focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 outline-none transition dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900">
                                    <option value="MAX">Max</option>
                                    <option value="MIN">Min</option>
                                </select>
                            </td>
                            <td class="px-6 py-4">
                                <input type="number" name="items[0][bobot]" min="0" max="100" required
                                    class="w-full rounded-lg border border-gray-200 bg-gray-50 px-9 py-2 text-sm text-center text-gray-800 focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 outline-none transition dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900" 
                                    placeholder="0">
                            </td>
                            <td class="px-6 py-4">
                                <input type="number" name="items[0][target]" min="0" required
                                    class="w-full rounded-lg border border-gray-200 bg-gray-50 px-9  py-2 text-sm text-center text-gray-800 focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 outline-none transition dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900" 
                                    placeholder="0">
                            </td>
                            <td class="px-10 py-4">
                                <div class="relative">
                                    <select name="items[0][perspektif]" class="w-full appearance-none rounded-lg border border-gray-200 bg-gray-50 px-9 py-2 text-sm text-center text-gray-800 focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 outline-none transition dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900" {{ $perspektifList->isEmpty() ? 'disabled' : '' }}>
                                    @if($perspektifList->isEmpty())
                                        <option value="">Belum ada perspektif</option>
                                    @else
                                        @foreach($perspektifList as $perspektif)
                                            <option value="{{ $perspektif }}">{{ $perspektif }}</option>
                                        @endforeach
                                    @endif
                                    </select>
                                    <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button type="button" class="remove-row p-2 text-gray-400 hover:text-red-500 hover:bg-error-50 rounded-lg transition-colors dark:hover:bg-error-900/20" title="Hapus Baris">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            @if($perspektifList->isEmpty())
            <div class="bg-warning-50 text-warning-800 p-4 text-sm flex items-center gap-2 border-t border-warning-100 dark:bg-warning-900/10 dark:text-warning-400 dark:border-warning-900/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span class="font-medium">Perhatian:</span> Belum ada data perspektif. Mohon tambahkan perspektif terlebih dahulu di menu Master Perspektif.
            </div>
            @endif
        </div>

        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('kpi.index', ['tahun' => $tahun]) }}" 
                class="rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                Batal
            </a>
            <button type="submit" 
                class="rounded-xl bg-brand-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-brand-200 hover:bg-brand-700 hover:-translate-y-0.5 dark:shadow-none transition-all duration-200">
                Simpan & Tetapkan
            </button>
        </div>
    </form>
</div>

<script>
    (function(){
        const addBtn = document.getElementById('add-row');
        const tbody = document.querySelector('#items-table tbody');
        
        // Function to clone form template manually to avoid event listener issues with cloneNode(true)
        function createNewRow(index) {
            const tr = document.createElement('tr');
            tr.className = 'item-row group hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors';
            
            // Generate HTML with valid indices
            tr.innerHTML = `
                <td class="px-6 py-4 text-center text-gray-400 font-medium row-number">${index + 1}</td>
                <td class="px-6 py-4">
                    <input type="text" name="items[${index}][key_result_area]" required
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 outline-none transition dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900" 
                        placeholder="Area Kinerja Utama">
                </td>
                <td class="px-6 py-4">
                    <input type="text" name="items[${index}][key_performance_indicator]" required
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 outline-none transition dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900" 
                        placeholder="Indikator Kinerja">
                </td>
                <td class="px-6 py-4">
                    <input type="text" name="items[${index}][units]" 
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 outline-none transition dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900" 
                        placeholder="Contoh: %">
                </td>
                <td class="px-6 py-4">
                    <select name="items[${index}][polaritas]" 
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 outline-none transition dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900">
                        <option value="MAX">Max</option>
                        <option value="MIN">Min</option>
                    </select>
                </td>
                <td class="px-6 py-4">
                    <input type="number" name="items[${index}][bobot]" min="0" max="100" required
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 outline-none transition dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900" 
                        placeholder="0">
                </td>
                <td class="px-6 py-4">
                    <input type="number" name="items[${index}][target]" min="0" required
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 outline-none transition dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900" 
                        placeholder="0">
                </td>
                <td class="px-6 py-4">
                    <div class="relative">
                        <select name="items[${index}][perspektif]" class="w-full appearance-none rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 outline-none transition dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900" {{ $perspektifList->isEmpty() ? 'disabled' : '' }}>
                        @if($perspektifList->isEmpty())
                            <option value="">Belum ada perspektif</option>
                        @else
                            @foreach($perspektifList as $perspektif)
                                <option value="{{ $perspektif }}">{{ $perspektif }}</option>
                            @endforeach
                        @endif
                        </select>
                        <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-center">
                    <button type="button" class="remove-row p-2 text-gray-400 hover:text-error-500 hover:bg-error-50 rounded-lg transition-colors dark:hover:bg-error-900/20" title="Hapus Baris">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </td>
            `;
            return tr;
        }

        function reindexRows() {
            const rows = tbody.querySelectorAll('tr.item-row');
            rows.forEach((row, index) => {
                // Update row number
                const numCell = row.querySelector('.row-number');
                if(numCell) numCell.innerText = index + 1;

                // Update input names
                row.querySelectorAll('input, select').forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        input.setAttribute('name', name.replace(/items\[\d+\]/, `items[${index}]`));
                    }
                });
            });
        }

        addBtn.addEventListener('click', function(){
            const currentRowCount = tbody.querySelectorAll('tr.item-row').length;
            const newRow = createNewRow(currentRowCount);
            tbody.appendChild(newRow);
        });

        document.addEventListener('click', function(e){
            const btn = e.target.closest('.remove-row');
            if (btn) {
                // Do not remove the last row
                if (tbody.querySelectorAll('tr.item-row').length > 1) {
                    const row = btn.closest('tr');
                    row.remove();
                    reindexRows();
                } else {
                    alert("Minimal harus ada satu baris indikator.");
                }
            }
        });
    })();
</script>
@endsection