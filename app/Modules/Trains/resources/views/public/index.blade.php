<div class="container mx-auto px-4 py-10">
    <header class="mb-6">
        <h1 class="text-3xl font-semibold">Search trains</h1>
        <p class="text-zinc-500 mt-1">Powered by {{ ucfirst(config('hk-modules.modules.trains.provider', 'stub')) }} provider.</p>
    </header>

    <x-ui.card class="mb-6">
        <form wire:submit="search" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
            <div class="md:col-span-1"><x-ui.input wire:model="origin" label="From (station)" required /></div>
            <div class="md:col-span-1"><x-ui.input wire:model="destination" label="To (station)" required /></div>
            <div class="md:col-span-1"><x-ui.input type="date" wire:model="departDate" label="Depart" required /></div>
            <div class="md:col-span-1"><x-ui.input type="date" wire:model="returnDate" label="Return (optional)" /></div>
            <div class="md:col-span-1">
                <label class="block text-sm font-medium mb-1.5">Class</label>
                <select wire:model="class" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                    <option value="standard">Standard</option>
                    <option value="first">First</option>
                    <option value="business">Business</option>
                    <option value="sleeper">Sleeper</option>
                </select>
            </div>
            <div class="md:col-span-1"><x-ui.button type="submit" variant="primary" class="w-full">Search</x-ui.button></div>
        </form>
        @error('origin')<p class="text-xs text-hk-danger mt-2">{{ $message }}</p>@enderror
        @error('destination')<p class="text-xs text-hk-danger mt-2">{{ $message }}</p>@enderror
        @error('departDate')<p class="text-xs text-hk-danger mt-2">{{ $message }}</p>@enderror
    </x-ui.card>

    @if ($hasSearched)
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-zinc-500">{{ $results->count() }} result(s)</p>
            <select wire:model.live="sort" class="rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                <option value="price_asc">Price (low → high)</option>
                <option value="price_desc">Price (high → low)</option>
                <option value="duration">Shortest duration</option>
                <option value="depart">Earliest departure</option>
            </select>
        </div>

        <div class="space-y-3">
            @forelse ($results as $r)
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 p-5 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs text-zinc-500 uppercase">{{ $r->operator }} · {{ $r->trainNumber }}</p>
                        <p class="font-semibold mt-1 text-lg">{{ $r->origin }} → {{ $r->destination }}</p>
                        <p class="text-sm text-zinc-500">
                            {{ \Illuminate\Support\Carbon::parse($r->departTime)->format('D, d M H:i') }} –
                            {{ \Illuminate\Support\Carbon::parse($r->arriveTime)->format('H:i') }} ·
                            {{ floor($r->durationMinutes / 60) }}h {{ $r->durationMinutes % 60 }}m ·
                            {{ $r->changes === 0 ? 'Direct' : $r->changes.' change(s)' }}
                            @if ($r->fareType) · {{ ucfirst($r->fareType) }}@endif
                            @if ($r->refundable) · Refundable @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-semibold">{{ $r->currency }} {{ number_format($r->price, 0) }}</p>
                        <p class="text-xs text-zinc-500 capitalize">{{ str_replace('_', ' ', $r->class) }}</p>
                    </div>
                </div>
            @empty
                <x-ui.alert variant="info">No trains found for this route. Try different dates or stations.</x-ui.alert>
            @endforelse
        </div>
    @endif
</div>
