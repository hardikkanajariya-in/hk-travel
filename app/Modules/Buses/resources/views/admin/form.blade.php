<div class="space-y-6 max-w-4xl">
    <h1 class="text-2xl font-semibold">{{ $route ? 'Edit bus route' : 'New bus route' }}</h1>

    <form wire:submit="save" class="space-y-6">
        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">Route</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input wire:model.blur="title" label="Title" required :error="$errors->first('title')" />
                <x-ui.input wire:model="slug" label="Slug" required :error="$errors->first('slug')" />
                <x-ui.input wire:model="operator" label="Operator" required :error="$errors->first('operator')" />
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium">Type</label>
                    <select wire:model="bus_type" class="block w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                        <option value="standard">Standard</option><option value="ac">A/C</option><option value="sleeper">Sleeper</option><option value="luxury">Luxury</option>
                    </select>
                </div>
                <x-ui.input wire:model="origin" label="Origin" required />
                <x-ui.input wire:model="destination" label="Destination" required />
                <x-ui.input wire:model="departure_time" label="Departure time" required />
                <x-ui.input wire:model="arrival_time" label="Arrival time" required />
                <x-ui.input type="number" wire:model="duration_minutes" label="Duration (min)" required />
                <x-ui.input type="number" wire:model="distance_km" label="Distance (km)" required />
            </div>
            <x-ui.textarea wire:model="description" label="Description" rows="4" class="mt-4" />
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">Schedule days</h2>
            <div class="flex flex-wrap gap-3">
                @foreach (['mon','tue','wed','thu','fri','sat','sun'] as $d)
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" wire:model="schedule_days" value="{{ $d }}" class="rounded">
                        <span>{{ ucfirst($d) }}</span>
                    </label>
                @endforeach
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">Fare</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input type="number" step="0.01" wire:model="fare" label="Fare" />
                <x-ui.input wire:model="currency" label="Currency" />
            </div>
        </x-ui.card>

        <x-ui.card>
            <div class="flex items-center gap-6">
                <x-ui.checkbox wire:model="is_published" label="Published" />
                <x-ui.checkbox wire:model="is_featured" label="Featured" />
            </div>
        </x-ui.card>

        <div class="flex items-center gap-3">
            <x-ui.button type="submit" variant="primary">Save</x-ui.button>
            <a href="{{ route('admin.buses.index') }}" wire:navigate><x-ui.button variant="ghost" type="button">Cancel</x-ui.button></a>
        </div>
    </form>
</div>
