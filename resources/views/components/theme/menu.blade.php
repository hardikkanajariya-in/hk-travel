@props([
    'location' => 'primary',
    'orientation' => 'horizontal',
    'class' => null,
])

@php
    $menu = \App\Models\Menu::forLocation($location);
    $items = $menu ? $menu->rootItems()->with('children.children')->get() : collect();
    $locale = app()->getLocale();
    $isVertical = $orientation === 'vertical';
@endphp

@if ($items->isNotEmpty())
    <nav aria-label="{{ $location }} menu" class="{{ $class }}">
        <ul class="{{ $isVertical
            ? 'flex flex-col gap-1'
            : 'flex flex-wrap items-center gap-1 sm:gap-2' }}">
            @foreach ($items as $item)
                <li class="relative group" @if ($item->children->isNotEmpty()) x-data="{ open: false }" @endif>
                    <a href="{{ $item->resolveUrl($locale) ?? '#' }}"
                       target="{{ $item->target ?? '_self' }}"
                       @if ($item->children->isNotEmpty())
                           @click.prevent="open = ! open"
                           @click.away="open = false"
                       @endif
                       class="inline-flex items-center gap-1 rounded-md px-3 py-2 text-sm font-medium text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition {{ $item->css_class }}">
                        {{ $item->localizedLabel($locale) }}
                        @if ($item->children->isNotEmpty())
                            <svg class="size-3 opacity-60" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.06l3.71-3.83a.75.75 0 011.08 1.04l-4.25 4.39a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        @endif
                    </a>

                    @if ($item->children->isNotEmpty())
                        <ul x-show="open" x-transition x-cloak
                            class="{{ $isVertical
                                ? 'mt-1 ml-4 space-y-1'
                                : 'absolute left-0 top-full z-30 mt-1 min-w-48 rounded-md border border-zinc-200 bg-white p-1 shadow-lg dark:border-zinc-800 dark:bg-zinc-900' }}">
                            @foreach ($item->children as $child)
                                <li>
                                    <a href="{{ $child->resolveUrl($locale) ?? '#' }}"
                                       target="{{ $child->target ?? '_self' }}"
                                       class="block rounded px-3 py-2 text-sm text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 {{ $child->css_class }}">
                                        {{ $child->localizedLabel($locale) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </nav>
@endif
