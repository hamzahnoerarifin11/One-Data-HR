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
                            <td colspan="4" class="p-4 text-right font-bold uppercase text-gray-600 dark:text-gray-300">TOTAL SKOR AKHIR</td>
                            <td class="p-4 text-center font-bold text-2xl text-blue-700 dark:text-blue-400">
                                <span id="grand-total">{{ $kpi->total_skor_akhir }}</span>
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

        // --- HELPER: Mengubah format "0,5" atau "10%" menjadi angka desimal ---
        function parseNumber(val) {
            if (!val) return 0;
            // Ubah string jadi string biasa, hapus %, ganti Koma jadi Titik
            let cleanStr = val.toString().replace('%', '').replace(',', '.');
            return parseFloat(cleanStr) || 0;
        }

        // --- HELPER: Mengubah angka jadi format tampilan (2 desimal) ---
        function formatNumber(num) {
            // Tampilkan 2 desimal, ganti Titik jadi Koma (opsional, biar indonesia banget)
            return num.toFixed(2).replace('.', ',');
        }

        function calculateRow(row) {
            // 1. Ambil Elemen Input
            const targetInput = row.querySelector('.input-target');
            const realisasiInput = row.querySelector('.input-realisasi');
            const bobotInput = row.querySelector('.input-bobot');
            const polaritasInput = row.querySelector('.input-polaritas');
            
            const skorEl = row.querySelector('.text-skor');
            const akhirEl = row.querySelector('.text-akhir');

            // 2. Cek apakah input "Masih Kosong" (String kosong)
            // Tujuannya membedakan antara "Belum diisi" dengan "User mengetik 0"
            const isTargetEmpty = targetInput.value.trim() === '';
            const isRealisasiEmpty = realisasiInput.value.trim() === '';

            // Ambil Nilai Angka (Kalau kosong dianggap 0)
            let target    = parseNumber(targetInput.value);
            let realisasi = parseNumber(realisasiInput.value);
            let bobot     = parseNumber(bobotInput.value);
            let polaritas = polaritasInput.value;

            let pencapaian = 0;

            // --- LOGIKA BARU: CEK KEKOSONGAN ---
            // Jika Target 0 DAN Realisasi 0 (Kasus baris belum diisi / default), paksa 0.
            // Ini akan menimpa logika "Negatif 0/0 = 100" agar tidak membingungkan saat form baru dibuka.
            if (target === 0 && realisasi === 0) {
                pencapaian = 0;
            } 
            // Jika salah satu ada isinya, baru hitung rumus
            else {
                // KASUS KHUSUS: Jika Target 0 tapi Realisasi ada isinya (misal target 0 kecelakaan, tapi terjadi 1)
                if (target === 0) {
                     if (polaritas === 'Negatif' || polaritas === 'Minimize') {
                        // Target 0, Realisasi > 0 (Jelek) -> Skor 0 (atau minus, tergantung kebijakan)
                        // Target 0, Realisasi 0 (Bagus) -> Skor 100 (TAPI ini sudah dihandle if diatas jika dua-duanya 0)
                        // Jadi kalau lolos ke sini berarti Realisasi > 0, maka skor 0.
                        pencapaian = 0;
                     } else {
                        pencapaian = 0; // Positif target 0 = skor 0
                     }
                } 
                // KASUS NORMAL: Target > 0
                else {
                    if (polaritas === 'Positif' || polaritas === 'Maximize') {
                        pencapaian = (realisasi / target) * 100;
                    } 
                    else if (polaritas === 'Negatif' || polaritas === 'Minimize') {
                        // Rumus Linear (200% - Ratio)
                        let ratio = realisasi / target;
                        pencapaian = (2 - ratio) * 100;
                    } 
                    else if (polaritas === 'Yes/No') {
                        pencapaian = (realisasi >= target) ? 100 : 0;
                    }
                }
            }

            // 4. VALIDASI TERAKHIR (Jaring Pengaman NaN)
            if (isNaN(pencapaian) || !isFinite(pencapaian)) {
                pencapaian = 0;
            }
            
            // Opsional: Skor tidak boleh minus
            if (pencapaian < 0) pencapaian = 0;

            // 5. Hitung Nilai Akhir
            let nilaiAkhir = (pencapaian * bobot) / 100;

            // 6. Update Tampilan
            skorEl.textContent = formatNumber(pencapaian);
            akhirEl.textContent = formatNumber(nilaiAkhir);

            return nilaiAkhir;
        }

        function calculateGrandTotal() {
            let total = 0;
            rows.forEach(row => {
                total += calculateRow(row);
            });
            // Update Total Besar di Bawah
            grandTotalEl.textContent = formatNumber(total);
        }

        // Event Listener: Hitung ulang setiap kali user mengetik
        rows.forEach(row => {
            const inputs = row.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('input', calculateGrandTotal);
            });
        });

        // Hitung awal saat halaman pertama dimuat
        calculateGrandTotal();
    });
</script>

</body>
</html>