@props(['title' => null])

@php
    $branding = app(\App\Core\Branding\BrandingService::class);
    $locale = app()->getLocale();
    $isRtl = app(\App\Core\Localization\LocaleManager::class)->isRtl();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}"
      dir="{{ $isRtl ? 'rtl' : 'ltr' }}"
      class="h-full scroll-smooth"
      x-data="{
          dark: localStorage.getItem('hk-theme') === 'dark'
              || (!('hk-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
      }"
      x-init="$watch('dark', v => { localStorage.setItem('hk-theme', v ? 'dark' : 'light'); document.documentElement.classList.toggle('dark', v); }); document.documentElement.classList.toggle('dark', dark);">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' · '.$branding->siteName() : $branding->siteName() }}</title>
    {!! $branding->headTags() !!}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('head')
</head>
<body class="min-h-full bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 antialiased">
    <a href="#main"
       class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:rounded-md focus:bg-hk-primary-600 focus:px-3 focus:py-2 focus:text-sm focus:text-white">
        {{ __('Skip to content') }}
    </a>

    @if (($branding->showHeader ?? true) !== false)
        @include('layouts.partials.header')
    @endif

    <main id="main" class="min-h-[60vh]">
        {{ $slot }}
    </main>

    @if (($branding->showFooter ?? true) !== false)
        @include('layouts.partials.footer')
    @endif

    @livewireScriptConfig
    @stack('scripts')
</body>
</html>
