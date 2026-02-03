@extends('layouts.app')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <h2 class="text-xl font-bold mb-4">Buat & Tetapkan Template KPI untuk Semua Karyawan (Tahun {{ $tahun }})</h2>

    <form action="{{ route('kpi.bulk-store') }}" method="POST" id="bulk-kpi-form">
        @csrf
        <input type="hidden" name="tahun" value="{{ $tahun }}">

        <div class="mb-4">
            <label class="text-sm font-semibold">Nama Template (opsional)</label>
            <input type="text" name="template_name" class="w-full mt-1 px-3 py-2 border rounded" placeholder="Contoh: KPI Umum 2026">
        </div>

        <div class="mb-3">
            <div class="flex justify-between items-center mb-2">
                <h3 class="font-bold">Daftar Indikator</h3>
                <button type="button" id="add-row" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Tambah Indikator</button>
            </div>

            <table class="w-full border-collapse text-sm" id="items-table">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2">#</th>
                        <th class="p-2">KRA</th>
                        <th class="p-2">KPI</th>
                        <th class="p-2">Units</th>
                        <th class="p-2">Polaritas</th>
                        <th class="p-2">Bobot (%)</th>
                        <th class="p-2">Target</th>
                        <th class="p-2">Perspektif</th>
                        <th class="p-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="item-row">
                        <td class="p-2">1</td>
                        <td class="p-2"><input type="text" name="items[0][key_result_area]" class="w-full px-2 py-1 border rounded" required></td>
                        <td class="p-2"><input type="text" name="items[0][key_performance_indicator]" class="w-full px-2 py-1 border rounded" required></td>
                        <td class="p-2"><input type="text" name="items[0][units]" class="w-full px-2 py-1 border rounded" placeholder="Contoh: Presentase"></td>
                        <td class="p-2">
                            <select name="items[0][polaritas]" class="px-2 py-1 border rounded">
                                <option value="MAX">Max</option>
                                <option value="MIN">Min</option>
                            </select>
                        </td>
                        <td class="p-2"><input type="number" name="items[0][bobot]" class="w-20 px-2 py-1 border rounded" min="0" max="100" required></td>
                        <td class="p-2"><input type="number" name="items[0][target]" class="w-20 px-2 py-1 border rounded" min="0" max="100" required></td>
                        <td class="p-2"><input type="text" name="items[0][perspektif]" class="w-full px-2 py-1 border rounded" placeholder="Contoh: Keuangan"></td>
                        <td class="p-2"><button type="button" class="remove-row bg-red-500 text-white px-2 py-1 rounded text-xs">Hapus</button></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('kpi.index', ['tahun' => $tahun]) }}" class="px-4 py-2 border rounded text-sm">Batal</a>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded text-sm">Simpan & Tetapkan ke Semua Karyawan</button>
        </div>
    </form>
</div>

<script>
    (function(){
        let idx = 1;
        const addBtn = document.getElementById('add-row');
        const tbody = document.querySelector('#items-table tbody');

        addBtn.addEventListener('click', function(){
            const tr = document.createElement('tr');
            tr.classList.add('item-row');
            tr.innerHTML = `
                <td class="p-2">${idx+1}</td>
                <td class="p-2"><input type="text" name="items[${idx}][key_result_area]" class="w-full px-2 py-1 border rounded" required></td>
                <td class="p-2"><input type="text" name="items[${idx}][key_performance_indicator]" class="w-full px-2 py-1 border rounded" required></td>
                <td class="p-2"><input type="number" name="items[${idx}][bobot]" class="w-20 px-2 py-1 border rounded" min="0" max="100" required></td>
                <td class="p-2"><input type="text" name="items[${idx}][perspektif]" class="w-full px-2 py-1 border rounded" placeholder="Contoh: Keuangan"></td>
                <td class="p-2">
                    <select name="items[${idx}][polaritas]" class="px-2 py-1 border rounded">
                        <option value="MAX">Max</option>
                        <option value="MIN">Min</option>
                    </select>
                </td>
                <td class="p-2"><button type="button" class="remove-row bg-red-500 text-white px-2 py-1 rounded text-xs">Hapus</button></td>
            `;
            tbody.appendChild(tr);
            idx++;
        });

        document.addEventListener('click', function(e){
            if (e.target && e.target.classList.contains('remove-row')) {
                const row = e.target.closest('tr');
                row.remove();
                // reindex rows
                document.querySelectorAll('#items-table tbody tr').forEach((r, i) => r.querySelector('td').innerText = i+1);
            }
        });
    })();
</script>
@endsection