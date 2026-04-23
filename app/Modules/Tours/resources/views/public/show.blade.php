<div>
    @php
        $urls = app(\App\Core\Routing\PublicUrlGenerator::class);
        $modules = app(\App\Core\Modules\ModuleManager::class);
    @endphp
    @php $schema = $tour->toSeoMeta()['schema'] ?? null; @endphp
    @if ($schema)
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endif

    <section class="relative">
        <div class="aspect-[16/6] bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
            @if ($tour->cover_image)
                <img src="{{ $tour->cover_image }}" alt="{{ $tour->name }}" class="h-full w-full object-cover">
            @endif
        </div>
        <div class="mx-auto max-w-7xl px-6 -mt-16 relative">
            <div class="rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 md:p-8 shadow-sm">
                <nav class="text-xs text-zinc-500 mb-2">
                    <a href="{{ $urls->route('tours.index') }}" wire:navigate class="hover:underline">Tours</a>
                    @if ($tour->destination)
                        <span class="mx-1">/</span>
                        <a href="{{ $urls->entity('destination', ['slug' => $tour->destination->slug]) }}" wire:navigate class="hover:underline">{{ $tour->destination->name }}</a>
                    @endif
                    <span class="mx-1">/</span>
                    <span>{{ $tour->name }}</span>
                </nav>
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold">{{ $tour->name }}</h1>
                        <p class="mt-1 text-sm text-zinc-500">
                            {{ $tour->duration_days }} days · max {{ $tour->max_group_size }} guests · {{ ucfirst($tour->difficulty) }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold">{{ $tour->currency }} {{ number_format($tour->effectivePrice(), 0) }}</p>
                        @if ($tour->discount_price)
                            <p class="text-sm text-zinc-400 line-through">{{ number_format((float) $tour->price, 0) }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-6 py-12 grid grid-cols-1 lg:grid-cols-3 gap-10">
        <div class="lg:col-span-2 space-y-8">
            <div class="prose dark:prose-invert max-w-none">
                {!! nl2br(e($tour->description)) !!}
            </div>

            @if (! empty($tour->inclusions) || ! empty($tour->exclusions))
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if (! empty($tour->inclusions))
                        <x-ui.card>
                            <h3 class="font-semibold mb-3 text-hk-success">Included</h3>
                            <ul class="space-y-1.5 text-sm">
                                @foreach ($tour->inclusions as $item)
                                    <li class="flex gap-2"><span class="text-hk-success">✓</span> <span>{{ $item }}</span></li>
                                @endforeach
                            </ul>
                        </x-ui.card>
                    @endif
                    @if (! empty($tour->exclusions))
                        <x-ui.card>
                            <h3 class="font-semibold mb-3 text-hk-danger">Not included</h3>
                            <ul class="space-y-1.5 text-sm">
                                @foreach ($tour->exclusions as $item)
                                    <li class="flex gap-2"><span class="text-hk-danger">✗</span> <span>{{ $item }}</span></li>
                                @endforeach
                            </ul>
                        </x-ui.card>
                    @endif
                </div>
            @endif

            @if ($related->isNotEmpty())
                <div>
                    <h2 class="text-xl font-semibold mb-4">You may also like</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach ($related as $r)
                            <a href="{{ $urls->entity('tour', ['slug' => $r->slug]) }}" wire:navigate class="block rounded-lg border border-zinc-200 dark:border-zinc-800 p-4 hover:shadow-sm transition">
                                <h3 class="font-medium">{{ $r->name }}</h3>
                                <p class="text-xs text-zinc-500 mt-1">{{ $r->duration_days }}d · {{ $r->currency }} {{ number_format($r->effectivePrice(), 0) }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($modules->enabled('reviews'))
                <section class="space-y-6">
                    <livewire:reviews-public.review-list :reviewable="$tour" />

                    <x-ui.card class="p-6">
                        <h2 class="mb-4 text-xl font-semibold">{{ __('reviews::reviews.leave_review') }}</h2>
                        <livewire:reviews-public.review-form :reviewable="$tour" />
                    </x-ui.card>
                </section>
            @endif
        </div>

        <aside class="space-y-4">
            <livewire:hk.enquiry-form
                source="tour"
                heading="Enquire about this tour"
                :leadable-type="get_class($tour)"
                :leadable-id="$tour->id"
                :extra-fields="[
                    ['key' => 'travel_date', 'label' => 'Preferred travel date', 'type' => 'date', 'required' => false],
                    ['key' => 'travelers', 'label' => 'Number of travelers', 'type' => 'number', 'required' => false],
                ]"
            />
        </aside>
    </section>
</div>
