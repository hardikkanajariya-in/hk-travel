@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ app(\App\Core\Localization\LocaleManager::class)->isRtl() ? 'rtl' : 'ltr' }}"
      class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' · '.config('hk.brand.name') : config('hk.brand.name').' Admin' }}</title>
    @vite(['resources/css/admin.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-full bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 antialiased">
    <div class="flex min-h-screen">
        <x-admin.sidebar />

        <div class="flex flex-1 flex-col">
            <x-admin.topbar :title="$title" />

            <main class="flex-1 px-6 py-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScriptConfig
</body>
</html>
