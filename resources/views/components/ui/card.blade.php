@props([
    'padded' => true,
    'as' => 'div',
])

@php
    $base = 'rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-hk-sm';
    if ($padded) { $base .= ' p-6'; }
@endphp

<{{ $as }} {{ $attributes->class($base) }}>
    {{ $slot }}
</{{ $as }}>
