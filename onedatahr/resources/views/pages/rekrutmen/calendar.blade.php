@extends('layouts.app')

@section('title','Dashboard Rekrutmen')

@section('content')
<div class="px-4 py-6">
    <x-rekrutmen.card title="Dashboard Pelamar Per Posisi">
        <x-slot name="actions">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <label class="text-xs font-semibold text-gray-500 uppercase">Tahap:</label>
                    <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                    <select id="stage-select" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                        <option value="total_pelamar">Total Pelamar</option>
                        <option value="lolos_cv">Lolos CV</option>
                        <option value="lolos_psikotes">Lolos Psikotes</option>
                        <option value="lolos_kompetensi">Lolos Kompetensi</option>
                        <option value="lolos_hr">Lolos HR</option>
                        <option value="lolos_user">Lolos User</option>
                    </select>
                    <span
                        class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                        <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                    </span>
                    </div>
                </div>

                <div class="h-8 w-[1px] bg-gray-200 mx-1"></div>
                    <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                             <select id="month-select" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                            </select>
                            <span
                                class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                            </span>
                    </div>
                    <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                    <select id="year-select" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true"></select>
                    <span
                                class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                            </span>
                    </div>
                <button id="refresh-calendar" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 dark:text-white text-sm font-medium rounded-md transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Refresh
                </button>
            </div>
        </x-slot>

        <div class="overflow-x-auto shadow-sm border border-gray-200 rounded-xl">
            <table class="w-full text-[12px] border-separate border-spacing-0" id="rekrutmen-table">
                <thead>
                    <tr class="bg-gray-50">
                        <th rowspan="2" class="sticky left-0 z-20 bg-gray-50 border-b border-r p-3 text-center font-bold text-gray-600">No.</th>
                        <th rowspan="2" class="sticky left-[45px] z-20 bg-gray-50 border-b border-r p-3 min-w-[220px] text-left font-bold text-gray-600 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">Posisi</th>
                        <th id="month-header" class="border-b border-r p-2 bg-purple-700 uppercase font-bold text-center tracking-wider"></th>
                        <th rowspan="2" class="sticky right-0 z-20 bg-blue-50 border-b border-l p-3 min-w-[70px] text-center font-bold text-blue-700 shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.1)]">Total</th>
                    </tr>
                    <tr id="header-row-days" class="bg-purple-600 dark:text-white text-center font-medium">
                        </tr>
                </thead>
                <tbody id="table-body" class="divide-y divide-gray-100">
                    </tbody>
                <tfoot id="table-footer" class="bg-gray-50 font-bold sticky bottom-0 z-30">
                    </tfoot>
            </table>
        </div>
    </x-rekrutmen.card>
</div>

<x-modal id="edit-daily" title="Input Data Harian" size="sm" :showFooter="false">
    <form id="daily-form" class="p-5">
        <div class="mb-5 p-3 bg-purple-50 rounded-lg border border-purple-100">
            <p class="text-[10px] text-purple-600 font-bold uppercase tracking-widest mb-1">
                Update Progres Untuk:
            </p>
            <p class="text-sm font-bold text-gray-800 dark:text-white" id="modal-info"></p>
        </div>

        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">
                    Jumlah Baru
                </label>
                <input
                    id="input_value"
                    type="number"
                    min="0"
                    required
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 py-2.5 px-4 border transition-all"
                    placeholder="0">
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-8 pt-4 border-t">
            <button
                type="button"
                class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium transition-colors"
                onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:{id:'edit-daily'}}))">
                Batal
            </button>

            <!-- ✅ TOMBOL MODAL BAWAAN (SUBMIT) -->
            <button
                type="submit"
                id="modal-submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all active:scale-95">
                Simpan
            </button>
        </div>
    </form>
