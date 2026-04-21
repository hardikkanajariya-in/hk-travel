<div>
    <section class="relative overflow-hidden border-b border-zinc-200 bg-gradient-to-br from-sky-50 via-white to-teal-50 dark:border-zinc-800 dark:from-sky-950/30 dark:via-zinc-950 dark:to-teal-950/30">
        <div class="absolute inset-y-0 right-0 hidden w-1/3 opacity-10 sm:block" aria-hidden="true">
            <svg viewBox="0 0 200 200" class="h-full w-full text-sky-600" fill="currentColor">
                <path d="M40 180V90l60-50 60 50v90H40zm20-20h40v-50H60v50zm60 0h20v-50h-20v50z"/>
            </svg>
        </div>
        <div class="relative mx-auto max-w-7xl px-6 py-12 sm:py-16">
            <p class="text-xs font-semibold uppercase tracking-wider text-sky-600 dark:text-sky-400">{{ __('Stays for every traveller') }}</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">{{ __('Find your perfect hotel') }}</h1>
            <p class="mt-3 max-w-2xl text-base text-zinc-600 dark:text-zinc-400">{{ __('Browse hand-picked hotels, resorts and boutique stays — filter by destination, star rating and budget.') }}</p>
        </div>
    </section>

    <section class="mx-auto w-full max-w-7xl px-6 py-8 sm:py-10">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">
            <aside class="lg:col-span-1">
                <x-ui.card class="p-5 lg:sticky lg:top-24">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500">{{ __('Refine your search') }}</h2>
                    <div class="mt-4 space-y-4">
                        <x-ui.input wire:model.live.debounce.500ms="search" :label="__('Hotel name')" placeholder="{{ __('Search by name…') }}" />
                        <x-ui.select wire:model.live="destinationId" :label="__('Destination')" :options="collect($destinations)->mapWithKeys(fn ($d) => [$d->id => $d->name])->prepend(__('Any destination'), '')->all()" />
                        <x-ui.select wire:model.live="stars" :label="__('Star rating')" :options="['' => __('Any rating'), '5' => '5 star', '4' => '4 star and above', '3' => '3 star and above', '2' => '2 star and above']" />
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">{{ __('Price per night') }}</label>
                            <div class="grid grid-cols-2 gap-2">
                                <x-ui.input type="number" wire:model.live.debounce.500ms="priceMin" placeholder="{{ __('Min') }}" />
                                <x-ui.input type="number" wire:model.live.debounce.500ms="priceMax" placeholder="{{ __('Max') }}" />
                            </div>
                        </div>
                        <button type="button" wire:click="clearFilters" class="text-sm font-medium text-sky-600 hover:underline dark:text-sky-400">{{ __('Clear filters') }}</button>
                    </div>
                </x-ui.card>
            </aside>

            <section class="space-y-5 lg:col-span-3">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        {{ trans_choice('{0} No hotels found|{1} :count hotel found|[2,*] :count hotels found', $hotels->total(), ['count' => $hotels->total()]) }}
                    </p>
                    <div class="flex items-center gap-2">
                        <label for="hotel-sort" class="text-sm text-zinc-500">{{ __('Sort by') }}</label>
                        <x-ui.select id="hotel-sort" wire:model.live="sort" :options="['featured' => __('Featured first'), 'price_asc' => __('Price (low to high)'), 'price_desc' => __('Price (high to low)'), 'rating' => __('Top rated'), 'stars' => __('Most stars')]" class="w-56" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    @forelse ($hotels as $h)
                        <a href="{{ route('hotels.show', $h->slug) }}" wire:navigate
                            class="group block overflow-hidden rounded-2xl border border-zinc-200 bg-white transition hover:-translate-y-0.5 hover:border-sky-300 hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-sky-700">
                            <div class="relative aspect-video overflow-hidden">
                                @if ($h->cover_image)
                                    <img src="{{ $h->cover_image }}" alt="" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <div class="h-full w-full bg-gradient-to-br from-sky-200 to-teal-400"></div>
                                @endif
                                @if ($h->star_rating)
                                    <span class="absolute left-3 top-3 inline-flex items-center gap-1 rounded-full bg-white/90 px-2 py-0.5 text-xs font-medium text-amber-600 shadow-sm dark:bg-zinc-900/90">
                                        <span aria-hidden="true">★</span> {{ $h->star_rating }}
                                    </span>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="text-base font-semibold leading-tight group-hover:text-sky-600 dark:group-hover:text-sky-400">{{ $h->name }}</h3>
                                <p class="mt-1 line-clamp-1 text-xs text-zinc-500">{{ $h->destination?->name ?? $h->address }}</p>
                                <div class="mt-3 flex items-end justify-between">
                                    <p class="text-xs text-zinc-500">{{ __('From') }}</p>
                                    <p class="text-sm"><span class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ $h->currency }} {{ number_format((float) $h->price_from, 0) }}</span> <span class="text-xs text-zinc-500">/ {{ __('night') }}</span></p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-zinc-200 bg-zinc-50 p-10 text-center dark:border-zinc-700 dark:bg-zinc-800/40">
                            <p class="text-sm font-medium">{{ __('No hotels match your search') }}</p>
                            <p class="mt-1 text-sm text-zinc-500">{{ __('Try removing a filter or expanding your price range.') }}</p>
                        </div>
                    @endforelse
                </div>

                <div>{{ $hotels->links() }}</div>
            </section>
        </div>
    </section>
</div>
