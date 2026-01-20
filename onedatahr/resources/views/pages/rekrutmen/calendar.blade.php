@extends('layouts.app')

@section('title','Dashboard Harian Rekrutmen')

@section('content')
<div class="px-4 py-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            Monitoring Harian
        </h2>
        <p class="text-sm text-gray-500">Rekap data harian per posisi (Manual & Otomatis)</p>
    </div>

    <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold uppercase text-gray-500">Filter Data:</span>
            <select id="stage-select" class="h-10 rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm font-semibold text-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">
                <option value="total_pelamar">🟢 Total Pelamar (Manual)</option>
                <option value="lolos_cv">🔵 Lolos CV</option>
                <option value="lolos_psikotes">🟣 Lolos Psikotes</option>
                <option value="lolos_kompetensi">🟠 Lolos Kompetensi</option>
                <option value="lolos_hr">🔴 Lolos HR</option>
                <option value="lolos_user">⚫ Lolos User</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <select id="month-select" class="h-10 rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm text-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500"></select>
            
            <select id="year-select" class="h-10 rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm text-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500"></select>
            
            <button id="refresh-calendar" class="h-10 px-4 flex items-center gap-2 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-medium transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                <span>Refresh</span>
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs border-separate border-spacing-0" id="rekrutmen-table">
                <thead>
                    <tr>
                        <th rowspan="2" class="sticky left-0 z-30 bg-gray-50 border-b border-r border-gray-200 p-3 text-center font-bold text-gray-600 w-12">No</th>
                        <th rowspan="2" class="sticky left-12 z-30 bg-gray-50 border-b border-r border-gray-200 p-3 text-left font-bold text-gray-600 min-w-[200px] shadow-[4px_0_10px_-4px_rgba(0,0,0,0.1)]">
                            Posisi
                        </th>
                        <th id="month-header" class="border-b border-r border-purple-800 p-2 bg-purple-700 text-white font-bold text-center tracking-wider uppercase"></th>
                        
                        <th rowspan="2" class="sticky right-[80px] z-30 bg-blue-600 border-b border-l border-blue-700 p-2 min-w-[80px] text-center font-bold text-white shadow-[-4px_0_10px_-4px_rgba(0,0,0,0.2)]">
                            Total<br>Bulan
                        </th>
                        <th rowspan="2" class="sticky right-0 z-30 bg-yellow-500 border-b border-l border-yellow-600 p-2 min-w-[80px] text-center font-bold text-white shadow-[-4px_0_10px_-4px_rgba(0,0,0,0.2)]">
                            Total<br>Tahun
                        </th>
                    </tr>
                    <tr id="header-row-days" class="bg-purple-600 text-white text-center font-medium">
                        </tr>
                </thead>
                <tbody id="table-body" class="divide-y divide-gray-100 bg-white dark:bg-gray-800 dark:divide-gray-700">
                    </tbody>
                <tfoot id="table-footer" class="bg-gray-100 font-bold sticky bottom-0 z-30 shadow-[0_-4px_10px_-4px_rgba(0,0,0,0.1)]">
                    </tfoot>
            </table>
        </div>

        <div class="flex flex-wrap gap-6 items-center p-4 bg-gray-50 border-t border-gray-200 text-xs">
            <span class="font-bold uppercase text-gray-500 tracking-wider">Keterangan:</span>
            
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-green-500 rounded-sm"></div>
                <span class="text-gray-700 font-medium">Input Manual (Total Pelamar)</span>
            </div>
            
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-blue-500 rounded-sm"></div>
                <span class="text-gray-700 font-medium">Data Otomatis (Lolos Tahapan)</span>
            </div>

            <div class="flex items-center gap-2 ml-auto text-gray-400 italic">
                *Klik pada angka berwarna hijau untuk mengedit data manual.
            </div>
        </div>
    </div>
</div>

