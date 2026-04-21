@php
    $branding = app(\App\Core\Branding\BrandingService::class);

    /**
     * Curated default content. Photos are hot-linked from Unsplash (free,
     * https — already covered by our CSP `img-src https:`). The CMS still
     * controls everything once admins start editing pages.
     *
     * @var array<int, array{name:string,country:string,tag:string,price:string,image:string,duration:string}> $destinations
     */
    $destinations = [
        ['name' => 'Bali', 'country' => 'Indonesia', 'tag' => 'Island escape', 'duration' => '7 days', 'price' => '$1,290',
         'image' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=900&q=80&auto=format&fit=crop'],
        ['name' => 'Santorini', 'country' => 'Greece', 'tag' => 'Aegean blues', 'duration' => '6 days', 'price' => '$1,690',
         'image' => 'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=900&q=80&auto=format&fit=crop'],
        ['name' => 'Kyoto', 'country' => 'Japan', 'tag' => 'Ancient calm', 'duration' => '8 days', 'price' => '$2,150',
         'image' => 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=900&q=80&auto=format&fit=crop'],
        ['name' => 'Marrakech', 'country' => 'Morocco', 'tag' => 'Spice & souk', 'duration' => '5 days', 'price' => '$1,090',
         'image' => 'https://images.unsplash.com/photo-1597212720291-936e539d9498?w=900&q=80&auto=format&fit=crop'],
        ['name' => 'Patagonia', 'country' => 'Argentina', 'tag' => 'Wild south', 'duration' => '10 days', 'price' => '$2,890',
         'image' => 'https://images.unsplash.com/photo-1531168556467-80aace0d0144?w=900&q=80&auto=format&fit=crop'],
        ['name' => 'Maldives', 'country' => 'Indian Ocean', 'tag' => 'Turquoise dreams', 'duration' => '5 days', 'price' => '$2,490',
         'image' => 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?w=900&q=80&auto=format&fit=crop'],
    ];

    /** @var array<int, array{icon:string,title:string,body:string,color:string}> $features */
    $features = [
        ['icon' => '🧭', 'title' => 'Hand-picked tours', 'body' => 'Curated by local experts who know each destination inside out.', 'color' => 'from-emerald-400 to-teal-500'],
        ['icon' => '💎', 'title' => 'Best-price guarantee', 'body' => 'Find it cheaper? We will match the price and refund the difference.', 'color' => 'from-sky-400 to-indigo-500'],
        ['icon' => '💬', 'title' => '24/7 human support', 'body' => 'Real humans, ready to help wherever you are in the world.', 'color' => 'from-rose-400 to-fuchsia-500'],
        ['icon' => '🛡️', 'title' => 'Flexible cancellation', 'body' => 'Plans change. So do ours — book confidently with free changes.', 'color' => 'from-amber-400 to-orange-500'],
    ];

    /** @var array<int, array{title:string,image:string,duration:string,tag:string}> $experiences */
    $experiences = [
        ['title' => 'Sunrise hot-air balloon over Cappadocia', 'tag' => 'Adventure', 'duration' => '3 hrs',
         'image' => 'https://images.unsplash.com/photo-1641128324972-af3212f0f6bd?w=1100&q=80&auto=format&fit=crop'],
        ['title' => 'Private gondola through Venice canals', 'tag' => 'Romantic', 'duration' => '90 min',
         'image' => 'https://images.unsplash.com/photo-1523906834658-6e24ef2386f9?w=1100&q=80&auto=format&fit=crop'],
        ['title' => 'Northern Lights chase in Tromsø', 'tag' => 'Nature', 'duration' => '6 hrs',
         'image' => 'https://images.unsplash.com/photo-1483347756197-71ef80e95f73?w=1100&q=80&auto=format&fit=crop'],
    ];

    /** @var array<int, array{name:string,city:string,quote:string,avatar:string,rating:int}> $testimonials */
    $testimonials = [
        ['name' => 'Aanya P.', 'city' => 'Mumbai · India', 'rating' => 5,
         'quote' => 'They planned every tiny detail of our honeymoon. We just had to show up and fall in love with the place — and each other — all over again.',
         'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&q=80&auto=format&fit=crop'],
        ['name' => 'James R.', 'city' => 'London · UK', 'rating' => 5,
         'quote' => 'Best support I have ever had from a travel agent. When our flight was cancelled, they had us re-booked before we even reached the gate.',
         'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&q=80&auto=format&fit=crop'],
        ['name' => 'Maria S.', 'city' => 'Madrid · Spain', 'rating' => 5,
         'quote' => 'They turned a vague idea into the trip of a lifetime. Locally guided, beautifully paced, and completely worth it.',
         'avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=200&q=80&auto=format&fit=crop'],
    ];

    /** @var array<int, array{title:string,date:string,read:string,tag:string,image:string}> $stories */
    $stories = [
        ['title' => '10 places where autumn looks like a painting', 'tag' => 'Inspiration', 'date' => 'Oct 12', 'read' => '6 min',
         'image' => 'https://images.unsplash.com/photo-1507371341162-763b5e419408?w=900&q=80&auto=format&fit=crop'],
        ['title' => 'A first-timer\'s guide to slow travel in Vietnam', 'tag' => 'Guides', 'date' => 'Sep 28', 'read' => '8 min',
         'image' => 'https://images.unsplash.com/photo-1528127269322-539801943592?w=900&q=80&auto=format&fit=crop'],
        ['title' => 'Why off-season Europe is our favourite secret', 'tag' => 'Tips', 'date' => 'Sep 03', 'read' => '5 min',
         'image' => 'https://images.unsplash.com/photo-1467269204594-9661b134dd2b?w=900&q=80&auto=format&fit=crop'],
    ];
@endphp

<x-layouts.app>
    @push('head')
        <style>
            @keyframes hk-float { 0%,100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-22px) rotate(4deg); } }
            @keyframes hk-float-slow { 0%,100% { transform: translateY(0) translateX(0); } 50% { transform: translateY(-28px) translateX(18px); } }
            @keyframes hk-blob { 0%,100% { transform: translate(0,0) scale(1); } 33% { transform: translate(20px,-30px) scale(1.05); } 66% { transform: translate(-25px,15px) scale(.95); } }
            @keyframes hk-marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }
            @keyframes hk-pulse-ring { 0% { transform: scale(.85); opacity: .7; } 100% { transform: scale(2.2); opacity: 0; } }
            @keyframes hk-shine { 0% { transform: translateX(-150%); } 60%,100% { transform: translateX(150%); } }
            .hk-float { animation: hk-float 8s ease-in-out infinite; }
            .hk-float-slow { animation: hk-float-slow 12s ease-in-out infinite; }
            .hk-blob { animation: hk-blob 18s ease-in-out infinite; }
            .hk-marquee { animation: hk-marquee 40s linear infinite; }
            .hk-pulse-ring::before, .hk-pulse-ring::after {
                content: ''; position: absolute; inset: 0; border-radius: 9999px;
                background: rgba(255,255,255,.4); animation: hk-pulse-ring 2.4s cubic-bezier(.2,.7,.2,1) infinite;
            }
            .hk-pulse-ring::after { animation-delay: 1.2s; }
            .hk-shine::after {
                content:''; position:absolute; inset:0;
                background: linear-gradient(120deg, transparent 30%, rgba(255,255,255,.4) 50%, transparent 70%);
                transform: translateX(-150%);
            }
            .hk-card:hover .hk-shine::after { animation: hk-shine 1.1s ease; }
            @media (prefers-reduced-motion: reduce) {
                .hk-float, .hk-float-slow, .hk-blob, .hk-marquee, .hk-pulse-ring::before, .hk-pulse-ring::after, .hk-shine::after { animation: none !important; }
            }
        </style>
    @endpush

    {{-- ── Launch ribbon ───────────────────────────────────────────── --}}
    <div class="relative z-10 overflow-hidden bg-gradient-to-r from-amber-400 via-orange-500 to-rose-500 text-white">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-center gap-x-4 gap-y-1 px-4 py-2 text-center text-sm font-medium">
            <span class="relative inline-flex size-2 rounded-full bg-white hk-pulse-ring" aria-hidden="true"></span>
            <span class="font-semibold uppercase tracking-widest">{{ __('Coming soon') }}</span>
            <span class="opacity-90">{{ __('We are crafting something extraordinary. Tours, hotels & unforgettable journeys — launching soon.') }}</span>
        </div>
    </div>

    {{-- ── Hero ────────────────────────────────────────────────────── --}}
    <section class="relative isolate overflow-hidden">
        <img src="https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?w=2000&q=80&auto=format&fit=crop"
             alt="" loading="eager" decoding="async"
             class="absolute inset-0 -z-20 h-full w-full object-cover">
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-hk-primary-900/85 via-hk-primary-800/70 to-indigo-900/85"></div>

        <x-theme.decoration variant="blob-a" class="hk-blob absolute -left-24 top-10 size-[28rem] text-white/10" />
        <x-theme.decoration variant="blob-b" class="hk-blob absolute -right-32 bottom-0 size-[34rem] text-amber-300/15" />
        <x-theme.decoration variant="dots" class="absolute right-10 top-10 size-32 text-white/30 hidden md:block" />

        <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
            <div class="hk-float absolute left-[8%] top-[20%] size-20 rounded-3xl bg-white/10 ring-1 ring-white/20 backdrop-blur"></div>
            <div class="hk-float-slow absolute right-[8%] top-[18%] size-28 rotate-12 rounded-full bg-white/10 ring-1 ring-white/20 backdrop-blur"></div>
            <div class="hk-float absolute bottom-[18%] left-[16%] size-16 rotate-45 rounded-2xl bg-amber-300/20 ring-1 ring-white/20 backdrop-blur"></div>
        </div>

        <div class="mx-auto flex max-w-6xl flex-col items-center gap-8 px-4 pb-32 pt-24 text-center text-white sm:px-6 sm:pt-32 lg:pt-44">
            <p class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.2em] ring-1 ring-inset ring-white/25 backdrop-blur">
                <span class="relative inline-flex size-2 rounded-full bg-white hk-pulse-ring"></span>
                {{ $branding->tagline() ?? __('Your next adventure starts here') }}
            </p>

            <h1 class="text-balance text-5xl font-extrabold leading-[1.05] tracking-tight drop-shadow-2xl sm:text-6xl lg:text-7xl">
                {{ __('Travel beyond') }}
                <span class="block bg-gradient-to-r from-amber-200 via-white to-amber-100 bg-clip-text text-transparent">
                    {{ __('the ordinary') }}
                </span>
            </h1>

            <p class="max-w-2xl text-balance text-lg opacity-95 sm:text-xl">
                {{ __('Curated tours, hand-picked hotels, and seamless journeys — all from :brand.', ['brand' => $branding->siteName()]) }}
            </p>

            <form
                x-data="{ type: 'tours', where: '', when: '' }"
                @submit.prevent
                class="mt-4 w-full max-w-3xl rounded-2xl bg-white/95 p-2 text-zinc-900 shadow-2xl ring-1 ring-white/40 backdrop-blur dark:bg-zinc-900/90 dark:text-zinc-100"
            >
                <div class="flex flex-wrap gap-1 px-2 pt-1">
                    @foreach (['tours' => '🧭 Tours', 'hotels' => '🏨 Hotels', 'flights' => '✈️ Flights', 'activities' => '🎟️ Activities'] as $key => $label)
                        <button type="button"
                                @click="type = @js($key)"
                                :class="type === @js($key) ? 'bg-hk-primary-600 text-white shadow' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800'"
                                class="rounded-lg px-3 py-1.5 text-xs font-semibold transition">{{ __($label) }}</button>
                    @endforeach
                </div>

                <div class="mt-2 grid grid-cols-1 gap-2 md:grid-cols-[1fr_1fr_auto]">
                    <label class="flex items-center gap-2 rounded-xl bg-zinc-100 px-3 py-2.5 dark:bg-zinc-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="size-5 text-zinc-500"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                        <input x-model="where" type="text" placeholder="{{ __('Where to?') }}" class="w-full bg-transparent text-sm outline-none placeholder:text-zinc-500">
                    </label>
                    <label class="flex items-center gap-2 rounded-xl bg-zinc-100 px-3 py-2.5 dark:bg-zinc-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="size-5 text-zinc-500"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                        <input x-model="when" type="date" class="w-full bg-transparent text-sm outline-none">
                    </label>
                    <button type="button" disabled title="{{ __('Launching soon') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-hk-primary-600 to-hk-primary-500 px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition hover:from-hk-primary-500 hover:to-hk-primary-400 disabled:opacity-80">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                        {{ __('Search') }}
                    </button>
                </div>
                <p class="px-3 py-2 text-center text-[11px] text-zinc-500">{{ __('Search goes live with our launch. Stay tuned!') }}</p>
            </form>

            <dl class="mt-6 grid w-full max-w-3xl grid-cols-3 gap-4 text-center">
                @foreach ([['120+', 'Destinations'], ['50k+', 'Happy travelers'], ['4.9★', 'Average rating']] as [$k, $v])
                    <div class="rounded-xl bg-white/10 p-4 ring-1 ring-white/20 backdrop-blur transition hover:bg-white/15">
                        <dt class="text-2xl font-bold sm:text-3xl">{{ $k }}</dt>
                        <dd class="mt-1 text-xs uppercase tracking-widest opacity-80">{{ __($v) }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        <x-theme.decoration variant="wave" class="absolute inset-x-0 bottom-0 text-white dark:text-zinc-950" />
    </section>

    {{-- ── Trust strip / marquee ───────────────────────────────────── --}}
    <section class="overflow-hidden border-y border-zinc-200/60 bg-white py-4 dark:border-zinc-800/60 dark:bg-zinc-950">
        <div class="hk-marquee flex w-max gap-10 whitespace-nowrap text-sm font-medium text-zinc-500">
            @for ($i = 0; $i < 2; $i++)
                @foreach (['✈️ Worldwide flights', '🏨 Handpicked hotels', '🧭 Guided tours', '🎟️ Exclusive activities', '🚢 Luxury cruises', '🛂 Hassle-free visas', '💎 Best-price guarantee', '🕑 24/7 support'] as $chip)
                    <span>{{ __($chip) }}</span>
                @endforeach
            @endfor
        </div>
    </section>

    {{-- ── Destinations ────────────────────────────────────────────── --}}
    <section class="relative mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
        <x-theme.decoration variant="blob-a" class="absolute -left-20 top-20 -z-10 size-[26rem] text-hk-primary-100 dark:text-hk-primary-950/40 hk-blob" />
        <x-theme.decoration variant="dots" class="absolute right-0 top-32 -z-10 size-40 text-hk-primary-200 dark:text-hk-primary-900/40" />

        <div class="mb-12 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-hk-primary-600">{{ __('Explore') }}</p>
                <h2 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">{{ __('Dreamy destinations') }}</h2>
                <p class="mt-2 max-w-xl text-zinc-600 dark:text-zinc-400">{{ __('A taste of what awaits. Our full catalogue rolls out at launch.') }}</p>
            </div>
            <span class="inline-flex items-center gap-2 rounded-full bg-zinc-100 px-3 py-1 text-xs font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                <span class="size-1.5 rounded-full bg-amber-500"></span> {{ __('Preview') }}
            </span>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($destinations as $d)
                <a href="#"
                   class="group hk-card relative aspect-[4/5] overflow-hidden rounded-3xl ring-1 ring-zinc-900/5 shadow-md transition hover:-translate-y-2 hover:shadow-2xl">
                    <img src="{{ $d['image'] }}" alt="{{ $d['name'] }}" loading="lazy" decoding="async"
                         class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    <div class="hk-shine absolute inset-0 overflow-hidden"></div>

                    <div class="absolute right-4 top-4 rounded-full bg-white/95 px-3 py-1 text-xs font-bold text-zinc-900 shadow">
                        {{ __('from') }} <span class="text-hk-primary-600">{{ $d['price'] }}</span>
                    </div>
                    <div class="absolute left-4 top-4 inline-flex items-center gap-1 rounded-full bg-black/40 px-3 py-1 text-[11px] font-semibold uppercase tracking-widest text-white backdrop-blur">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-3"><path d="M12 2a7 7 0 0 0-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5Z"/></svg>
                        {{ $d['country'] }}
                    </div>

                    <div class="absolute inset-x-5 bottom-5 text-white">
                        <p class="text-[11px] font-semibold uppercase tracking-widest opacity-80">{{ __($d['tag']) }}</p>
                        <h3 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">{{ __($d['name']) }}</h3>
                        <div class="mt-3 flex items-center justify-between text-xs opacity-90">
                            <span class="inline-flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-3.5"><path fill-rule="evenodd" d="M12 2.25a.75.75 0 0 1 .75.75v.75A8.25 8.25 0 1 1 3.75 12 8.25 8.25 0 0 1 12 3.75ZM12 6a.75.75 0 0 1 .75.75v4.94l3.22 1.86a.75.75 0 0 1-.75 1.3l-3.6-2.08A.75.75 0 0 1 11.25 12V6.75A.75.75 0 0 1 12 6Z" clip-rule="evenodd"/></svg>
                                {{ $d['duration'] }}
                            </span>
                            <span class="inline-flex translate-x-2 items-center gap-1 font-semibold opacity-0 transition group-hover:translate-x-0 group-hover:opacity-100">
                                {{ __('Discover') }}
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ── Why us ──────────────────────────────────────────────────── --}}
    <section class="relative bg-zinc-50 py-24 dark:bg-zinc-900/30">
        <x-theme.decoration variant="grid" class="absolute inset-0 text-zinc-200 dark:text-zinc-800/40 opacity-40" />

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-hk-primary-600">{{ __('Why us') }}</p>
                <h2 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">{{ __('Travel, beautifully organised') }}</h2>
                <p class="mt-3 text-zinc-600 dark:text-zinc-400">{{ __('Everything you need, nothing you do not. We sweat the small stuff so you do not have to.') }}</p>
            </div>

            <div class="mt-14 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($features as $f)
                    <article class="group relative overflow-hidden rounded-2xl bg-white p-6 ring-1 ring-zinc-200 transition hover:-translate-y-1 hover:shadow-xl dark:bg-zinc-900 dark:ring-zinc-800">
                        <div class="absolute -right-12 -top-12 size-40 rounded-full bg-gradient-to-br {{ $f['color'] }} opacity-10 transition group-hover:scale-125 group-hover:opacity-20"></div>
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

    {{-- ── Experiences ─────────────────────────────────────────────── --}}
    <section class="relative mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
        <div class="mb-12 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-hk-primary-600">{{ __('Experiences') }}</p>
                <h2 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">{{ __('Moments worth flying for') }}</h2>
            </div>
            <a href="#" class="text-sm font-semibold text-hk-primary-600 hover:text-hk-primary-700">{{ __('Browse all experiences') }} →</a>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            @foreach ($experiences as $i => $exp)
                <a href="#"
                   class="group hk-card relative overflow-hidden rounded-3xl shadow-md ring-1 ring-zinc-900/5 transition hover:-translate-y-1 hover:shadow-2xl
                          @if ($i === 0) lg:col-span-2 lg:aspect-[2/1] aspect-[4/3] @else aspect-[4/3] @endif">
                    <img src="{{ $exp['image'] }}" alt="{{ $exp['title'] }}" loading="lazy" decoding="async"
                         class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                    <div class="hk-shine absolute inset-0 overflow-hidden"></div>

                    <div class="absolute left-5 top-5 inline-flex items-center gap-2 rounded-full bg-white/95 px-3 py-1 text-[11px] font-semibold uppercase tracking-widest text-hk-primary-700">
                        {{ $exp['tag'] }}
                    </div>
                    <div class="absolute right-5 top-5 inline-flex items-center gap-1 rounded-full bg-black/40 px-3 py-1 text-[11px] font-semibold text-white backdrop-blur">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-3"><path fill-rule="evenodd" d="M12 2.25a9.75 9.75 0 1 0 0 19.5 9.75 9.75 0 0 0 0-19.5ZM12 6a.75.75 0 0 1 .75.75v4.94l3.22 1.86a.75.75 0 0 1-.75 1.3l-3.6-2.08A.75.75 0 0 1 11.25 12V6.75A.75.75 0 0 1 12 6Z" clip-rule="evenodd"/></svg>
                        {{ $exp['duration'] }}
                    </div>

                    <div class="absolute inset-x-5 bottom-5 text-white">
                        <h3 class="text-xl font-bold leading-snug sm:text-2xl">{{ __($exp['title']) }}</h3>
                        <span class="mt-2 inline-flex translate-x-2 items-center gap-1 text-sm font-semibold opacity-0 transition group-hover:translate-x-0 group-hover:opacity-100">
                            {{ __('Book this experience') }}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ── Testimonials ────────────────────────────────────────────── --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-hk-primary-50 to-amber-50 py-24 dark:from-zinc-900 dark:to-zinc-900/50">
        <x-theme.decoration variant="blob-b" class="absolute -right-32 top-0 size-[34rem] text-amber-200/40 dark:text-amber-900/20 hk-blob" />

        <div class="relative mx-auto max-w-6xl px-4 sm:px-6 lg:px-8"
             x-data="{ active: 0, total: {{ count($testimonials) }}, next() { this.active = (this.active + 1) % this.total; }, prev() { this.active = (this.active - 1 + this.total) % this.total; } }"
             x-init="setInterval(() => next(), 7000)">
            <div class="mx-auto max-w-2xl text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-hk-primary-600">{{ __('Travellers') }}</p>
                <h2 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">{{ __('Loved by adventurers worldwide') }}</h2>
            </div>

            <div class="relative mt-12">
                @foreach ($testimonials as $i => $t)
                    <figure x-show="active === {{ $i }}" x-transition.opacity.duration.500ms
                            class="mx-auto max-w-3xl rounded-3xl bg-white p-8 text-center shadow-xl ring-1 ring-zinc-900/5 sm:p-12 dark:bg-zinc-900 dark:ring-zinc-800"
                            @if ($i !== 0) x-cloak @endif>
                        <div class="flex justify-center gap-1 text-amber-400">
                            @for ($s = 0; $s < $t['rating']; $s++)
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354l-4.625 2.825c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.08-5.006Z" clip-rule="evenodd"/></svg>
                            @endfor
                        </div>
                        <blockquote class="mt-5 text-balance text-lg italic text-zinc-700 sm:text-xl dark:text-zinc-300">
                            “{{ __($t['quote']) }}”
                        </blockquote>
                        <figcaption class="mt-6 flex items-center justify-center gap-3">
                            <img src="{{ $t['avatar'] }}" alt="{{ $t['name'] }}" loading="lazy" decoding="async"
                                 class="size-12 rounded-full object-cover ring-2 ring-white dark:ring-zinc-800">
                            <div class="text-left">
                                <div class="text-sm font-semibold">{{ $t['name'] }}</div>
                                <div class="text-xs text-zinc-500">{{ $t['city'] }}</div>
                            </div>
                        </figcaption>
                    </figure>
                @endforeach

                <div class="mt-8 flex items-center justify-center gap-3">
                    <button type="button" @click="prev()" aria-label="{{ __('Previous testimonial') }}"
                            class="rounded-full bg-white p-2 shadow ring-1 ring-zinc-200 hover:bg-zinc-50 dark:bg-zinc-800 dark:ring-zinc-700">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                    </button>
                    <template x-for="i in total" :key="i">
                        <button type="button" @click="active = i - 1" :aria-label="`Go to testimonial ${i}`"
                                :class="active === i - 1 ? 'w-8 bg-hk-primary-600' : 'w-2 bg-zinc-300 dark:bg-zinc-700'"
                                class="h-2 rounded-full transition-all"></button>
                    </template>
                    <button type="button" @click="next()" aria-label="{{ __('Next testimonial') }}"
                            class="rounded-full bg-white p-2 shadow ring-1 ring-zinc-200 hover:bg-zinc-50 dark:bg-zinc-800 dark:ring-zinc-700">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Stories ─────────────────────────────────────────────────── --}}
    <section class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
        <div class="mb-12 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-hk-primary-600">{{ __('Journal') }}</p>
                <h2 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">{{ __('Stories from the road') }}</h2>
            </div>
            <a href="#" class="text-sm font-semibold text-hk-primary-600 hover:text-hk-primary-700">{{ __('Read all stories') }} →</a>
        </div>

        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
            @foreach ($stories as $s)
                <article class="group">
                    <a href="#" class="block overflow-hidden rounded-2xl">
                        <div class="aspect-[4/3] overflow-hidden">
                            <img src="{{ $s['image'] }}" alt="{{ $s['title'] }}" loading="lazy" decoding="async"
                                 class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                        </div>
                    </a>
                    <div class="mt-4 flex items-center gap-3 text-xs text-zinc-500">
                        <span class="inline-flex items-center rounded-full bg-hk-primary-100 px-2.5 py-0.5 text-hk-primary-700 dark:bg-hk-primary-900/40 dark:text-hk-primary-300">{{ $s['tag'] }}</span>
                        <span>{{ $s['date'] }}</span>
                        <span>·</span>
                        <span>{{ $s['read'] }} {{ __('read') }}</span>
                    </div>
                    <h3 class="mt-2 text-lg font-semibold leading-snug transition group-hover:text-hk-primary-700 dark:group-hover:text-hk-primary-300">
                        <a href="#">{{ __($s['title']) }}</a>
                    </h3>
                </article>
            @endforeach
        </div>
    </section>

    {{-- ── Newsletter / CTA ────────────────────────────────────────── --}}
    <section class="relative isolate overflow-hidden py-24">
        <img src="https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=2000&q=80&auto=format&fit=crop"
             alt="" loading="lazy" decoding="async"
             class="absolute inset-0 -z-20 h-full w-full object-cover">
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-hk-primary-900/85 via-hk-primary-700/80 to-indigo-900/85"></div>
        <x-theme.decoration variant="dots" class="absolute right-12 top-12 size-40 text-white/30" />
        <x-theme.decoration variant="blob-a" class="absolute -left-24 bottom-0 size-[26rem] text-amber-300/15 hk-blob" />

        <div class="mx-auto grid max-w-5xl grid-cols-1 items-center gap-10 px-4 text-white sm:px-6 lg:grid-cols-2">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] opacity-90">{{ __('Be first to know') }}</p>
                <h2 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">{{ __('Get early access when we launch') }}</h2>
                <p class="mt-3 max-w-md text-white/85">{{ __('Join our launch list for exclusive offers and early access to curated journeys.') }}</p>
                <ul class="mt-5 space-y-2 text-sm text-white/85">
                    @foreach (['Curated weekly inspiration', 'Subscriber-only flash deals', 'Unsubscribe in one click'] as $perk)
                        <li class="flex items-center gap-2">
                            <span class="inline-flex size-5 items-center justify-center rounded-full bg-white/20">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.4" stroke="currentColor" class="size-3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            </span>
                            {{ __($perk) }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <form @submit.prevent class="flex w-full flex-col gap-2 rounded-2xl bg-white/10 p-2 ring-1 ring-white/20 backdrop-blur sm:flex-row">
                <label class="sr-only" for="hk-notify">{{ __('Email address') }}</label>
                <input id="hk-notify" type="email" required placeholder="{{ __('you@example.com') }}"
                       class="w-full rounded-xl bg-white/95 px-4 py-3 text-sm text-zinc-900 placeholder:text-zinc-500 outline-none focus:ring-2 focus:ring-white">
                <button type="button" disabled title="{{ __('Launching soon') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-hk-primary-700 shadow-lg transition hover:bg-zinc-50 disabled:opacity-80">
                    {{ __('Notify me') }}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                </button>
            </form>
        </div>
    </section>
</x-layouts.app>
