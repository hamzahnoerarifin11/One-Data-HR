<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <title>Form Penilaian KPI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>tailwind.config = { darkMode: 'class' }</script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 p-6">

<div class="max-w-[95%] mx-auto">

    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
        <div>
            <h1 class="text-2xl font-bold mb-1">Form Penilaian Kinerja (KPI)</h1>
            <p class="text-sm text-gray-500">
                Karyawan: <span class="font-semibold text-blue-600">{{ $karyawan->Nama_Lengkap_Sesuai_Ijazah }}</span> | 
                Periode: {{ $kpi->tahun }}
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-3">
            <a href="{{ route('kpi.index') }}" class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                Kembali
            </a>
            <button form="kpiForm" type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition shadow-lg flex items-center gap-2">
                <i class="fas fa-save"></i> Simpan Penilaian
            </button>
            {{-- LOGIKA TOMBOL BERDASARKAN STATUS --}}
            @if($kpi->status != 'FINAL')
                
                {{-- Tombol Simpan (Hanya muncul jika BELUM Final) --}}
                <button form="kpiForm" type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition shadow-lg flex items-center gap-2">
                    <i class="fas fa-save"></i> Simpan Draft
                </button>

                {{-- Tombol Finalisasi (Muncul terpisah) --}}
                <form action="{{ route('kpi.finalize', $kpi->id_kpi_assessment) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin? Setelah difinalisasi, data TIDAK BISA diubah lagi.')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 transition shadow-lg flex items-center gap-2">
                        <i class="fas fa-check-circle"></i> Finalisasi / Selesai
                    </button>
                </form>

            @else
                {{-- Jika Sudah Final, Tampilkan Badge --}}
                <span class="px-4 py-2 bg-gray-500 text-white rounded-lg text-sm flex items-center gap-2 cursor-not-allowed">
                    <i class="fas fa-lock"></i> Data Terkunci (Final)
                </span>
            @endif
        </div>
        
    </div>

    <form id="kpiForm" action="{{ route('kpi.update', $kpi->id_kpi_assessment) }}" method="POST">
        @csrf
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-xs font-bold border-b dark:border-gray-600">
                            <th class="p-4 w-10 text-center border-r dark:border-gray-600">No</th>
                            <th class="p-4 w-32 border-r dark:border-gray-600">Perspektif</th>
                            <th class="p-4 w-1/4 border-r dark:border-gray-600">Indikator Kinerja (KPI)</th>
                            <th class="p-4 w-20 text-center border-r dark:border-gray-600">Bobot</th>
                            <th class="p-4 w-20 text-center border-r dark:border-gray-600">Polaritas</th>
                            
                            <th class="p-4 w-32 text-center bg-blue-50 dark:bg-blue-900/20 border-r dark:border-gray-600">Target</th>
                            <th class="p-4 w-32 text-center bg-yellow-50 dark:bg-yellow-900/20 border-r dark:border-gray-600">Realisasi</th>
                            
                            <th class="p-4 w-24 text-center border-r dark:border-gray-600">Skor (%)</th>
                            <th class="p-4 w-24 text-center">Nilai Akhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($kpi->items as $index => $item)
                        @php
                            // Ambil data semester 1 (Sesuaikan jika nanti ada semester 2)
                            $scoreData = $item->scores->where('nama_periode', 'Semester 1')->first();
                            $targetVal = $scoreData ? $scoreData->target : 0;
                            $realisasiVal = $scoreData ? $scoreData->realisasi : 0;
                            $skorVal = $scoreData ? $scoreData->skor : 0;
                            $akhirVal = $scoreData ? $scoreData->skor_akhir : 0;
                        @endphp

                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition row-kpi" data-id="{{ $item->id_kpi_item }}">
                            <td class="p-4 text-center border-r dark:border-gray-600">{{ $index + 1 }}</td>
                            <td class="p-4 border-r dark:border-gray-600 font-medium">{{ $item->perspektif }}</td>
                            <td class="p-4 border-r dark:border-gray-600">
                                <div class="font-bold text-gray-800 dark:text-gray-200">{{ $item->key_result_area }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $item->key_performance_indicator }}</div>
                            </td>
                            
                            <input type="hidden" class="input-bobot" value="{{ $item->bobot }}">
                            <input type="hidden" class="input-polaritas" value="{{ $item->polaritas }}">

                            <td class="p-4 text-center border-r dark:border-gray-600">
                                <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs font-bold">{{ $item->bobot }}%</span>
                            </td>
                            <td class="p-4 text-center border-r dark:border-gray-600 text-xs">
                                {{ $item->polaritas }}
                            </td>

                            <td class="p-2 border-r dark:border-gray-600 bg-blue-50 dark:bg-blue-900/10">
                                <input type="number" step="0.01" 
                                       name="kpi[{{ $item->id_kpi_item }}][target]" 
                                       value="{{ $targetVal }}"
                                       @disabled($kpi->status == 'FINAL')
                                       class="input-target w-full p-2 border border-gray-300 dark:border-gray-600 rounded text-center font-bold text-blue-700 focus:ring-2 focus:ring-blue-500 outline-none">
                            </td>

                            <td class="p-2 border-r dark:border-gray-600 bg-yellow-50 dark:bg-yellow-900/10">
                                <input type="number" step="0.01" 
                                       name="kpi[{{ $item->id_kpi_item }}][realisasi]" 
                                       value="{{ $realisasiVal }}"
                                       @disabled($kpi->status == 'FINAL')
                                       class="input-realisasi w-full p-2 border border-gray-300 dark:border-gray-600 rounded text-center font-bold text-gray-800 dark:text-white focus:ring-2 focus:ring-yellow-500 outline-none">
                            </td>

                            <td class="p-4 text-center border-r dark:border-gray-600">
                                <span class="text-skor font-bold text-gray-800 dark:text-gray-200">{{ $skorVal }}</span>%
                            </td>
                            <td class="p-4 text-center bg-gray-50 dark:bg-gray-800 font-bold text-lg text-blue-600 dark:text-blue-400">
                                <span class="text-akhir">{{ $akhirVal }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-gray-500">Belum ada item KPI.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-100 dark:bg-gray-800 border-t-2 border-gray-300 dark:border-gray-600">
                        <tr>
                            <td colspan="3" class="p-4 text-right font-bold uppercase text-gray-600 dark:text-gray-300">Total Bobot</td>
                            <td class="p-4 text-center font-bold text-gray-800 dark:text-white">
                                {{ $kpi->items->sum('bobot') }}%
                            </td>
                            <td colspan="4" class="p-4 text-right font-bold uppercase text-gray-600 dark:text-gray-300 align-middle">
                                TOTAL SKOR AKHIR & GRADE
                            </td>
                            <td class="p-4 text-center">
                                {{-- Angka Total Skor --}}
                                <div class="font-bold text-3xl text-blue-700 dark:text-blue-400">
                                    <span id="grand-total">{{ number_format($kpi->total_skor_akhir, 2) }}</span>
                                </div>
                                
                                {{-- Label Grade Dinamis --}}
                                <div id="grade-container" class="mt-1">
                                    <span id="grade-label" class="px-3 py-1 rounded-full text-xs font-bold bg-gray-200 text-gray-600">
                                        {{ $kpi->grade ?? '...' }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.row-kpi');
        const grandTotalEl = document.getElementById('grand-total');
        const gradeLabel = document.getElementById('grade-label');

        // --- HELPER: Format Angka ---
        function parseNumber(val) {
            if (!val) return 0;
            let cleanStr = val.toString().replace('%', '').replace(',', '.');
            return parseFloat(cleanStr) || 0;
        }

        function formatNumber(num) {
            return num.toFixed(2); // Pakai titik dulu biar JS aman, nanti bisa direplace koma kalau mau
        }

        // --- LOGIKA GRADE (Sama dengan Controller) ---
        function updateGrade(totalSkor) {
            let grade = '';
            let classes = '';

            // Tentukan Grade & Warna
            if (totalSkor >= 100) { 
                grade = 'Outstanding'; 
                classes = 'bg-purple-100 text-purple-700 border border-purple-300';
            } else if (totalSkor >= 90) { 
                grade = 'Great'; 
                classes = 'bg-green-100 text-green-700 border border-green-300';
            } else if (totalSkor >= 75) { 
                grade = 'Good'; 
                classes = 'bg-blue-100 text-blue-700 border border-blue-300';
            } else if (totalSkor >= 60) { 
                grade = 'Enough'; 
                classes = 'bg-yellow-100 text-yellow-700 border border-yellow-300';
            } else { 
                grade = 'Poor'; 
                classes = 'bg-red-100 text-red-700 border border-red-300';
            }

            // Update Teks & Warna Badge
            gradeLabel.textContent = grade;
            gradeLabel.className = `px-3 py-1 rounded-full text-xs font-bold transition-colors ${classes}`;
        }

        function calculateRow(row) {
            const targetInput = row.querySelector('.input-target');
            const realisasiInput = row.querySelector('.input-realisasi');
            const bobotInput = row.querySelector('.input-bobot');
            const polaritasInput = row.querySelector('.input-polaritas');
            
            const skorEl = row.querySelector('.text-skor');
            const akhirEl = row.querySelector('.text-akhir');

            let target    = parseNumber(targetInput.value);
            let realisasi = parseNumber(realisasiInput.value);
            let bobot     = parseNumber(bobotInput.value);
            let polaritas = polaritasInput.value;

            let pencapaian = 0;

            // Logika Cek Kekosongan
            if (target === 0 && realisasi === 0) {
                pencapaian = 0;
            } else {
                if (target === 0) {
                     if (polaritas === 'Negatif' || polaritas === 'Minimize') pencapaian = 0;
                     else pencapaian = 0;
                } else {
                    if (polaritas === 'Positif' || polaritas === 'Maximize') {
                        pencapaian = (realisasi / target) * 100;
                    } else if (polaritas === 'Negatif' || polaritas === 'Minimize') {
                        let ratio = realisasi / target;
                        pencapaian = (2 - ratio) * 100;
                    } else if (polaritas === 'Yes/No') {
                        pencapaian = (realisasi >= target) ? 100 : 0;
                    }
                }
            }

            if (isNaN(pencapaian) || !isFinite(pencapaian)) pencapaian = 0;
            if (pencapaian < 0) pencapaian = 0;

            let nilaiAkhir = (pencapaian * bobot) / 100;

            skorEl.textContent = formatNumber(pencapaian).replace('.', ',');
            akhirEl.textContent = formatNumber(nilaiAkhir).replace('.', ',');

            return nilaiAkhir;
        }

        function calculateGrandTotal() {
            let total = 0;
            rows.forEach(row => {
                total += calculateRow(row);
            });
            
            // Update Angka Total
            grandTotalEl.textContent = formatNumber(total).replace('.', ',');
            
            // Update Grade secara Real-time
            updateGrade(total);
        }

        rows.forEach(row => {
            const inputs = row.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('input', calculateGrandTotal);
            });
        });

        // Hitung saat load pertama kali
        calculateGrandTotal();
    });
</script>

</body>
</html>