<div>
    <section class="bg-gradient-to-b from-hk-primary-50 to-white dark:from-hk-primary-950 dark:to-zinc-950 py-14 border-b border-zinc-200 dark:border-zinc-800">
        <div class="mx-auto max-w-7xl px-6">
            <h1 class="text-4xl font-bold tracking-tight">{{ __('tours::tours.title') }}</h1>
            <p class="mt-3 text-lg text-zinc-600 dark:text-zinc-400 max-w-2xl">
                Hand-crafted multi-day itineraries with expert local guides.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-6 py-10 grid grid-cols-1 lg:grid-cols-4 gap-8">
        <aside class="lg:col-span-1 space-y-4">
            <x-ui.card>
                <h3 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-3">Filters</h3>
                <div class="space-y-3">
                    <x-ui.input wire:model.live.debounce.500ms="search" placeholder="Search tours…" />

                    <div class="space-y-1.5">
                        <label class="block text-xs font-medium">Destination</label>
                        <select wire:model.live="destinationId" class="block w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                            <option value="">All destinations</option>
                            @foreach ($destinations as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-medium">Difficulty</label>
                        <select wire:model.live="difficulty" class="block w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                            <option value="">Any</option>
                            <option value="easy">Easy</option>
                            <option value="moderate">Moderate</option>
                            <option value="challenging">Challenging</option>
                            <option value="extreme">Extreme</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <x-ui.input type="number" wire:model.live.debounce.500ms="priceMin" label="Min $" />
                        <x-ui.input type="number" wire:model.live.debounce.500ms="priceMax" label="Max $" />
                    </div>

                    <button wire:click="clearFilters" class="text-xs text-hk-primary-600 hover:underline">Clear filters</button>
                </div>
            </x-ui.card>
        </aside>

        <div class="lg:col-span-3 space-y-6">
            <div class="flex items-center justify-between">
                <p class="text-sm text-zinc-500">{{ $tours->total() }} tours</p>
                <select wire:model.live="sort" class="rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                    <option value="featured">Featured</option>
                    <option value="newest">Newest</option>
                    <option value="price_asc">Price ↑</option>
                    <option value="price_desc">Price ↓</option>
                    <option value="rating">Top rated</option>
                </select>
            </div>

            @if ($tours->isEmpty())
                <x-ui.card>
                    <p class="text-center text-zinc-500 py-8">No tours match your filters.</p>
                </x-ui.card>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach ($tours as $tour)
                        <a href="{{ route('tours.show', $tour->slug) }}" wire:navigate
                           class="group overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:shadow-md transition flex flex-col">
                            <div class="aspect-[4/3] bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                                @if ($tour->cover_image)
                                    <img src="{{ $tour->cover_image }}" alt="{{ $tour->name }}" loading="lazy"
                                         class="h-full w-full object-cover group-hover:scale-105 transition duration-300">
                                @endif
                            </div>
                            <div class="p-4 flex-1 flex flex-col">
                                <div class="flex items-start justify-between gap-2">
                                    <h2 class="font-semibold group-hover:text-hk-primary-600">{{ $tour->name }}</h2>
                                    @if ($tour->is_featured)
                                        <x-ui.badge variant="warning" size="sm">Featured</x-ui.badge>
                                    @endif
                                </div>
                                @if ($tour->destination)
                                    <p class="text-xs text-zinc-500 mt-1">{{ $tour->destination->name }}</p>
                                @endif
                                <p class="text-sm text-zinc-500 mt-2">{{ $tour->duration_days }} days · {{ ucfirst($tour->difficulty) }}</p>
                                <div class="mt-auto pt-3 flex items-end justify-between">
                                    <div>
                                        <p class="text-lg font-bold">{{ $tour->currency }} {{ number_format($tour->effectivePrice(), 0) }}</p>
                                        @if ($tour->discount_price)
                                            <p class="text-xs text-zinc-400 line-through">{{ number_format((float) $tour->price, 0) }}</p>
                                        @endif
                                    </div>
                                    @if ($tour->rating_count > 0)
                                        <span class="text-xs text-amber-500">★ {{ number_format((float) $tour->rating_avg, 1) }} <span class="text-zinc-400">({{ $tour->rating_count }})</span></span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div>{{ $tours->links() }}</div>
            @endif
        </div>
    </section>
</div>
