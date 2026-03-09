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
                <span class="text-gray-900 dark:text-white">Edit Divisi</span>
            </li>
        </ol>
    </nav>

    <!-- HEADER -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Edit Divisi
        </h1>
        <p class="mt-1 text-gray-600 dark:text-gray-400">
            Perbarui data divisi struktur organisasi
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
        <form action="{{ route('organization.division.update', $division) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

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
                    <div class="space-y-2" x-data="searchableCompanySelect()">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Pilih Perusahaan <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <!-- Hidden Select for Form Submission -->
                            <input type="hidden" name="company_id" id="company_id" :value="selectedId">
                            
                            <!-- Display Input -->
                            <div @click="toggle" @click.away="close" class="relative cursor-pointer">
                                <div class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white flex items-center justify-between">
                                    <span x-text="selectedName || '-- Pilih Perusahaan --'" :class="{'text-gray-500': !selectedName}"></span>
                                    <svg class="w-4 h-4 text-gray-500 transition-transform" :class="{'rotate-180': isOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>

                                <!-- Dropdown -->
                                <div x-show="isOpen" 
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900 max-h-60 flex flex-col">
                                    
                                    <!-- Search Box -->
                                    <div class="p-2 sticky top-0 bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-700 z-10">
                                        <input type="text" x-model="search" placeholder="Cari perusahaan..." 
                                            class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                            @click.stop>
                                    </div>

                                    <!-- Options List -->
                                    <ul class="overflow-y-auto flex-1 p-1">
                                        <template x-for="option in filteredOptions" :key="option.id">
                                            <li @click.stop="select(option)" 
                                                class="cursor-pointer rounded-md px-3 py-2 text-sm hover:bg-blue-50 dark:hover:bg-blue-900/20"
                                                :class="{'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300': selectedId == option.id, 'text-gray-700 dark:text-gray-200': selectedId != option.id}">
                                                <div class="flex flex-col">
                                                    <span class="font-medium" x-text="option.name"></span>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400" x-html="option.detail_html"></span>
                                                </div>
                                            </li>
                                        </template>
                                        <li x-show="filteredOptions.length === 0" class="px-3 py-2 text-sm text-gray-500 text-center">
                                            Tidak ada hasil.
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @error('company_id') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Select Parent (Optional) -->
                    <div class="rounded-lg border border-blue-100 bg-blue-50 p-4 dark:border-blue-900/30 dark:bg-blue-900/10">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="flex-1 space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-blue-900 dark:text-blue-100 mb-1">
                                        Induk Divisi (Opsional)
                                    </label>
                                    <p class="text-xs text-blue-700 dark:text-blue-300 mb-2" id="parent-help-text">
                                        Jika perusahaan ini tergabung dalam Holding, Anda bisa memilih divisi di Holding sebagai induk (parent).
                                    </p>
                                </div>
                                
                                <select name="parent_id" id="parent_id" class="w-full rounded-lg border-blue-200 bg-white text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                    <option value="">Tidak ada induk (Berdiri Sendiri)</option>
                                </select>
                                <div id="loading-parents" class="hidden text-xs text-blue-600 animate-pulse">Sedang memuat data parent...</div>
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
                    <input type="text" id="name" name="name" value="{{ old('name', $division->name) }}"
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
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function divisionForm() {
        return {
            basedOn: '{{ old('based_on', $division->based_on ?? 'company') }}',
            selectedHolding: '{{ old('holding_id', $division->holding_id) }}',
            init() {
                // Initial load handled by searchableCompanySelect
            }
        }
    }

    function searchableCompanySelect() {
        return {
            isOpen: false,
            search: '',
            selectedId: '{{ old('company_id', $division->company_id) }}', 
            
            // Raw data from PHP
            options: [
                @foreach($companies as $company)
                {
                    id: "{{ $company->id }}",
                    name: "{{ $company->name }}",
                    holding_id: "{{ $company->holding_id }}",
                    parent_id: "{{ $company->parent_id }}",
                    type: "{!! $company->parent_id ? 'Anak Perusahaan' : 'Perusahaan' !!}",
                    detail_html: "{!! $company->parent_id 
                        ? 'Anak Perusahaan &bull; Induk: ' . ($company->parent->name ?? '-') 
                        : 'Perusahaan &bull; Holding: ' . ($company->holding->name ?? '-') !!}"
                },
                @endforeach
            ],

            init() {
                // If ID is pre-selected (old input), trigger fetchParents
                if (this.selectedId) {
                    this.$nextTick(() => window.fetchParents(this.selectedOption));
                }
            },

            get selectedName() {
                const opt = this.options.find(o => o.id == this.selectedId);
                return opt ? opt.name : null;
            },

            get selectedOption() {
                return this.options.find(o => o.id == this.selectedId);
            },

            get filteredOptions() {
                if (!this.search) return this.options;
                const lower = this.search.toLowerCase();
                return this.options.filter(item => 
                    item.name.toLowerCase().includes(lower) || 
                    item.detail_html.toLowerCase().includes(lower)
                );
            },

            toggle() { this.isOpen = !this.isOpen; },
            close() { this.isOpen = false; },
            
            select(option) {
                this.selectedId = option.id;
                this.close();
                this.search = '';
                
                // Trigger the global function to fetch logic
                window.fetchParents(option);
            }
        }
    }

    // Expose fetchParents globally
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

        if (parentCompanyId) {
            // Subsidiary
            url = "{{ url('organization/division/by-company') }}/" + parentCompanyId;
            if(helpText) helpText.textContent = "Menampilkan divisi dari Perusahaan Induk (" + option.type + ").";
        } else if (holdingId) {
            // Company
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
                    if (row.id == "{{ old('parent_id', $division->parent_id) }}") opt.selected = true;
                    parentSelect.appendChild(opt);
                });
            })
            .catch(err => console.error('Error fetching parents:', err))
            .finally(() => {
                if(loading) loading.classList.add('hidden');
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initial fetch handled by Alpine init
    });

</script>
@endsection
