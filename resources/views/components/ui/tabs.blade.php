@props([
    'tabs' => [],
    'active' => null,
    'param' => 'tab',
])

@php
    $current = $active ?? request()->query($param, array_key_first($tabs));
@endphp

<div {{ $attributes->merge(['class' => 'space-y-4']) }}
     x-data="{ tab: @js($current) }"
     x-init="$watch('tab', v => { const url = new URL(window.location); url.searchParams.set(@js($param), v); window.history.replaceState({}, '', url); })">
    <nav class="flex gap-1 border-b border-zinc-200 dark:border-zinc-800 overflow-x-auto [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
        @foreach ($tabs as $key => $label)
            <button type="button"
                    x-on:click="tab = @js($key)"
                    :class="tab === @js($key)
                        ? 'border-hk-primary-600 text-hk-primary-700 dark:text-hk-primary-300'
                        : 'border-transparent text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100'"
                    class="px-4 py-2 text-sm font-medium border-b-2 -mb-px whitespace-nowrap transition">
                {{ $label }}
            </button>
        @endforeach
    </nav>

    <div>
        {{ $slot }}
    </div>
</div>
