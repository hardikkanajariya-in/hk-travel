<div>
    <section class="bg-gradient-to-b from-hk-primary-50 to-white dark:from-hk-primary-950 dark:to-zinc-950 py-16 border-b border-zinc-200 dark:border-zinc-800">
        <div class="mx-auto max-w-7xl px-6">
            <h1 class="text-4xl font-bold tracking-tight">Discover destinations</h1>
            <p class="mt-3 text-lg text-zinc-600 dark:text-zinc-400 max-w-2xl">
                Browse cities, regions and countries we cover. Find inspiration for your next trip.
            </p>
            <div class="mt-6 flex flex-wrap gap-3 max-w-3xl">
                <x-ui.input wire:model.live.debounce.500ms="search" placeholder="Search destinations…" class="flex-1 min-w-[260px]" />
                <select wire:model.live="type" class="rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                    <option value="">All types</option>
                    <option value="country">Country</option>
                    <option value="region">Region</option>
                    <option value="city">City</option>
                </select>
                <x-ui.input wire:model.live.debounce.500ms="country" placeholder="Country code" class="w-32" />
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-6 py-12">
        @if ($destinations->isEmpty())
            <x-ui.card>
                <p class="text-center text-zinc-500 py-8">No destinations found.</p>
            </x-ui.card>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($destinations as $d)
                    <a href="{{ route('destinations.show', $d->slug) }}" wire:navigate
                       class="group overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:shadow-md transition">
                        <div class="aspect-[4/3] bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                            @if ($d->cover_image)
                                <img src="{{ $d->cover_image }}" alt="{{ $d->name }}" loading="lazy"
                                     class="h-full w-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="h-full w-full flex items-center justify-center text-zinc-300 text-5xl font-bold">
                                    {{ strtoupper(substr($d->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <h2 class="font-semibold group-hover:text-hk-primary-600">{{ $d->name }}</h2>
                                @if ($d->is_featured)
                                    <x-ui.badge variant="warning" size="sm">Featured</x-ui.badge>
                                @endif
                            </div>
                            <p class="text-xs text-zinc-500 mt-1 capitalize">{{ $d->type }} @if ($d->country_code) · {{ $d->country_code }} @endif</p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-10">{{ $destinations->links() }}</div>
        @endif
    </section>
</div>
