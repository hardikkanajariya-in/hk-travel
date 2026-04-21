<div class="container mx-auto px-4 py-10">
    <header class="mb-8">
        <h1 class="text-3xl font-semibold">Visa Services</h1>
        <p class="text-zinc-500 mt-1">Visa requirements, processing times, and fees.</p>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <aside class="lg:col-span-1 space-y-4">
            <x-ui.card>
                <h2 class="font-semibold mb-3">Filters</h2>
                <div class="space-y-3 text-sm">
                    <x-ui.input wire:model.live.debounce.500ms="search" placeholder="Search…" />
                    <select wire:model.live="country" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                        <option value="">All countries</option>
                        @foreach ($countries as $c)<option value="{{ $c }}">{{ $c }}</option>@endforeach
                    </select>
                    <select wire:model.live="type" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                        <option value="">All types</option>
                        @foreach ($types as $t)<option value="{{ $t }}">{{ $t }}</option>@endforeach
                    </select>
                </div>
            </x-ui.card>
        </aside>

        <section class="lg:col-span-3">
            <div class="space-y-3">
                @forelse ($services as $s)
                    <a href="{{ route('visa.show', $s->slug) }}" wire:navigate class="block rounded-xl border border-zinc-200 dark:border-zinc-800 p-5 hover:shadow-md transition">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="text-xs text-zinc-500 uppercase">{{ $s->country }}</p>
                                <h3 class="font-semibold text-lg">{{ $s->title }}</h3>
                                <p class="text-sm text-zinc-500 mt-1">Stay up to {{ $s->allowed_stay_days }} days · Validity {{ $s->validity_days }} days · Processing {{ $s->processing_days_min }}–{{ $s->processing_days_max }} days</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-zinc-500">From</p>
                                <p class="text-2xl font-semibold">{{ $s->currency }} {{ number_format((float) $s->fee + (float) $s->service_fee, 0) }}</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="text-zinc-500 text-center py-12">No visa services found.</p>
                @endforelse
            </div>
            <div class="mt-6">{{ $services->links() }}</div>
        </section>
    </div>
</div>
