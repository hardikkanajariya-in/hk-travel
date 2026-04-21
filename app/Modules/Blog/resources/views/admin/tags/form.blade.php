<form wire:submit="save" class="mx-auto max-w-xl space-y-5">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">{{ $tag ? __('Edit tag') : __('New tag') }}</h1>
        <div class="space-x-2">
            <x-ui.button :href="route('admin.blog.tags.index')" wire:navigate variant="secondary">{{ __('Cancel') }}</x-ui.button>
            <x-ui.button type="submit">{{ __('Save') }}</x-ui.button>
        </div>
    </div>

    <x-ui.card class="space-y-4 p-5">
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
    </x-ui.card>
</form>
