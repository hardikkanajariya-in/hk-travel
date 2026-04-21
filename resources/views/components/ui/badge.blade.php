@props([
    'variant' => 'neutral',
    'size' => 'md',
])

@php
    $variants = [
        'neutral' => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300',
        'primary' => 'bg-hk-primary-100 text-hk-primary-800 dark:bg-hk-primary-900 dark:text-hk-primary-200',
        'success' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'warning' => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200',
        'danger'  => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        'info'    => 'bg-sky-100 text-sky-800 dark:bg-sky-900 dark:text-sky-200',
    ];
    $sizes = [
        'sm' => 'px-2 py-0.5 text-[11px]',
        'md' => 'px-2.5 py-0.5 text-xs',
        'lg' => 'px-3 py-1 text-sm',
    ];
    $cls = 'inline-flex items-center gap-1 rounded-full font-medium '
        .($variants[$variant] ?? $variants['neutral']).' '
        .($sizes[$size] ?? $sizes['md']);
@endphp

<span {{ $attributes->class($cls) }}>{{ $slot }}</span>
