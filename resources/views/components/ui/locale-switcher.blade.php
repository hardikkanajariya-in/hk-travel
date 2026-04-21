@props(['variant' => 'dropdown'])

@php
    $manager = app(\App\Core\Localization\LocaleManager::class);
    $languages = $manager->languages();
    $current = app()->getLocale();
    $currentLang = $languages->firstWhere('code', $current);
@endphp

@if ($languages->count() > 1)
    @if ($variant === 'inline')
        <div {{ $attributes->merge(['class' => 'flex items-center gap-2 text-sm']) }}>
            @foreach ($languages as $lang)
                <a href="?lang={{ $lang['code'] }}"
                   class="px-2 py-1 rounded {{ $lang['code'] === $current ? 'bg-hk-primary-50 text-hk-primary-700 dark:bg-hk-primary-950 dark:text-hk-primary-300 font-medium' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400' }}">
                    @if (!empty($lang['flag']))<span class="mr-1 uppercase text-xs">{{ $lang['flag'] }}</span>@endif{{ $lang['native_name'] ?: $lang['name'] }}
                </a>
            @endforeach
        </div>
    @else
        <div x-data="{ open: false }" class="relative" {{ $attributes }}>
            <button type="button" x-on:click="open = !open"
                    class="flex items-center gap-2 px-3 py-1.5 rounded-md border border-zinc-200 dark:border-zinc-800 text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800">
                @if ($currentLang && !empty($currentLang['flag']))
                    <span class="uppercase text-xs">{{ $currentLang['flag'] }}</span>
                @endif
                <span>{{ $currentLang['native_name'] ?? $currentLang['name'] ?? strtoupper($current) }}</span>
                <svg class="size-3" viewBox="0 0 12 12" fill="currentColor"><path d="M6 8L2 4h8z"/></svg>
            </button>
            <div x-show="open" x-on:click.away="open = false" x-cloak
                 class="absolute right-0 mt-1 min-w-[180px] rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-lg z-50">
                @foreach ($languages as $lang)
                    <a href="?lang={{ $lang['code'] }}"
                       class="flex items-center gap-2 px-3 py-2 text-sm {{ $lang['code'] === $current ? 'bg-hk-primary-50 dark:bg-hk-primary-950 text-hk-primary-700 dark:text-hk-primary-300' : 'hover:bg-zinc-50 dark:hover:bg-zinc-800' }}">
                        @if (!empty($lang['flag']))<span class="uppercase text-xs w-6">{{ $lang['flag'] }}</span>@endif
                        <span>{{ $lang['native_name'] ?: $lang['name'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
@endif
