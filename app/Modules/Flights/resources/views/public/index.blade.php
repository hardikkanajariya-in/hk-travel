<div class="mx-auto w-full max-w-7xl px-6 py-8 sm:py-10">
    <header class="mb-6 sm:mb-8">
        <h1 class="text-2xl font-semibold sm:text-3xl">{{ __('Search flights') }}</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Compare fares across airlines and book the trip that fits.') }}</p>
    </header>

    <x-ui.card class="mb-6 p-4 sm:p-6">
        <form wire:submit="search" class="space-y-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <x-ui.input
                    wire:model="origin"
                    :label="__('From')"
                    placeholder="{{ __('e.g. LON') }}"
                    hint="{{ __('Three-letter airport code') }}"
                    maxlength="3"
                    required
                />
                <x-ui.input
                    wire:model="destination"
                    :label="__('To')"
                    placeholder="{{ __('e.g. NYC') }}"
                    hint="{{ __('Three-letter airport code') }}"
                    maxlength="3"
                    required
                />
                <x-ui.input type="date" wire:model="departDate" :label="__('Depart')" required />
                <x-ui.input type="date" wire:model="returnDate" :label="__('Return')" hint="{{ __('Leave blank for one-way') }}" />
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 lg:grid-cols-4 lg:items-end">
                <x-ui.select
                    wire:model="cabin"
                    :label="__('Cabin')"
                    :options="[
                        'economy' => __('Economy'),
                        'premium_economy' => __('Premium economy'),
                        'business' => __('Business'),
                        'first' => __('First'),
                    ]"
                />
                <x-ui.select
                    wire:model="adults"
                    :label="__('Adults')"
                    :options="collect(range(1, 9))->mapWithKeys(fn ($n) => [$n => $n])->all()"
                />
                <x-ui.select
                    wire:model="children"
                    :label="__('Children')"
                    :options="collect(range(0, 8))->mapWithKeys(fn ($n) => [$n => $n])->all()"
                />
                <x-ui.button type="submit" variant="primary" class="h-10 w-full sm:w-auto sm:justify-self-end lg:w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                    {{ __('Search') }}
                </x-ui.button>
            </div>

            @error('origin')<p class="text-xs text-hk-danger">{{ $message }}</p>@enderror
            @error('destination')<p class="text-xs text-hk-danger">{{ $message }}</p>@enderror
            @error('departDate')<p class="text-xs text-hk-danger">{{ $message }}</p>@enderror
            @error('returnDate')<p class="text-xs text-hk-danger">{{ $message }}</p>@enderror
        </form>
    </x-ui.card>

    @if ($hasSearched)
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                {{ trans_choice('{0} No flights found|{1} :count flight found|[2,*] :count flights found', $results->count(), ['count' => $results->count()]) }}
            </p>
            <div class="flex items-center gap-2">
                <label for="flight-sort" class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Sort by') }}</label>
                <select id="flight-sort" wire:model.live="sort"
                        class="rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm focus:border-hk-primary-500 focus:ring focus:ring-hk-primary-100 dark:border-zinc-700 dark:bg-zinc-900 dark:focus:ring-hk-primary-900/40">
                    <option value="price_asc">{{ __('Price (low to high)') }}</option>
                    <option value="price_desc">{{ __('Price (high to low)') }}</option>
                    <option value="duration">{{ __('Shortest duration') }}</option>
                    <option value="depart">{{ __('Earliest departure') }}</option>
                </select>
            </div>
        </div>

        <div class="space-y-3">
            @forelse ($results as $r)
                <article class="flex flex-col gap-4 rounded-xl border border-zinc-200 bg-white p-4 transition hover:border-hk-primary-300 hover:shadow-sm sm:flex-row sm:items-center sm:justify-between sm:gap-6 sm:p-5 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-hk-primary-700">
                    <div class="flex flex-1 items-start gap-4 min-w-0">
                        <div class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-hk-primary-50 text-hk-primary-600 dark:bg-hk-primary-950/40 dark:text-hk-primary-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ $r->airline }} · {{ $r->flightNumber }}</p>
                            <p class="mt-1 text-lg font-semibold">
                                {{ $r->origin }}
                                <span class="mx-1 text-zinc-400">→</span>
                                {{ $r->destination }}
                            </p>
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                {{ \Illuminate\Support\Carbon::parse($r->departTime)->format('D, d M · H:i') }}
                                <span class="mx-1 text-zinc-400">–</span>
                                {{ \Illuminate\Support\Carbon::parse($r->arriveTime)->format('H:i') }}
                                <span class="mx-1 text-zinc-400">·</span>
                                {{ floor($r->durationMinutes / 60) }}h {{ $r->durationMinutes % 60 }}m
                                <span class="mx-1 text-zinc-400">·</span>
                                @if ($r->stops === 0)
                                    <span class="font-medium text-green-600 dark:text-green-400">{{ __('Direct') }}</span>
                                @else
                                    {{ trans_choice('{1} :count stop|[2,*] :count stops', $r->stops, ['count' => $r->stops]) }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-4 sm:flex-col sm:items-end sm:justify-center sm:text-right sm:shrink-0">
                        <div>
                            <p class="text-2xl font-semibold">{{ $r->currency }} {{ number_format($r->price, 0) }}</p>
                            <p class="text-xs capitalize text-zinc-500 dark:text-zinc-400">{{ str_replace('_', ' ', $r->cabin) }}</p>
                        </div>
                    </div>
                </article>
            @empty
                <x-ui.alert variant="info">{{ __('No flights match this route. Try different dates or nearby airports.') }}</x-ui.alert>
            @endforelse
        </div>
    @else
        <div class="rounded-xl border border-dashed border-zinc-200 bg-zinc-50 p-8 text-center dark:border-zinc-700 dark:bg-zinc-800/40">
            <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-white text-hk-primary-600 shadow-sm dark:bg-zinc-900 dark:text-hk-primary-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/>
                </svg>
            </div>
            <p class="mt-3 text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ __('Where would you like to fly?') }}</p>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Enter your route and dates above to see live fares.') }}</p>
        </div>
    @endif
</div>
