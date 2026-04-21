@php
    use App\Models\Widget;

    $branding = app(\App\Core\Branding\BrandingService::class);
    $settings = app(\App\Core\Settings\SettingsRepository::class);
    $year = now()->year;

    $email = $settings->get('contact.email');
    $phone = $settings->get('contact.phone');
    $addr  = $settings->get('contact.address');

    // Admin-configurable: how many content columns sit next to the brand block.
    // Brand column is always shown, so total columns = $columns + 1 (capped at 5).
    $columns = (int) $settings->get('theme.footer_columns', 3);
    $columns = max(1, min(4, $columns));

    // Friendly fallback content shown when an admin has not yet added widgets
    // to a footer column — so a fresh install never looks empty.
    $hasWidgets = [
        1 => Widget::forZone('footer-1')->isNotEmpty(),
        2 => Widget::forZone('footer-2')->isNotEmpty(),
        3 => Widget::forZone('footer-3')->isNotEmpty(),
        4 => Widget::forZone('footer-4')->isNotEmpty(),
    ];

    // Tailwind needs the classes to appear verbatim, so map column count to a
    // pre-known class string instead of building it dynamically.
    $gridClass = match ($columns) {
        1 => 'md:grid-cols-2 lg:grid-cols-2',
        2 => 'md:grid-cols-2 lg:grid-cols-3',
        3 => 'md:grid-cols-2 lg:grid-cols-4',
        4 => 'md:grid-cols-2 lg:grid-cols-5',
    };
@endphp

