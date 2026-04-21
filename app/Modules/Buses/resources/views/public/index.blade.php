<div class="container mx-auto px-4 py-10">
    <header class="mb-8">
        <h1 class="text-3xl font-semibold">Bus routes</h1>
        <p class="text-zinc-500 mt-1">Search intercity bus services.</p>
    </header>

    <x-ui.card class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-ui.input wire:model.live.debounce.500ms="origin" label="From" placeholder="Origin city" />
            <x-ui.input wire:model.live.debounce.500ms="destination" label="To" placeholder="Destination city" />
            <div class="space-y-1.5">
                <label class="block text-sm font-medium">Type</label>
                <select wire:model.live="type" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                    <option value="">Any</option><option value="standard">Standard</option><option value="ac">A/C</option><option value="sleeper">Sleeper</option><option value="luxury">Luxury</option>
                </select>
            </div>
        </div>
    </x-ui.card>

    <div class="space-y-3">
        @forelse ($routes as $r)
            <a href="{{ route('buses.show', $r->slug) }}" wire:navigate class="block rounded-xl border border-zinc-200 dark:border-zinc-800 p-5 hover:shadow-md transition">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex-1">
                        <p class="text-xs text-zinc-500 uppercase">{{ $r->operator }} · {{ ucfirst($r->bus_type) }}</p>
                        <h3 class="font-semibold mt-1 text-lg">{{ $r->origin }} → {{ $r->destination }}</h3>
                        <p class="text-sm text-zinc-500 mt-1">{{ $r->departure_time }} – {{ $r->arrival_time }} · {{ floor($r->duration_minutes / 60) }}h {{ $r->duration_minutes % 60 }}m · {{ $r->distance_km }} km</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-semibold">{{ $r->currency }} {{ number_format((float) $r->fare, 0) }}</p>
                        <p class="text-xs text-zinc-500">per seat</p>
                    </div>
                </div>
            </a>
        @empty
            <p class="text-zinc-500 text-center py-12">No routes match your search.</p>
        @endforelse
    </div>
    <div class="mt-6">{{ $routes->links() }}</div>
</div>
