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
<body class="min-h-full bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 antialiased">
    {{ $slot }}
    @livewireScriptConfig
</body>
</html>
