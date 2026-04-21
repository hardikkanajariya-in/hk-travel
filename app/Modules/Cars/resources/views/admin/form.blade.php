<div class="space-y-6 max-w-4xl">
    <h1 class="text-2xl font-semibold">{{ $car ? 'Edit vehicle' : 'New vehicle' }}</h1>

    <form wire:submit="save" class="space-y-6">
        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">Vehicle</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input wire:model.blur="name" label="Name" required :error="$errors->first('name')" />
                <x-ui.input wire:model="slug" label="Slug" required :error="$errors->first('slug')" />
                <x-ui.input wire:model="make" label="Make" />
                <x-ui.input wire:model="model" label="Model" />
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium">Class</label>
                    <select wire:model="vehicle_class" class="block w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                        @foreach (['economy','compact','sedan','suv','luxury','van'] as $c)<option value="{{ $c }}">{{ ucfirst($c) }}</option>@endforeach
                    </select>
                </div>
                <x-ui.input wire:model="cover_image" label="Cover image URL" />
            </div>
            <x-ui.textarea wire:model="description" label="Description" rows="4" class="mt-4" />
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">Specs</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <x-ui.input type="number" wire:model="seats" label="Seats" />
                <x-ui.input type="number" wire:model="doors" label="Doors" />
                <x-ui.input type="number" wire:model="luggage" label="Luggage bags" />
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium">Transmission</label>
                    <select wire:model="transmission" class="block w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                        <option value="automatic">Automatic</option><option value="manual">Manual</option>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium">Fuel</label>
                    <select wire:model="fuel_type" class="block w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                        <option value="petrol">Petrol</option><option value="diesel">Diesel</option><option value="hybrid">Hybrid</option><option value="electric">Electric</option>
                    </select>
                </div>
                <x-ui.checkbox wire:model="has_ac" label="Air conditioning" />
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">Rates</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-ui.input type="number" step="0.01" wire:model="daily_rate" label="Daily rate" />
                <x-ui.input type="number" step="0.01" wire:model="weekly_rate" label="Weekly rate" />
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
            <a href="{{ route('admin.cars.index') }}" wire:navigate><x-ui.button variant="ghost" type="button">Cancel</x-ui.button></a>
        </div>
    </form>
</div>
