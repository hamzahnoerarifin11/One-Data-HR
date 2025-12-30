@if ($paginator->hasPages())
    <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
        
        {{-- INFO SHOWING DATA --}}
        <div class="text-sm text-gray-600 dark:text-gray-400">
            Showing 
            <span class="font-bold">{{ $paginator->firstItem() }}</span> 
            to 
            <span class="font-bold">{{ $paginator->lastItem() }}</span> 
            of 
            <span class="font-bold">{{ $paginator->total() }}</span> 
            entries
        </div>

        {{-- BUTTONS GROUP --}}
        <div class="flex items-center gap-2">
            
            {{-- TOMBOL PREVIOUS --}}
            @if ($paginator->onFirstPage())
                <button disabled class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-400 dark:border-gray-600 dark:text-gray-600 cursor-not-allowed">
                    Prev
                </button>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                    Prev
                </a>
            @endif

            {{-- TOMBOL NOMOR HALAMAN (Looping Element) --}}
            @foreach ($elements as $element)
                
                {{-- "Three Dots" Separator (...) --}}
                @if (is_string($element))
                    <span class="flex h-8 w-8 items-center justify-center text-gray-500">...</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            {{-- HALAMAN AKTIF (Biru) --}}
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg text-theme-sm font-medium bg-blue-500 text-white shadow-sm cursor-default">
                                {{ $page }}
                            </span>
                        @else
                            {{-- HALAMAN LAIN (Abu-abu) --}}
                            <a href="{{ $url }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-theme-sm font-medium text-gray-700 hover:bg-blue-500/[0.08] hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-500 transition">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- TOMBOL NEXT --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                    Next
                </a>
            @else
                <button disabled class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-400 dark:border-gray-600 dark:text-gray-600 cursor-not-allowed">
                    Next
                </button>
            @endif
        </div>
    </div>
@endif