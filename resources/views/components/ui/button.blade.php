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
    $base = implode(' ', [
        'group/btn relative inline-flex items-center justify-center gap-2 cursor-pointer select-none whitespace-nowrap',
        'font-semibold tracking-tight rounded-xl',
        'transition-[transform,box-shadow,background-color,border-color,color] duration-200 ease-out',
        'will-change-transform',
        'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-hk-primary-500',
        'focus-visible:ring-offset-white dark:focus-visible:ring-offset-zinc-950',
        'hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98]',
        'disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0',
    ]);

    $variants = [
        'primary' => implode(' ', [
            'text-white shadow-md shadow-hk-primary-600/25',
            'bg-[linear-gradient(135deg,var(--color-hk-primary-500)_0%,var(--color-hk-primary-700)_100%)]',
            'hover:shadow-lg hover:shadow-hk-primary-600/35',
            'hover:bg-[linear-gradient(135deg,var(--color-hk-primary-400)_0%,var(--color-hk-primary-600)_100%)]',
            'active:shadow-sm',
        ]),
        'secondary' => implode(' ', [
            'bg-white text-zinc-900 ring-1 ring-zinc-200 shadow-sm',
            'hover:bg-zinc-50 hover:ring-zinc-300 hover:shadow-md',
            'dark:bg-zinc-800 dark:text-zinc-100 dark:ring-zinc-700 dark:hover:bg-zinc-700 dark:hover:ring-zinc-600',
        ]),
        'outline' => implode(' ', [
            'bg-transparent text-zinc-900 ring-1 ring-zinc-300',
            'hover:bg-zinc-50 hover:ring-hk-primary-400 hover:text-hk-primary-700 hover:shadow-sm',
            'dark:text-zinc-100 dark:ring-zinc-700 dark:hover:bg-zinc-800/60 dark:hover:text-hk-primary-300',
        ]),
        'ghost' => implode(' ', [
            'bg-transparent text-zinc-700',
            'hover:bg-zinc-100 hover:text-zinc-900',
            'dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-zinc-100',
        ]),
        'danger' => implode(' ', [
            'text-white shadow-md shadow-red-600/25',
            'bg-[linear-gradient(135deg,#f87171_0%,#dc2626_100%)]',
            'hover:shadow-lg hover:shadow-red-600/35',
            'hover:bg-[linear-gradient(135deg,#fca5a5_0%,#ef4444_100%)]',
        ]),
        'success' => implode(' ', [
            'text-white shadow-md shadow-emerald-600/25',
            'bg-[linear-gradient(135deg,#34d399_0%,#059669_100%)]',
            'hover:shadow-lg hover:shadow-emerald-600/35',
            'hover:bg-[linear-gradient(135deg,#6ee7b7_0%,#10b981_100%)]',
        ]),
        'accent' => implode(' ', [
            'text-white shadow-md shadow-hk-accent-600/25',
            'bg-[linear-gradient(135deg,var(--color-hk-accent-400)_0%,var(--color-hk-accent-600)_100%)]',
            'hover:shadow-lg hover:shadow-hk-accent-600/35',
            'hover:bg-[linear-gradient(135deg,var(--color-hk-accent-300)_0%,var(--color-hk-accent-500)_100%)]',
        ]),
        'link' => implode(' ', [
            'bg-transparent text-hk-primary-600 underline-offset-4',
            'hover:text-hk-primary-700 hover:underline',
            'dark:text-hk-primary-400 dark:hover:text-hk-primary-300',
            'shadow-none hover:translate-y-0',
        ]),
    ];

    $sizes = [
        'xs' => 'px-2.5 py-1 text-xs rounded-lg',
        'sm' => 'px-3.5 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-5 py-2.5 text-base',
        'xl' => 'px-7 py-3 text-base',
    ];

    $iconSize = match ($size) {
        'xs', 'sm' => 'size-3.5',
        'lg', 'xl' => 'size-5',
        default => 'size-4',
    };

    $classes = trim(
        $base
        .' '.($variants[$variant] ?? $variants['primary'])
        .' '.($sizes[$size] ?? $sizes['md'])
    );
@endphp

@if ($href)
    <a
        href="{{ $href }}"
        {{ $attributes->class($classes) }}
        @if ($disabled) aria-disabled="true" tabindex="-1" @endif
        wire:navigate
    >
        @if ($loading)
            <x-ui.spinner size="sm" />
        @elseif ($icon)
            <x-dynamic-component
                :component="'heroicon-'.$icon"
                class="{{ $iconSize }} transition-transform duration-200 group-hover/btn:-translate-x-0.5"
            />
        @endif
        <span class="inline-flex items-center">{{ $slot }}</span>
        @if ($iconTrailing)
            <x-dynamic-component
                :component="'heroicon-'.$iconTrailing"
                class="{{ $iconSize }} transition-transform duration-200 group-hover/btn:translate-x-0.5"
            />
        @endif
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->class($classes) }}
        @disabled($disabled || $loading)
    >
        @if ($loading)
            <x-ui.spinner size="sm" />
        @elseif ($icon)
            <x-dynamic-component
                :component="'heroicon-'.$icon"
                class="{{ $iconSize }} transition-transform duration-200 group-hover/btn:-translate-x-0.5"
            />
        @endif
        <span class="inline-flex items-center">{{ $slot }}</span>
        @if ($iconTrailing)
            <x-dynamic-component
                :component="'heroicon-'.$iconTrailing"
                class="{{ $iconSize }} transition-transform duration-200 group-hover/btn:translate-x-0.5"
            />
        @endif
    </button>
@endif

