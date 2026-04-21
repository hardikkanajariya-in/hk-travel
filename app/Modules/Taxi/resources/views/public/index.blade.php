<div>
    <section class="relative overflow-hidden border-b border-amber-100 bg-gradient-to-r from-amber-400 via-yellow-400 to-amber-500 dark:border-amber-900/40">
        <div class="absolute -bottom-10 right-10 hidden opacity-20 sm:block" aria-hidden="true">
            <svg viewBox="0 0 64 64" class="h-40 w-40 text-zinc-900" fill="currentColor"><path d="M14 38l4-12a4 4 0 014-4h20a4 4 0 014 4l4 12v10a2 2 0 01-2 2h-4a2 2 0 01-2-2v-2H22v2a2 2 0 01-2 2h-4a2 2 0 01-2-2V38zm6-2h24l-3-9H23l-3 9z"/></svg>
        </div>
        <div class="relative mx-auto max-w-7xl px-6 py-12 sm:py-16">
            <p class="text-xs font-semibold uppercase tracking-wider text-zinc-900/70">{{ __('Door-to-door travel') }}</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-zinc-900 sm:text-4xl">{{ __('Taxis and transfers') }}</h1>
            <p class="mt-3 max-w-2xl text-base text-zinc-900/80">{{ __('Airport pickups, hourly hire and point-to-point rides — booked in advance with a fixed price.') }}</p>
        </div>
    </section>

    <section class="mx-auto w-full max-w-7xl px-6 py-8 sm:py-10">
        <x-ui.card class="mb-6 p-5">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-ui.select wire:model.live="type" :label="__('Service type')" :options="['' => __('All services'), 'airport_transfer' => __('Airport transfer'), 'hourly' => __('Hourly hire'), 'point_to_point' => __('Point to point')]" />
                <x-ui.input wire:model.live.debounce.500ms="vehicle" :label="__('Vehicle type')" placeholder="{{ __('e.g. Sedan, SUV, Van') }}" />
            </div>
        </x-ui.card>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($services as $s)
                <a href="{{ route('taxi.show', $s->slug) }}" wire:navigate class="group block overflow-hidden rounded-2xl border border-zinc-200 bg-white transition hover:-translate-y-0.5 hover:border-amber-400 hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-amber-600">
                    <div class="relative aspect-video overflow-hidden bg-amber-50 dark:bg-amber-950/30">
                        @if ($s->cover_image)
                            <img src="{{ $s->cover_image }}" alt="" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-amber-300 to-amber-500 text-white/40">
                                <svg viewBox="0 0 64 64" class="h-16 w-16" fill="currentColor"><path d="M14 38l4-12a4 4 0 014-4h20a4 4 0 014 4l4 12v10a2 2 0 01-2 2h-4a2 2 0 01-2-2v-2H22v2a2 2 0 01-2 2h-4a2 2 0 01-2-2V38z"/></svg>
                            </div>
                        @endif
                        <span class="absolute left-3 top-3 rounded-full bg-zinc-900/80 px-2.5 py-0.5 text-[11px] font-medium uppercase tracking-wide text-amber-300">{{ str_replace('_', ' ', $s->service_type) }}</span>
                    </div>
                    <div class="p-4">
                        <h3 class="text-base font-semibold leading-tight">{{ $s->title }}</h3>
                        <ul class="mt-2 flex flex-wrap gap-x-3 gap-y-1 text-xs text-zinc-500">
                            <li>{{ $s->vehicle_type }}</li>
                            <li>·</li>
                            <li>{{ trans_choice('{1} :count passenger|[2,*] :count passengers', $s->capacity, ['count' => $s->capacity]) }}</li>
                            <li>·</li>
                            <li>{{ trans_choice('{1} :count bag|[2,*] :count bags', $s->luggage, ['count' => $s->luggage]) }}</li>
                        </ul>
                        <div class="mt-3 flex items-end justify-between border-t border-zinc-100 pt-3 dark:border-zinc-800">
                            <p class="text-xs text-zinc-500">{{ __('Fixed price from') }}</p>
                            <p class="text-lg font-semibold">{{ $s->currency }} {{ number_format((float) max($s->flat_rate, $s->base_fare), 0) }}</p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-zinc-200 bg-zinc-50 p-10 text-center dark:border-zinc-700 dark:bg-zinc-800/40">
                    <p class="text-sm font-medium">{{ __('No transfer services available') }}</p>
                    <p class="mt-1 text-sm text-zinc-500">{{ __('Try a different service or vehicle type.') }}</p>
                </div>
            @endforelse
        </div>
        <div class="mt-8">{{ $services->links() }}</div>
    </section>
</div>
