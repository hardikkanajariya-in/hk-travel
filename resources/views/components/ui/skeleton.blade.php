@props([
    'shape' => 'rect',
    'class' => '',
])

@php
    $base = 'animate-pulse bg-zinc-200 dark:bg-zinc-800';
    $shapes = [
        'rect'   => 'rounded-md',
        'circle' => 'rounded-full',
        'pill'   => 'rounded-full',
        'text'   => 'rounded h-3',
    ];
    $shapeCls = $shapes[$shape] ?? $shapes['rect'];
@endphp

<div {{ $attributes->class("$base $shapeCls") }} aria-hidden="true"></div>
