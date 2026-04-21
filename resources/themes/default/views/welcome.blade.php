<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ app(\App\Core\Localization\LocaleManager::class)->isRtl() ? 'rtl' : 'ltr' }}"
      class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('hk.brand.name') }} — {{ config('hk.brand.tagline') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 antialiased">
    <div class="mx-auto max-w-3xl px-6 py-24 text-center">
        <h1 class="text-4xl font-bold tracking-tight">{{ config('hk.brand.name') }}</h1>
        <p class="mt-4 text-lg text-zinc-600 dark:text-zinc-400">{{ config('hk.brand.tagline') }}</p>

        <div class="mt-10 flex items-center justify-center gap-3">
            @auth
                <a href="{{ route('admin.dashboard') }}"
                   class="inline-flex items-center rounded-md bg-hk-primary-600 px-4 py-2 text-white text-sm font-medium hover:bg-hk-primary-700 transition">
                    Go to admin
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="inline-flex items-center rounded-md bg-hk-primary-600 px-4 py-2 text-white text-sm font-medium hover:bg-hk-primary-700 transition">
                    Sign in
                </a>
            @endauth
        </div>

        <p class="mt-12 text-xs text-zinc-500">
            Default theme · enable modules from the admin panel to start adding tours, hotels, and more.
        </p>
    </div>
</body>
</html>
