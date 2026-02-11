@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-[1200px] p-4 md:p-8">
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-800 dark:text-white tracking-tight">Master Perspektif KPI</h1>
            <p class="mt-2 text-gray-500 dark:text-gray-400 text-lg">
                Kelola perspektif strategis perusahaan untuk penilaian kinerja.
            </p>
        </div>
        <button onclick="openPerspectiveModal()" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-200 dark:shadow-none hover:bg-brand-700 hover:-translate-y-0.5 transition-all duration-200">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Perspektif
        </button>
    </div>

    <!-- Bulk Actions -->
    <div class="mb-6 hidden animate-fade-in-down" id="bulkActionContainer">
        <div class="bg-brand-50 dark:bg-brand-900/20 border border-brand-100 dark:border-brand-800 rounded-xl p-4 flex flex-wrap items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="bg-brand-100 dark:bg-brand-800 p-2 rounded-lg text-brand-600 dark:text-brand-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                </div>
                <div class="text-sm text-brand-900 dark:text-brand-200 font-medium">
                    <span id="selectedCount" class="font-bold text-lg">0</span> perspektif terpilih
                </div>
            </div>
            <button type="button" onclick="confirmBulkDelete()" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-bold hover:bg-error-700 hover:shadow-lg transition-all flex items-center gap-2">
                <i class="fas fa-trash-alt"></i> Hapus Terpilih
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl border border-success-200 bg-success-50 p-4 text-success-800 dark:border-success-900 dark:bg-success-900/20 dark:text-success-400 shadow-sm flex items-start gap-3">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 rounded-xl border border-error-200 bg-error-50 p-4 text-error-800 dark:border-error-900 dark:bg-error-900/20 dark:text-error-400 shadow-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden shadow-sm">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full min-w-full align-middle">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                        <th class="px-6 py-4 w-12 text-center">
                            <input type="checkbox" id="selectAll" class="rounded-md border-gray-300 text-brand-600 focus:ring-brand-500 w-4 h-4 cursor-pointer transition">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama Perspektif</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Penggunaan</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($perspectives as $index => $perspective)
                        @php $usage = $usageCounts[$perspective->name] ?? 0; @endphp
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/30 transition-colors group">
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" class="perspective-checkbox rounded-md border-gray-300 text-brand-600 focus:ring-brand-500 w-4 h-4 cursor-pointer transition user-select-none" value="{{ $perspective->id }}">
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-700 dark:text-gray-200 text-sm group-hover:text-brand-600 transition-colors">{{ $perspective->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($perspective->is_active)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-success-50 px-2.5 py-1 text-xs font-semibold text-success-700 dark:bg-success-900/30 dark:text-success-400 border border-success-100 dark:border-success-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-success-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-400 border border-gray-200 dark:border-gray-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- <div class="h-2 w-24 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-brand-500 rounded-full" style="width: {{ min($usage * 2, 100) }}%"></div>
                                    </div> -->
                                    <span class="text-xs text-gray-500 font-medium">{{ $usage }} item</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2 transition-opacity">
                                    <button
                                        onclick="openPerspectiveModal({{ $perspective->id }}, '{{ addslashes($perspective->name) }}', {{ $perspective->is_active ? 'true' : 'false' }})"
                                        title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffd500" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen-icon lucide-square-pen"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"/></svg>
                                    </button>

                                    <form method="POST" action="{{ route('kpi.perspectives.toggle', $perspective) }}">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center justify-center rounded-lg p-2 transition
                                            {{ $perspective->is_active
                                                ? 'text-success-500 hover:bg-success-50 dark:hover:bg-success-900/20'
                                                : 'text-error-500 hover:bg-error-50 dark:hover:bg-error-900/20'
                                            }}"
                                            title="{{ $perspective->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-power-icon lucide-square-power"><path d="M12 7v4"/><path d="M7.998 9.003a5 5 0 1 0 8-.005"/><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-full mb-3">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                    </div>
                                    <h3 class="text-gray-900 dark:text-white font-medium mb-1">Belum ada data</h3>
                                    <p class="text-gray-500 text-sm">Mulai dengan menambahkan perspektif baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modern Modal -->
