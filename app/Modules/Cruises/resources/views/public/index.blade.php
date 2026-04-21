<div class="container mx-auto px-4 py-10">
    <header class="mb-8">
        <h1 class="text-3xl font-semibold">Cruises</h1>
        <p class="text-zinc-500 mt-1">Set sail across the world with our curated cruise selection.</p>
    </header>

    <x-ui.card class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="space-y-1.5">
                <label class="block text-sm font-medium">Cruise line</label>
                <select wire:model.live="line" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                    <option value="">All</option>
                    @foreach ($lines as $l)<option value="{{ $l }}">{{ $l }}</option>@endforeach
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-medium">Duration</label>
                <select wire:model.live="nights" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                    <option value="">Any</option>
                    <option value="short">Up to 5 nights</option>
                    <option value="medium">6-9 nights</option>
                    <option value="long">10+ nights</option>
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-medium">Sort</label>
                <select wire:model.live="sort" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                    <option value="departure_asc">Departure (soonest)</option>
                    <option value="price_asc">Price (low → high)</option>
                    <option value="price_desc">Price (high → low)</option>
                    <option value="duration">Duration (shortest)</option>
                </select>
            </div>
        </div>
    </x-ui.card>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($cruises as $c)
            <a href="{{ route('cruises.show', $c->slug) }}" wire:navigate class="block rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden hover:shadow-lg transition">
                @if ($c->cover_image)
                    <img src="{{ $c->cover_image }}" alt="" class="w-full aspect-video object-cover">
                @else
                    <div class="w-full aspect-video bg-gradient-to-br from-cyan-400 to-blue-600"></div>
                @endif
                <div class="p-4">
                    <p class="text-xs text-zinc-500 uppercase">{{ $c->cruise_line }}</p>
                    <h3 class="font-semibold mt-1 line-clamp-2">{{ $c->title }}</h3>
                    <p class="text-xs text-zinc-500 mt-1">{{ $c->departure_port }} → {{ $c->arrival_port }} · {{ $c->duration_nights }} nights</p>
                    <p class="mt-2 text-sm">From <span class="font-semibold">{{ $c->currency }} {{ number_format((float) $c->price_from, 0) }}</span></p>
                </div>
            </a>
        @empty
            <p class="text-zinc-500 col-span-full text-center py-12">No cruises found.</p>
        @endforelse
    </div>
    <div class="mt-6">{{ $cruises->links() }}</div>
</div>
