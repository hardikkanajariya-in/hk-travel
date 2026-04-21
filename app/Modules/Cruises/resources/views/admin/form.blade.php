<div class="space-y-6 max-w-5xl">
    <h1 class="text-2xl font-semibold">{{ $cruise ? 'Edit cruise' : 'New cruise' }}</h1>

    <form wire:submit="save" class="space-y-6">
        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">General</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input wire:model.blur="title" label="Title" required />
                <x-ui.input wire:model="slug" label="Slug" required />
                <x-ui.input wire:model="cruise_line" label="Cruise line" required />
                <x-ui.input wire:model="ship_name" label="Ship name" />
                <x-ui.input wire:model="departure_port" label="Departure port" required />
                <x-ui.input wire:model="arrival_port" label="Arrival port" required />
                <x-ui.input type="date" wire:model="departure_date" label="Departure date" />
                <x-ui.input type="date" wire:model="return_date" label="Return date" />
                <x-ui.input type="number" wire:model="duration_nights" label="Nights" />
                <x-ui.input wire:model="cover_image" label="Cover image URL" />
            </div>
            <x-ui.textarea wire:model="description" label="Description" rows="4" class="mt-4" />
            <x-ui.textarea wire:model="highlights" label="Highlights" rows="2" class="mt-4" />
        </x-ui.card>

        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500">Cabin types</h2>
                <button type="button" wire:click="addCabin" class="text-xs text-hk-primary-600 hover:underline">+ Add cabin</button>
            </div>
            <div class="space-y-3">
                @foreach ($cabin_types as $idx => $cabin)
                    <div wire:key="cabin-{{ $idx }}" class="grid grid-cols-12 gap-2 items-start">
                        <input wire:model="cabin_types.{{ $idx }}.name" placeholder="Cabin name" class="col-span-5 rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                        <input type="number" step="0.01" wire:model="cabin_types.{{ $idx }}.price" placeholder="Price" class="col-span-3 rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                        <input type="number" wire:model="cabin_types.{{ $idx }}.capacity" placeholder="Capacity" class="col-span-3 rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                        <button type="button" wire:click="removeCabin({{ $idx }})" class="col-span-1 text-hk-danger text-xs">×</button>
                    </div>
                @endforeach
            </div>
        </x-ui.card>

        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500">Itinerary</h2>
                <button type="button" wire:click="addDay" class="text-xs text-hk-primary-600 hover:underline">+ Add day</button>
            </div>
            <div class="space-y-3">
                @foreach ($itinerary as $idx => $d)
                    <div wire:key="day-{{ $idx }}" class="grid grid-cols-12 gap-2 items-start">
                        <input type="number" wire:model="itinerary.{{ $idx }}.day" class="col-span-1 rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-2 py-2 text-sm">
                        <input wire:model="itinerary.{{ $idx }}.port" placeholder="Port" class="col-span-4 rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                        <input wire:model="itinerary.{{ $idx }}.activity" placeholder="Activity" class="col-span-6 rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                        <button type="button" wire:click="removeDay({{ $idx }})" class="col-span-1 text-hk-danger text-xs">×</button>
                    </div>
                @endforeach
            </div>
        </x-ui.card>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-ui.card>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500">Inclusions</h2>
                    <button type="button" wire:click="addInclusion" class="text-xs text-hk-primary-600 hover:underline">+ Add</button>
                </div>
                @foreach ($inclusions as $idx => $i)
                    <div wire:key="inc-{{ $idx }}" class="flex gap-2 mb-2">
                        <input wire:model="inclusions.{{ $idx }}" class="flex-1 rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                        <button type="button" wire:click="removeInclusion({{ $idx }})" class="text-hk-danger text-xs">×</button>
                    </div>
                @endforeach
            </x-ui.card>
            <x-ui.card>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500">Exclusions</h2>
                    <button type="button" wire:click="addExclusion" class="text-xs text-hk-primary-600 hover:underline">+ Add</button>
                </div>
                @foreach ($exclusions as $idx => $e)
                    <div wire:key="exc-{{ $idx }}" class="flex gap-2 mb-2">
                        <input wire:model="exclusions.{{ $idx }}" class="flex-1 rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                        <button type="button" wire:click="removeExclusion({{ $idx }})" class="text-hk-danger text-xs">×</button>
                    </div>
                @endforeach
            </x-ui.card>
        </div>

        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">Price</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-3">
                <x-ui.input type="number" step="0.01" wire:model="price_from" label="Price from" />
                <x-ui.input wire:model="currency" label="Currency" />
            </div>
            <div class="flex items-center gap-6">
                <x-ui.checkbox wire:model="is_published" label="Published" />
                <x-ui.checkbox wire:model="is_featured" label="Featured" />
            </div>
        </x-ui.card>

        <div class="flex items-center gap-3">
            <x-ui.button type="submit" variant="primary">Save</x-ui.button>
            <a href="{{ route('admin.cruises.index') }}" wire:navigate><x-ui.button variant="ghost" type="button">Cancel</x-ui.button></a>
        </div>
    </form>
</div>
