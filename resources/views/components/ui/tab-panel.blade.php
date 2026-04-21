@props(['name', 'class' => ''])

<div x-show="tab === @js($name)" x-cloak {{ $attributes->merge(['class' => 'space-y-6 '.$class]) }}>
    {{ $slot }}
</div>
