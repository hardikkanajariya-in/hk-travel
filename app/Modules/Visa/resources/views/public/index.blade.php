<div>
    <section class="relative overflow-hidden border-b border-indigo-100 bg-gradient-to-br from-indigo-50 via-white to-violet-50 dark:border-indigo-900/40 dark:from-indigo-950/30 dark:via-zinc-950 dark:to-violet-950/30">
        <div class="absolute right-6 top-6 hidden opacity-20 sm:block" aria-hidden="true">
            <svg viewBox="0 0 96 96" class="h-28 w-28 text-indigo-600" fill="currentColor"><path d="M24 8h36l16 16v52a8 8 0 01-8 8H24a8 8 0 01-8-8V16a8 8 0 018-8zm32 6v14h14L56 14zM28 40h40v4H28v-4zm0 12h40v4H28v-4zm0 12h28v4H28v-4z"/></svg>
        </div>
        <div class="relative mx-auto max-w-7xl px-6 py-12 sm:py-16">
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-400">{{ __('Travel paperwork made simple') }}</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">{{ __('Visa services') }}</h1>
            <p class="mt-3 max-w-2xl text-base text-zinc-600 dark:text-zinc-400">{{ __('Check requirements, fees and processing times for tourist, business and transit visas — and apply with our help.') }}</p>
        </div>
    </section>

    <section class="mx-auto w-full max-w-7xl px-6 py-8 sm:py-10">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">
            <aside class="lg:col-span-1">
                <x-ui.card class="p-5 lg:sticky lg:top-24">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500">{{ __('Find a visa') }}</h2>
                    <div class="mt-4 space-y-4">
                        <x-ui.input wire:model.live.debounce.500ms="search" :label="__('Search')" placeholder="{{ __('Country or visa type…') }}" />
                        <x-ui.select wire:model.live="country" :label="__('Country')" :options="collect($countries)->mapWithKeys(fn ($c) => [$c => $c])->prepend(__('All countries'), '')->all()" />
                        <x-ui.select wire:model.live="type" :label="__('Visa type')" :options="collect($types)->mapWithKeys(fn ($t) => [$t => $t])->prepend(__('All types'), '')->all()" />
                    </div>
                </x-ui.card>
            </aside>

            <section class="lg:col-span-3">
                <div class="space-y-3">
                    @forelse ($services as $s)
                        <a href="{{ route('visa.show', $s->slug) }}" wire:navigate class="group block rounded-2xl border border-zinc-200 bg-white p-5 transition hover:border-indigo-300 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-indigo-700">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex flex-1 items-start gap-4">
                                    <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300">
                                        <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2zm-7-13h.01"/></svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-medium uppercase tracking-wide text-indigo-700 dark:text-indigo-400">{{ $s->country }}</p>
                                        <h3 class="mt-1 text-lg font-semibold leading-tight group-hover:text-indigo-700 dark:group-hover:text-indigo-300">{{ $s->title }}</h3>
                                        <dl class="mt-3 grid grid-cols-1 gap-2 text-sm sm:grid-cols-3">
                                            <div><dt class="text-xs text-zinc-500">{{ __('Stay up to') }}</dt><dd class="font-medium">{{ trans_choice('{1} :count day|[2,*] :count days', $s->allowed_stay_days, ['count' => $s->allowed_stay_days]) }}</dd></div>
                                            <div><dt class="text-xs text-zinc-500">{{ __('Valid for') }}</dt><dd class="font-medium">{{ trans_choice('{1} :count day|[2,*] :count days', $s->validity_days, ['count' => $s->validity_days]) }}</dd></div>
                                            <div><dt class="text-xs text-zinc-500">{{ __('Processing') }}</dt><dd class="font-medium">{{ $s->processing_days_min }}–{{ $s->processing_days_max }} {{ __('days') }}</dd></div>
                                        </dl>
                                    </div>
                                </div>
                                <div class="text-right sm:shrink-0">
                                    <p class="text-xs text-zinc-500">{{ __('Total fee from') }}</p>
                                    <p class="text-2xl font-semibold">{{ $s->currency }} {{ number_format((float) $s->fee + (float) $s->service_fee, 0) }}</p>
                                    <p class="mt-2 inline-flex items-center gap-1 text-sm font-medium text-indigo-700 dark:text-indigo-400">{{ __('View details') }} <span aria-hidden="true">→</span></p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-2xl border border-dashed border-zinc-200 bg-zinc-50 p-10 text-center dark:border-zinc-700 dark:bg-zinc-800/40">
                            <p class="text-sm font-medium">{{ __('No visa services match your search') }}</p>
                            <p class="mt-1 text-sm text-zinc-500">{{ __('Try a different country or visa type.') }}</p>
                        </div>
                    @endforelse
                </div>
                <div class="mt-6">{{ $services->links() }}</div>
            </section>
        </div>
    </section>
</div>
