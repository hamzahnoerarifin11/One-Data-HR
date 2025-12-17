@props(['id','size' => 'md', 'title' => '','closeLabel' => 'Batal','confirmLabel' => 'Konfirmasi'])

<div x-cloak x-data="{ open:false, title: '{{ $title }}', message: '', targetForm: null, sizeClass: {
        'sm': 'max-w-md',
        'md': 'max-w-xl',
        'lg': 'max-w-3xl'
    }['{{ $size }}'], onConfirm(){ if(this.targetForm){ const f = document.getElementById(this.targetForm); if(f) f.submit(); } window.dispatchEvent(new CustomEvent('modal-confirmed',{detail:{id: '{{ $id }}', targetForm}})); this.open = false } }"
     x-init="window.addEventListener('open-modal', e => { if(!e?.detail) return; if(e.detail.id !== '{{ $id }}') return; title = e.detail.title || title; message = e.detail.message || message; targetForm = e.detail.targetForm || null; open = true; }); window.addEventListener('close-modal', e => { if(!e?.detail || !e.detail.id || e.detail.id === '{{ $id }}') open = false; });"
     x-show="open"
     @keydown.escape.window="open = false"
     role="dialog" aria-modal="true"
     class="fixed inset-0 z-50 flex items-center justify-center px-4">

    <!-- Overlay -->
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="open = false"></div>

    <!-- Dialog -->
    <div x-show="open" x-transition x-trap="open" aria-labelledby="{{ $id }}-title" class="relative w-full flex items-center justify-center">
        <div :class="sizeClass" class="mx-auto rounded-2xl bg-white p-6 shadow-lg dark:bg-gray-800">
            <header class="flex items-start justify-between">
                <h3 id="{{ $id }}-title" class="text-lg font-semibold text-gray-900 dark:text-white" x-text="title">{{ $title }}</h3>
                <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </header>

            <div class="mt-4 text-sm text-gray-600 dark:text-gray-300">
                <div x-show="message" x-text="message"></div>
                <div x-show="!message">{{ $slot }}</div>
            </div>

            <footer class="mt-6 flex justify-end gap-3">
                <button type="button" @click="open = false" class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ $closeLabel }}</button>
                <button type="button" @click="onConfirm()" class="rounded-lg bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700">{{ $confirmLabel }}</button>
            </footer>
        </div>
    </div>
</div>
