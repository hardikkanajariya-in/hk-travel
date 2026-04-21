<div class="container mx-auto px-4 py-10">
    <header class="mb-8">
        <h1 class="text-3xl font-semibold">Hotels</h1>
        <p class="text-zinc-500 mt-1">Find your perfect stay.</p>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <aside class="lg:col-span-1 space-y-4">
            <x-ui.card>
                <h2 class="font-semibold mb-3">Filters</h2>
                <div class="space-y-3 text-sm">
                    <x-ui.input wire:model.live.debounce.500ms="search" placeholder="Search…" />
                    <select wire:model.live="destinationId" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                        <option value="">All destinations</option>
                        @foreach ($destinations as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach
                    </select>
                    <select wire:model.live="stars" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                        <option value="">Any stars</option>
                        @foreach ([5,4,3,2] as $s)<option value="{{ $s }}">{{ $s }}+ star</option>@endforeach
                    </select>
                    <div class="grid grid-cols-2 gap-2">
                        <x-ui.input type="number" wire:model.live.debounce.500ms="priceMin" placeholder="Min $" />
                        <x-ui.input type="number" wire:model.live.debounce.500ms="priceMax" placeholder="Max $" />
                    </div>
                    <button wire:click="clearFilters" class="text-xs text-hk-primary-600 hover:underline">Reset</button>
                </div>
            </x-ui.card>
        </aside>

        <section class="lg:col-span-3 space-y-4">
            <div class="flex items-center justify-between">
                <p class="text-sm text-zinc-500">{{ $hotels->total() }} {{ Str::plural('result', $hotels->total()) }}</p>
                <select wire:model.live="sort" class="rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                    <option value="featured">Featured</option>
                    <option value="price_asc">Price ↑</option>
                    <option value="price_desc">Price ↓</option>
                    <option value="rating">Rating</option>
                    <option value="stars">Stars</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse ($hotels as $h)
                    <a href="{{ route('hotels.show', $h->slug) }}" wire:navigate
                        class="group block rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden hover:shadow-lg transition">
                        @if ($h->cover_image)
                            <img src="{{ $h->cover_image }}" alt="" class="w-full aspect-video object-cover">
                        @else
                            <div class="w-full aspect-video bg-gradient-to-br from-hk-primary-200 to-hk-primary-500"></div>
                        @endif
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold group-hover:text-hk-primary-600">{{ $h->name }}</h3>
                                <span class="text-amber-500 text-sm">{{ str_repeat('★', $h->star_rating) }}</span>
                            </div>
                            <p class="text-xs text-zinc-500 mt-1">{{ $h->destination?->name ?? $h->address }}</p>
                            <p class="mt-3 text-sm">From <span class="font-semibold">{{ $h->currency }} {{ number_format((float) $h->price_from, 0) }}</span> / night</p>
                        </div>
                    </a>
                @empty
                    <p class="text-zinc-500 col-span-full text-center py-12">No hotels match your filters.</p>
                @endforelse
            </div>

            <div>{{ $hotels->links() }}</div>
        </section>
    </div>
</div>
