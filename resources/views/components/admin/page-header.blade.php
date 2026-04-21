@props([
    'title' => null,           // kept for back-compat — no longer rendered (topbar shows it)
    'description' => null,
    'subtitle' => null,        // alias for description
    'breadcrumbs' => null,     // array<int, ['label' => string, 'route' => ?string, 'url' => ?string]>
])

@php
    $crumbs = $breadcrumbs ?: \App\Core\Support\Breadcrumbs::forCurrentRoute();
    $caption = $description ?? $subtitle;
@endphp

<header class="mb-6 flex flex-wrap items-end justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-4">
    <div class="min-w-0">
        @if (! empty($crumbs))
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-1 text-xs text-zinc-500 dark:text-zinc-400">
                    @foreach ($crumbs as $i => $crumb)
                        @php
                            $isLast = $i === count($crumbs) - 1;
                            $href = isset($crumb['route']) && \Illuminate\Support\Facades\Route::has($crumb['route'])
                                ? route($crumb['route'])
                                : ($crumb['url'] ?? null);
                        @endphp
                        <li class="flex items-center gap-1">
                            @if ($i > 0)
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-3.5 text-zinc-300 dark:text-zinc-600" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                                </svg>
                            @endif

                            @if ($href && ! $isLast)
                                <a href="{{ $href }}" wire:navigate class="rounded px-1 py-0.5 hover:text-hk-primary-600 hover:bg-zinc-100 dark:hover:bg-zinc-800 dark:hover:text-hk-primary-400">
                                    {!! $crumb['label'] !!}
                                </a>
                            @else
                                <span @class([
                                    'rounded px-1 py-0.5',
                                    'font-medium text-zinc-900 dark:text-zinc-100' => $isLast,
                                ])>{!! $crumb['label'] !!}</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        @endif

        @if ($caption)
            <p class="mt-2 max-w-3xl text-sm text-zinc-500 dark:text-zinc-400">{{ $caption }}</p>
        @endif
    </div>

    @isset ($actions)
        <div class="flex items-center gap-2">{{ $actions }}</div>
    @endisset
</header>
