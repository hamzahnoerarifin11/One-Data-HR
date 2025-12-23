@props(["title" => null, "actions" => null])

<div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
    @if($title)
        <div class="flex items-start justify-between mb-3">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $title }}</h3>
            </div>
            @if($actions)
                <div class="flex items-center gap-2">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    <div>
        {{ $slot }}
    </div>
</div>
