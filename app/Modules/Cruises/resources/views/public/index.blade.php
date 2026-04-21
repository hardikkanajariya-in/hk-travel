<div>
    <section class="relative overflow-hidden border-b border-cyan-100 bg-gradient-to-br from-cyan-600 via-blue-700 to-indigo-800 text-white dark:border-cyan-900/40">
        <svg class="absolute bottom-0 left-0 right-0 w-full text-white/10" viewBox="0 0 1440 120" fill="currentColor" aria-hidden="true">
            <path d="M0 60 Q360 0 720 60 T1440 60 V120 H0Z"/>
        </svg>
        <div class="relative mx-auto max-w-7xl px-6 py-12 sm:py-16">
            <p class="text-xs font-semibold uppercase tracking-wider text-cyan-100">{{ __('All aboard') }}</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">{{ __('Cruises around the world') }}</h1>
            <p class="mt-3 max-w-2xl text-base text-cyan-50/90">{{ __('Mediterranean voyages, Caribbean escapes and river cruises — pick your departure, line and length.') }}</p>
        </div>
    </section>

    <section class="mx-auto w-full max-w-7xl px-6 py-8 sm:py-10">
        <x-ui.card class="mb-6 p-5">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <x-ui.select wire:model.live="line" :label="__('Cruise line')" :options="collect($lines)->mapWithKeys(fn ($l) => [$l => $l])->prepend(__('All cruise lines'), '')->all()" />
                <x-ui.select wire:model.live="nights" :label="__('Trip length')" :options="['' => __('Any length'), 'short' => __('Short break (up to 5 nights)'), 'medium' => __('Mid (6 to 9 nights)'), 'long' => __('Long voyage (10+ nights)')]" />
                <x-ui.select wire:model.live="sort" :label="__('Sort by')" :options="['departure_asc' => __('Soonest departure'), 'price_asc' => __('Price (low to high)'), 'price_desc' => __('Price (high to low)'), 'duration' => __('Shortest trip')]" />
            </div>
        </x-ui.card>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($cruises as $c)
                <a href="{{ route('cruises.show', $c->slug) }}" wire:navigate class="group block overflow-hidden rounded-2xl border border-zinc-200 bg-white transition hover:-translate-y-0.5 hover:border-cyan-300 hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-cyan-700">
                    <div class="relative aspect-video overflow-hidden">
                        @if ($c->cover_image)
                            <img src="{{ $c->cover_image }}" alt="" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-cyan-400 to-blue-600 text-white/30">
                                <svg viewBox="0 0 64 64" class="h-16 w-16" fill="currentColor"><path d="M32 6l4 16h-8l4-16zM10 28h44l-4 14a8 8 0 01-7 5H21a8 8 0 01-7-5l-4-14z"/></svg>
                            </div>
                        @endif
                        <span class="absolute right-3 top-3 rounded-full bg-white/90 px-2.5 py-0.5 text-[11px] font-medium text-cyan-700 dark:bg-zinc-900/90 dark:text-cyan-300">{{ $c->duration_nights }} {{ __('nights') }}</span>
                    </div>
                    <div class="p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-cyan-700 dark:text-cyan-400">{{ $c->cruise_line }}</p>
                        <h3 class="mt-1 line-clamp-2 text-base font-semibold leading-tight">{{ $c->title }}</h3>
                        <p class="mt-2 flex items-center gap-1.5 text-xs text-zinc-500">
                            <span>{{ $c->departure_port }}</span>
                            <svg class="size-3 text-zinc-400" viewBox="0 0 16 16" fill="currentColor"><path d="M9 3l5 5-5 5V9H1V7h8V3z"/></svg>
                            <span>{{ $c->arrival_port }}</span>
                        </p>
                        <div class="mt-3 flex items-end justify-between border-t border-zinc-100 pt-3 dark:border-zinc-800">
                            <p class="text-xs text-zinc-500">{{ __('From') }}</p>
                            <p class="text-lg font-semibold">{{ $c->currency }} {{ number_format((float) $c->price_from, 0) }}</p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-zinc-200 bg-zinc-50 p-10 text-center dark:border-zinc-700 dark:bg-zinc-800/40">
                    <p class="text-sm font-medium">{{ __('No cruises match your search') }}</p>
                    <p class="mt-1 text-sm text-zinc-500">{{ __('Try a different cruise line or trip length.') }}</p>
                </div>
            @endforelse
        </div>
        <div class="mt-8">{{ $cruises->links() }}</div>
    </section>
</div>
