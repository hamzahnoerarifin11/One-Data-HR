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
                <a href="{{ route('organization.unit.index') }}" class="hover:text-blue-600 transition">Data Unit</a>
            </li>
            <li class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 01-1.414 1.414L7.293 14.707z" clip-rule="evenodd"/>
                </svg>
                <span class="text-gray-900 dark:text-white">Edit Unit</span>
            </li>
        </ol>
    </nav>

    <!-- HEADER -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Edit Unit
        </h1>
        <p class="mt-1 text-gray-600 dark:text-gray-400">
            Edit data unit
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
    <div x-data="unitForm()" x-init="initForm()" class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <form action="{{ route('organization.unit.update', $unit) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Perusahaan -->
            <div>
                <label for="company_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Perusahaan <span class="text-red-500">*</span>
                </label>
                <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                <select
                    name="company_id"
                    id="company_id"
                    x-model="selectedCompany"
                    @change="updateDivisions()"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                    required
                >
                    <option value="">Pilih Perusahaan</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id', $unit->company_id) == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                    @endforeach
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
                @error('company_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Divisi -->
            <div>
                <label for="division_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Divisi <span class="text-red-500">*</span>
                </label>
                <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">

                <select
                    name="division_id"
                    id="division_id"
                    x-model="selectedDivision"
                    @change="updateDepartments()"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                    required
                >
                    <option value="">Pilih Divisi</option>
                    <template x-for="division in filteredDivisions" :key="division.id">
                        <option :value="division.id" :selected="selectedDivision == division.id" x-text="division.name"></option>
                    </template>
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
                @error('division_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Departemen -->
            <div>
                <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Departemen <span class="text-red-500">*</span>
                </label>
                <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">

                <select
                    name="department_id"
                    id="department_id"
                    x-model="selectedDepartment"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                    required
                >
                    <option value="">Pilih Departemen</option>
                    <template x-for="department in filteredDepartments" :key="department.id">
                        <option :value="department.id" :selected="selectedDepartment == department.id" x-text="department.name"></option>
                    </template>
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
                @error('department_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama Unit -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nama Unit <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $unit->name) }}"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-900 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 @error('name') border-red-500 @enderror"
                    placeholder="Masukkan nama unit"
                    required
                />
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- BUTTONS -->
            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('organization.unit.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow hover:bg-gray-50 transition dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Batal
                </a>

                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function unitForm() {
    return {
        selectedCompany: '{{ old('company_id', $unit->company_id) }}',
        selectedDivision: '{{ old('division_id', $unit->division_id) }}',
        selectedDepartment: '{{ old('department_id', $unit->department_id) }}',

        divisions: @json($divisions->map(fn($d) => [
            'id' => $d->id,
            'name' => $d->name,
            'company_id' => $d->company_id
        ])),

        departments: @json($departments->map(fn($d) => [
            'id' => $d->id,
            'name' => $d->name,
            'division_id' => $d->division_id
        ])),

        filteredDivisions: [],
        filteredDepartments: [],

        initForm() {
            // Load data saat edit pertama kali
            this.filteredDivisions = this.divisions.filter(
                d => d.company_id == this.selectedCompany
            );

            this.filteredDepartments = this.departments.filter(
                d => d.division_id == this.selectedDivision
            );
        },

        updateDivisions() {
            this.filteredDivisions = this.divisions.filter(
                d => d.company_id == this.selectedCompany
            );

            this.selectedDivision = '';
            this.selectedDepartment = '';
            this.filteredDepartments = [];
        },

        updateDepartments() {
            this.filteredDepartments = this.departments.filter(
                d => d.division_id == this.selectedDivision
            );

            this.selectedDepartment = '';
        }
    }
}
</script>

@endsection
