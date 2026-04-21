<div class="mx-auto w-full max-w-7xl px-6 py-8 sm:py-10">
    <header class="mb-6 sm:mb-8">
        <p class="text-xs font-semibold uppercase tracking-wider text-purple-700 dark:text-purple-400">{{ __('Rail travel') }}</p>
        <h1 class="mt-1 text-2xl font-semibold sm:text-3xl">{{ __('Search trains') }}</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Compare timetables and fares across train operators.') }}</p>
    </header>

    <x-ui.card class="mb-6 p-4 sm:p-6">
        <form wire:submit="search" class="space-y-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <x-ui.input wire:model="origin" :label="__('From station')" placeholder="{{ __('e.g. London Kings Cross') }}" required />
                <x-ui.input wire:model="destination" :label="__('To station')" placeholder="{{ __('e.g. Edinburgh Waverley') }}" required />
                <x-ui.input type="date" wire:model="departDate" :label="__('Depart')" required />
                <x-ui.input type="date" wire:model="returnDate" :label="__('Return')" hint="{{ __('Leave blank for one-way') }}" />
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:items-end">
                <x-ui.select wire:model="class" :label="__('Travel class')" :options="['standard' => __('Standard'), 'first' => __('First class'), 'business' => __('Business'), 'sleeper' => __('Sleeper')]" />
                <div class="sm:col-span-2 lg:col-start-4">
                    <x-ui.button type="submit" variant="primary" class="h-10 w-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                        {{ __('Search') }}
                    </x-ui.button>
                </div>
            </div>

            @error('origin')<p class="text-xs text-hk-danger">{{ $message }}</p>@enderror
            @error('destination')<p class="text-xs text-hk-danger">{{ $message }}</p>@enderror
            @error('departDate')<p class="text-xs text-hk-danger">{{ $message }}</p>@enderror
        </form>
    </x-ui.card>

    @if ($hasSearched)
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                {{ trans_choice('{0} No trains found|{1} :count train found|[2,*] :count trains found', $results->count(), ['count' => $results->count()]) }}
            </p>
            <div class="flex items-center gap-2">
                <label for="train-sort" class="text-sm text-zinc-500">{{ __('Sort by') }}</label>
                <x-ui.select id="train-sort" wire:model.live="sort" :options="['price_asc' => __('Price (low to high)'), 'price_desc' => __('Price (high to low)'), 'duration' => __('Shortest duration'), 'depart' => __('Earliest departure')]" class="w-56" />
            </div>
        </div>

        <div class="space-y-3">
            @forelse ($results as $r)
                <article class="flex flex-col gap-4 rounded-xl border border-zinc-200 bg-white p-4 transition hover:border-purple-300 hover:shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-5 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-purple-700">
                    <div class="flex flex-1 items-start gap-4 min-w-0">
                        <div class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-300">
                            <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19l-2 3m10-3l2 3M5 17h14a2 2 0 002-2V7a4 4 0 00-4-4H7a4 4 0 00-4 4v8a2 2 0 002 2zm5-3.5h.01M14 13.5h.01M9 9h6"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ $r->operator }} · {{ $r->trainNumber }}</p>
                            <p class="mt-1 text-lg font-semibold">{{ $r->origin }} <span class="mx-1 text-zinc-400">→</span> {{ $r->destination }}</p>
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                {{ \Illuminate\Support\Carbon::parse($r->departTime)->format('D, d M · H:i') }}
                                <span class="mx-1 text-zinc-400">–</span>
                                {{ \Illuminate\Support\Carbon::parse($r->arriveTime)->format('H:i') }}
                                <span class="mx-1 text-zinc-400">·</span>
                                {{ floor($r->durationMinutes / 60) }}h {{ $r->durationMinutes % 60 }}m
                                <span class="mx-1 text-zinc-400">·</span>
                                @if ($r->changes === 0)
                                    <span class="font-medium text-green-600 dark:text-green-400">{{ __('Direct') }}</span>
                                @else
                                    {{ trans_choice('{1} :count change|[2,*] :count changes', $r->changes, ['count' => $r->changes]) }}
                                @endif
                                @if ($r->refundable) <span class="mx-1 text-zinc-400">·</span> <span class="text-emerald-600 dark:text-emerald-400">{{ __('Refundable') }}</span> @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-4 sm:flex-col sm:items-end sm:justify-center sm:text-right sm:shrink-0">
                        <div>
                            <p class="text-2xl font-semibold">{{ $r->currency }} {{ number_format($r->price, 0) }}</p>
                            <p class="text-xs capitalize text-zinc-500 dark:text-zinc-400">{{ str_replace('_', ' ', $r->class) }}</p>
                        </div>
                    </div>
                </article>
            @empty
                <x-ui.alert variant="info">{{ __('No trains match this route. Try different dates or nearby stations.') }}</x-ui.alert>
            @endforelse
        </div>
    @else
        <div class="rounded-xl border border-dashed border-zinc-200 bg-zinc-50 p-8 text-center dark:border-zinc-700 dark:bg-zinc-800/40">
            <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-white text-purple-700 shadow-sm dark:bg-zinc-900 dark:text-purple-300">
                <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 17h14a2 2 0 002-2V7a4 4 0 00-4-4H7a4 4 0 00-4 4v8a2 2 0 002 2z"/></svg>
            </div>
            <p class="mt-3 text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ __('Where are you travelling by train?') }}</p>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Enter your stations and dates to see live fares and timings.') }}</p>
        </div>
    @endif
</div>
