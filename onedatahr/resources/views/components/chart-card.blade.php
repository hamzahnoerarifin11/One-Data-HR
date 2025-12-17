<div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-lg dark:border-gray-800 dark:bg-white/[0.03]">
    @if(isset($title))
        <h3 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white">{{ $title }}</h3>
    @endif
    <div>
        {{ $slot }}
    </div>
</div>
