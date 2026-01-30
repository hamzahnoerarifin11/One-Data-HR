@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center gap-2">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <button disabled class="rounded-lg border px-3 py-2 text-sm bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-gray-700 dark:text-gray-500 dark:border-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Prev
            </button>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="rounded-lg border px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white dark:border-gray-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Prev
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="rounded-lg border px-3 py-2 text-sm bg-gray-100 text-gray-400 dark:bg-gray-700 dark:text-gray-500 dark:border-gray-600">
                    {{ $element }}
                </span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <button class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500 text-white text-sm font-medium">
                            {{ $page }}
                        </button>
                    @else
                        <a href="{{ $url }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-sm hover:bg-blue-500/10 hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-500 transition">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="rounded-lg border px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white dark:border-gray-600 transition">
                Next
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        @else
            <button disabled class="rounded-lg border px-3 py-2 text-sm bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-gray-700 dark:text-gray-500 dark:border-gray-600">
                Next
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        @endif
    </nav>
@endif
