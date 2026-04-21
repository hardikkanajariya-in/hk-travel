<div>
    <section class="relative overflow-hidden border-b border-emerald-100 bg-gradient-to-br from-emerald-50 via-white to-lime-50 dark:border-emerald-900/40 dark:from-emerald-950/30 dark:via-zinc-950 dark:to-lime-950/20">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-6 py-12 sm:flex-row sm:items-center sm:justify-between sm:py-16">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">{{ __('Things to do') }}</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">{{ __('Activities and experiences') }}</h1>
                <p class="mt-3 text-base text-zinc-600 dark:text-zinc-400">{{ __('Day trips, guided tours and unique moments — book the experiences that make a holiday memorable.') }}</p>
            </div>
            <svg viewBox="0 0 64 64" class="hidden h-24 w-24 text-emerald-500/40 sm:block" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M8 50l12-22 10 14 8-10 18 18" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="46" cy="18" r="6"/>
            </svg>
        </div>
    </section>

    <section class="mx-auto w-full max-w-7xl px-6 py-8 sm:py-10">
        <div class="mb-6 grid grid-cols-1 gap-3 rounded-2xl border border-zinc-200 bg-white p-4 sm:grid-cols-3 dark:border-zinc-800 dark:bg-zinc-900">
            <x-ui.input wire:model.live.debounce.500ms="search" placeholder="{{ __('Search experiences…') }}" />
            <x-ui.select wire:model.live="destinationId" :options="collect($destinations)->mapWithKeys(fn ($d) => [$d->id => $d->name])->prepend(__('Any destination'), '')->all()" />
            <x-ui.select wire:model.live="category" :options="collect($categories)->mapWithKeys(fn ($c) => [$c => ucfirst(str_replace('_', ' ', $c))])->prepend(__('All categories'), '')->all()" />
        </div>

        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                {{ trans_choice('{0} No experiences found|{1} :count experience|[2,*] :count experiences', $activities->total(), ['count' => $activities->total()]) }}
            </p>
            <div class="flex items-center gap-2">
                <label for="act-sort" class="text-sm text-zinc-500">{{ __('Sort by') }}</label>
                <x-ui.select id="act-sort" wire:model.live="sort" :options="['featured' => __('Featured first'), 'price_asc' => __('Price (low to high)'), 'price_desc' => __('Price (high to low)'), 'duration' => __('Shortest first')]" class="w-56" />
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($activities as $a)
                <a href="{{ route('activities.show', $a->slug) }}" wire:navigate
                    class="group block overflow-hidden rounded-2xl border border-zinc-200 bg-white transition hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-emerald-700">
                    <div class="relative aspect-video overflow-hidden">
                        @if ($a->cover_image)
                            <img src="{{ $a->cover_image }}" alt="" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        @else
                            <div class="h-full w-full bg-gradient-to-br from-emerald-300 to-lime-500"></div>
                        @endif
                        @if ($a->category)
                            <span class="absolute left-3 top-3 rounded-full bg-emerald-600 px-2.5 py-0.5 text-[11px] font-medium uppercase tracking-wide text-white">{{ str_replace('_', ' ', $a->category) }}</span>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="text-base font-semibold leading-tight group-hover:text-emerald-700 dark:group-hover:text-emerald-400">{{ $a->name }}</h3>
                        <p class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-zinc-500">
                            <span class="inline-flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ trans_choice('{1} :count hour|[2,*] :count hours', $a->duration_hours, ['count' => $a->duration_hours]) }}
                            </span>
                            <span aria-hidden="true">·</span>
                            <span class="capitalize">{{ str_replace('_', ' ', $a->difficulty) }}</span>
                        </p>
                        <div class="mt-3 flex items-end justify-between">
                            <p class="text-xs text-zinc-500">{{ __('From') }}</p>
                            <p class="text-lg font-semibold">{{ $a->currency }} {{ number_format((float) $a->price, 0) }}</p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-zinc-200 bg-zinc-50 p-10 text-center dark:border-zinc-700 dark:bg-zinc-800/40">
                    <p class="text-sm font-medium">{{ __('No experiences match your filters') }}</p>
                    <p class="mt-1 text-sm text-zinc-500">{{ __('Try a different category or destination.') }}</p>
                </div>
            @endforelse
        </div>
        <div class="mt-8">{{ $activities->links() }}</div>
    </section>
</div>
