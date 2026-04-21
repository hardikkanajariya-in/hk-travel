@props(['heading' => null, 'subheading' => null])

@php
    $items = [
        ['route' => 'profile.edit', 'label' => __('settings.shell.nav.profile')],
        ['route' => 'security.edit', 'label' => __('settings.shell.nav.security')],
        ['route' => 'appearance.edit', 'label' => __('settings.shell.nav.appearance')],
    ];

    // Build a breadcrumb trail for the main container so each settings
    // page doesn't need to push entries individually.
    $breadcrumbs = app(\App\Core\Seo\BreadcrumbService::class);
    $breadcrumbs->clear();
    $breadcrumbs->push(__('settings.shell.title'), route('profile.edit'));
    foreach ($items as $i) {
        if (request()->routeIs($i['route'])) {
            $breadcrumbs->push($i['label']);
            break;
        }
    }
    $trail = $breadcrumbs->all();
@endphp

<div class="space-y-6">
    {{-- Breadcrumb --}}
    @if (! empty($trail))
        <nav aria-label="{{ __('Breadcrumb') }}" class="text-sm">
            <ol class="flex flex-wrap items-center gap-1.5 text-zinc-500 dark:text-zinc-400">
                @foreach ($trail as $i => $crumb)
                    <li class="flex items-center gap-1.5">
                        @if ($i > 0)
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="size-3.5 text-zinc-400" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                            </svg>
                        @endif
                        @if (! empty($crumb['url']) && ! $loop->last)
                            <a href="{{ $crumb['url'] }}" wire:navigate class="hover:text-hk-primary-600 dark:hover:text-hk-primary-400">{{ $crumb['name'] }}</a>
                        @else
                            <span @class(['text-zinc-700 dark:text-zinc-200 font-medium' => $loop->last])>{{ $crumb['name'] }}</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
    @endif

    <div class="flex items-start gap-10 max-md:flex-col">
        <nav class="md:w-56 w-full" aria-label="{{ __('settings.shell.title') }}">
            <ul class="space-y-1">
                @foreach ($items as $item)
                    @php $active = request()->routeIs($item['route']); @endphp
                    <li>
                        <a href="{{ route($item['route']) }}" wire:navigate
                           @class([
                               'block rounded-md px-3 py-2 text-sm transition',
                               'bg-hk-primary-50 text-hk-primary-700 dark:bg-hk-primary-900/30 dark:text-hk-primary-300 font-medium' => $active,
                               'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' => ! $active,
                           ])>
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="flex-1 self-stretch max-md:pt-2 max-w-2xl">
            @if ($heading)
                <h2 class="text-lg font-semibold">{{ $heading }}</h2>
            @endif
            @if ($subheading)
                <p class="mt-1 text-sm text-zinc-500">{{ $subheading }}</p>
            @endif

            <div class="mt-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
