<div class="space-y-6 max-w-3xl">
    <h1 class="text-2xl font-semibold">{{ $destination ? 'Edit destination' : 'New destination' }}</h1>

    <form wire:submit="save" class="space-y-6">
        <x-ui.card>
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-ui.input wire:model.blur="name" label="Name" required :error="$errors->first('name')" />
                    <x-ui.input wire:model="slug" label="Slug" required :error="$errors->first('slug')" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium">Type</label>
                        <select wire:model="type" class="block w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                            <option value="country">Country</option>
                            <option value="region">Region</option>
                            <option value="city">City</option>
                            <option value="area">Area</option>
                            <option value="poi">Point of interest</option>
                        </select>
                    </div>

                    <x-ui.input wire:model="country_code" label="Country code (ISO-2)" :error="$errors->first('country_code')" />

                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium">Parent</label>
                        <select wire:model="parent_id" class="block w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                            <option value="">— top level —</option>
                            @foreach ($parents as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->type }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <x-ui.textarea wire:model="description" label="Description" rows="5" :error="$errors->first('description')" />
                <x-ui.textarea wire:model="highlights" label="Highlights" rows="3" :error="$errors->first('highlights')" />

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-ui.input wire:model="cover_image" label="Cover image URL" :error="$errors->first('cover_image')" />
                    <x-ui.input type="number" step="0.0000001" wire:model="lat" label="Latitude" :error="$errors->first('lat')" />
                    <x-ui.input type="number" step="0.0000001" wire:model="lng" label="Longitude" :error="$errors->first('lng')" />
                </div>

                <div class="flex items-center gap-6">
                    <x-ui.checkbox wire:model="is_featured" label="Featured" />
                    <x-ui.checkbox wire:model="is_published" label="Published" />
                </div>
            </div>
        </x-ui.card>

        <div class="flex items-center gap-3">
            <x-ui.button type="submit" variant="primary">Save</x-ui.button>
            <a href="{{ route('admin.destinations.index') }}" wire:navigate>
                <x-ui.button variant="ghost" type="button">Cancel</x-ui.button>
            </a>
        </div>
    </form>
</div>
