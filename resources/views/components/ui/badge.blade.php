@props([
    'variant' => 'neutral',
    'size' => 'md',
])

@php
    $variants = [
        'neutral' => 'bg-zinc-100 text-zinc-700 ring-zinc-200/70 dark:bg-zinc-800 dark:text-zinc-300 dark:ring-zinc-700/70',
        'primary' => 'bg-hk-primary-50 text-hk-primary-700 ring-hk-primary-200 dark:bg-hk-primary-900/40 dark:text-hk-primary-200 dark:ring-hk-primary-800',
        'success' => 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-200 dark:ring-emerald-800',
        'warning' => 'bg-amber-50 text-amber-800 ring-amber-200 dark:bg-amber-900/40 dark:text-amber-200 dark:ring-amber-800',
        'danger'  => 'bg-red-50 text-red-700 ring-red-200 dark:bg-red-900/40 dark:text-red-200 dark:ring-red-800',
        'info'    => 'bg-sky-50 text-sky-700 ring-sky-200 dark:bg-sky-900/40 dark:text-sky-200 dark:ring-sky-800',
    ];
    $sizes = [
        'sm' => 'px-2 py-0.5 text-[11px]',
        'md' => 'px-2.5 py-1 text-xs',
        'lg' => 'px-3 py-1.5 text-sm',
    ];
    $cls = 'inline-flex items-center gap-1 rounded-full font-semibold ring-1 ring-inset '
        .($variants[$variant] ?? $variants['neutral']).' '
        .($sizes[$size] ?? $sizes['md']);
@endphp

<span {{ $attributes->class($cls) }}>{{ $slot }}</span>
