@php
    $branding = app(\App\Core\Branding\BrandingService::class);
    $destinations = [
        ['name' => 'Bali', 'tag' => 'Island escape', 'emoji' => '🌴', 'gradient' => 'from-emerald-400 via-teal-500 to-cyan-600'],
        ['name' => 'Paris', 'tag' => 'City of lights', 'emoji' => '🗼', 'gradient' => 'from-rose-400 via-pink-500 to-fuchsia-600'],
        ['name' => 'Tokyo', 'tag' => 'Neon nights', 'emoji' => '🏯', 'gradient' => 'from-indigo-500 via-violet-500 to-purple-600'],
        ['name' => 'Santorini', 'tag' => 'Aegean blues', 'emoji' => '⛵', 'gradient' => 'from-sky-400 via-blue-500 to-indigo-600'],
        ['name' => 'Dubai', 'tag' => 'Gold & dunes', 'emoji' => '🕌', 'gradient' => 'from-amber-400 via-orange-500 to-red-500'],
        ['name' => 'Maldives', 'tag' => 'Turquoise dreams', 'emoji' => '🐠', 'gradient' => 'from-cyan-400 via-teal-400 to-emerald-500'],
    ];
@endphp

<x-layouts.app>
    @push('head')
        <style>
            @keyframes hk-float { 0%,100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-24px) rotate(6deg); } }
            @keyframes hk-float-slow { 0%,100% { transform: translateY(0) translateX(0); } 50% { transform: translateY(-30px) translateX(20px); } }
            @keyframes hk-gradient-shift { 0%,100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }
            @keyframes hk-marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }
            @keyframes hk-pulse-ring { 0% { transform: scale(.8); opacity: .7; } 100% { transform: scale(2.2); opacity: 0; } }
            .hk-animated-bg { background-size: 220% 220%; animation: hk-gradient-shift 18s ease infinite; }
            .hk-float { animation: hk-float 8s ease-in-out infinite; }
            .hk-float-slow { animation: hk-float-slow 12s ease-in-out infinite; }
            .hk-marquee { animation: hk-marquee 40s linear infinite; }
            .hk-pulse-ring::before,
            .hk-pulse-ring::after {
                content: ''; position: absolute; inset: 0; border-radius: 9999px;
                background: rgba(255,255,255,.35); animation: hk-pulse-ring 2.4s cubic-bezier(.2,.7,.2,1) infinite;
            }
            .hk-pulse-ring::after { animation-delay: 1.2s; }
            @media (prefers-reduced-motion: reduce) {
                .hk-float, .hk-float-slow, .hk-animated-bg, .hk-marquee, .hk-pulse-ring::before, .hk-pulse-ring::after { animation: none !important; }
            }
        </style>
    @endpush

    {{-- Coming soon ribbon --}}
    <div class="relative z-10 overflow-hidden bg-gradient-to-r from-amber-400 via-orange-500 to-rose-500 text-white">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-center gap-x-4 gap-y-1 px-4 py-2 text-center text-sm font-medium">
            <span class="inline-flex size-2 animate-ping rounded-full bg-white/80" aria-hidden="true"></span>
            <span class="font-semibold uppercase tracking-widest">{{ __('Coming soon') }}</span>
            <span class="opacity-90">{{ __('We are crafting something extraordinary. Tours, hotels & unforgettable journeys — launching soon.') }}</span>
        </div>
    </div>

    {{-- Hero --}}
    <section class="relative isolate overflow-hidden">
        {{-- animated gradient layer --}}
        <div class="hk-animated-bg absolute inset-0 -z-20 bg-[linear-gradient(120deg,#0ea5e9,#6366f1_25%,#8b5cf6_50%,#ec4899_75%,#f59e0b)]"></div>
        {{-- soft radial glow --}}
        <div class="absolute inset-0 -z-10 opacity-60 [background-image:radial-gradient(circle_at_20%_20%,rgba(255,255,255,.35),transparent_50%),radial-gradient(circle_at_80%_60%,rgba(255,255,255,.25),transparent_45%)]"></div>

        {{-- floating shapes --}}
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
            <div class="hk-float absolute left-[8%] top-[18%] size-24 rounded-3xl bg-white/10 backdrop-blur-md ring-1 ring-white/20"></div>
            <div class="hk-float-slow absolute right-[10%] top-[12%] size-32 rotate-12 rounded-full bg-white/10 backdrop-blur-md ring-1 ring-white/20"></div>
            <div class="hk-float absolute bottom-[14%] left-[18%] size-16 rotate-45 rounded-2xl bg-white/10 backdrop-blur-md ring-1 ring-white/20"></div>
            <div class="hk-float-slow absolute bottom-[8%] right-[22%] size-20 rounded-full bg-white/10 backdrop-blur-md ring-1 ring-white/20"></div>
        </div>

        <div class="mx-auto flex max-w-6xl flex-col items-center gap-8 px-4 py-28 text-center text-white sm:px-6 sm:py-36 lg:py-44">
            <p class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.2em] ring-1 ring-inset ring-white/25 backdrop-blur">
                <span class="relative inline-flex size-2 rounded-full bg-white hk-pulse-ring"></span>
                {{ $branding->tagline() ?? __('Your next adventure starts here') }}
            </p>

            <h1 class="text-balance text-5xl font-extrabold leading-[1.05] tracking-tight drop-shadow-xl sm:text-6xl lg:text-7xl">
                {{ __('Travel beyond') }}
                <span class="block bg-gradient-to-r from-yellow-200 via-white to-amber-100 bg-clip-text text-transparent">
                    {{ __('the ordinary') }}
                </span>
            </h1>

            <p class="max-w-2xl text-balance text-lg opacity-95 sm:text-xl">
                {{ __('Curated tours, hand-picked hotels, and seamless journeys — all from :brand.', ['brand' => $branding->siteName()]) }}
            </p>

            {{-- Interactive search card --}}
            <form
                x-data="{ type: 'tours', where: '', when: '' }"
                @submit.prevent
                class="mt-4 w-full max-w-3xl rounded-2xl bg-white/95 p-2 text-zinc-900 shadow-2xl ring-1 ring-white/40 backdrop-blur dark:bg-zinc-900/90 dark:text-zinc-100"
            >
                <div class="flex flex-wrap gap-1 px-2 pt-1">
                    @foreach (['tours' => '🧭 Tours', 'hotels' => '🏨 Hotels', 'flights' => '✈️ Flights', 'activities' => '🎟️ Activities'] as $key => $label)
                        <button
                            type="button"
                            @click="type = @js($key)"
                            :class="type === @js($key) ? 'bg-hk-primary-600 text-white shadow' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800'"
                            class="rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                        >{{ __($label) }}</button>
                    @endforeach
                </div>

                <div class="mt-2 grid grid-cols-1 gap-2 md:grid-cols-[1fr_1fr_auto]">
                    <label class="flex items-center gap-2 rounded-xl bg-zinc-100 px-3 py-2.5 dark:bg-zinc-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="size-5 text-zinc-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                        </svg>
                        <input x-model="where" type="text" placeholder="{{ __('Where to?') }}" class="w-full bg-transparent text-sm outline-none placeholder:text-zinc-500">
                    </label>
                    <label class="flex items-center gap-2 rounded-xl bg-zinc-100 px-3 py-2.5 dark:bg-zinc-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="size-5 text-zinc-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                        </svg>
                        <input x-model="when" type="date" class="w-full bg-transparent text-sm outline-none">
                    </label>
                    <button type="button" disabled
                            title="{{ __('Launching soon') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-hk-primary-600 to-hk-primary-500 px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition hover:from-hk-primary-500 hover:to-hk-primary-400 disabled:opacity-80">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                        {{ __('Search') }}
                    </button>
                </div>
                <p class="px-3 py-2 text-center text-[11px] text-zinc-500">
                    {{ __('Search goes live with our launch. Stay tuned!') }}
                </p>
            </form>

            {{-- Stats --}}
            <dl class="mt-6 grid w-full max-w-3xl grid-cols-3 gap-4 text-center">
                @foreach ([['120+', 'Destinations'], ['50k+', 'Happy travelers'], ['4.9★', 'Average rating']] as [$k, $v])
                    <div class="rounded-xl bg-white/10 p-4 ring-1 ring-white/20 backdrop-blur transition hover:bg-white/15">
                        <dt class="text-2xl font-bold sm:text-3xl">{{ $k }}</dt>
                        <dd class="mt-1 text-xs uppercase tracking-widest opacity-80">{{ __($v) }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        {{-- Wave bottom --}}
        <svg class="absolute inset-x-0 bottom-0 block w-full text-white dark:text-zinc-950" viewBox="0 0 1440 80" preserveAspectRatio="none" aria-hidden="true">
            <path fill="currentColor" d="M0,64L60,58.7C120,53,240,43,360,48C480,53,600,75,720,74.7C840,75,960,53,1080,42.7C1200,32,1320,32,1380,32L1440,32L1440,80L0,80Z"/>
        </svg>
    </section>

    {{-- Marquee strip --}}
    <section class="overflow-hidden border-y border-zinc-200/60 bg-white py-4 dark:border-zinc-800/60 dark:bg-zinc-950">
        <div class="hk-marquee flex w-max gap-10 whitespace-nowrap text-sm font-medium text-zinc-500">
            @for ($i = 0; $i < 2; $i++)
                @foreach (['✈️ Worldwide flights', '🏨 Handpicked hotels', '🧭 Guided tours', '🎟️ Exclusive activities', '🚢 Luxury cruises', '🛂 Hassle-free visas', '💎 Best-price guarantee', '🕑 24/7 support'] as $chip)
                    <span>{{ __($chip) }}</span>
                @endforeach
            @endfor
        </div>
    </section>

    {{-- Destinations --}}
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="mb-10 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-hk-primary-600">{{ __('Explore') }}</p>
                <h2 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">{{ __('Dreamy destinations') }}</h2>
                <p class="mt-2 max-w-xl text-zinc-600 dark:text-zinc-400">{{ __('A taste of what awaits. Our full catalogue rolls out at launch.') }}</p>
            </div>
            <span class="inline-flex items-center gap-2 rounded-full bg-zinc-100 px-3 py-1 text-xs font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                <span class="size-1.5 rounded-full bg-amber-500"></span> {{ __('Preview') }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-3">
            @foreach ($destinations as $d)
                <a href="#"
                   class="group relative aspect-[4/5] overflow-hidden rounded-2xl ring-1 ring-zinc-900/5 transition hover:-translate-y-1 hover:shadow-2xl">
                    <div class="absolute inset-0 bg-gradient-to-br {{ $d['gradient'] }} transition duration-700 group-hover:scale-110"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>
                    <div class="absolute right-3 top-3 text-4xl transition group-hover:scale-125 sm:text-5xl">{{ $d['emoji'] }}</div>
                    <div class="absolute inset-x-4 bottom-4 text-white">
                        <p class="text-[11px] font-semibold uppercase tracking-widest opacity-80">{{ __($d['tag']) }}</p>
                        <h3 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">{{ __($d['name']) }}</h3>
                        <span class="mt-2 inline-flex items-center gap-1 text-xs font-medium opacity-0 transition group-hover:opacity-100">
                            {{ __('Discover') }}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Features --}}
    <section class="bg-zinc-50 py-20 dark:bg-zinc-900/30">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-hk-primary-600">{{ __('Why us') }}</p>
                <h2 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">{{ __('Travel, beautifully organised') }}</h2>
            </div>

            <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-3">
                @foreach ([
                    ['icon' => '🧭', 'title' => 'Hand-picked tours', 'body' => 'Curated by local experts who know each destination inside out.', 'color' => 'from-emerald-400 to-teal-500'],
                    ['icon' => '💎', 'title' => 'Best-price guarantee', 'body' => 'Find it cheaper? We will match the price and refund the difference.', 'color' => 'from-sky-400 to-indigo-500'],
                    ['icon' => '💬', 'title' => '24/7 human support', 'body' => 'Real humans, ready to help wherever you are in the world.', 'color' => 'from-rose-400 to-fuchsia-500'],
                ] as $f)
                    <article class="group relative overflow-hidden rounded-2xl bg-white p-6 ring-1 ring-zinc-200 transition hover:-translate-y-1 hover:shadow-xl dark:bg-zinc-900 dark:ring-zinc-800">
                        <div class="absolute -right-12 -top-12 size-40 rounded-full bg-gradient-to-br {{ $f['color'] }} opacity-10 transition group-hover:opacity-20 group-hover:scale-125"></div>
                        <div class="relative">
                            <div class="inline-flex size-12 items-center justify-center rounded-xl bg-gradient-to-br {{ $f['color'] }} text-2xl shadow-lg">
                                <span>{{ $f['icon'] }}</span>
                            </div>
                            <h3 class="mt-4 text-lg font-semibold">{{ __($f['title']) }}</h3>
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ __($f['body']) }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Notify / CTA --}}
    <section class="relative isolate overflow-hidden py-20">
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-hk-primary-700 via-hk-primary-600 to-indigo-700"></div>
        <div class="absolute inset-0 -z-10 opacity-30 [background-image:radial-gradient(circle_at_70%_30%,white,transparent_45%)]"></div>

        <div class="mx-auto grid max-w-5xl grid-cols-1 items-center gap-10 px-4 text-white sm:px-6 lg:grid-cols-2">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] opacity-90">{{ __('Be first to know') }}</p>
                <h2 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">{{ __('Get early access when we launch') }}</h2>
                <p class="mt-3 max-w-md text-white/85">{{ __('Join our launch list for exclusive offers and early access to curated journeys.') }}</p>
            </div>

            <form @submit.prevent class="flex w-full flex-col gap-2 rounded-2xl bg-white/10 p-2 ring-1 ring-white/20 backdrop-blur sm:flex-row">
                <label class="sr-only" for="hk-notify">{{ __('Email address') }}</label>
                <input id="hk-notify" type="email" required placeholder="{{ __('you@example.com') }}"
                       class="w-full rounded-xl bg-white/90 px-4 py-3 text-sm text-zinc-900 placeholder:text-zinc-500 outline-none focus:ring-2 focus:ring-white">
                <button type="button" disabled title="{{ __('Launching soon') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-hk-primary-700 shadow-lg transition hover:bg-zinc-50 disabled:opacity-80">
                    {{ __('Notify me') }}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                </button>
            </form>
        </div>
    </section>
</x-layouts.app>