<footer class="relative mt-20 overflow-hidden border-t border-zinc-200 bg-gradient-to-b from-zinc-50 to-white dark:border-zinc-800 dark:from-zinc-900 dark:to-zinc-950">
    <x-theme.decoration variant="dots" class="absolute -right-10 top-10 size-40 text-hk-primary-200/40 dark:text-hk-primary-900/30" />
    <x-theme.decoration variant="blob-a" class="absolute -left-32 bottom-0 size-[26rem] text-hk-primary-100/40 dark:text-hk-primary-950/30" />

    <div class="relative mx-auto grid max-w-7xl grid-cols-1 gap-10 px-4 py-14 sm:px-6 {{ $gridClass }} lg:px-8">

        {{-- Brand column --}}
        <div class="lg:col-span-1">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
                @if ($logo = $branding->logoUrl())
                    <img src="{{ $logo }}" alt="{{ $branding->siteName() }}" class="h-9 w-auto">
                @else
                    <span class="inline-flex size-9 items-center justify-center rounded-xl bg-gradient-to-br from-hk-primary-500 to-indigo-500 text-white font-bold shadow">
                        {{ \Illuminate\Support\Str::of($branding->siteName())->substr(0, 1)->upper() }}
                    </span>
                @endif
                <span class="text-lg font-bold tracking-tight">{{ $branding->siteName() }}</span>
            </a>
            <p class="mt-3 max-w-xs text-sm text-zinc-600 dark:text-zinc-400">
                {{ $branding->tagline() ?? __('Curated journeys, hand-picked stays, and unforgettable experiences.') }}
            </p>

            {{-- Social --}}
            <div class="mt-5 flex gap-2">
                @foreach ([
                    ['label' => 'Facebook', 'href' => '#', 'd' => 'M22 12a10 10 0 1 0-11.6 9.9V14.9H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.3v6.9A10 10 0 0 0 22 12Z'],
                    ['label' => 'Instagram', 'href' => '#', 'd' => 'M12 2.2c3.2 0 3.6 0 4.8.1 1.2.1 2 .2 2.5.5.7.3 1.2.6 1.7 1.1.5.5.8 1 1.1 1.7.2.6.4 1.4.5 2.5.1 1.2.1 1.6.1 4.8s0 3.6-.1 4.8c-.1 1.2-.2 2-.5 2.5-.3.7-.6 1.2-1.1 1.7-.5.5-1 .8-1.7 1.1-.6.2-1.4.4-2.5.5-1.2.1-1.6.1-4.8.1s-3.6 0-4.8-.1c-1.2-.1-2-.2-2.5-.5-.7-.3-1.2-.6-1.7-1.1-.5-.5-.8-1-1.1-1.7-.2-.6-.4-1.4-.5-2.5C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.8c.1-1.2.2-2 .5-2.5.3-.7.6-1.2 1.1-1.7.5-.5 1-.8 1.7-1.1.6-.2 1.4-.4 2.5-.5C8.4 2.2 8.8 2.2 12 2.2Zm0 5.3a4.5 4.5 0 1 0 0 9 4.5 4.5 0 0 0 0-9Zm0 1.8a2.7 2.7 0 1 1 0 5.4 2.7 2.7 0 0 1 0-5.4Zm5.7-1.9a1.05 1.05 0 1 1 0-2.1 1.05 1.05 0 0 1 0 2.1Z'],
                    ['label' => 'X', 'href' => '#', 'd' => 'M18.244 3H21l-6.52 7.45L22 21h-6.812l-4.93-6.451L4.5 21H2l7.07-8.082L2 3h6.94l4.46 5.91L18.244 3Zm-2.39 16.2h1.49L7.27 4.7H5.69l10.164 14.5Z'],
                    ['label' => 'YouTube', 'href' => '#', 'd' => 'M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4A2.5 2.5 0 0 0 2.4 7.2 26 26 0 0 0 2 12a26 26 0 0 0 .4 4.8 2.5 2.5 0 0 0 1.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8A26 26 0 0 0 22 12a26 26 0 0 0-.4-4.8ZM10 15V9l5.2 3-5.2 3Z'],
                ] as $s)
                    <a href="{{ $s['href'] }}" aria-label="{{ $s['label'] }}"
                       class="inline-flex size-9 items-center justify-center rounded-full bg-white text-zinc-600 shadow-sm ring-1 ring-zinc-200 transition hover:-translate-y-0.5 hover:bg-hk-primary-600 hover:text-white hover:ring-hk-primary-600 dark:bg-zinc-800 dark:text-zinc-300 dark:ring-zinc-700">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4"><path d="{{ $s['d'] }}"/></svg>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Column 1: zone or fallback "About" --}}
        @if ($columns >= 1)
        <div>
            @if ($hasWidgets[1])
                @zone('footer-1')
            @else
                <h3 class="text-sm font-bold uppercase tracking-widest text-zinc-900 dark:text-zinc-100">{{ __('Explore') }}</h3>
                <ul class="mt-4 space-y-2 text-sm text-zinc-600 dark:text-zinc-400">
                    <li><a href="{{ url('/') }}" class="transition hover:text-hk-primary-600">{{ __('Home') }}</a></li>
                    <li><a href="{{ url('/tours') }}" class="transition hover:text-hk-primary-600">{{ __('Tours') }}</a></li>
                    <li><a href="{{ url('/about') }}" class="transition hover:text-hk-primary-600">{{ __('About us') }}</a></li>
                    <li><a href="{{ url('/contact') }}" class="transition hover:text-hk-primary-600">{{ __('Contact') }}</a></li>
                </ul>
            @endif
        </div>
        @endif

        {{-- Column 2: zone or fallback footer menu --}}
        @if ($columns >= 2)
        <div>
            @if ($hasWidgets[2])
                @zone('footer-2')
            @else
                <h3 class="text-sm font-bold uppercase tracking-widest text-zinc-900 dark:text-zinc-100">{{ __('Helpful') }}</h3>
                <ul class="mt-4 space-y-2 text-sm text-zinc-600 dark:text-zinc-400">
                    <li><a href="{{ url('/privacy-policy') }}" class="transition hover:text-hk-primary-600">{{ __('Privacy policy') }}</a></li>
                    <li><a href="{{ url('/terms-and-conditions') }}" class="transition hover:text-hk-primary-600">{{ __('Terms & conditions') }}</a></li>
                    <li><a href="{{ url('/contact') }}" class="transition hover:text-hk-primary-600">{{ __('Get in touch') }}</a></li>
                </ul>
            @endif
        </div>
        @endif

        {{-- Column 3: zone or fallback contact --}}
        @if ($columns >= 3)
        <div>
            @if ($hasWidgets[3])
                @zone('footer-3')
            @else
                <h3 class="text-sm font-bold uppercase tracking-widest text-zinc-900 dark:text-zinc-100">{{ __('Stay in touch') }}</h3>
                <ul class="mt-4 space-y-3 text-sm text-zinc-600 dark:text-zinc-400">
                    @if ($email)
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mt-0.5 size-4 shrink-0 text-hk-primary-500"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                            <a href="mailto:{{ $email }}" class="break-all transition hover:text-hk-primary-600">{{ $email }}</a>
                        </li>
                    @endif
                    @if ($phone)
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mt-0.5 size-4 shrink-0 text-hk-primary-500"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                            <a href="tel:{{ $phone }}" class="transition hover:text-hk-primary-600">{{ $phone }}</a>
                        </li>
                    @endif
                    @if ($addr)
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mt-0.5 size-4 shrink-0 text-hk-primary-500"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                            <span>{{ $addr }}</span>
                        </li>
                    @endif
                    @unless ($email || $phone || $addr)
                        <li class="text-zinc-500">{{ __('Add your contact details from the admin to display them here.') }}</li>
                    @endunless
                </ul>
            @endif
        </div>
        @endif

        {{-- Column 4: optional, widget zone only (no fallback) --}}
        @if ($columns >= 4)
        <div>
            @if ($hasWidgets[4])
                @zone('footer-4')
            @else
                <h3 class="text-sm font-bold uppercase tracking-widest text-zinc-900 dark:text-zinc-100">{{ __('More') }}</h3>
                <p class="mt-4 text-sm text-zinc-500">{{ __('Add blocks to this column from the admin to fill it.') }}</p>
            @endif
        </div>
        @endif
    </div>

    <div class="relative border-t border-zinc-200 dark:border-zinc-800">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-5 text-xs text-zinc-500 sm:px-6 lg:px-8">
            <span>© {{ $year }} {{ $branding->siteName() }}. {{ __('All rights reserved.') }}</span>
            <x-theme.menu location="footer" class="text-xs" />
            <span>
                {{ __('Powered by') }}
                <a href="https://hardikkanajariya.in" target="_blank" rel="noopener"
                   class="font-semibold text-zinc-700 hover:text-hk-primary-600 dark:text-zinc-300">hardikkanajariya.in</a>
            </span>
        </div>
    </div>
</footer>
