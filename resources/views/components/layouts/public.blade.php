@props(['title' => null])
@php
    use App\Core\Localization\LocaleManager;
    use App\Core\Seo\SeoManager;
    $locale = app()->getLocale();
    $isRtl = app(LocaleManager::class)->isRtl($locale);
    $seo = app(SeoManager::class);
    if ($title) { $seo->title($title); }
    $snap = $seo->snapshot();
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $snap['title'] }}</title>
    @if ($snap['description'])
        <meta name="description" content="{{ $snap['description'] }}">
    @endif
    @if ($snap['canonical'])
        <link rel="canonical" href="{{ $snap['canonical'] }}">
    @endif
    @if ($snap['noindex'])
        <meta name="robots" content="noindex,nofollow">
    @endif
    @if ($snap['image'])
        <meta property="og:image" content="{{ $snap['image'] }}">
    @endif
    <meta property="og:title" content="{{ $snap['title'] }}">
    @if ($snap['description'])
        <meta property="og:description" content="{{ $snap['description'] }}">
    @endif
    {!! app(\App\Core\Branding\BrandingService::class)->headTags() !!}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-full bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 antialiased">
    <header class="border-b border-zinc-200 dark:border-zinc-800 bg-white/80 dark:bg-zinc-950/80 backdrop-blur sticky top-0 z-30">
        <div class="mx-auto max-w-7xl px-6 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" wire:navigate class="flex items-center gap-2 font-bold text-lg">
                <span class="flex size-8 items-center justify-center rounded-md bg-hk-primary-600 text-white">
                    {{ substr(config('hk.brand.name', 'HK'), 0, 1) }}
                </span>
                <span>{{ config('hk.brand.name') }}</span>
            </a>

            <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
                @php $manager = app(\App\Core\Modules\ModuleManager::class); @endphp
                @if ($manager->enabled('tours'))
                    <a href="{{ route('tours.index') }}" wire:navigate class="hover:text-hk-primary-600">Tours</a>
                @endif
                @if ($manager->enabled('hotels'))
                    <a href="{{ route('hotels.index') }}" wire:navigate class="hover:text-hk-primary-600">Hotels</a>
                @endif
                @if ($manager->enabled('flights'))
                    <a href="{{ route('flights.index') }}" wire:navigate class="hover:text-hk-primary-600">Flights</a>
                @endif
                @if ($manager->enabled('activities'))
                    <a href="{{ route('activities.index') }}" wire:navigate class="hover:text-hk-primary-600">Activities</a>
                @endif
                @if ($manager->enabled('cruises'))
                    <a href="{{ route('cruises.index') }}" wire:navigate class="hover:text-hk-primary-600">Cruises</a>
                @endif
                @if ($manager->enabled('cars'))
                    <a href="{{ route('cars.index') }}" wire:navigate class="hover:text-hk-primary-600">Car Rentals</a>
                @endif
                @if ($manager->enabled('visa'))
                    <a href="{{ route('visa.index') }}" wire:navigate class="hover:text-hk-primary-600">Visa</a>
                @endif
                @if ($manager->enabled('destinations'))
                    <a href="{{ route('destinations.index') }}" wire:navigate class="hover:text-hk-primary-600">Destinations</a>
                @endif
            </nav>

            <div class="flex items-center gap-3">
                <x-ui.locale-switcher />
                @auth
                    <a href="{{ url('/account') }}" wire:navigate class="text-sm font-medium hover:text-hk-primary-600">Account</a>
                @else
                    <a href="{{ route('login') }}" wire:navigate class="text-sm font-medium hover:text-hk-primary-600">Sign in</a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    <footer class="mt-16 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50">
        <div class="mx-auto max-w-7xl px-6 py-10 text-sm text-zinc-500 flex flex-col md:flex-row gap-4 items-center justify-between">
            <span>&copy; {{ date('Y') }} {{ config('hk.brand.name') }}. {{ __('All rights reserved.') }}</span>
            @zone('footer-1')
        </div>
    </footer>

    @livewireScriptConfig
</body>
</html>
