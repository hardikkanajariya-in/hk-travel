@php
    $branding = app(\App\Core\Branding\BrandingService::class);
@endphp

<x-layouts.app>
    <section class="relative isolate overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-hk-primary-600 via-hk-primary-700 to-hk-primary-900"></div>
        <div class="absolute inset-0 -z-10 opacity-30 [background-image:radial-gradient(circle_at_30%_20%,white,transparent_40%)]"></div>

        <div class="mx-auto flex max-w-5xl flex-col items-center gap-6 px-6 py-28 text-center text-white sm:py-36">
            <p class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-widest">
                {{ $branding->tagline() ?? __('Your next adventure starts here') }}
            </p>
            <h1 class="text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
                {{ $branding->siteName() }}
            </h1>
            <p class="max-w-2xl text-lg opacity-90">
                {{ __('Curated tours, hand-picked hotels, and seamless trips — all in one place. Publish your homepage from the admin to replace this welcome screen.') }}
            </p>
            <div class="mt-2 flex flex-wrap justify-center gap-3">
                <a href="{{ url('/admin') }}"
                   class="inline-flex items-center rounded-md bg-white px-5 py-2.5 text-sm font-semibold text-hk-primary-700 shadow hover:bg-zinc-50 transition">
                    {{ __('Open admin') }}
                </a>
                <a href="#"
                   class="inline-flex items-center rounded-md border border-white/40 px-5 py-2.5 text-sm font-semibold text-white hover:bg-white/10 transition">
                    {{ __('Browse tours') }}
                </a>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            @foreach (['Hand-picked tours' => 'Curated by local experts who know each destination inside out.', 'Best-price guarantee' => 'Find it cheaper? We will match the price and refund the difference.', '24/7 support' => 'Real humans, ready to help wherever you are in the world.'] as $title => $body)
                <article class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900">
                    <h3 class="text-lg font-semibold">{{ __($title) }}</h3>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ __($body) }}</p>
                </article>
            @endforeach
        </div>
    </section>
</x-layouts.app>
