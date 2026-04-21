<div class="container mx-auto px-4 py-10">
    <header class="mb-8">
        <h1 class="text-3xl font-semibold">Activities & Experiences</h1>
        <p class="text-zinc-500 mt-1">Day trips and curated experiences.</p>
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
                    <select wire:model.live="category" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                        <option value="">All categories</option>
                        @foreach ($categories as $c)<option value="{{ $c }}">{{ ucfirst($c) }}</option>@endforeach
                    </select>
                </div>
            </x-ui.card>
        </aside>

        <section class="lg:col-span-3 space-y-4">
            <div class="flex items-center justify-between">
                <p class="text-sm text-zinc-500">{{ $activities->total() }} {{ Str::plural('activity', $activities->total()) }}</p>
                <select wire:model.live="sort" class="rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                    <option value="featured">Featured</option>
                    <option value="price_asc">Price ↑</option>
                    <option value="price_desc">Price ↓</option>
                    <option value="duration">Duration</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($activities as $a)
                    <a href="{{ route('activities.show', $a->slug) }}" wire:navigate
                        class="group block rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden hover:shadow-lg transition">
                        @if ($a->cover_image)
                            <img src="{{ $a->cover_image }}" alt="" class="w-full aspect-video object-cover">
                        @else
                            <div class="w-full aspect-video bg-gradient-to-br from-emerald-300 to-emerald-600"></div>
                        @endif
                        <div class="p-4">
                            <p class="text-xs text-zinc-500 uppercase">{{ $a->category }}</p>
                            <h3 class="font-semibold group-hover:text-hk-primary-600 mt-1">{{ $a->name }}</h3>
                            <p class="text-xs text-zinc-500 mt-1">{{ $a->duration_hours }}h · {{ ucfirst($a->difficulty) }}</p>
                            <p class="mt-2 text-sm">From <span class="font-semibold">{{ $a->currency }} {{ number_format((float) $a->price, 0) }}</span></p>
                        </div>
                    </a>
                @empty
                    <p class="text-zinc-500 col-span-full text-center py-12">No activities found.</p>
                @endforelse
            </div>
            <div>{{ $activities->links() }}</div>
        </section>
    </div>
</div>
