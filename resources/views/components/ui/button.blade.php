@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'icon' => null,
    'iconTrailing' => null,
    'loading' => false,
    'disabled' => false,
])

@php
    $base = 'inline-flex items-center justify-center gap-2 font-medium rounded-md transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-hk-primary-500 disabled:opacity-50 disabled:cursor-not-allowed';

    $variants = [
        'primary'   => 'bg-hk-primary-600 text-white hover:bg-hk-primary-700 active:bg-hk-primary-800',
        'secondary' => 'bg-zinc-100 text-zinc-900 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-100 dark:hover:bg-zinc-700',
        'outline'   => 'border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 hover:bg-zinc-50 dark:hover:bg-zinc-800',
        'ghost'     => 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800',
        'danger'    => 'bg-hk-danger text-white hover:opacity-90',
        'success'   => 'bg-hk-success text-white hover:opacity-90',
        'link'      => 'text-hk-primary-600 hover:underline underline-offset-4',
    ];

    $sizes = [
        'xs' => 'px-2 py-1 text-xs',
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-5 py-2.5 text-base',
        'xl' => 'px-6 py-3 text-base',
    ];

    $classes = trim($base.' '.($variants[$variant] ?? $variants['primary']).' '.($sizes[$size] ?? $sizes['md']));
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }} @if($disabled) aria-disabled="true" tabindex="-1" @endif wire:navigate>
        @if ($loading)
            <x-ui.spinner size="sm" />
        @elseif ($icon)
            <x-dynamic-component :component="'heroicon-'.$icon" class="size-4" />
        @endif
        {{ $slot }}
        @if ($iconTrailing)
            <x-dynamic-component :component="'heroicon-'.$iconTrailing" class="size-4" />
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }} @disabled($disabled || $loading)>
        @if ($loading)
            <x-ui.spinner size="sm" />
        @endif
        {{ $slot }}
    </button>
@endif
