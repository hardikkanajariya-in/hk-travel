@props(['href' => null, 'icon' => null])

@php
    $base = 'flex items-center gap-2 w-full px-4 py-2 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($base) }} role="menuitem" wire:navigate>
        @if ($icon)<x-dynamic-component :component="'heroicon-'.$icon" class="size-4" />@endif
        <span>{{ $slot }}</span>
    </a>
@else
    <button type="button" {{ $attributes->class($base.' text-left') }} role="menuitem">
        @if ($icon)<x-dynamic-component :component="'heroicon-'.$icon" class="size-4" />@endif
        <span>{{ $slot }}</span>
    </button>
@endif
