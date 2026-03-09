<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <title>Penilaian KBI (Budaya Kerja)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* Styling untuk Radio Button Custom */
        .radio-score:checked + label {
            background-color: #3b82f6; /* Blue-500 */
            color: white;
            border-color: #3b82f6;
        }
    </style>
</head>

    {{-- Data dari Controller dikonversi ke JSON untuk Alpine --}}
    @php
        // Grouping items by Kategori agar mudah di loop
        $groupedItems = $kbiItems->groupBy('kategori');
    @endphp

    <body class="bg-gray-50 text-gray-800 p-4 md:p-8 font-sans"
        x-data="kbiForm()">

    <div class="max-w-5xl mx-auto">
        
        {{-- Header --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Form Penilaian Perilaku (KBI)</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Menilai: <span class="font-bold text-blue-600">{{ $karyawan->Nama_Lengkap_Sesuai_Ijazah }}</span> | 
                    Sebagai: <span class="font-bold text-orange-600 uppercase">{{ $tipe_penilai }}</span>
                </p>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500">Rata-rata Total</div>
                <div class="text-3xl font-bold text-blue-700" x-text="grandAverage">0.0</div>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ route('kbi.store') }}" method="POST">
            @csrf
            <input type="hidden" name="karyawan_id" value="{{ $karyawan->id }}">
            <input type="hidden" name="tipe_penilai" value="{{ $tipe_penilai }}">

            <div class="space-y-6">
                @foreach($groupedItems as $kategori => $items)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    {{-- Header Kategori --}}
                    <div class="bg-gray-100 p-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="font-bold text-lg text-gray-700 flex items-center gap-2">
                            <i class="fas fa-tag text-blue-500"></i> {{ $kategori }}
                        </h3>
                        <div class="text-xs font-semibold bg-blue-100 text-blue-700 px-3 py-1 rounded-full">
                            Rata-rata: <span x-text="getCategoryAvg('{{ $kategori }}')">0</span>
                        </div>
                    </div>

                    {{-- List Pertanyaan --}}
                    <div class="p-4">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-gray-500 text-xs uppercase text-center border-b">
                                    <th class="text-left py-2 w-3/5">Indikator Perilaku</th>
                                    <th class="w-1/5">Penilaian</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($items as $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-4 pr-4 align-middle">
                                        <p class="text-gray-800 font-medium">{{ $item->perilaku }}</p>
                                    </td>
                                    <td class="py-4 align-middle">
                                        {{-- Radio Button 1-4 --}}
                                        <div class="flex justify-center gap-2">
                                            @for($i=1; $i<=4; $i++)
                                            <div class="relative">
                                                <input type="radio" 
                                                    name="scores[{{ $item->id_kbi_item }}]" 
                                                    id="score_{{ $item->id_kbi_item }}_{{ $i }}" 
                                                    value="{{ $i }}" 
                                                    class="radio-score sr-only"
                                                    x-model.number="answers['{{ $item->id_kbi_item }}']"
                                                    @change="calculate()"
                                                    required>
                                                <label for="score_{{ $item->id_kbi_item }}_{{ $i }}" 
                                                    class="block w-10 h-10 leading-10 text-center border rounded-full cursor-pointer hover:bg-blue-50 transition font-bold text-gray-500">
                                                    {{ $i }}
                                                </label>
                                                {{-- Tooltip Penjelasan --}}
                                                <div class="text-[10px] text-center text-gray-400 mt-1">
                                                    @if($i==1) Kurang @elseif($i==2) Cukup @elseif($i==3) Baik @else S.Baik @endif
                                                </div>
                                            </div>
                                            @endfor
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Footer Actions --}}
            <div class="mt-8 flex justify-end gap-4 pb-10">
                <button type="button" onclick="history.back()" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg shadow hover:bg-blue-700 transition transform hover:scale-105">
                    Simpan Penilaian
                </button>
            </div>
        </form>
    </div>


    <script>
        function kbiForm() {
            return {
                // Menyimpan jawaban: { id_item: score }
                answers: {},
                
                // Mapping kategori ke list ID item (di-generate dari PHP)
                categories: {
                    @foreach($groupedItems as $kategori => $items)
                        '{{ $kategori }}': [{{ $items->pluck('id_kbi_item')->implode(',') }}],
                    @endforeach
                },

                grandAverage: '0.00',

                // Hitung rata-rata
                calculate() {
                    let totalScore = 0;
                    let totalCount = 0;

                    // Hitung total semua jawaban yang terisi
                    for (let key in this.answers) {
                        totalScore += parseFloat(this.answers[key]);
                        totalCount++;
                    }

                    this.grandAverage = totalCount > 0 
                        ? (totalScore / totalCount).toFixed(2) 
                        : '0.00';
                },

                // Hitung rata-rata per kategori
                getCategoryAvg(categoryName) {
                    let ids = this.categories[categoryName];
                    let sum = 0;
                    let count = 0;

                    ids.forEach(id => {
                        if (this.answers[id]) {
                            sum += parseFloat(this.answers[id]);
                            count++;
                        }
                    });

                    return count > 0 ? (sum / count).toFixed(1) : '0';
                }
            }
        }
    </script>
</body>
</html>