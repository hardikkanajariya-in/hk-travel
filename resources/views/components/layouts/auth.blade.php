@php
    use App\Core\Localization\LocaleManager;
    $locale = $locale ?? app()->getLocale();
    $isRtl = app(LocaleManager::class)->isRtl($locale);
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('hk.brand.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-full bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 antialiased">
    <main class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md space-y-6">
            <div class="text-center">
                <a href="{{ url('/') }}" class="inline-block">
                    <span class="text-2xl font-bold tracking-tight">{{ config('hk.brand.name') }}</span>
                </a>
                @isset($title)
                    <h1 class="mt-6 text-2xl font-semibold">{{ $title }}</h1>
                @endisset
                @isset($description)
                    <p class="mt-2 text-sm text-zinc-500">{{ $description }}</p>
                @endisset
            </div>

            <x-ui.card>
                {{ $slot }}
            </x-ui.card>
        </div>
    </main>

    @livewireScriptConfig
</body>
</html>
