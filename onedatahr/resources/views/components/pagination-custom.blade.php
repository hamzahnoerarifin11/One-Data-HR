@if ($paginator->hasPages())
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3 border-t bg-white">

    {{-- INFO (DESKTOP ONLY) --}}
    <div class="hidden sm:block text-sm text-gray-600">
        Showing 
        <span class="font-semibold">{{ $paginator->firstItem() }}</span>
        –
        <span class="font-semibold">{{ $paginator->lastItem() }}</span>
        of
        <span class="font-semibold">{{ $paginator->total() }}</span>
    </div>

    {{-- PAGINATION --}}
    <div class="flex items-center justify-center gap-1">

        {{-- PREV --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1 text-sm text-gray-400 cursor-not-allowed">Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" 
               class="px-3 py-1 text-sm rounded hover:bg-gray-100">
                Prev
            </a>
        @endif

        {{-- PAGE INFO (MOBILE) --}}
        <span class="sm:hidden text-xs text-gray-500 px-2">
            Page {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
        </span>

        {{-- PAGE NUMBERS (DESKTOP ONLY) --}}
        <div class="hidden sm:flex gap-1">
            @foreach ($elements as $element)
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-3 py-1 text-sm rounded bg-blue-600 text-white">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" 
                               class="px-3 py-1 text-sm rounded hover:bg-blue-100">
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
               class="px-3 py-1 text-sm rounded hover:bg-gray-100">
                Next
            </a>
        @else
            <span class="px-3 py-1 text-sm text-gray-400 cursor-not-allowed">
                Next
            </span>
        @endif
    </div>
</div>
@endif
