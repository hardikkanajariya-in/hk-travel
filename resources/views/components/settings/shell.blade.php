@props(['heading' => null, 'subheading' => null])

@php
    $items = [
        ['route' => 'profile.edit', 'label' => __('Profile')],
        ['route' => 'security.edit', 'label' => __('Security')],
        ['route' => 'appearance.edit', 'label' => __('Appearance')],
    ];
@endphp

<div class="space-y-6">
    <header>
        <h1 class="text-2xl font-semibold">{{ __('Settings') }}</h1>
        <p class="mt-1 text-sm text-zinc-500">{{ __('Manage your profile and account settings') }}</p>
    </header>

    <hr class="border-zinc-200 dark:border-zinc-800">

    <div class="flex items-start gap-10 max-md:flex-col">
        <nav class="md:w-56 w-full" aria-label="{{ __('Settings') }}">
            <ul class="space-y-1">
                @foreach ($items as $item)
                    @php $active = request()->routeIs($item['route']); @endphp
                    <li>
                        <a href="{{ route($item['route']) }}" wire:navigate
                           @class([
                               'block rounded-md px-3 py-2 text-sm transition',
                               'bg-hk-primary-50 text-hk-primary-700 dark:bg-hk-primary-900/30 dark:text-hk-primary-300 font-medium' => $active,
                               'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' => ! $active,
                           ])>
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="flex-1 self-stretch max-md:pt-6 max-w-2xl">
            @if ($heading)
                <h2 class="text-lg font-semibold">{{ $heading }}</h2>
            @endif
            @if ($subheading)
                <p class="mt-1 text-sm text-zinc-500">{{ $subheading }}</p>
            @endif

            <div class="mt-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
