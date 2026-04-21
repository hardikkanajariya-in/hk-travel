@props([
    'variant' => 'info',
    'title' => null,
    'dismissible' => false,
])

@php
    $variants = [
        'info'    => 'bg-hk-primary-50 text-hk-primary-900 border-hk-primary-200 dark:bg-hk-primary-950 dark:text-hk-primary-100 dark:border-hk-primary-800',
        'success' => 'bg-green-50 text-green-900 border-green-200 dark:bg-green-950 dark:text-green-100 dark:border-green-800',
        'warning' => 'bg-amber-50 text-amber-900 border-amber-200 dark:bg-amber-950 dark:text-amber-100 dark:border-amber-800',
        'danger'  => 'bg-red-50 text-red-900 border-red-200 dark:bg-red-950 dark:text-red-100 dark:border-red-800',
    ];
    $cls = 'rounded-md border p-4 text-sm '.($variants[$variant] ?? $variants['info']);
@endphp

<div
    role="alert"
    @if ($dismissible) x-data="{ shown: true }" x-show="shown" x-transition.opacity @endif
    {{ $attributes->class($cls) }}
>
    <div class="flex items-start gap-3">
        <div class="grow">
            @if ($title)<p class="font-semibold mb-1">{{ $title }}</p>@endif
            <div>{{ $slot }}</div>
        </div>
        @if ($dismissible)
            <button type="button" @click="shown = false" class="shrink-0 opacity-60 hover:opacity-100" aria-label="Dismiss">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg>
            </button>
        @endif
    </div>
</div>
