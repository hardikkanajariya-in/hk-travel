<div class="space-y-6 max-w-4xl">
    <h1 class="text-2xl font-semibold">{{ $service ? 'Edit service' : 'New taxi service' }}</h1>

    <form wire:submit="save" class="space-y-6">
        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">Service</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input wire:model.blur="title" label="Title" required />
                <x-ui.input wire:model="slug" label="Slug" required />
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium">Service type</label>
                    <select wire:model="service_type" class="block w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                        <option value="airport_transfer">Airport transfer</option>
                        <option value="hourly">Hourly hire</option>
                        <option value="point_to_point">Point to point</option>
                    </select>
                </div>
                <x-ui.input wire:model="vehicle_type" label="Vehicle type" required />
                <x-ui.input type="number" wire:model="capacity" label="Passenger capacity" />
                <x-ui.input type="number" wire:model="luggage" label="Luggage capacity" />
            </div>
            <x-ui.textarea wire:model="description" label="Description" rows="4" class="mt-4" />
            <x-ui.input wire:model="cover_image" label="Cover image URL" class="mt-4" />
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">Pricing</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <x-ui.input type="number" step="0.01" wire:model="base_fare" label="Base fare" />
                <x-ui.input type="number" step="0.01" wire:model="per_km_rate" label="Per km" />
                <x-ui.input type="number" step="0.01" wire:model="per_hour_rate" label="Per hour" />
                <x-ui.input type="number" step="0.01" wire:model="flat_rate" label="Flat rate" />
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
            <a href="{{ route('admin.taxi.index') }}" wire:navigate><x-ui.button variant="ghost" type="button">Cancel</x-ui.button></a>
        </div>
    </form>
</div>
