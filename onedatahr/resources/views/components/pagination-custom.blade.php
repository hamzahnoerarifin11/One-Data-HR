@if ($paginator->hasPages())
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3 border-t bg-white dark:bg-gray-800 dark:text-gray-950 border-gray-200 dark:border-gray-700">

    {{-- INFO (DESKTOP ONLY) --}}
    <div class="hidden sm:block text-sm text-gray-600 dark:text-gray-400">
        Showing 
        <span class="font-semibold text-gray-900 dark:text-white">{{ $paginator->firstItem() }}</span>
        to
        <span class="font-semibold text-gray-900 dark:text-white">{{ $paginator->lastItem() }}</span>
        of
        <span class="font-semibold text-gray-900 dark:text-white">{{ $paginator->total() }}</span>
        results
    </div>

    {{-- PAGINATION BUTTONS --}}
    <div class="flex items-center justify-center gap-1 w-full sm:w-auto">

        {{-- PREV --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1 text-sm text-gray-400 dark:text-gray-600 cursor-not-allowed border border-transparent">
                Prev
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" 
               class="px-3 py-1 text-sm rounded border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300 transition">
                Prev
            </a>
        @endif

        {{-- PAGE INFO (MOBILE ONLY) --}}
        <span class="sm:hidden text-xs text-gray-500 px-2">
            Page {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
        </span>

        {{-- PAGE NUMBERS (DESKTOP ONLY) --}}
        <div class="hidden sm:flex gap-1">
            @foreach ($elements as $element)
                
                {{-- 1. BAGIAN INI YANG KEMARIN HILANG (UNTUK MENAMPILKAN TITIK-TITIK) --}}
                @if (is_string($element))
                    <span class="px-3 py-1 text-sm text-gray-500 dark:text-gray-400 cursor-default">
                        {{ $element }}
                    </span>
                @endif

                {{-- 2. ARRAY OF LINKS --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-3 py-1 text-sm rounded bg-blue-600 text-white border border-blue-600">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" 
                               class="px-3 py-1 text-sm rounded border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300 transition">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif

            @endforeach
        </div>

        {{-- NEXT --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" 
               class="px-3 py-1 text-sm rounded border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300 transition">
                Next
            </a>
        @else
            <span class="px-3 py-1 text-sm text-gray-400 dark:text-gray-600 cursor-not-allowed border border-transparent">
                Next
            </span>
        @endif
    </div>
</div>
@endif