@props([
    'align' => 'right',
    'width' => '48',
])

@php
    $aligns = [
        'left' => 'origin-top-left left-0',
        'right' => 'origin-top-right right-0',
    ];
    $alignCls = $aligns[$align] ?? $aligns['right'];
    $widthCls = "w-{$width}";
@endphp

<div x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative inline-block text-left">
    <div @click="open = !open" class="cursor-pointer">{{ $trigger ?? '' }}</div>

    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-40 mt-2 {{ $widthCls }} {{ $alignCls }} rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-hk-lg ring-1 ring-black/5 focus:outline-none"
        role="menu"
    >
        <div class="py-1">{{ $slot }}</div>
    </div>
</div>
