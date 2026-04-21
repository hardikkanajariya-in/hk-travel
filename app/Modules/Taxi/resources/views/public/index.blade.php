<div class="container mx-auto px-4 py-10">
    <header class="mb-8">
        <h1 class="text-3xl font-semibold">Taxi & Transfers</h1>
        <p class="text-zinc-500 mt-1">Airport pickups, hourly hire and point-to-point.</p>
    </header>

    <x-ui.card class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-sm font-medium">Service type</label>
                <select wire:model.live="type" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                    <option value="">All</option>
                    <option value="airport_transfer">Airport transfer</option>
                    <option value="hourly">Hourly hire</option>
                    <option value="point_to_point">Point to point</option>
                </select>
            </div>
            <x-ui.input wire:model.live="vehicle" label="Vehicle type" placeholder="Sedan, SUV…" />
        </div>
    </x-ui.card>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($services as $s)
            <a href="{{ route('taxi.show', $s->slug) }}" wire:navigate class="block rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden hover:shadow-lg transition">
                @if ($s->cover_image)
                    <img src="{{ $s->cover_image }}" alt="" class="w-full aspect-video object-cover">
                @else
                    <div class="w-full aspect-video bg-gradient-to-br from-amber-300 to-amber-500"></div>
                @endif
                <div class="p-4">
                    <p class="text-xs text-zinc-500 uppercase">{{ str_replace('_', ' ', $s->service_type) }}</p>
                    <h3 class="font-semibold mt-1">{{ $s->title }}</h3>
                    <p class="text-xs text-zinc-500 mt-1">{{ $s->vehicle_type }} · {{ $s->capacity }} pax · {{ $s->luggage }} bags</p>
                    <p class="mt-2 text-sm">From <span class="font-semibold">{{ $s->currency }} {{ number_format((float) max($s->flat_rate, $s->base_fare), 0) }}</span></p>
                </div>
            </a>
        @empty
            <p class="text-zinc-500 col-span-full text-center py-12">No services found.</p>
        @endforelse
    </div>
    <div class="mt-6">{{ $services->links() }}</div>
</div>
