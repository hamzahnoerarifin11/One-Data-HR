<!-- Modal untuk Tambah Level Jabatan -->
<div id="levelModal"
     class="fixed inset-0 z-50 flex items-center justify-center hidden">

    <!-- Backdrop -->
    <div onclick="closeLevelModal()" class="absolute inset-0 bg-black/50 dark:bg-black/80"></div>

    <!-- Modal Content -->
    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-md w-full mx-4 p-6">

        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Tambah Level Jabatan</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Tambahkan level jabatan baru dengan cepat</p>
        </div>

        <!-- Alert Messages -->
        <div id="levelModalAlert" class="mb-4 hidden rounded-lg border p-3 text-sm"></div>

        <!-- Form -->
        <form onsubmit="submitLevelForm(event)" class="space-y-4">
            <input type="hidden" id="_token" value="{{ csrf_token() }}">

            <!-- Name Field -->
            <div>
                <label for="levelName" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Nama Level Jabatan <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="levelName"
                    placeholder="Misal: Manager, Supervisor, Staff"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    required
                />
                <div id="levelNameError" class="text-xs text-red-600 dark:text-red-400 mt-1 hidden"></div>
            </div>

            <!-- Level Order Field -->
            <div>
                <label for="levelOrder" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Urutan Level <span class="text-red-500">*</span>
                </label>
                <input
                    type="number"
                    id="levelOrder"
                    placeholder="Misal: 1, 2, 3"
                    min="1"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    required
                />
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Urutan digunakan untuk menentukan hierarki level (1 = tertinggi)</p>
                <div id="levelOrderError" class="text-xs text-red-600 dark:text-red-400 mt-1 hidden"></div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-4">
                <button
                    type="button"
                    onclick="closeLevelModal()"
                    class="flex-1 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                    Batal
                </button>
                <button
                    type="submit"
                    id="submitLevelBtn"
                    class="flex-1 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-blue-500 dark:hover:bg-blue-600 transition">
                    Simpan Level
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openLevelModal() {
    document.getElementById('levelModal').classList.remove('hidden');
    document.getElementById('levelName').focus();
}

function closeLevelModal() {
    document.getElementById('levelModal').classList.add('hidden');
    document.getElementById('levelName').value = '';
    document.getElementById('levelOrder').value = '';
    document.getElementById('levelModalAlert').classList.add('hidden');
    document.getElementById('levelNameError').classList.add('hidden');
    document.getElementById('levelOrderError').classList.add('hidden');
}

function showLevelAlert(message, type) {
    const alert = document.getElementById('levelModalAlert');
    alert.className = `mb-4 rounded-lg border p-3 text-sm ${
        type === 'error'
            ? 'border-red-200 bg-red-50 text-red-800 dark:border-red-900 dark:bg-red-900/20 dark:text-red-400'
            : 'border-green-200 bg-green-50 text-green-800 dark:border-green-900 dark:bg-green-900/20 dark:text-green-400'
    }`;
    alert.textContent = message;
    alert.classList.remove('hidden');
}

async function submitLevelForm(event) {
    event.preventDefault();

    const name = document.getElementById('levelName').value;
    const level_order = document.getElementById('levelOrder').value;
    const submitBtn = document.getElementById('submitLevelBtn');

    // Clear previous errors
    document.getElementById('levelNameError').classList.add('hidden');
    document.getElementById('levelOrderError').classList.add('hidden');

    // Validate
    if (!name.trim()) {
        document.getElementById('levelNameError').textContent = 'Nama level harus diisi';
        document.getElementById('levelNameError').classList.remove('hidden');
        return;
    }

    if (!level_order) {
        document.getElementById('levelOrderError').textContent = 'Urutan level harus diisi';
        document.getElementById('levelOrderError').classList.remove('hidden');
        return;
    }

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="inline-flex items-center gap-2"><svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>Loading...</span>';

    try {
        const response = await fetch('{{ route("organization.level.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.getElementById('_token').value,
            },
            body: JSON.stringify({
                name: name.trim(),
                level_order: parseInt(level_order),
            }),
        });

        const data = await response.json();

        if (data.success) {
            showLevelAlert('✓ Level jabatan berhasil ditambahkan!', 'success');

            // Add new option to select
            const levelSelect = document.getElementById('levelSelect');
            const newOption = document.createElement('option');
            newOption.value = data.data.id;
            newOption.textContent = data.data.name;
            levelSelect.appendChild(newOption);
            levelSelect.value = data.data.id;

            // Close modal after 1.5 seconds
            setTimeout(() => {
                closeLevelModal();
            }, 1500);
        } else {
            if (data.errors) {
                if (data.errors.name) {
                    document.getElementById('levelNameError').textContent = data.errors.name[0];
                    document.getElementById('levelNameError').classList.remove('hidden');
                }
                if (data.errors.level_order) {
                    document.getElementById('levelOrderError').textContent = data.errors.level_order[0];
                    document.getElementById('levelOrderError').classList.remove('hidden');
                }
            } else {
                showLevelAlert(data.message || 'Terjadi kesalahan saat menyimpan', 'error');
            }
        }
    } catch (error) {
        showLevelAlert('Terjadi kesalahan jaringan: ' + error.message, 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Simpan Level';
    }
}

// Close modal when clicking outside
document.getElementById('levelModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeLevelModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('levelModal').classList.contains('hidden')) {
        closeLevelModal();
    }
});
</script>
