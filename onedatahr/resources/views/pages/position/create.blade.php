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
                <a href="{{ route('position.index') }}" class="hover:text-blue-600 transition">Data Jabatan</a>
            </li>
            <li class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 01-1.414 1.414L7.293 14.707z" clip-rule="evenodd"/>
                </svg>
                <span class="text-gray-900 dark:text-white">Tambah Jabatan</span>
            </li>
        </ol>
    </nav>

    <!-- HEADER -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Tambah Jabatan
        </h1>
        <p class="mt-1 text-gray-600 dark:text-gray-400">
            Tambahkan data jabatan baru
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
    <div x-data="positionForm()" class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <form action="{{ route('position.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Perusahaan -->
            <div>
                <label for="company_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Perusahaan <span class="text-red-500">*</span>
                </label>
                <select
                    name="company_id"
                    id="company_id"
                    x-model="selectedCompany"
                    @change="updateDivisions()"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-900 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 @error('company_id') border-red-500 @enderror"
                    required
                >
                    <option value="">Pilih Perusahaan</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                    @endforeach
                </select>
                @error('company_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Divisi -->
            <div>
                <label for="division_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Divisi <span class="text-red-500">*</span>
                </label>
                <select
                    name="division_id"
                    id="division_id"
                    x-model="selectedDivision"
                    @change="updateDepartments()"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-900 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 @error('division_id') border-red-500 @enderror"
                    required
                >
                    <option value="">Pilih Divisi</option>
                    <template x-for="division in filteredDivisions" :key="division.id">
                        <option :value="division.id" x-text="division.name"></option>
                    </template>
                </select>
                @error('division_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Departemen -->
            <div>
                <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Departemen <span class="text-red-500">*</span>
                </label>
                <select
                    name="department_id"
                    id="department_id"
                    x-model="selectedDepartment"
                    @change="updateUnits()"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-900 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 @error('department_id') border-red-500 @enderror"
                    required
                >
                    <option value="">Pilih Departemen</option>
                    <template x-for="department in filteredDepartments" :key="department.id">
                        <option :value="department.id" x-text="department.name"></option>
                    </template>
                </select>
                @error('department_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Unit -->
            <div>
                <label for="unit_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Unit <span class="text-red-500">*</span>
                </label>
                <select
                    name="unit_id"
                    id="unit_id"
                    x-model="selectedUnit"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-900 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 @error('unit_id') border-red-500 @enderror"
                    required
                >
                    <option value="">Pilih Unit</option>
                    <template x-for="unit in filteredUnits" :key="unit.id">
                        <option :value="unit.id" x-text="unit.name"></option>
                    </template>
                </select>
                @error('unit_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama Jabatan -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nama Jabatan <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-900 outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 @error('name') border-red-500 @enderror"
                    placeholder="Masukkan nama jabatan"
                    required
                />
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- BUTTONS -->
            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('position.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-900 transition">
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
function positionForm() {
    return {
        selectedCompany: '{{ old('company_id') }}',
        selectedDivision: '{{ old('division_id') }}',
        selectedDepartment: '{{ old('department_id') }}',
        selectedUnit: '{{ old('unit_id') }}',
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
        units: @json($units->map(fn($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'department_id' => $u->department_id
        ])),

        get filteredDivisions() {
            if (!this.selectedCompany) return [];
            return this.divisions.filter(division => division.company_id == this.selectedCompany);
        },

        get filteredDepartments() {
            if (!this.selectedDivision) return [];
            return this.departments.filter(department => department.division_id == this.selectedDivision);
        },

        get filteredUnits() {
            if (!this.selectedDepartment) return [];
            return this.units.filter(unit => unit.department_id == this.selectedDepartment);
        },

        updateDivisions() {
            this.selectedDivision = '';
            this.selectedDepartment = '';
            this.selectedUnit = '';
        },

        updateDepartments() {
            this.selectedDepartment = '';
            this.selectedUnit = '';
        },

        updateUnits() {
            this.selectedUnit = '';
        }
    }
}
</script>

@endsection
