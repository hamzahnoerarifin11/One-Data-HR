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
                <a href="{{ route('organization.department.index') }}" class="hover:text-blue-600 transition">Data Departemen</a>
            </li>
            <li class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 01-1.414 1.414L7.293 14.707z" clip-rule="evenodd"/>
                </svg>
                <span class="text-gray-900 dark:text-white">Tambah Departemen</span>
            </li>
        </ol>
    </nav>

    <!-- HEADER -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Tambah Departemen
        </h1>
        <p class="mt-1 text-gray-600 dark:text-gray-400">
            Tambahkan data departemen baru
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
    <div x-data="departmentForm()" x-init="init()" class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <form action="{{ route('organization.department.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- STEP 1: Pilih Tipe -->
            <div class="space-y-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Langkah 1: Tentukan Induk Organisasi</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pilih departemen ini berada di bawah Holding langsung atau Perusahaan.</p>
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
                                        Departemen milik perusahaan spesifik dan divisinya.
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
                                        Departemen tingkat pusat di bawah divisi holding.
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

            <!-- STEP 2: Pilih Organisasi -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Langkah 2: Pilih Organisasi</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pilih hierarki organisasi untuk departemen ini.</p>
                </div>

                <!-- FORM FOR COMPANY -->
                <div x-show="basedOn == 'company'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                    
                    <!-- Select Company (Searchable) -->
                    <x-searchable-select 
                        name="company_id" 
                        label="Pilih Perusahaan"
                        :options="$companies->map(fn($c) => [
                            'id' => $c->id, 
                            'name' => $c->name, 
                            'detail_html' => $c->parent_id 
                                ? 'Anak Perusahaan &bull; Induk: ' . ($c->parent->name ?? '-') 
                                : 'Perusahaan &bull; Holding: ' . ($c->holding->name ?? '-'),
                        ])"
                        detail-key="detail_html"
                        x-model="selectedCompany"
                        @change="updateDivisions($event.detail)"
                        required
                    />
                </div>

                <!-- FORM FOR HOLDING -->
                <div x-show="basedOn == 'holding'" class="space-y-6" x-transition:enter="transition ease-out duration-300">
                    <x-searchable-select 
                        name="holding_id" 
                        label="Pilih Holding"
                        :options="$holdings"
                        x-model="selectedHolding"
                        @change="updateDivisions($event.detail)"
                        required
                    />
                </div>

                <!-- COMMON: DIVISI SELECTOR -->
                <div class="space-y-2" x-show="selectedCompany || selectedHolding" x-transition>
                    <x-searchable-select 
                        name="division_id" 
                        label="Pilih Divisi"
                        x-effect="dynamicOptionsRaw = divisions"
                        x-model="selectedDivision"
                        required
                    />
                    <div x-show="isLoading" class="text-xs text-blue-500 animate-pulse mt-1">Sedang memuat data divisi...</div>
                    @error('division_id') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- PARENT SELECTOR REMOVED -->

                <!-- NAME -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nama Departemen <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                        placeholder="Contoh: Dept. Accounting, Dept. Payroll" required>
                    @error('name') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- BUTTONS -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('organization.department.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    Batal
                </a>

                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 transition focus:ring-4 focus:ring-blue-500/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Departemen
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function departmentForm() {
        return {
            basedOn: '{{ old('based_on', 'company') }}',
            selectedCompany: '{{ old('company_id') }}',
            selectedHolding: '{{ old('holding_id') }}',
            selectedDivision: '{{ old('division_id') }}',
            divisions: [],
            isLoading: false,

            updateDivisions(entityId = null) {
                // Use passed entityId for both company and holding cases
                const targetCompanyId = this.basedOn === 'company' ? (entityId || this.selectedCompany) : this.selectedCompany;
                const targetHoldingId = this.basedOn === 'holding' ? (entityId || this.selectedHolding) : this.selectedHolding;
                
                // Keep old selection for potential restore
                const oldDivision = this.selectedDivision;
                
                this.selectedDivision = '';
                this.divisions = [];
                this.isLoading = true;

                const targetUrl = this.basedOn === 'company' 
                    ? "{{ url('organization/division/by-company') }}/" + targetCompanyId
                    : "{{ url('organization/division/by-holding') }}/" + targetHoldingId;

                const idToCheck = this.basedOn === 'company' ? targetCompanyId : targetHoldingId;

                if (!idToCheck) {
                    this.isLoading = false;
                    return Promise.resolve();
                }

                return fetch(targetUrl)
                    .then(res => res.json())
                    .then(data => { 
                        this.divisions = data; 
                        // Logic to restore old value if it exists in new list
                        if (oldDivision && this.divisions.some(d => d.id == oldDivision)) {
                            this.selectedDivision = oldDivision;
                        }
                    })
                    .catch(err => { console.error(err); this.divisions = []; })
                    .finally(() => { this.isLoading = false; });
            },
            
            async init() {
                if (this.selectedCompany || this.selectedHolding) {
                    await this.updateDivisions();
                }
            }
        }
    }

    function searchableCompanySelect() {
        return {
            isOpen: false,
            search: '',
            selectedId: '{{ old('company_id') }}',
            
            options: [
                @foreach($companies as $company)
                {
                    id: "{{ $company->id }}",
                    name: "{{ $company->name }}",
                    type: "{!! $company->parent_id ? 'Anak Perusahaan' : 'Perusahaan' !!}",
                    detail_html: "{!! $company->parent_id 
                        ? 'Anak Perusahaan &bull; Induk: ' . ($company->parent->name ?? '-') 
                        : 'Perusahaan &bull; Holding: ' . ($company->holding->name ?? '-') !!}"
                },
                @endforeach
            ],

            get selectedName() {
                const opt = this.options.find(o => o.id == this.selectedId);
                return opt ? opt.name : null;
            },

            get filteredOptions() {
                if (!this.search) return this.options;
                const lower = this.search.toLowerCase();
                return this.options.filter(item => 
                    item.name.toLowerCase().includes(lower) || 
                    item.detail_html.toLowerCase().includes(lower)
                );
            },

            toggle() { 
                // Only toggle if basedOn is company
                // Accessing variables from parent scope in Alpine is tricky if not dispatched
                // But here we can check the DOM or rely on parent creating this only when shown?
                // Actually the div is x-show="basedOn == 'company'".
                // But if disabled?
                this.isOpen = !this.isOpen; 
            },
            close() { this.isOpen = false; },
            
            select(option) {
                this.selectedId = option.id;
                this.close();
                this.search = '';
                this.$dispatch('company-change', { id: option.id });
            }
        }
    }
</script>

@endsection
