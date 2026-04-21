@php
    $branding = app(\App\Core\Branding\BrandingService::class);
@endphp

<header class="sticky top-0 z-40 border-b border-zinc-200/80 bg-white/80 backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/80">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-6 px-4 sm:px-6 lg:px-8">
        <a href="{{ url('/') }}" class="flex items-center gap-3" wire:navigate>
            @if ($branding->logoUrl())
                <img src="{{ $branding->logoUrl() }}" alt="{{ $branding->siteName() }}"
                     class="h-9 w-auto dark:hidden">
                <img src="{{ $branding->darkLogoUrl() ?? $branding->logoUrl() }}" alt="{{ $branding->siteName() }}"
                     class="hidden h-9 w-auto dark:block">
            @else
                <span class="flex size-9 items-center justify-center rounded-md bg-hk-primary-600 font-bold text-white">
                    {{ substr($branding->siteName(), 0, 1) }}
                </span>
                <span class="text-lg font-semibold tracking-tight">{{ $branding->siteName() }}</span>
            @endif
        </a>

        <div class="hidden flex-1 justify-center md:flex">
            <x-theme.menu location="primary" />
        </div>

        <div class="flex items-center gap-2">
            <x-ui.locale-switcher />

            <button type="button" @click="dark = ! dark"
                    class="rounded-md p-2 text-zinc-600 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800"
                    :aria-label="dark ? 'Switch to light mode' : 'Switch to dark mode'">
                <svg x-show="!dark" class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                </svg>
                <svg x-show="dark" x-cloak class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm5.657 2.343a1 1 0 010 1.414l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 0zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zm-2.343 5.657a1 1 0 01-1.414 0l-.707-.707a1 1 0 011.414-1.414l.707.707a1 1 0 010 1.414zM10 18a1 1 0 01-1-1v-1a1 1 0 112 0v1a1 1 0 01-1 1zm-5.657-2.343a1 1 0 010-1.414l.707-.707a1 1 0 011.414 1.414l-.707.707a1 1 0 01-1.414 0zM2 10a1 1 0 011-1h1a1 1 0 110 2H3a1 1 0 01-1-1zm2.343-5.657a1 1 0 011.414 0l.707.707A1 1 0 015.05 6.464l-.707-.707a1 1 0 010-1.414zM10 6a4 4 0 100 8 4 4 0 000-8z" clip-rule="evenodd" />
                </svg>
            </button>

            <button type="button"
                    x-data="{ open: false }"
                    @click="open = ! open"
                    class="rounded-md p-2 text-zinc-600 hover:bg-zinc-100 md:hidden dark:text-zinc-300 dark:hover:bg-zinc-800"
                    aria-label="Toggle menu">
                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </div>

    <div x-data="{ open: false }" @click.away="open = false" class="md:hidden">
        <div x-show="open" x-collapse class="border-t border-zinc-200 px-4 pb-4 pt-2 dark:border-zinc-800">
            <x-theme.menu location="primary" orientation="vertical" />
        </div>
    </div>
</header>
