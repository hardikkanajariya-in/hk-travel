<div class="container mx-auto px-4 py-10">
    <header class="mb-8">
        <h1 class="text-3xl font-semibold">Car rentals</h1>
        <p class="text-zinc-500 mt-1">Choose the right vehicle for your trip.</p>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <aside class="lg:col-span-1 space-y-4">
            <x-ui.card>
                <h2 class="font-semibold mb-3">Filters</h2>
                <div class="space-y-3 text-sm">
                    <x-ui.input wire:model.live.debounce.500ms="search" placeholder="Search…" />
                    <select wire:model.live="class" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                        <option value="">All classes</option>
                        @foreach ($classes as $c)<option value="{{ $c }}">{{ ucfirst($c) }}</option>@endforeach
                    </select>
                    <select wire:model.live="transmission" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                        <option value="">Any transmission</option>
                        <option value="automatic">Automatic</option><option value="manual">Manual</option>
                    </select>
                </div>
            </x-ui.card>
        </aside>

        <section class="lg:col-span-3 space-y-4">
            <div class="flex items-center justify-between">
                <p class="text-sm text-zinc-500">{{ $cars->total() }} vehicles</p>
                <select wire:model.live="sort" class="rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                    <option value="price_asc">Price ↑</option>
                    <option value="price_desc">Price ↓</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($cars as $c)
                    <a href="{{ route('cars.show', $c->slug) }}" wire:navigate class="block rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden hover:shadow-lg transition">
                        @if ($c->cover_image)
                            <img src="{{ $c->cover_image }}" alt="" class="w-full aspect-video object-cover">
                        @else
                            <div class="w-full aspect-video bg-gradient-to-br from-zinc-200 to-zinc-400 dark:from-zinc-700 dark:to-zinc-900"></div>
                        @endif
                        <div class="p-4">
                            <p class="text-xs text-zinc-500 uppercase">{{ ucfirst($c->vehicle_class) }}</p>
                            <h3 class="font-semibold mt-1">{{ $c->name }}</h3>
                            <p class="text-xs text-zinc-500 mt-1">{{ $c->seats }} seats · {{ ucfirst($c->transmission) }} · {{ ucfirst($c->fuel_type) }}</p>
                            <p class="mt-2 text-sm">From <span class="font-semibold">{{ $c->currency }} {{ number_format((float) $c->daily_rate, 0) }}</span> / day</p>
                        </div>
                    </a>
                @empty
                    <p class="text-zinc-500 col-span-full text-center py-12">No vehicles found.</p>
                @endforelse
            </div>
            <div>{{ $cars->links() }}</div>
        </section>
    </div>
</div>
