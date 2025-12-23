<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <title>Form Penilaian KPI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>tailwind.config = { darkMode: 'class' }</script>
    <style>
        .custom-scrollbar::-webkit-scrollbar { height: 10px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 5px; border: 2px solid #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }
        
        /* Agar input number tidak ada panah spin */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 p-2 md:p-6 font-sans">

<div class="w-full max-w-7xl mx-auto">

    {{-- HEADER --}}
    <div class="mb-6 flex flex-col lg:flex-row justify-between items-start lg:items-center bg-white dark:bg-gray-800 p-4 md:p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 gap-4">
        <div class="w-full lg:w-auto">
            <h1 class="text-xl md:text-2xl font-bold mb-1 text-gray-800 dark:text-white">Form Penilaian Kinerja (KPI)</h1>
            <p class="text-xs md:text-sm text-gray-500">
                Karyawan: <span class="font-semibold text-blue-600">{{ $karyawan->Nama_Lengkap_Sesuai_Ijazah }}</span> | 
                Periode: {{ $kpi->tahun }}
            </p>
        </div>
        
        <div class="flex flex-wrap gap-2 w-full lg:w-auto justify-start lg:justify-end">
            <a href="{{ route('kpi.index') }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition flex-1 lg:flex-none text-center">
                Kembali
            </a>
            
            <button form="kpiForm" type="submit" class="px-3 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition shadow-lg flex items-center justify-center gap-2 flex-1 lg:flex-none">
                <i class="fas fa-save"></i> <span class="hidden sm:inline">Simpan</span>
            </button>
        </div>
    </div>

    {{-- ACTION BAR & ALERT --}}
    <div class="mb-4 flex flex-col sm:flex-row justify-between items-center gap-4">
        <div id="total-bobot-alert" class="text-sm font-bold w-full sm:w-auto text-center sm:text-left"></div>

        <button type="button" onclick="document.getElementById('modalTambahKPI').classList.remove('hidden')" 
                class="w-full sm:w-auto px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-bold hover:bg-green-700 transition shadow flex items-center justify-center gap-2">
            <i class="fas fa-plus"></i> Tambah KPI Baru
        </button>
    </div>

    <form id="kpiForm" action="{{ route('kpi.update', $kpi->id_kpi_assessment) }}" method="POST">
        @csrf
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden border border-gray-200 dark:border-gray-700 relative">
            
            {{-- WRAPPER SCROLL --}}
            <div class="w-full overflow-x-auto custom-scrollbar">
                
                {{-- TABEL UTAMA --}}
                <table class="w-full text-sm text-left border-collapse min-w-[3000px] md:min-w-[4500px]"> 
                    
                    {{-- HEADER TABEL --}}
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 sticky top-0 z-20 shadow-sm">
                        <tr>
                            {{-- Sticky Columns --}}
                            {{-- No selalu sticky --}}
                            <th rowspan="2" class="sticky left-0 bg-gray-200 z-30 p-2 md:p-3 w-10 md:w-12 text-center border border-gray-300 text-[10px] md:text-xs">No</th>
                            
                            {{-- Indikator Sticky HANYA di layar Medium ke atas (md:sticky) agar di HP tidak menutupi layar --}}
                            <th rowspan="2" class="md:sticky left-10 md:left-12 bg-gray-200 z-30 p-2 md:p-3 w-60 md:w-72 border border-gray-300 text-[10px] md:text-xs">
                                Key Performance Indicator
                            </th>

                            {{-- Kolom Tambahan --}}
                            <th rowspan="2" class="p-2 md:p-3 w-28 md:w-32 border border-gray-300 bg-gray-50">Perspektif</th>
                            <th rowspan="2" class="p-2 md:p-3 w-32 md:w-40 border border-gray-300 bg-gray-50">KRA</th>
                            <th rowspan="2" class="p-2 md:p-3 w-16 md:w-20 text-center border border-gray-300 bg-gray-50">Units</th>
                            <th rowspan="2" class="p-2 md:p-3 w-16 md:w-20 text-center border border-gray-300 bg-gray-50">Polaritas</th>

                            {{-- Kolom Data Utama --}}
                            <th rowspan="2" class="p-2 md:p-3 w-16 md:w-20 text-center border border-gray-300">Bobot</th>
                            <th rowspan="2" class="p-2 md:p-3 w-16 md:w-20 text-center border border-gray-300">Target</th>

                            {{-- Loop Bulan & Semester --}}
                            @foreach(['Semester 1','Juli','Agustus','September','Oktober','November','Desember'] as $bulan)
                                <th colspan="4" class="p-1 md:p-2 text-center border border-gray-300 bg-blue-50 text-[10px] md:text-xs">
                                    {{ $bulan }}
                                </th>
                            @endforeach

                            <th rowspan="2" class="p-2 md:p-3 w-20 md:w-24 text-center border border-gray-300 bg-gray-50">Total Sem. 2</th>
                            <th rowspan="2" class="p-2 md:p-3 w-20 md:w-24 text-center border border-gray-300 bg-orange-50">Adj.</th>
                            <th rowspan="2" class="p-2 md:p-3 w-20 md:w-24 text-center border border-gray-300 bg-gray-200 font-bold">Final</th>
                        </tr>

                        <tr>
                            @foreach(['smt1','jul','aug','sep','okt','nov','des'] as $bln)
                                <th class="p-1 text-center border border-gray-300 text-[9px] md:text-[10px] w-16 md:w-20 min-w-[60px] md:min-w-[80px]">Target</th>
                                <th class="p-1 text-center border border-gray-300 text-[9px] md:text-[10px] w-16 md:w-20 min-w-[60px] md:min-w-[80px]">Real</th>
                                <th class="p-1 text-center border border-gray-300 text-[9px] md:text-[10px] w-16 md:w-20 min-w-[60px] md:min-w-[80px]">Skor</th>
                                <th class="p-1 text-center border border-gray-300 text-[9px] md:text-[10px] w-16 md:w-20 min-w-[60px] md:min-w-[80px]">Nilai</th>
                            @endforeach
                        </tr>
                    </thead>

                    {{-- BODY TABEL --}}
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($kpi->items as $index => $item)
                        @php $score = $item->scores->first(); @endphp
                        <tr class="row-kpi hover:bg-gray-50 dark:hover:bg-gray-600 transition group text-xs md:text-sm">
                            
                            {{-- 1. NO (Sticky Always) --}}
                            <td class="sticky left-0 bg-white dark:bg-gray-800 z-10 p-2 md:p-3 text-center border-r border-gray-200 font-medium group-hover:bg-gray-50">{{ $index + 1 }}</td>
                            
                            {{-- 2. INDIKATOR (Sticky on Desktop Only) --}}
                            <td class="md:sticky left-10 md:left-12 bg-white dark:bg-gray-800 z-10 p-2 md:p-3 border-r border-gray-200 group-hover:bg-gray-50 align-top shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                                <div class="flex flex-col sm:flex-row justify-between items-start gap-2 min-w-[200px] md:min-w-0">
                                    <div class="text-xs font-semibold text-gray-900 dark:text-white leading-snug">
                                        {{ $item->key_performance_indicator ?? $item->indikator }}
                                    </div>
                                    <div class="flex gap-1 shrink-0">
                                        {{-- TOMBOL EDIT --}}
                                        <button type="button" 
                                                onclick="openEditModal({{ json_encode($item) }}, '{{ route('kpi.update-item', $item->id_kpi_item) }}')"
                                                class="text-gray-400 hover:text-yellow-600 transition p-1" title="Edit KPI">
                                            <i class="fas fa-pencil-alt text-[10px]"></i>
                                        </button>
                                        {{-- TOMBOL DELETE --}}
                                        <button type="button" 
                                                onclick="confirmDelete('{{ route('kpi.delete-item', $item->id_kpi_item) }}')"
                                                class="text-gray-400 hover:text-red-600 transition p-1" title="Hapus KPI">
                                            <i class="fas fa-trash-alt text-[10px]"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-[10px] text-gray-500 mt-1">
                                    {{ $item->units ?? $item->satuan }} | {{ $item->polaritas }}
                                </div>
                                <input type="hidden" class="input-bobot" value="{{ $item->bobot }}">
                                <input type="hidden" class="input-polaritas" value="{{ $item->polaritas }}">
                            </td>

                            {{-- INFO TAMBAHAN --}}
                            <td class="p-2 md:p-3 border-r border-gray-200 align-top text-[10px] md:text-xs">{{ $item->perspektif }}</td>
                            <td class="p-2 md:p-3 border-r border-gray-200 align-top text-[10px] md:text-xs">{{ $item->kra ?? $item->key_result_area }}</td>
                            <td class="p-2 md:p-3 text-center border-r border-gray-200 align-top text-[10px] md:text-xs">{{ $item->units ?? $item->satuan }}</td>
                            <td class="p-2 md:p-3 text-center border-r border-gray-200 align-top text-[10px] md:text-xs font-mono">{{ $item->polaritas }}</td>
                            <td class="p-2 md:p-3 text-center border-r border-gray-200 align-top font-bold text-blue-600 bg-blue-50/10">{{ $item->bobot }}%</td>
                            <td class="p-2 md:p-3 text-center border-r border-gray-200 align-top text-[10px] md:text-xs text-gray-500">100%</td>

                            {{-- LOOP BULAN --}}
                            @foreach(['smt1','jul', 'aug', 'sep', 'okt', 'nov', 'des'] as $bln)
                                <td class="p-1 border-r border-gray-100 align-top">
                                    <input type="number" step="0.01" 
                                        name="kpi[{{ $item->id }}][target_{{ $bln }}]" 
                                        value="{{ $score ? ($score->{'target_'.$bln} ?? 0) : 0 }}"
                                        class="input-bulanan-target input-target-{{ $bln }} w-full h-8 px-1 text-[10px] md:text-xs border border-gray-300 rounded text-center focus:border-blue-500 outline-none bg-white block" 
                                        placeholder="0">
                                </td>
                                <td class="p-1 border-r border-gray-100 align-top">
                                    <input type="number" step="0.01" 
                                        name="kpi[{{ $item->id }}][real_{{ $bln }}]" 
                                        value="{{ $score ? ($score->{'real_'.$bln} ?? 0) : 0 }}"
                                        class="input-bulanan-real input-real-{{ $bln }} w-full h-8 px-1 text-[10px] md:text-xs border border-gray-300 rounded text-center focus:border-green-500 bg-white outline-none block" 
                                        placeholder="0">
                                </td>
                                <td class="p-1 border-r border-gray-100 align-top text-center bg-gray-50">
                                    <div class="py-1.5 text-[10px] md:text-xs font-medium text-gray-600"><span class="span-skor-{{ $bln }}">0</span>%</div>
                                </td>
                                <td class="p-1 border-r-2 border-gray-300 align-top text-center bg-blue-50/20">
                                    <div class="py-1.5 text-[10px] md:text-xs font-bold text-blue-700"><span class="span-nilai-{{ $bln }}">0</span>%</div>
                                </td>
                            @endforeach

                            {{-- TOTAL & ADJ --}}
                            <td class="p-2 md:p-3 text-center border-r border-gray-200 bg-gray-50 font-bold text-gray-700">
                                <span class="span-total-sem-nilai">0</span>
                            </td>
                            <td class="p-1 border-r border-gray-200 bg-orange-50/30 align-top">
                                <input type="number" step="0.01" name="kpi[{{ $item->id }}][adjustment]" 
                                       value="{{ $score ? $score->adjustment : '' }}"
                                       class="input-adjustment w-full h-full p-2 bg-transparent text-center text-sm font-bold text-orange-700 outline-none placeholder-gray-300"
                                       placeholder="-">
                            </td>
                            <td class="p-2 md:p-3 text-center border-r border-gray-200 bg-gray-100 font-bold text-blue-800 text-sm">
                                <span class="text-akhir-total">0</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                    {{-- FOOTER --}}
                    <tfoot class="bg-white border-t-4 border-gray-300 sticky bottom-0 z-40 text-xs md:text-sm">
                        <tr class="bg-gray-50 border-b border-gray-200">
                            {{-- Colspan No + Indikator --}}
                            <td colspan="2" class="sticky left-0 bg-gray-100 p-2 md:p-3 text-left font-bold text-[10px] md:text-xs uppercase text-gray-600 border-r">
                                Total Skor Akhir :
                            </td>
                            <td colspan="6" class="border-r"></td>
                            @foreach(['smt1','jul', 'aug', 'sep', 'okt', 'nov', 'des'] as $bln)
                                <td colspan="3" class="p-2 border-r border-gray-200"></td>
                                <td class="p-2 text-center font-bold text-blue-800 border-r-2 border-gray-300 text-[10px] md:text-sm">
                                    <span id="footer-total-{{ $bln }}">0</span>%
                                </td>
                            @endforeach
                            <td class="p-2 text-center font-bold text-gray-700 border-r"><span id="footer-total-sem">0</span></td>
                            <td class="p-2 border-r bg-orange-50"></td>
                            <td class="p-2 text-center font-extrabold text-blue-900 bg-gray-200 text-sm md:text-base"><span id="footer-grand-total">0</span></td>
                        </tr>

                        <tr class="bg-white">
                            <td colspan="2" class="sticky left-0 bg-white p-2 md:p-3 text-left font-bold text-[10px] md:text-xs uppercase text-gray-500 border-r">
                                Uraian Penilaian :
                            </td>
                            <td colspan="6" class="border-r"></td>
                            @foreach(['smt1','jul', 'aug', 'sep', 'okt', 'nov', 'des'] as $bln)
                                <td colspan="3" class="border-r border-gray-100"></td>
                                <td class="p-1 text-center border-r-2 border-gray-300 align-middle">
                                    <span id="footer-grade-{{ $bln }}" class="px-1 md:px-2 py-0.5 rounded text-[8px] md:text-[10px] font-bold uppercase bg-gray-100 text-gray-400">-</span>
                                </td>
                            @endforeach
                            <td class="text-center border-r p-1"><span id="footer-grade-sem" class="text-[8px] md:text-[10px] font-bold text-gray-400">-</span></td>
                            <td class="border-r bg-orange-50"></td>
                            <td class="p-2 text-center bg-gray-50 border-t border-gray-300">
                                <span id="footer-grade-final" class="px-2 md:px-3 py-1 rounded-full text-[10px] md:text-xs font-bold bg-gray-200 text-gray-500">...</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </form>
</div>

{{-- MODAL TAMBAH KPI --}}
<div id="modalTambahKPI" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50 flex justify-center items-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl p-4 md:p-6 relative max-h-[90vh] overflow-y-auto">
        <h2 class="text-lg md:text-xl font-bold mb-4 text-gray-800 dark:text-white">Tambah Indikator Kinerja Baru</h2>
        
        <form action="{{ route('kpi.store-item') }}" method="POST">
            @csrf
            <input type="hidden" name="kpi_assessment_id" value="{{ $kpi->id_kpi_assessment }}">

            {{-- Grid Responsif: 1 kolom di HP, 2 kolom di Desktop --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Perspektif</label>
                    <select name="perspektif" class="w-full border rounded p-2 text-sm">
                        <option value="Financial">Financial</option>
                        <option value="Customer">Customer</option>
                        <option value="Internal Business Process">Internal Business Process</option>
                        <option value="Learning & Growth">Learning & Growth</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Key Result Area (KRA)</label>
                    <input type="text" name="key_result_area" class="w-full border rounded p-2 text-sm" placeholder="Contoh: Produktivitas" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Indikator Kinerja (KPI)</label>
                    <textarea name="key_performance_indicator" class="w-full border rounded p-2 text-sm" rows="2" placeholder="Contoh: Meningkatkan omset..." required></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Satuan (Units)</label>
                    <input type="text" name="units" class="w-full border rounded p-2 text-sm" placeholder="%, IDR" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Polaritas</label>
                    <select name="polaritas" class="w-full border rounded p-2 text-sm">
                        <option value="Maximize">Maximize (Positif)</option>
                        <option value="Minimize">Minimize (Negatif)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Bobot (%)</label>
                    <input type="number" step="0.01" name="bobot" class="w-full border rounded p-2 text-sm" placeholder="0" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Target (Angka)</label>
                    <input type="number" step="0.01" name="target" class="w-full border rounded p-2 text-sm" placeholder="0" required>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalTambahKPI').classList.add('hidden')" 
                        class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-100 text-sm">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Simpan KPI</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT KPI --}}
<div id="modalEditKPI" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50 flex justify-center items-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl p-4 md:p-6 relative max-h-[90vh] overflow-y-auto">
        <h2 class="text-lg md:text-xl font-bold mb-4 text-gray-800 dark:text-white">Edit Indikator Kinerja</h2>
        
        <form id="formEditKPI" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Perspektif</label>
                    <select id="edit_perspektif" name="perspektif" class="w-full border rounded p-2 text-sm">
                        <option value="Financial">Financial</option>
                        <option value="Customer">Customer</option>
                        <option value="Internal Business Process">Internal Business Process</option>
                        <option value="Learning & Growth">Learning & Growth</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Key Result Area (KRA)</label>
                    <input type="text" id="edit_kra" name="key_result_area" class="w-full border rounded p-2 text-sm" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Indikator Kinerja (KPI)</label>
                    <textarea id="edit_kpi" name="key_performance_indicator" class="w-full border rounded p-2 text-sm" rows="2" required></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Satuan</label>
                    <input type="text" id="edit_units" name="units" class="w-full border rounded p-2 text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Polaritas</label>
                    <select id="edit_polaritas" name="polaritas" class="w-full border rounded p-2 text-sm">
                        <option value="Maximize">Maximize (Positif)</option>
                        <option value="Minimize">Minimize (Negatif)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Bobot (%)</label>
                    <input type="number" step="0.01" id="edit_bobot" name="bobot" class="w-full border rounded p-2 text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Target</label>
                    <input type="number" step="0.01" id="edit_target" name="target" class="w-full border rounded p-2 text-sm" required>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalEditKPI').classList.add('hidden')" 
                        class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-100 text-sm">Batal</button>
                <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 text-sm">Update KPI</button>
            </div>
        </form>
    </div>
</div>

{{-- FORM DELETE RAHASIA (Dipakai bergantian oleh JS) --}}
<form id="globalDeleteForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

{{-- SCRIPT --}}
<script>
    function openEditModal(data, updateUrl) {
        document.getElementById('formEditKPI').action = updateUrl;
        document.getElementById('edit_perspektif').value = data.perspektif;
        document.getElementById('edit_kra').value = data.key_result_area || data.kra;
        document.getElementById('edit_kpi').value = data.key_performance_indicator || data.indikator;
        document.getElementById('edit_units').value = data.units || data.satuan;
        document.getElementById('edit_polaritas').value = data.polaritas;
        document.getElementById('edit_bobot').value = data.bobot;
        document.getElementById('edit_target').value = data.target || data.target_tahunan;
        document.getElementById('modalEditKPI').classList.remove('hidden');
        
    }
    // Fungsi untuk Submit Form Delete secara Global
    function confirmDelete(deleteUrl) {
        if (confirm('Yakin ingin menghapus KPI ini? Data nilai yang sudah diinput akan hilang.')) {
            const form = document.getElementById('globalDeleteForm');
            form.action = deleteUrl; // Set URL target sesuai tombol yang diklik
            form.submit(); // Kirim form
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.row-kpi');
        const listBulan = ['smt1','jul', 'aug', 'sep', 'okt', 'nov', 'des'];

        function parseNumber(val) {
            if (!val) return 0;
            let cleanStr = val.toString().replace('%', '').replace(',', '.');
            return parseFloat(cleanStr) || 0;
        }

        function formatNumber(num) {
            return num.toFixed(2).replace(/\.00$/, '');
        }

        function getGrade(skor) {
            if (skor === 0) return { label: '-', class: 'bg-gray-100 text-gray-400' };
            if (skor > 89) return { label: 'GREAT', class: 'bg-green-100 text-green-700' };
            if (skor > 79) return { label: 'GOOD', class: 'bg-blue-100 text-blue-700' };
            if (skor > 69) return { label: 'AVERAGE', class: 'bg-yellow-100 text-yellow-700' };
            return { label: 'NEED IMPROVEMENT', class: 'bg-red-100 text-red-700' };
        }

        function calculateSingleScore(target, real, polaritas) {
            if (target === 0) return 0;
            let score = 0;
            if (polaritas.includes('Positif') || polaritas.includes('Maximize')) {
                score = (real / target) * 100;
            } else if (polaritas.includes('Negatif') || polaritas.includes('Minimize')) {
                let ratio = real / target;
                score = (2 - ratio) * 100;
            } else {
                score = (real >= target) ? 100 : 0;
            }
            return Math.max(0, score);
        }

        function calculateAll() {
            let monthlyTotals = {smt1: 0, jul: 0, aug: 0, sep: 0, okt: 0, nov: 0, des: 0 };
            let semesterTotal = 0;
            let grandTotalAdjusted = 0;

            rows.forEach(row => {
                const bobot = parseNumber(row.querySelector('.input-bobot').value);
                const polaritas = row.querySelector('.input-polaritas').value;
                const adjustmentInput = row.querySelector('.input-adjustment');
                const spanTotalSemNilai = row.querySelector('.span-total-sem-nilai');
                const textAkhirTotal = row.querySelector('.text-akhir-total');

                let totalNilaiRow = 0;

                listBulan.forEach(bln => {
                    const t = parseNumber(row.querySelector(`.input-target-${bln}`).value);
                    const r = parseNumber(row.querySelector(`.input-real-${bln}`).value);
                    const spanSkor = row.querySelector(`.span-skor-${bln}`);
                    const spanNilai = row.querySelector(`.span-nilai-${bln}`);

                    let skorBulan = (t !== 0 || r !== 0) ? calculateSingleScore(t, r, polaritas) : 0;
                    let nilaiBulan = (skorBulan * bobot) / 100;

                    spanSkor.textContent = formatNumber(skorBulan);
                    spanNilai.textContent = formatNumber(nilaiBulan);

                    monthlyTotals[bln] += nilaiBulan;
                    totalNilaiRow += nilaiBulan;
                });

                let sumT = 0, sumR = 0;
                listBulan.forEach(b => {
                    sumT += parseNumber(row.querySelector(`.input-target-${b}`).value);
                    sumR += parseNumber(row.querySelector(`.input-real-${b}`).value);
                });
                
                let skorMurniSem = calculateSingleScore(sumT, sumR, polaritas);
                let nilaiAkhirSem = (skorMurniSem * bobot) / 100;

                spanTotalSemNilai.textContent = formatNumber(nilaiAkhirSem);
                semesterTotal += nilaiAkhirSem;

                let finalVal = nilaiAkhirSem;
                let adj = adjustmentInput.value;
                if (adj !== "" && adj !== null) {
                    let adjScore = parseNumber(adj);
                    finalVal = (adjScore * bobot) / 100;
                    textAkhirTotal.classList.add('text-orange-600');
                } else {
                    textAkhirTotal.classList.remove('text-orange-600');
                }
                
                textAkhirTotal.textContent = formatNumber(finalVal);
                grandTotalAdjusted += finalVal;
            });

            listBulan.forEach(bln => {
                const footerTotal = document.getElementById(`footer-total-${bln}`);
                const footerGrade = document.getElementById(`footer-grade-${bln}`);
                let val = monthlyTotals[bln];
                footerTotal.textContent = formatNumber(val);
                let g = getGrade(val);
                footerGrade.textContent = g.label;
                footerGrade.className = `px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider ${g.class}`;
            });

            document.getElementById('footer-total-sem').textContent = formatNumber(semesterTotal);
            document.getElementById('footer-grade-sem').textContent = getGrade(semesterTotal).label;
            document.getElementById('footer-grand-total').textContent = formatNumber(grandTotalAdjusted);
            
            let finalG = getGrade(grandTotalAdjusted);
            const finalBadge = document.getElementById('footer-grade-final');
            finalBadge.textContent = finalG.label;
            finalBadge.className = `px-3 py-1 rounded-full text-xs font-bold border ${finalG.class}`;
        }

        // Event listener kalkulasi
        rows.forEach(row => {
            const inputs = row.querySelectorAll('input');
            inputs.forEach(input => input.addEventListener('input', calculateAll));
        });

        // Validasi bobot saat load & input
        function checkBobot() {
            let totalBobot = 0;
            document.querySelectorAll('.input-bobot').forEach(el => {
                totalBobot += parseFloat(el.value) || 0;
            });
            const alertBox = document.getElementById('total-bobot-alert');
            if(totalBobot !== 100) {
                alertBox.innerHTML = `⚠️ Total Bobot: <span class="text-red-600">${totalBobot}%</span> (Harus 100%)`;
            } else {
                alertBox.innerHTML = `✅ Total Bobot: <span class="text-green-600">100%</span>`;
            }
        }
        checkBobot(); // Run on load

        calculateAll();
    });
</script>

</body>
</html>