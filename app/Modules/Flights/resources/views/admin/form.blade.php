<div class="space-y-6 max-w-3xl">
    <h1 class="text-2xl font-semibold">{{ $offer ? 'Edit flight offer' : 'New flight offer' }}</h1>

    <form wire:submit="save" class="space-y-6">
        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">Flight</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input wire:model="airline" label="Airline" required />
                <x-ui.input wire:model="airline_code" label="Airline code (IATA)" required />
                <x-ui.input wire:model="flight_number" label="Flight number" required />
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium">Cabin</label>
                    <select wire:model="cabin" class="block w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                        <option value="economy">Economy</option><option value="premium_economy">Premium Economy</option>
                        <option value="business">Business</option><option value="first">First</option>
                    </select>
                </div>
                <x-ui.input wire:model="origin" label="Origin (IATA)" required maxlength="3" />
                <x-ui.input wire:model="destination" label="Destination (IATA)" required maxlength="3" />
                <x-ui.input type="datetime-local" wire:model="depart_time" label="Departure" required />
                <x-ui.input type="datetime-local" wire:model="arrive_time" label="Arrival" required />
                <x-ui.input type="number" wire:model="duration_minutes" label="Duration (min)" />
                <x-ui.input type="number" wire:model="stops" label="Stops" />
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">Price</h2>
            <div class="grid grid-cols-2 gap-4 mb-3">
                <x-ui.input type="number" step="0.01" wire:model="price" label="Price" />
                <x-ui.input wire:model="currency" label="Currency" />
            </div>
            <div class="flex items-center gap-6">
                <x-ui.checkbox wire:model="is_published" label="Published" />
                <x-ui.checkbox wire:model="is_featured" label="Featured" />
            </div>
        </x-ui.card>

        <div class="flex items-center gap-3">
            <x-ui.button type="submit" variant="primary">Save</x-ui.button>
            <a href="{{ route('admin.flights.index') }}" wire:navigate><x-ui.button variant="ghost" type="button">Cancel</x-ui.button></a>
        </div>
    </form>
</div>
