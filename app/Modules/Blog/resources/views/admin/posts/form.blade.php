<form wire:submit="save" class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">{{ $post ? __('Edit post') : __('New post') }}</h1>
        <div class="space-x-2">
            <x-ui.button :href="route('admin.blog.posts.index')" wire:navigate variant="secondary">{{ __('Cancel') }}</x-ui.button>
            <x-ui.button type="submit">{{ __('Save') }}</x-ui.button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-ui.card class="space-y-4 p-5 lg:col-span-2">
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Title') }}</label>
                <x-ui.input wire:model.live.debounce.500ms="title" required />
                @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Slug') }}</label>
                <x-ui.input wire:model="slug" required />
                @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Excerpt') }}</label>
                <x-ui.textarea wire:model="excerpt" rows="2" />
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Body (HTML)') }}</label>
                <x-ui.textarea wire:model="body" rows="18" />
                <p class="mt-1 text-xs text-gray-500">{{ __('Use <h2> headings for the auto-generated table of contents.') }}</p>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Cover image URL') }}</label>
                <x-ui.input wire:model="cover_image" type="url" />
            </div>
        </x-ui.card>

        <div class="space-y-6">
            <x-ui.card class="space-y-4 p-5">
                <h3 class="text-sm font-semibold uppercase text-gray-500">{{ __('Publishing') }}</h3>
                <div>
                    <label class="mb-1 block text-sm font-medium">{{ __('Status') }}</label>
                    <select wire:model="status" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                        <option value="draft">{{ __('Draft') }}</option>
                        <option value="scheduled">{{ __('Scheduled') }}</option>
                        <option value="published">{{ __('Published') }}</option>
                        <option value="archived">{{ __('Archived') }}</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">{{ __('Publish at') }}</label>
                    <input type="datetime-local" wire:model="published_at" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                </div>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="is_featured" /> {{ __('Featured') }}</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="allow_comments" /> {{ __('Allow comments') }}</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="show_toc" /> {{ __('Show table of contents') }}</label>
            </x-ui.card>

            <x-ui.card class="space-y-3 p-5">
                <h3 class="text-sm font-semibold uppercase text-gray-500">{{ __('Categories') }}</h3>
                <div class="max-h-56 space-y-1 overflow-y-auto">
                    @foreach ($categories as $cat)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" value="{{ $cat->id }}" wire:model="categoryIds" />
                            {{ $cat->name }}
                        </label>
                    @endforeach
                </div>
            </x-ui.card>

            <x-ui.card class="space-y-3 p-5">
                <h3 class="text-sm font-semibold uppercase text-gray-500">{{ __('Tags') }}</h3>
                <div class="flex flex-wrap gap-1">
                    @foreach ($tags as $tag)
                        <span class="inline-flex items-center gap-1 rounded bg-gray-100 px-2 py-0.5 text-xs dark:bg-gray-800">
                            {{ $tag->name }}
                            <button type="button" wire:click="removeTag('{{ $tag->id }}')" class="text-red-500">&times;</button>
                        </span>
                    @endforeach
                </div>
                <div class="flex gap-2">
                    <x-ui.input wire:model="newTag" wire:keydown.enter.prevent="addTag" placeholder="{{ __('Add tag...') }}" />
                    <x-ui.button type="button" wire:click="addTag" variant="secondary">{{ __('Add') }}</x-ui.button>
                </div>
            </x-ui.card>
        </div>
    </div>
</form>
