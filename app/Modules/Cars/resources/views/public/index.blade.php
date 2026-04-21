<div>
    <section class="relative overflow-hidden border-b border-zinc-200 bg-zinc-900 text-white dark:border-zinc-800">
        <div class="absolute inset-0 opacity-20" style="background-image:radial-gradient(circle at 20% 50%, rgba(239,68,68,.6), transparent 40%), radial-gradient(circle at 80% 30%, rgba(99,102,241,.5), transparent 45%)" aria-hidden="true"></div>
        <div class="relative mx-auto flex max-w-7xl flex-col gap-4 px-6 py-12 sm:py-16">
            <p class="text-xs font-semibold uppercase tracking-wider text-red-300">{{ __('Drive your own way') }}</p>
            <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">{{ __('Car rentals') }}</h1>
            <p class="max-w-2xl text-base text-zinc-300">{{ __('From compact city cars to family SUVs — pick the vehicle that fits your route, your group and your budget.') }}</p>
        </div>
    </section>

    <section class="mx-auto w-full max-w-7xl px-6 py-8 sm:py-10">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">
            <aside class="lg:col-span-1">
                <x-ui.card class="p-5 lg:sticky lg:top-24">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500">{{ __('Filter vehicles') }}</h2>
                    <div class="mt-4 space-y-4">
                        <x-ui.input wire:model.live.debounce.500ms="search" :label="__('Vehicle name')" placeholder="{{ __('e.g. Toyota Corolla') }}" />
                        <x-ui.select wire:model.live="class" :label="__('Vehicle class')" :options="['' => __('All classes'), 'economy' => __('Economy'), 'compact' => __('Compact'), 'sedan' => __('Sedan'), 'suv' => __('SUV'), 'luxury' => __('Luxury'), 'van' => __('Van')]" />
                        <x-ui.select wire:model.live="transmission" :label="__('Transmission')" :options="['' => __('Any'), 'automatic' => __('Automatic'), 'manual' => __('Manual')]" />
                    </div>
                </x-ui.card>
            </aside>

            <section class="space-y-5 lg:col-span-3">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        {{ trans_choice('{0} No vehicles found|{1} :count vehicle|[2,*] :count vehicles', $cars->total(), ['count' => $cars->total()]) }}
                    </p>
                    <div class="flex items-center gap-2">
                        <label for="car-sort" class="text-sm text-zinc-500">{{ __('Sort by') }}</label>
                        <x-ui.select id="car-sort" wire:model.live="sort" :options="['price_asc' => __('Price (low to high)'), 'price_desc' => __('Price (high to low)')]" class="w-56" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($cars as $c)
                        <a href="{{ route('cars.show', $c->slug) }}" wire:navigate class="group block overflow-hidden rounded-2xl border border-zinc-200 bg-white transition hover:-translate-y-0.5 hover:border-red-300 hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-red-700">
                            <div class="relative aspect-video overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                                @if ($c->cover_image)
                                    <img src="{{ $c->cover_image }}" alt="" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-zinc-400">
                                        <svg viewBox="0 0 64 64" class="h-16 w-16" fill="currentColor"><path d="M14 38l4-12a4 4 0 014-4h20a4 4 0 014 4l4 12v10a2 2 0 01-2 2h-4a2 2 0 01-2-2v-2H22v2a2 2 0 01-2 2h-4a2 2 0 01-2-2V38zm6-2h24l-3-9H23l-3 9zm0 6a3 3 0 110 6 3 3 0 010-6zm24 0a3 3 0 110 6 3 3 0 010-6z"/></svg>
                                    </div>
                                @endif
                                <span class="absolute left-3 top-3 rounded-full bg-zinc-900/80 px-2.5 py-0.5 text-[11px] font-medium uppercase tracking-wide text-white">{{ str_replace('_', ' ', $c->vehicle_class) }}</span>
                            </div>
                            <div class="p-4">
                                <h3 class="text-base font-semibold leading-tight">{{ $c->name }}</h3>
                                <ul class="mt-2 flex flex-wrap gap-x-3 gap-y-1 text-xs text-zinc-500">
                                    <li class="inline-flex items-center gap-1">
                                        <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5a4 4 0 11-8 0 4 4 0 018 0zm6 4a3 3 0 100-6 3 3 0 000 6z"/></svg>
                                        {{ trans_choice('{1} :count seat|[2,*] :count seats', $c->seats, ['count' => $c->seats]) }}
                                    </li>
                                    <li class="capitalize">{{ str_replace('_', ' ', $c->transmission) }}</li>
                                    <li class="capitalize">{{ str_replace('_', ' ', $c->fuel_type) }}</li>
                                </ul>
                                <div class="mt-3 flex items-end justify-between border-t border-zinc-100 pt-3 dark:border-zinc-800">
                                    <p class="text-xs text-zinc-500">{{ __('From') }}</p>
                                    <p class="text-sm"><span class="text-lg font-semibold">{{ $c->currency }} {{ number_format((float) $c->daily_rate, 0) }}</span> <span class="text-xs text-zinc-500">/ {{ __('day') }}</span></p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-zinc-200 bg-zinc-50 p-10 text-center dark:border-zinc-700 dark:bg-zinc-800/40">
                            <p class="text-sm font-medium">{{ __('No vehicles match your filters') }}</p>
                            <p class="mt-1 text-sm text-zinc-500">{{ __('Try a different class or transmission type.') }}</p>
                        </div>
                    @endforelse
                </div>
                <div>{{ $cars->links() }}</div>
            </section>
        </div>
    </section>
</div>
