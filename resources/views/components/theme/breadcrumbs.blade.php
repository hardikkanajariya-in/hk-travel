@props(['homeLabel' => null])

@php
    $crumbs = app(\App\Core\Seo\BreadcrumbService::class)->all();
    $homeLabel = $homeLabel ?: __('Home');
@endphp

@if (! empty($crumbs))
    <nav aria-label="{{ __('Breadcrumb') }}" {{ $attributes->merge(['class' => 'text-sm']) }}>
        <ol class="flex flex-wrap items-center gap-1.5 text-zinc-500 dark:text-zinc-400">
            <li class="flex items-center gap-1.5">
                <a href="{{ url('/') }}" wire:navigate class="hover:text-hk-primary-600 dark:hover:text-hk-primary-400">
                    {{ $homeLabel }}
                </a>
            </li>
            @foreach ($crumbs as $i => $c)
                <li class="flex items-center gap-1.5">
                    <svg class="size-3 text-zinc-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                    </svg>
                    @if ($i < count($crumbs) - 1 && ! empty($c['url']))
                        <a href="{{ $c['url'] }}" wire:navigate class="hover:text-hk-primary-600 dark:hover:text-hk-primary-400">{{ $c['name'] }}</a>
                    @else
                        <span aria-current="page" class="text-zinc-700 dark:text-zinc-200 font-medium">{{ $c['name'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
