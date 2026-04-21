@php
    $items = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'home'],
        ['label' => 'Modules', 'route' => 'admin.modules', 'icon' => 'squares-2x2'],
        ['label' => 'Settings', 'route' => 'admin.settings', 'icon' => 'cog-6-tooth'],
        ['label' => 'Users', 'route' => 'admin.users', 'icon' => 'users'],
    ];
@endphp

<aside class="hidden md:flex w-64 shrink-0 flex-col border-r border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
    <div class="flex h-16 items-center gap-3 border-b border-zinc-200 dark:border-zinc-800 px-6">
        <div class="flex size-8 items-center justify-center rounded-md bg-hk-primary-600 text-white font-bold">
            {{ substr(config('hk.brand.name', 'HK'), 0, 1) }}
        </div>
        <span class="font-semibold">{{ config('hk.brand.name') }}</span>
    </div>

    <nav class="flex-1 space-y-1 p-3">
        @foreach ($items as $item)
            @php $active = request()->routeIs($item['route']); @endphp
            <a href="{{ \Illuminate\Support\Facades\Route::has($item['route']) ? route($item['route']) : '#' }}"
               wire:navigate
               class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition
                      {{ $active
                            ? 'bg-hk-primary-50 text-hk-primary-700 dark:bg-hk-primary-950 dark:text-hk-primary-300'
                            : 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}">
                <span class="size-4 inline-block">{{-- icon slot reserved --}}</span>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="border-t border-zinc-200 dark:border-zinc-800 p-3 text-xs text-zinc-500">
        v0.1.0
    </div>
</aside>
