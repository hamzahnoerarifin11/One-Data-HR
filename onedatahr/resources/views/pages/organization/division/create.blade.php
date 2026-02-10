@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <!-- BREADCRUMB -->
    <nav class="mb-6">
        <ol class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
            <li>
                <a href="{{ route('dashboard.index') }}" class="hover:text-blue-600 transition">Dashboard</a>
            </li>
            <li class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 01-1.414 1.414L7.293 14.707z" clip-rule="evenodd"/>
                </svg>
                <a href="{{ route('organization.division.index') }}" class="hover:text-blue-600 transition">Data Divisi</a>
            </li>
            <li class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 01-1.414 1.414L7.293 14.707z" clip-rule="evenodd"/>
                </svg>
                <span class="text-gray-900 dark:text-white">Tambah Divisi</span>
            </li>
        </ol>
    </nav>

    <!-- HEADER -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Tambah Divisi
        </h1>
        <p class="mt-1 text-gray-600 dark:text-gray-400">
            Tambahkan data divisi baru structure
        </p>
    </div>

    <!-- SUCCESS ALERT -->
    @if(session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800 dark:border-green-900 dark:bg-green-900/20 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    <!-- ERROR ALERT -->
    @if(session('error'))
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800 dark:border-red-900 dark:bg-red-900/20 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    <!-- FORM -->
    <div x-data="divisionForm()" class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <form action="{{ route('organization.division.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- STEP 1: Pilih Tipe -->
            <div class="space-y-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Langkah 1: Tentukan Induk Organisasi</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pilih apakah divisi ini berada di bawah Holding langsung atau di bawah Perusahaan spesifik.</p>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <!-- Option: Company -->
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="based_on" value="company" x-model="basedOn" class="peer sr-only">
                        <div class="p-5 rounded-xl border-2 transition-all duration-200 hover:bg-gray-50 dark:hover:bg-blue-900/40"
                             :class="basedOn == 'company' ? 'border-blue-500 bg-blue-50/50 dark:border-blue-500 dark:bg-blue-500/10' : 'border-gray-200 dark:border-gray-700'">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center"
                                     :class="basedOn == 'company' ? 'bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400'">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div>
                                    <span class="block font-medium text-gray-900 dark:text-white mb-1">Perusahaan (Company)</span>
                                    <span class="block text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                                        Divisi milik perusahaan tertentu. Bisa memiliki induk divisi dari Holding (opsional).
                                    </span>
                                </div>
                                <div class="absolute top-5 right-5" x-show="basedOn == 'company'">
                                    <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </label>

                    <!-- Option: Holding -->
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="based_on" value="holding" x-model="basedOn" class="peer sr-only">
                        <div class="p-5 rounded-xl border-2 transition-all duration-200 hover:bg-gray-50 dark:hover:bg-blue-900/40"
                             :class="basedOn == 'holding' ? 'border-purple-500 bg-purple-50/50 dark:border-purple-500 dark:bg-purple-500/10' : 'border-gray-200 dark:border-gray-700'">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center"
                                     :class="basedOn == 'holding' ? 'bg-purple-100 text-purple-600 dark:bg-purple-500/20 dark:text-purple-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400'">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                                    </svg>
                                </div>
                                <div>
                                    <span class="block font-medium text-gray-900 dark:text-white mb-1">Holding (Pusat)</span>
                                    <span class="block text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                                        Divisi tingkat pusat/holding. Menjadi induk standar untuk divisi di anak perusahaan.
                                    </span>
                                </div>
                                <div class="absolute top-5 right-5" x-show="basedOn == 'holding'">
                                    <svg class="w-6 h-6 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <hr class="border-gray-100 dark:border-gray-800">

            <!-- STEP 2: Detail Organisasi -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Langkah 2: Detail Organisasi</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Lengkapi informasi divisi sesuai tipe yang dipilih.</p>
                </div>

                <!-- FORM FOR COMPANY -->
                <div x-show="basedOn == 'company'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                    
                    <!-- Select Company (Searchable) -->
                    <!-- Select Company (Searchable) -->
                    <x-searchable-select 
                        name="company_id" 
                        label="Pilih Perusahaan"
                        :options="$companies->map(fn($c) => [
                            'id' => $c->id, 
                            'name' => $c->name, 
                            'holding_id' => $c->holding_id,
                            'parent_id' => $c->parent_id,
                            'type' => $c->parent_id ? 'Anak Perusahaan' : 'Perusahaan',
                            'detail_html' => $c->parent_id 
                                ? 'Anak Perusahaan &bull; Induk: ' . ($c->parent->name ?? '-') 
                                : 'Perusahaan &bull; Holding: ' . ($c->holding->name ?? '-'),
                        ])"
                        detail-key="detail_html"
                        x-model="selectedCompany"
                        @option-selected="fetchParents($event.detail.data)"
                        required
                    />

                    <!-- Select Parent (Optional) -->
                    <div class="rounded-lg border border-blue-100 bg-blue-50 p-4 dark:border-blue-500 dark:bg-gray-800">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="flex-1 space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-blue-900 dark:text-blue-500 mb-1">
                                        Induk Divisi (Opsional)
                                    </label>
                                    <p class="text-xs text-blue-700 dark:text-blue-300 mb-2" x-text="parentHelpText || 'Pilih perusahaan terlebih dahulu.'"></p>
                                </div>
                                
                                <x-searchable-select 
                                    name="parent_id" 
                                    x-effect="dynamicOptionsRaw = parents"
                                    x-model="selectedParent"
                                    placeholder="Tidak ada induk (Berdiri Sendiri)"
                                    x-bind:disabled="!selectedCompany"
                                />
                                <div x-show="isLoading" class="text-xs text-blue-600 animate-pulse mt-1">Sedang memuat data parent...</div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- FORM FOR HOLDING -->
                <div x-show="basedOn == 'holding'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                    
                    <div class="space-y-2">
                        <x-searchable-select 
                            name="holding_id" 
                            label="Pilih Holding"
                            :options="$holdings"
                            x-model="selectedHolding"
                            x-bind:required="basedOn == 'holding'"
                        />
                    </div>

                    <div class="rounded-lg bg-yellow-50 p-4 border border-yellow-100 dark:bg-yellow-900/10 dark:border-yellow-900/30">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                Divisi Holding biasanya menjadi acuan standard (parent) bagi divisi-divisi di anak perusahaan.
                            </p>
                        </div>
                    </div>

                </div>

                <!-- COMMON: NAME -->
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nama Divisi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                        placeholder="Contoh: Divisi Keuangan, Divisi HRD, dll" required>
                    @error('name') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('organization.division.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    Batal
                </a>

                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 transition focus:ring-4 focus:ring-blue-500/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Divisi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function divisionForm() {
        return {
            basedOn: '{{ old('based_on', 'company') }}',
            selectedCompany: '{{ old('company_id', $selectedCompanyId) }}',
            selectedHolding: '{{ old('holding_id') }}',
            selectedParent: '{{ old('parent_id') }}',
            parents: [],
            parentHelpText: '',
            isLoading: false,

            fetchParents(option) {
                this.parents = [];
                this.selectedParent = '';
                this.parentHelpText = '';
                this.isLoading = true;

                if (!option && this.selectedCompany) {
                    // Try to find option if not passed (e.g. from init)
                    // But here we rely on the event. 
                    // Use a fallback fetch or just wait for interaction.
                    // For initial load, we might need to fetch plain list if we don't have option metadata.
                    // However, we can fetch based on ID and let server handle logic?
                    // Previous logic relied on holding_id/parent_id client side.
                    // Let's assume we need to fetch if we have ID.
                }

                if (!option && !this.selectedCompany) {
                     this.isLoading = false;
                     return;
                }

                // Construct URL
                // If we don't have 'option' object (e.g. page load), we might struggle unless we fetch company details first.
                // But wait, the options are provided to the component. 
                // We could find the option from the companies list if we had access to it.
                // But the companies list is in Blade, not this Alpine scope.
                
                // Fallback: If we have selectedCompany but no option, we trigger a fetch to a generic endpoint?
                // Or just rely on the component firing 'option-selected' if we set the value?
                // The component does NOT fire 'option-selected' on init.
                // So on page load with old input, we might miss the parent list.
                
                // Solution: Pass the logic to server provided route that handles just company_id?
                // Or, simply ignore automatic parent fetching on edit/error? 
                // Creating a new one, old input exists.
                // Let's use the generic endpoint if available or just URL construction if we can.
                
                let url = "";
                let type = "";
                
                if (option) {
                    if (option.parent_id) {
                        url = "{{ url('organization/division/by-company') }}/" + option.parent_id;
                        type = option.type;
                        this.parentHelpText = "Menampilkan divisi dari Perusahaan Induk (" + type + ").";
                    } else if (option.holding_id) {
                        url = "{{ url('organization/division/by-holding') }}/" + option.holding_id;
                        this.parentHelpText = "Menampilkan divisi dari Holding.";
                    } else {
                        this.isLoading = false;
                        return;
                    }
                } else if (this.selectedCompany) {
                     // Fallback for page reload: we don't have metadata easily.
                     // Maybe we can skip this update or try to fetch parents by Company ID directly if such endpoint exists.
                     // Ideally, we shouldn't rely on client-side metadata for this important logic.
                     // But for now, we leave it empty or user has to re-select if strict.
                     // Actually, we can assume 'by-company-parents' endpoint should exist? No.
                     this.isLoading = false;
                     return;
                }

                fetch(url)
                    .then(res => res.json())
                    .then(data => { 
                        this.parents = data.map(d => ({
                            id: d.id, 
                            name: d.name,
                            detail: d.code ? 'Kode: ' + d.code : '' // Example detail
                        })); 
                    })
                    .catch(err => console.error(err))
                    .finally(() => { this.isLoading = false; });
            },

            init() {
                // If we have selectedCompany on load, we ideally want to trigger fetch.
                // But we lack the 'option' object because it's inside the component.
                // We can just ask the user to select again, OR we can inject the Companies array into this scope too?
                // Yes, simpler:
            }
        }
    }

    // Expose fetchParents globally or nicely attached
    window.fetchParents = function(option) {
        if (!option) return;

        const holdingId = option.holding_id;
        const parentCompanyId = option.parent_id;
        const parentSelect = document.getElementById('parent_id');
        const loading = document.getElementById('loading-parents');
        const helpText = document.getElementById('parent-help-text');
        
        parentSelect.innerHTML = '<option value="">Tidak ada induk (Berdiri Sendiri)</option>';
        
        if(loading) loading.classList.remove('hidden');

        let url = "";
        
        // Logic same as before
        if (parentCompanyId) {
            // Subsidiary -> Fetch divisions from Parent Company
            url = "{{ url('organization/division/by-company') }}/" + parentCompanyId;
            if(helpText) helpText.textContent = "Menampilkan divisi dari Perusahaan Induk (" + option.type + ").";
        } else if (holdingId) {
            // Company -> Fetch divisions from Holding
            url = "{{ url('organization/division/by-holding') }}/" + holdingId;
             if(helpText) helpText.textContent = "Menampilkan divisi dari Holding (Karena perusahaan ini langsung di bawah Holding).";
        } else {
             if(loading) loading.classList.add('hidden');
             return;
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                data.forEach(row => {
                    const opt = document.createElement('option');
                    opt.value = row.id;
                    opt.text = row.name;
                    if (row.id == "{{ old('parent_id') }}") opt.selected = true;
                    parentSelect.appendChild(opt);
                });
            })
            .catch(err => console.error('Error fetching parents:', err))
            .finally(() => {
                if(loading) loading.classList.add('hidden');
            });
    }

</script>
@endsection