<div id="perspectiveModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div onclick="closePerspectiveModal()" class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"></div>
    
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative w-full max-w-md rounded-2xl bg-white dark:bg-gray-800 shadow-2xl ring-1 ring-black/5 transform transition-all scale-100 p-6">
            <div class="mb-5">
                <h2 id="perspectiveModalTitle" class="text-xl font-bold text-gray-800 dark:text-white">Tambah Perspektif</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Atur nama dan status perspektif penilaian.</p>
            </div>

            <form id="perspectiveForm" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" id="perspectiveMethod" name="_method" value="POST">

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Nama Perspektif</label>
                    <input type="text" id="perspectiveName" name="name" 
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:placeholder-gray-500" 
                        placeholder="Contoh: Financial, Customer..." required>
                </div>

                <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-900/50 p-3 rounded-lg border border-gray-100 dark:border-gray-700">
                    <input type="checkbox" id="perspectiveActive" name="is_active" value="1" class="h-5 w-5 rounded border-gray-300 text-brand-600 focus:ring-brand-500 cursor-pointer">
                    <label for="perspectiveActive" class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                        Aktifkan Perspektif
                        <span class="block text-xs font-normal text-gray-500">Perspektif aktif akan muncul di form KPI.</span>
                    </label>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closePerspectiveModal()" class="flex-1 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">Batal</button>
                    <button type="submit" class="flex-1 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand-200 hover:bg-brand-700 hover:shadow-lg dark:shadow-none transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openPerspectiveModal(id = null, name = '', isActive = true) {
        const modal = document.getElementById('perspectiveModal');
        const form = document.getElementById('perspectiveForm');
        const title = document.getElementById('perspectiveModalTitle');
        const nameInput = document.getElementById('perspectiveName');
        const activeInput = document.getElementById('perspectiveActive');
        const methodInput = document.getElementById('perspectiveMethod');

        if (id) {
            title.textContent = 'Edit Perspektif';
            form.action = `/kpi/perspectives/${id}`;
            methodInput.value = 'PUT';
        } else {
            title.textContent = 'Tambah Perspektif';
            form.action = `{{ route('kpi.perspectives.store') }}`;
            methodInput.value = 'POST';
        }

        nameInput.value = name || '';
        activeInput.checked = !!isActive;
        modal.classList.remove('hidden');
        setTimeout(() => {
            nameInput.focus();
        }, 50);
    }

    function closePerspectiveModal() {
        const modal = document.getElementById('perspectiveModal');
        modal.classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closePerspectiveModal();
    });

    // BULK ACTIONS
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.perspective-checkbox');
    const bulkActionContainer = document.getElementById('bulkActionContainer');
    const selectedCountSpan = document.getElementById('selectedCount');

    function updateBulkUI() {
        const checkedBoxes = document.querySelectorAll('.perspective-checkbox:checked');
        const count = checkedBoxes.length;
        selectedCountSpan.innerText = count;

        if (count > 0) {
            bulkActionContainer.classList.remove('hidden');
        } else {
            bulkActionContainer.classList.add('hidden');
        }
    }

    if(selectAll){
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });
            updateBulkUI();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkUI);
    });

    function confirmBulkDelete() {
        const checkedBoxes = document.querySelectorAll('.perspective-checkbox:checked');
        if (checkedBoxes.length === 0) return;

        if (!confirm('Apakah Anda yakin ingin menghapus ' + checkedBoxes.length + ' perspektif yang dipilih?')) {
            return;
        }

        const ids = Array.from(checkedBoxes).map(cb => cb.value);
        const originalText = document.querySelector('#bulkActionContainer button').innerHTML;
        
        // Show Loading
        document.querySelector('#bulkActionContainer button').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';
        document.querySelector('#bulkActionContainer button').disabled = true;

        fetch('{{ route("kpi.perspectives.bulk-delete") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids: ids })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Gagal: ' + data.message);
                document.querySelector('#bulkActionContainer button').innerHTML = originalText;
                document.querySelector('#bulkActionContainer button').disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus data.');
            document.querySelector('#bulkActionContainer button').innerHTML = originalText;
            document.querySelector('#bulkActionContainer button').disabled = false;
        });
    }
</script>
@endsection
