<div class="space-y-6 max-w-4xl">
    <h1 class="text-2xl font-semibold">{{ $hotel ? 'Edit hotel' : 'New hotel' }}</h1>

    <form wire:submit="save" class="space-y-6">
        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">Property</h2>
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-ui.input wire:model.blur="name" label="Name" required :error="$errors->first('name')" />
                    <x-ui.input wire:model="slug" label="Slug" required :error="$errors->first('slug')" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium">Destination</label>
                        <select wire:model="destination_id" class="block w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                            <option value="">— none —</option>
                            @foreach ($destinations as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach
                        </select>
                    </div>
                    <x-ui.input type="number" min="1" max="5" wire:model="star_rating" label="Star rating" required :error="$errors->first('star_rating')" />
                    <x-ui.input wire:model="address" label="Address" :error="$errors->first('address')" />
                </div>

                <x-ui.textarea wire:model="description" label="Description" rows="5" :error="$errors->first('description')" />
                <x-ui.input wire:model="cover_image" label="Cover image URL" :error="$errors->first('cover_image')" />

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <x-ui.input wire:model="check_in" label="Check-in" required :error="$errors->first('check_in')" />
                    <x-ui.input wire:model="check_out" label="Check-out" required :error="$errors->first('check_out')" />
                    <x-ui.input type="number" step="0.0000001" wire:model="lat" label="Latitude" :error="$errors->first('lat')" />
                    <x-ui.input type="number" step="0.0000001" wire:model="lng" label="Longitude" :error="$errors->first('lng')" />
                </div>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">Pricing</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input type="number" step="0.01" wire:model="price_from" label="Starting price / night" required :error="$errors->first('price_from')" />
                <x-ui.input wire:model="currency" label="Currency" required :error="$errors->first('currency')" />
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">Amenities</h2>
            <div class="flex gap-2">
                <x-ui.input wire:model="newAmenity" placeholder="Add amenity…" class="flex-1" />
                <x-ui.button type="button" variant="secondary" wire:click="addAmenity">Add</x-ui.button>
            </div>
            <ul class="mt-3 flex flex-wrap gap-2">
                @foreach ($amenities as $i => $item)
                    <li class="flex items-center gap-2 bg-zinc-100 dark:bg-zinc-800 rounded-full px-3 py-1 text-sm" wire:key="am-{{ $i }}">
                        {{ $item }} <button type="button" wire:click="removeAmenity({{ $i }})" class="text-hk-danger">×</button>
                    </li>
                @endforeach
            </ul>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">Visibility</h2>
            <div class="flex items-center gap-6">
                <x-ui.checkbox wire:model="is_published" label="Published" />
                <x-ui.checkbox wire:model="is_featured" label="Featured" />
            </div>
        </x-ui.card>

        <div class="flex items-center gap-3">
            <x-ui.button type="submit" variant="primary">Save</x-ui.button>
            <a href="{{ route('admin.hotels.index') }}" wire:navigate><x-ui.button variant="ghost" type="button">Cancel</x-ui.button></a>
        </div>
    </form>
</div>