<x-modal id="edit-daily" title="Update Data Harian" size="sm" :showFooter="false">
    <form id="daily-form" class="p-6">
        <div id="modal-info-box" class="mb-6 rounded-lg border p-4">
            </div>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">
                    Jumlah Data
                </label>
                <input
                    id="input_value"
                    type="number"
                    min="0"
                    required
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 py-3 px-4 text-lg font-bold text-center transition-all disabled:bg-gray-100 disabled:text-gray-400"
                    placeholder="0">
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-100">
            <button
                type="button"
                class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:{id:'edit-daily'}}))">
                Tutup
            </button>

            <button
                type="submit"
                id="modal-submit"
                class="px-6 py-2.5 text-sm font-bold text-white bg-green-600 hover:bg-green-700 rounded-lg shadow-lg shadow-green-200 transition-all active:scale-95">
                Simpan Perubahan
            </button>
        </div>
    </form>
</x-modal>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- ELEMENT REFERENCES ---
    const tableBody = document.getElementById('table-body');
    const tableFooter = document.getElementById('table-footer');
    const headerDays = document.getElementById('header-row-days');
    const monthHeader = document.getElementById('month-header');
    
    // --- SAFETY CHECK (PENTING) ---
    const monthSel = document.getElementById('month-select');
    const yearSel = document.getElementById('year-select');
    const stageSel = document.getElementById('stage-select');

    if(!monthSel || !yearSel || !stageSel) {
        console.error("FATAL ERROR: Filter elements not found in DOM.");
        alert("Terjadi kesalahan tampilan. Elemen filter tidak ditemukan.");
        return;
    }

    // --- CONFIG ---
    const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const now = new Date();
    // Ambil data posisi dari backend
    let positions = @json(App\Models\Posisi::orderBy('nama_posisi')->get());
    let editingData = { posisi_id: null, date: null };

    // --- INITIALIZATION ---
    // Populate Month
    if (monthSel.options.length === 0) {
        months.forEach((m, i) => {
            const o = document.createElement('option');
            o.value = i + 1;
            o.text = m;
            if(i + 1 === now.getMonth() + 1) o.selected = true;
            monthSel.appendChild(o);
        });
    }

    // Populate Year
    if (yearSel.options.length === 0) {
        const currentYear = now.getFullYear();
        for(let y = currentYear - 2; y <= currentYear + 2; y++){
            const o = document.createElement('option');
            o.value = y;
            o.text = y;
            if(y === currentYear) o.selected = true;
            yearSel.appendChild(o);
        }
    }

    // --- MAIN RENDER FUNCTION ---
    async function renderTable() {
        const month = parseInt(monthSel.value);
        const year = parseInt(yearSel.value);
        const currentStage = stageSel.value; 
        const daysInMonth = new Date(year, month, 0).getDate();
        const isManual = currentStage === 'total_pelamar';

        // Update UI Headers
        monthHeader.innerText = `${months[month-1].toUpperCase()} ${year}`;
        monthHeader.colSpan = daysInMonth;

        // Render Angka Tanggal
        headerDays.innerHTML = '';
        for(let d=1; d<=daysInMonth; d++) {
            const dateObj = new Date(year, month-1, d);
            const isSunday = dateObj.getDay() === 0;
            const bgClass = isSunday ? 'bg-red-500 text-white' : 'bg-purple-600 text-purple-100';
            headerDays.innerHTML += `<th class="border-b border-r border-purple-500/30 p-1 w-10 text-center text-[10px] font-bold ${bgClass}">${d}</th>`;
        }

        tableBody.innerHTML = `<tr><td colspan="${daysInMonth + 4}" class="p-8 text-center text-gray-500 animate-pulse">Memuat data...</td></tr>`;

        try {
            const res = await fetch(`{{ route('rekrutmen.daily.index') }}?year=${year}`, {
                headers: { 'Accept': 'application/json' }
            });
            const apiData = await res.json();

            // Mapping Data
            const dataMap = {};
            const yearlyMap = {};

            if (Array.isArray(apiData)) {
                apiData.forEach(d => {
                    const dateKey = d.date ? d.date.substring(0, 10) : null;
                    if(!dateKey) return;

                    if (!dataMap[d.posisi_id]) dataMap[d.posisi_id] = {};
                    dataMap[d.posisi_id][dateKey] = d;

                    if (!yearlyMap[d.posisi_id]) yearlyMap[d.posisi_id] = 0;
                    yearlyMap[d.posisi_id] += parseInt(d[currentStage]) || 0;
                });
            }

            // Render Rows
            tableBody.innerHTML = '';
            let dailyTotals = Array(daysInMonth).fill(0); 
            let monthlyGrandTotal = 0;
            let yearlyGrandTotal = 0;

            positions.forEach((p, index) => {
                let rowMonthlyTotal = 0;
                let cellsHtml = '';

                for(let d=1; d<=daysInMonth; d++) {
                    const dateStr = `${year}-${String(month).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
                    const rowData = dataMap[p.id_posisi]?.[dateStr];
                    const val = rowData ? (parseInt(rowData[currentStage]) || 0) : 0;

                    rowMonthlyTotal += val;
                    dailyTotals[d-1] += val;

                    let cellClass = "text-gray-300"; 
                    // Escape petik satu pada nama posisi agar JS tidak error
                    const safeName = p.nama_posisi.replace(/'/g, "\\'");
                    let clickAction = `onclick="openEdit(${p.id_posisi}, '${safeName}', '${dateStr}', ${val})"`;

                    if (val > 0) {
                        cellClass = isManual 
                            ? "font-bold text-green-700 bg-green-50 hover:bg-green-100 cursor-pointer border-green-200"
                            : "font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 cursor-pointer border-blue-200";
                    } else {
                        if (isManual) {
                            cellClass = "hover:bg-gray-50 cursor-pointer text-transparent hover:text-gray-400";
                        } else {
                            cellClass = "bg-gray-50/50 cursor-not-allowed"; 
                            clickAction = ""; 
                        }
                    }

                    cellsHtml += `
                        <td class="border-b border-r border-gray-100 p-1.5 text-center transition-colors border-dashed ${cellClass}" ${clickAction}>
                            ${val > 0 ? val : (isManual ? '-' : '')}
                        </td>`;
                }

                tableBody.innerHTML += `
                    <tr class="hover:bg-gray-50 transition-colors group">
                        <td class="sticky left-0 z-10 border-b border-r border-gray-200 p-2 text-center bg-gray-50 group-hover:bg-gray-100 text-[10px] text-gray-500">${index + 1}</td>
                        <td class="sticky left-12 z-10 border-b border-r border-gray-200 p-2 text-left bg-white group-hover:bg-gray-50 truncate text-[11px] font-medium text-gray-700 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                            ${p.nama_posisi}
                        </td>
                        ${cellsHtml}
                        <td class="sticky right-[80px] z-10 border-b border-l border-blue-100 p-2 bg-blue-50 font-bold text-center text-blue-700 shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                            ${rowMonthlyTotal}
                        </td>
                        <td class="sticky right-0 z-10 border-b border-l border-yellow-100 p-2 bg-yellow-50 text-yellow-700 font-bold text-center shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                            ${yearlyMap[p.id_posisi] || 0}
                        </td>
                    </tr>`;

                monthlyGrandTotal += rowMonthlyTotal;
                yearlyGrandTotal += (yearlyMap[p.id_posisi] || 0);
            });

            renderFooter(dailyTotals, monthlyGrandTotal, yearlyGrandTotal);

        } catch (e) {
            console.error(e);
            tableBody.innerHTML = `<tr><td colspan="${daysInMonth + 4}" class="p-4 text-center text-red-500 font-bold">Terjadi kesalahan JS: ${e.message}</td></tr>`;
        }
    }

    function renderFooter(dailyTotals, monthlyGrandTotal, yearlyGrandTotal) {
        let footerHtml = `
            <tr>
                <td colspan="2" class="sticky left-0 z-20 border-r border-gray-200 p-3 text-right text-[10px] uppercase bg-gray-100 text-gray-500 tracking-wider">
                    Total Harian
                </td>
        `;
        dailyTotals.forEach(t => {
            footerHtml += `<td class="border-r border-gray-200 p-1 text-center text-xs ${t > 0 ? 'text-blue-700 bg-blue-50 font-extrabold' : 'text-gray-300'}">${t}</td>`;
        });
        footerHtml += `
            <td class="sticky right-[80px] z-20 p-2 text-center bg-blue-600 text-white text-xs border-l border-blue-700 shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.2)]">${monthlyGrandTotal}</td>
            <td class="sticky right-0 z-20 p-2 text-center bg-yellow-500 text-white text-xs border-l border-yellow-600 shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.2)]">${yearlyGrandTotal}</td>
            </tr>`;
        tableFooter.innerHTML = footerHtml;
    }

    // --- MODAL LOGIC ---
    window.openEdit = function(id, name, date, val) {
        editingData = { posisi_id: id, date: date };
        const stageSel = document.getElementById('stage-select'); // Ambil ulang biar aman
        const currentStage = stageSel.value;
        const stageLabel = stageSel.options[stageSel.selectedIndex].text;
        const isManual = currentStage === 'total_pelamar';

        const inputVal = document.getElementById('input_value');
        const btnSubmit = document.getElementById('modal-submit');
        const infoBox = document.getElementById('modal-info-box');

        inputVal.value = val;
        
        if (isManual) {
            inputVal.disabled = false;
            inputVal.classList.remove('bg-gray-100', 'text-gray-400');
            btnSubmit.style.display = 'inline-flex';
            infoBox.className = "mb-6 rounded-lg border border-green-200 bg-green-50 p-4";
            infoBox.innerHTML = `
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-green-200 rounded-full text-green-700">✏️</div>
                    <div>
                        <h4 class="font-bold text-green-900 text-sm">Input Data Manual</h4>
                        <p class="text-xs text-green-700 mt-1">Posisi: <strong>${name}</strong><br>Tanggal: <strong>${date}</strong></p>
                    </div>
                </div>`;
        } else {
            inputVal.disabled = true;
            inputVal.classList.add('bg-gray-100', 'text-gray-400');
            btnSubmit.style.display = 'none';
            infoBox.className = "mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4";
            infoBox.innerHTML = `
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-blue-200 rounded-full text-blue-700">ℹ️</div>
                    <div>
                        <h4 class="font-bold text-blue-900 text-sm">Data Otomatis</h4>
                        <p class="text-xs text-blue-700 mt-1">Angka <strong>${stageLabel}</strong> ini otomatis dari sistem.</p>
                    </div>
                </div>`;
        }
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'edit-daily' } }));
    };

    // --- FORM SUBMIT ---
    document.getElementById('daily-form').onsubmit = async function(e) {
        e.preventDefault();
        const val = document.getElementById('input_value').value;
        const stageSel = document.getElementById('stage-select');
        const currentStage = stageSel.value;
        const btn = document.getElementById('modal-submit');

        btn.disabled = true; btn.innerHTML = 'Menyimpan...';

        try {
            const response = await fetch(`{{ route('rekrutmen.daily.store') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ posisi_id: editingData.posisi_id, date: editingData.date, [currentStage]: val })
            });

            if(response.ok) {
                window.dispatchEvent(new CustomEvent('close-modal', {detail: {id: 'edit-daily'}}));
                await renderTable();
            } else {
                alert("Gagal menyimpan data.");
            }
        } catch (err) { console.error(err); } 
        finally { btn.disabled = false; btn.innerText = 'Simpan Perubahan'; }
    };

    // --- EVENTS ---
    monthSel.onchange = renderTable;
    yearSel.onchange = renderTable;
    stageSel.onchange = renderTable;
    document.getElementById('refresh-calendar').onclick = renderTable;

    renderTable();
});
</script>

<style>
    .overflow-x-auto::-webkit-scrollbar { height: 10px; }
    .overflow-x-auto::-webkit-scrollbar-track { background: #f8f9fa; }
    .overflow-x-auto::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 6px; border: 2px solid #f8f9fa; }
    .overflow-x-auto::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
</style>
@endsection