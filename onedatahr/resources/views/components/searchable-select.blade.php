@props([
    'name',
    'id' => null,
    'label' => null,
    'options' => [],
    'placeholder' => '-- Pilih --',
    'required' => false,
    'disabled' => false,
    'valueKey' => 'id',
    'labelKey' => 'name',
    'detailKey' => 'detail_html',
])

@php
    $id = $id ?? $name;
    // Map PHP options to standard format if provided statically
    $normalizedOptions = collect($options)->map(function ($item) use ($valueKey, $labelKey, $detailKey) {
        $itemObj = (object) $item;
        return [
            'value' => $itemObj->{$valueKey} ?? '',
            'label' => $itemObj->{$labelKey} ?? '',
            'detail' => $itemObj->{$detailKey} ?? '',
            'data'   => $item // Keep original data accessible
        ];
    })->values()->all();
@endphp

<div x-data="searchableSelect({
        initialOptions: {{ json_encode($normalizedOptions) }},
        value: '{{ $attributes->get('value') }}',
        placeholder: '{{ $placeholder }}'
     })"
     x-modelable="value"
     class="space-y-2 {{ $attributes->get('class') }}"
     {{ $attributes->except(['class']) }}
>
    @if($label)
    <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        {{ $label }} @if($required) <span class="text-red-500">*</span> @endif
    </label>
    @endif

    <div class="relative">
        <input type="hidden" name="{{ $name }}" id="{{ $id }}" :value="value">

        <!-- Trigger -->
        <div @click="if(!isDisabled) toggle()" @click.away="close()" 
             class="relative cursor-pointer w-full rounded-lg border bg-white px-4 py-3 text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:bg-gray-900 dark:text-white flex items-center justify-between transition-colors"
             :class="{'bg-gray-100 cursor-not-allowed border-gray-200 dark:border-gray-700': isDisabled, 'border-gray-300 dark:border-gray-700 hover:border-blue-400': !isDisabled}"
        >
            <span x-text="selectedLabel || placeholder" :class="{'text-gray-500': !selectedLabel}"></span>
            <svg class="w-4 h-4 text-gray-500 transition-transform" :class="{'rotate-180': isOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </div>

        <!-- Dropdown -->
        <div x-show="isOpen" x-cloak
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             class="absolute z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900 max-h-60 flex flex-col overflow-hidden"
             style="display: none;">
            
            <!-- Search -->
            <div class="p-2 sticky top-0 bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-700 z-10">
                <input type="text" x-model="search" placeholder="Cari..." 
                    class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    @click.stop>
            </div>

            <!-- List -->
            <ul class="overflow-y-auto flex-1 p-1 scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600">
                <template x-for="option in filteredOptions" :key="option.value">
                    <li @click.stop="select(option)" 
                        class="cursor-pointer rounded-md px-3 py-2 text-sm transition-colors hover:bg-blue-50 dark:hover:bg-blue-900/40"
                        :class="{'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300': value == option.value, 'text-gray-900 dark:text-gray-200': value != option.value}">
                        
                        <div class="flex flex-col">
                            <span class="font-medium" x-text="option.label"></span>
                            <span x-show="option.detail" x-html="option.detail" class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"></span>
                        </div>
                    </li>
                </template>
                <li x-show="filteredOptions.length === 0" class="px-3 py-2 text-sm text-gray-500 text-center italic">
                    Tidak ada hasil.
                </li>
            </ul>
        </div>
    </div>
</div>

@once
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('searchableSelect', ({ initialOptions, value, placeholder }) => ({
            isOpen: false,
            search: '',
            value: value,
            options: initialOptions,
            dynamicOptionsRaw: [], 
            placeholder: placeholder,

            init() {
                this.$watch('dynamicOptionsRaw', (val) => {
                    if (val && Array.isArray(val)) {
                        this.options = val.map(item => ({
                            value: item.id || item.value, 
                            label: item.name || item.label,
                            detail: item.detail_html || item.detail || '',
                            data: item // Keep original data
                        }));
                    }
                });

                if (this.value) {
                    this.$nextTick(() => {
                        const opt = this.options.find(o => o.value == this.value);
                        if (opt) {
                             this.$dispatch('option-selected', opt);
                        }
                    });
                }
            },
            
            // ... getters ...
            
            get computedOptions() { return this.options; },
            
            get filteredOptions() {
                if (!this.search) return this.computedOptions;
                const lower = this.search.toLowerCase();
                return this.computedOptions.filter(item => 
                    String(item.label).toLowerCase().includes(lower) || 
                    (item.detail && String(item.detail).toLowerCase().includes(lower))
                );
            },

            get selectedLabel() {
                const opt = this.options.find(o => o.value == this.value);
                return opt ? opt.label : null;
            },

            get isDisabled() { return this.$el.hasAttribute('disabled'); },

            toggle() {
                this.isOpen = !this.isOpen;
                if (this.isOpen) {
                    this.$nextTick(() => { this.$el.querySelector('input[type="text"]')?.focus(); });
                }
            },
            close() { this.isOpen = false; },

            select(option) {
                this.value = option.value;
                this.close();
                this.search = '';
                this.$dispatch('input', option.value); 
                this.$dispatch('change', option.value);
                this.$dispatch('option-selected', option); // Dispatch full option wrapper
            }
        }));
    });
</script>
@endonce
