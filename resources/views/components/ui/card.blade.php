@props([
    'padded' => true,
    'as' => 'div',
    'interactive' => false,
])

@php
    $base = implode(' ', [
        'relative rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80',
        'bg-white/95 dark:bg-zinc-900/80 backdrop-blur-sm',
        'shadow-[0_1px_2px_rgb(0_0_0/0.04),0_8px_24px_-12px_rgb(0_0_0/0.08)]',
        'dark:shadow-[0_1px_2px_rgb(0_0_0/0.5),0_12px_32px_-12px_rgb(0_0_0/0.6)]',
    ]);

    if ($padded) {
        $base .= ' p-6 sm:p-8';
    }

    if ($interactive) {
        $base .= ' hk-lift cursor-pointer hover:border-hk-primary-300/80 dark:hover:border-hk-primary-700/80 hover:shadow-[0_4px_8px_rgb(0_0_0/0.06),0_24px_48px_-16px_rgb(0_0_0/0.18)]';
    }
@endphp

<{{ $as }} {{ $attributes->class($base) }}>
    {{ $slot }}
</{{ $as }}>
