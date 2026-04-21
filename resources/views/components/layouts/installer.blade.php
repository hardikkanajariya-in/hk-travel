<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'HK Travel — Install' }}</title>
    @vite(['resources/css/admin.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="hk-aurora min-h-full bg-gradient-to-br from-zinc-50 via-white to-zinc-100 dark:from-zinc-950 dark:via-zinc-900 dark:to-zinc-950 text-zinc-900 dark:text-zinc-100 antialiased">
    <main class="relative z-10 flex min-h-screen items-center justify-center px-4 py-10 sm:py-16">
        {{ $slot }}
    </main>
    @livewireScriptConfig
</body>
</html>
