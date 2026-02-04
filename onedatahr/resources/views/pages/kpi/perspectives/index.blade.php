@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Master Perspektif KPI</h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">
                Kelola perspektif KPI yang aktif untuk input baru tanpa mengganggu data historis.
            </p>
        </div>
        <button onclick="openPerspectiveModal()" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Perspektif
        </button>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800 dark:border-green-900 dark:bg-green-900/20 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800 dark:border-red-900 dark:bg-red-900/20 dark:text-red-400">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full min-w-full">
                <thead>
                    <tr class="border-y border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400">Nama Perspektif</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400">Status</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-400">Dipakai di KPI</th>
                        <th class="px-5 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($perspectives as $perspective)
                        @php $usage = $usageCounts[$perspective->name] ?? 0; @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20 transition">
                            <td class="px-5 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $perspective->name }}</td>
                            <td class="px-5 py-4 text-sm">
                                @if($perspective->is_active)
                                    <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700 dark:bg-green-900/20 dark:text-green-400">Aktif</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-900/20 dark:text-gray-300">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $usage }} item</td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        onclick="openPerspectiveModal({{ $perspective->id }}, '{{ addslashes($perspective->name) }}', {{ $perspective->is_active ? 'true' : 'false' }})"
                                        class="inline-flex items-center justify-center rounded-lg bg-yellow-50 p-2 text-yellow-600 hover:bg-yellow-100 dark:bg-yellow-900/20 dark:text-yellow-400 dark:hover:bg-yellow-900/40 transition"
                                        title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

                                    <form method="POST" action="{{ route('kpi.perspectives.toggle', $perspective) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-50 p-2 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/40 transition" title="Toggle Status">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                Belum ada perspektif KPI. Tambahkan perspektif baru untuk mulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="perspectiveModal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div onclick="closePerspectiveModal()" class="absolute inset-0 bg-black/50"></div>

    <div class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-lg dark:bg-gray-800">
        <h2 id="perspectiveModalTitle" class="text-xl font-bold text-gray-900 dark:text-white">Tambah Perspektif KPI</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Perspektif nonaktif tetap bisa dipakai untuk data KPI lama.</p>

        <form id="perspectiveForm" method="POST" class="mt-5 space-y-4">
            @csrf
            <input type="hidden" id="perspectiveMethod" name="_method" value="POST">

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Perspektif</label>
                <input type="text" id="perspectiveName" name="name" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" required>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="perspectiveActive" name="is_active" value="1" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="perspectiveActive" class="text-sm text-gray-600 dark:text-gray-400">Aktifkan perspektif</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closePerspectiveModal()" class="flex-1 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 transition">Batal</button>
                <button type="submit" class="flex-1 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">Simpan</button>
            </div>
        </form>
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
            title.textContent = 'Edit Perspektif KPI';
            form.action = `/kpi/perspectives/${id}`;
            methodInput.value = 'PUT';
        } else {
            title.textContent = 'Tambah Perspektif KPI';
            form.action = `{{ route('kpi.perspectives.store') }}`;
            methodInput.value = 'POST';
        }

        nameInput.value = name || '';
        activeInput.checked = !!isActive;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        nameInput.focus();
    }

    function closePerspectiveModal() {
        const modal = document.getElementById('perspectiveModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closePerspectiveModal();
    });
</script>
@endsection
