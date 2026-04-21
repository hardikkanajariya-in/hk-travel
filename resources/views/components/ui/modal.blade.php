@props([
    'name' => null,
    'title' => null,
    'maxWidth' => '2xl',
    'closable' => true,
])

@php
    $widths = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '3xl' => 'sm:max-w-3xl',
        '4xl' => 'sm:max-w-4xl',
    ];
    $width = $widths[$maxWidth] ?? $widths['2xl'];
@endphp

{{--
    Self-contained Alpine modal.
    Open via: $dispatch('open-modal', { name: 'foo' }) or wire:click="$dispatch('open-modal', { name: 'foo' })"
    Close via: $dispatch('close-modal', { name: 'foo' }) or any nested element setting `open = false`.
--}}
<div
    x-data="{ open: false, name: @js($name) }"
    x-on:open-modal.window="if ($event.detail?.name === name) { open = true; $nextTick(() => $refs.panel?.focus()); }"
    x-on:close-modal.window="if ($event.detail?.name === name) open = false"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    role="dialog"
    aria-modal="true"
    @if ($title) aria-label="{{ $title }}" @endif
    class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4"
>
    <div
        x-show="open"
        x-transition.opacity.duration.150ms
        @if ($closable) @click="open = false" @endif
        class="absolute inset-0 bg-zinc-900/50 backdrop-blur-sm"
        aria-hidden="true"
    ></div>

    <div
        x-ref="panel"
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-trap.inert.noscroll="open"
        tabindex="-1"
        class="relative w-full {{ $width }} rounded-lg bg-white dark:bg-zinc-900 shadow-hk-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden"
    >
        @if ($title || $closable)
            <div class="flex items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 px-6 py-4">
                <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ $title }}</h2>
                @if ($closable)
                    <button type="button" @click="open = false" class="rounded p-1 text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg>
                    </button>
                @endif
            </div>
        @endif

        <div class="px-6 py-5">{{ $slot }}</div>

        @isset ($footer)
            <div class="border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 px-6 py-3 flex justify-end gap-2">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
