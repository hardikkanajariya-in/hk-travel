<div class="space-y-6 max-w-4xl">
    <h1 class="text-2xl font-semibold">{{ $activity ? 'Edit activity' : 'New activity' }}</h1>

    <form wire:submit="save" class="space-y-6">
        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">Details</h2>
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
                    <x-ui.input wire:model="category" label="Category" placeholder="adventure, culture…" />
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium">Difficulty</label>
                        <select wire:model="difficulty" class="block w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                            <option value="easy">Easy</option><option value="moderate">Moderate</option><option value="challenging">Challenging</option>
                        </select>
                    </div>
                </div>

                <x-ui.textarea wire:model="short_description" label="Short description" rows="2" />
                <x-ui.textarea wire:model="description" label="Description" rows="6" />
                <x-ui.input wire:model="cover_image" label="Cover image URL" />
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">Logistics & pricing</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-ui.input type="number" step="0.5" wire:model="duration_hours" label="Duration (hours)" required :error="$errors->first('duration_hours')" />
                <x-ui.input type="number" wire:model="min_age" label="Min age" required />
                <x-ui.input type="number" wire:model="max_group_size" label="Max group size" required />
                <x-ui.input type="number" step="0.01" wire:model="price" label="Price" required :error="$errors->first('price')" />
                <x-ui.input wire:model="currency" label="Currency" required />
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
            <a href="{{ route('admin.activities.index') }}" wire:navigate><x-ui.button variant="ghost" type="button">Cancel</x-ui.button></a>
        </div>
    </form>
</div>
