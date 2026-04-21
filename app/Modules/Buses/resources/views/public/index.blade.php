<div>
    <section class="relative overflow-hidden border-b border-orange-100 bg-gradient-to-br from-orange-50 via-white to-rose-50 dark:border-orange-900/40 dark:from-orange-950/30 dark:via-zinc-950 dark:to-rose-950/20">
        <div class="mx-auto max-w-7xl px-6 py-12 sm:py-16">
            <p class="text-xs font-semibold uppercase tracking-wider text-orange-700 dark:text-orange-400">{{ __('Intercity travel') }}</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">{{ __('Bus routes') }}</h1>
            <p class="mt-3 max-w-2xl text-base text-zinc-600 dark:text-zinc-400">{{ __('Compare operators, comfort levels and timings on intercity coach routes — book your seat in seconds.') }}</p>
        </div>
    </section>

    <section class="mx-auto w-full max-w-7xl px-6 py-8 sm:py-10">
        <x-ui.card class="mb-6 p-5">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <x-ui.input wire:model.live.debounce.500ms="origin" :label="__('Travelling from')" placeholder="{{ __('Origin city') }}" />
                <x-ui.input wire:model.live.debounce.500ms="destination" :label="__('Travelling to')" placeholder="{{ __('Destination city') }}" />
                <x-ui.select wire:model.live="type" :label="__('Bus type')" :options="['' => __('Any type'), 'standard' => __('Standard'), 'ac' => __('Air-conditioned'), 'sleeper' => __('Sleeper'), 'luxury' => __('Luxury')]" />
            </div>
        </x-ui.card>

        <div class="space-y-3">
            @forelse ($routes as $r)
                <a href="{{ route('buses.show', $r->slug) }}" wire:navigate class="group block rounded-2xl border border-zinc-200 bg-white p-5 transition hover:border-orange-300 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-orange-700">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-1 items-start gap-4">
                            <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-orange-50 text-orange-700 dark:bg-orange-950/50 dark:text-orange-400">
                                <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 6v6a2 2 0 002 2h4a2 2 0 002-2V6M5 19a2 2 0 100-4 2 2 0 000 4zm14 0a2 2 0 100-4 2 2 0 000 4zM4 17V8a4 4 0 014-4h8a4 4 0 014 4v9"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-medium uppercase tracking-wide text-orange-700 dark:text-orange-400">{{ $r->operator }} · {{ str_replace('_', ' ', ucfirst($r->bus_type)) }}</p>
                                <h3 class="mt-1 flex items-center gap-2 text-lg font-semibold leading-tight">
                                    <span>{{ $r->origin }}</span>
                                    <svg class="size-4 text-zinc-400" viewBox="0 0 16 16" fill="currentColor"><path d="M9 3l5 5-5 5V9H1V7h8V3z"/></svg>
                                    <span>{{ $r->destination }}</span>
                                </h3>
                                <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-sm text-zinc-500">
                                    <span><span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $r->departure_time }}</span> – <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $r->arrival_time }}</span></span>
                                    <span>{{ floor($r->duration_minutes / 60) }}h {{ $r->duration_minutes % 60 }}m</span>
                                    <span>{{ $r->distance_km }} km</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-4 sm:flex-col sm:items-end sm:text-right">
                            <div>
                                <p class="text-2xl font-semibold">{{ $r->currency }} {{ number_format((float) $r->fare, 0) }}</p>
                                <p class="text-xs text-zinc-500">{{ __('per seat') }}</p>
                            </div>
                            <span class="text-sm font-medium text-orange-700 group-hover:underline dark:text-orange-400">{{ __('View seats') }} →</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="rounded-2xl border border-dashed border-zinc-200 bg-zinc-50 p-10 text-center dark:border-zinc-700 dark:bg-zinc-800/40">
                    <p class="text-sm font-medium">{{ __('No routes match your search') }}</p>
                    <p class="mt-1 text-sm text-zinc-500">{{ __('Try a different city pair or bus type.') }}</p>
                </div>
            @endforelse
        </div>
        <div class="mt-6">{{ $routes->links() }}</div>
    </section>
</div>