</x-modal>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('table-body');
    const tableFooter = document.getElementById('table-footer');
    const headerDays = document.getElementById('header-row-days');
    const monthHeader = document.getElementById('month-header');
    const monthSel = document.getElementById('month-select');
    const yearSel = document.getElementById('year-select');
    const stageSel = document.getElementById('stage-select');

    const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const now = new Date();
    let positions = @json(App\Models\Posisi::orderBy('nama_posisi')->get());
    let editingData = { posisi_id: null, date: null };

    // 1. Inisialisasi Dropdown Bulan
    months.forEach((m, i) => {
        const o = document.createElement('option');
        o.value = i + 1;
        o.text = m;
        if(i + 1 === now.getMonth() + 1) o.selected = true;
        monthSel.appendChild(o);
    });

    // Inisialisasi Dropdown Tahun (DINAMIS: Tidak mentok di 2026)
    const startYear = now.getFullYear() - 5;
    const endYear = now.getFullYear() + 5;
    for(let y = startYear; y <= endYear; y++){
        const o = document.createElement('option');
        o.value = y;
        o.text = y;
        if(y === now.getFullYear()) o.selected = true;
        yearSel.appendChild(o);
    }

    // 2. Fungsi Render Utama
    async function renderTable() {
        const month = parseInt(monthSel.value);
        const year = parseInt(yearSel.value);
        const currentStage = stageSel.value;
        const daysInMonth = new Date(year, month, 0).getDate();

        // Header Style TailAdmin
        monthHeader.innerText = `${months[month-1].toUpperCase()} ${year}`;
        monthHeader.colSpan = daysInMonth;

        headerDays.innerHTML = '';
        for(let d=1; d<=daysInMonth; d++) {
            headerDays.innerHTML += `<th class="border-b border-r border-stroke dark:border-strokedark p-1.5 w-10 text-center text-[10px] font-bold text-white uppercase tracking-wider bg-blue-600">${d}</th>`;
        }

        try {
            const res = await fetch(`{{ route('rekrutmen.daily.index') }}?month=${month}&year=${year}`, {
                headers: { 'Accept': 'application/json' }
            });
            const apiData = await res.json();

            const dataMap = {};
            apiData.forEach(d => {
                const dOnly = d.date.split('T')[0];
                if(!dataMap[d.posisi_id]) dataMap[d.posisi_id] = {};
                dataMap[d.posisi_id][dOnly] = d[currentStage] || 0;
            });

            tableBody.innerHTML = '';
            let dailyTotals = Array(daysInMonth).fill(0);
            let grandTotal = 0;

            positions.forEach((p, index) => {
                let rowTotal = 0;
                let cellsHtml = '';

                for(let d=1; d<=daysInMonth; d++) {
                    const dateStr = `${year}-${String(month).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
                    const val = dataMap[p.id_posisi]?.[dateStr] || 0;

                    rowTotal += val;
                    dailyTotals[d-1] += val;

                    // Hover effect & color logic TailAdmin hover:bg-primary/5
                    // <tr class="hover:bg-gray-50 dark:hover:bg-meta-4/10">
                    cellsHtml += `
                        <td class="border-b border-r border-stroke dark:border-strokedark p-1.5 cursor-pointer transition-colors text-center ${val > 0 ? 'font-bold text-primary dark:text-white bg-primary/5' : 'text-gray-400 dark:text-gray-600'}" 
                            onclick="openEdit(${p.id_posisi}, '${p.nama_posisi.replace(/'/g, "\\'")}', '${dateStr}', ${val})">
                            ${val > 0 ? val : '-'}
                        </td>`;
                }

                tableBody.innerHTML += `
                    <tr>
                        <td class="sticky left-0 z-10 border-b border-r border-stroke dark:border-strokedark p-2 text-center dark:bg-boxdark text-gray-400 font-medium text-[11px]">${index + 1}</td>
                        <td class="sticky left-[44px] z-10 border-b border-r border-stroke dark:border-strokedark p-2 text-left font-semibold text-black dark:text-white dark:bg-boxdark truncate shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] text-[11px]">${p.nama_posisi}</td>
                        ${cellsHtml}
                        <td class="sticky right-0 z-10 border-b border-l border-stroke dark:border-strokedark p-2 bg-blue-50 dark:bg-meta-4 font-bold text-center text-primary shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.1)]">${rowTotal}</td>
                    </tr>`;
                grandTotal += rowTotal;
            });

            // Footer Totals (Style Meta-4 TailAdmin)
            let footerHtml = `
                <tr class="dark:bg-meta-4 text-black/90">
                    <td colspan="2" class="sticky left-0 z-20 border-r border-stroke dark:border-strokedark p-3 text-right font-bold uppercase tracking-wider text-[10px]">Total Per Hari</td>`;
            
            dailyTotals.forEach(t => {
                footerHtml += `<td class="border-r border-stroke text-black dark:border-strokedark p-1 text-center font-bold ${t > 0 ? 'text-primary' : 'text-gray-400'}">${t}</td>`;
            });
            
            footerHtml += `
                    <td class="sticky right-0 z-20 p-3 text-center bg-primary text-black font-black text-[14px] shadow-[-2px_0_10px_rgba(0,0,0,0.1)]">${grandTotal}</td>
                </tr>`;
            tableFooter.innerHTML = footerHtml;

        } catch (e) {
            console.error("Error loading table:", e);
        }
    }

    // 3. Modal & Save Logic
    window.openEdit = function(id, name, date, val) {
        editingData = { posisi_id: id, date: date };
        const stageLabel = stageSel.options[stageSel.selectedIndex].text;
        document.getElementById('modal-info').innerText = `${name} | ${date} (${stageLabel})`;
        document.getElementById('input_value').value = val;
        window.dispatchEvent(new CustomEvent('open-modal', {detail: {id: 'edit-daily'}}));
    };

    // document.getElementById('save-daily').onclick = async function() {
    //     const val = document.getElementById('input_value').value;
    //     const currentStage = stageSel.value;
    //     const btn = this;

    //     btn.disabled = true;
    //     const originalText = btn.innerText;
    //     btn.innerHTML = '<span class="inline-block animate-spin mr-2">↻</span> Menyimpan...';

    //     try {
    //         const bodyData = {
    //             posisi_id: editingData.posisi_id,
    //             date: editingData.date,
    //         };
    //         bodyData[currentStage] = val;

    //         const r = await fetch('{{ route("rekrutmen.daily.store") }}', {
    //             method: 'POST',
    //             headers: {
    //                 'Content-Type': 'application/json',
    //                 'Accept': 'application/json',
    //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    //             },
    //             body: JSON.stringify(bodyData)
    //         });

    //         if(r.ok) {
    //             window.dispatchEvent(new CustomEvent('close-modal', {detail: {id: 'edit-daily'}}));
    //             await renderTable();
    //         } else {
    //             const error = await r.json();
    //             alert('Error: ' + (error.message || 'Gagal menyimpan data.'));
    //         }
    //     } catch(e) {
    //         console.error(e);
    //     } finally {
    //         btn.disabled = false;
    //         btn.innerText = originalText;
    //     }
    // };
    document.getElementById('daily-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const val = document.getElementById('input_value').value;
    const currentStage = stageSel.value;
    const btn = document.getElementById('modal-submit');

    btn.disabled = true;
    const originalText = btn.innerText;
    btn.innerHTML = '<span class="inline-block animate-spin mr-2">↻</span> Menyimpan...';

    try {
        const bodyData = {
            posisi_id: editingData.posisi_id,
            date: editingData.date,
        };
        bodyData[currentStage] = val;

        const r = await fetch(`{{ route('rekrutmen.daily.store') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content')
            },
            body: JSON.stringify(bodyData)
        });

        if (!r.ok) {
            const error = await r.json();
            throw new Error(error.message || 'Gagal menyimpan data');
        }

        window.dispatchEvent(new CustomEvent('close-modal', {
            detail: { id: 'edit-daily' }
        }));

        await renderTable();

    } catch (err) {
        alert(err.message);
    } finally {
        btn.disabled = false;
        btn.innerText = originalText;
    }
});


    // Event Listeners
    monthSel.onchange = renderTable;
    yearSel.onchange = renderTable;
    stageSel.onchange = renderTable;
    document.getElementById('refresh-calendar').onclick = function() {
        const icon = this.querySelector('svg');
        if(icon) icon.classList.add('animate-spin');
        renderTable().finally(() => {
            if(icon) setTimeout(() => icon.classList.remove('animate-spin'), 500);
        });
    };

    renderTable();
});
</script>

<style>
    /* Custom Scrollbar for better TailAdmin feel */
    .overflow-x-auto::-webkit-scrollbar {
        height: 8px;
    }
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 4px;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #a855f7;
    }
</style>
@endsection
