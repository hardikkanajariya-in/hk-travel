<div class="space-y-6 max-w-4xl">
    <h1 class="text-2xl font-semibold">{{ $tour ? 'Edit tour' : 'New tour' }}</h1>

    <form wire:submit="save" class="space-y-6">
        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">Overview</h2>
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-ui.input wire:model.blur="name" label="Name" required :error="$errors->first('name')" />
                    <x-ui.input wire:model="slug" label="Slug" required :error="$errors->first('slug')" />
                </div>

                <div class="space-y-1.5">
                    <label class="block text-sm font-medium">Destination</label>
                    <select wire:model="destination_id" class="block w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                        <option value="">— none —</option>
                        @foreach ($destinations as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>

                <x-ui.textarea wire:model="description" label="Description" rows="6" :error="$errors->first('description')" />
                <x-ui.input wire:model="cover_image" label="Cover image URL" :error="$errors->first('cover_image')" />
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">Pricing & schedule</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-ui.input type="number" step="0.01" wire:model="price" label="Price" required :error="$errors->first('price')" />
                <x-ui.input type="number" step="0.01" wire:model="discount_price" label="Discount price" :error="$errors->first('discount_price')" />
                <x-ui.input wire:model="currency" label="Currency" required :error="$errors->first('currency')" />
                <x-ui.input type="number" wire:model="duration_days" label="Duration (days)" required :error="$errors->first('duration_days')" />
                <x-ui.input type="number" wire:model="max_group_size" label="Max group size" required :error="$errors->first('max_group_size')" />
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium">Difficulty</label>
                    <select wire:model="difficulty" class="block w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                        <option value="easy">Easy</option>
                        <option value="moderate">Moderate</option>
                        <option value="challenging">Challenging</option>
                        <option value="extreme">Extreme</option>
                    </select>
                </div>
                <x-ui.input wire:model="language" label="Language" required :error="$errors->first('language')" />
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">What's included</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-2">Inclusions</label>
                    <div class="flex gap-2">
                        <x-ui.input wire:model="newInclusion" placeholder="Add an item…" class="flex-1" />
                        <x-ui.button type="button" variant="secondary" wire:click="addInclusion">Add</x-ui.button>
                    </div>
                    <ul class="mt-3 space-y-1">
                        @foreach ($inclusions as $i => $item)
                            <li class="flex items-center justify-between text-sm bg-zinc-50 dark:bg-zinc-800 rounded px-3 py-1.5" wire:key="inc-{{ $i }}">
                                <span>{{ $item }}</span>
                                <button type="button" wire:click="removeInclusion({{ $i }})" class="text-xs text-hk-danger">×</button>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Exclusions</label>
                    <div class="flex gap-2">
                        <x-ui.input wire:model="newExclusion" placeholder="Add an item…" class="flex-1" />
                        <x-ui.button type="button" variant="secondary" wire:click="addExclusion">Add</x-ui.button>
                    </div>
                    <ul class="mt-3 space-y-1">
                        @foreach ($exclusions as $i => $item)
                            <li class="flex items-center justify-between text-sm bg-zinc-50 dark:bg-zinc-800 rounded px-3 py-1.5" wire:key="exc-{{ $i }}">
                                <span>{{ $item }}</span>
                                <button type="button" wire:click="removeExclusion({{ $i }})" class="text-xs text-hk-danger">×</button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
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
            <a href="{{ route('admin.tours.index') }}" wire:navigate>
                <x-ui.button variant="ghost" type="button">Cancel</x-ui.button>
            </a>
        </div>
    </form>
</div>
