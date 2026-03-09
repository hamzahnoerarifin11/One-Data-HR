@props(['label', 'value'])

<div {{ $attributes }}>
    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</p>
    <p class="mt-1 font-medium text-gray-900 dark:text-white">
        {{ $value ?: '-' }}
    </p>
</div>
