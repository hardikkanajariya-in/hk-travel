<form wire:submit="save" class="mx-auto max-w-2xl space-y-5">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">{{ $category ? __('Edit category') : __('New category') }}</h1>
        <div class="space-x-2">
            <x-ui.button :href="route('admin.blog.categories.index')" wire:navigate variant="secondary">{{ __('Cancel') }}</x-ui.button>
            <x-ui.button type="submit">{{ __('Save') }}</x-ui.button>
        </div>
    </div>

    <x-ui.card class="space-y-4 p-5">
        <div>
            <label class="mb-1 block text-sm font-medium">{{ __('Parent') }}</label>
            <select wire:model="parent_id" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                <option value="">{{ __('— None —') }}</option>
                @foreach ($parents as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium">{{ __('Name') }}</label>
            <x-ui.input wire:model.live.debounce.500ms="name" required />
            @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium">{{ __('Slug') }}</label>
            <x-ui.input wire:model="slug" required />
            @error('slug')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium">{{ __('Description') }}</label>
            <x-ui.textarea wire:model="description" rows="3" />
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium">{{ __('Cover image URL') }}</label>
            <x-ui.input wire:model="cover_image" type="url" />
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium">{{ __('Position') }}</label>
            <x-ui.input wire:model="position" type="number" min="0" />
        </div>
    </x-ui.card>
</form>
