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
    {!! app(\App\Core\Branding\BrandingService::class)->headTags() !!}
    @vite(['resources/css/admin.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full overflow-hidden bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 antialiased">
    <div
        class="flex h-screen overflow-hidden"
        x-data="{
            sidebarOpen: false,
            sidebarCollapsed: (typeof localStorage !== 'undefined' && localStorage.getItem('hk.admin.sidebarCollapsed') === '1'),
        }"
        x-on:keydown.escape.window="sidebarOpen = false"
    >
        <x-admin.sidebar />

        <div class="flex min-w-0 flex-1 flex-col overflow-hidden">
            <x-admin.topbar :title="$title" />

            <main class="flex-1 overflow-y-auto px-4 py-6 sm:px-6 sm:py-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScriptConfig
    @stack('scripts')
</body>
</html>
