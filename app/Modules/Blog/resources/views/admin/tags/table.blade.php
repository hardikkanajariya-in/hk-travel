<div class="space-y-4">
    <div class="flex items-center justify-between gap-3">
        <h1 class="text-2xl font-semibold">{{ __('Tags') }}</h1>
        <x-ui.button :href="route('admin.blog.tags.create')" wire:navigate>{{ __('New tag') }}</x-ui.button>
    </div>

    <x-ui.card class="p-4">
        <x-ui.input wire:model.live.debounce.400ms="search" placeholder="{{ __('Search...') }}" class="max-w-sm" />
    </x-ui.card>

    @if (session('status'))<x-ui.alert>{{ session('status') }}</x-ui.alert>@endif

    <x-ui.card>
        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th class="px-3 py-2 text-left">{{ __('Name') }}</th>
                    <th class="px-3 py-2 text-left">{{ __('Slug') }}</th>
                    <th class="px-3 py-2 text-left">{{ __('Posts') }}</th>
                    <th class="px-3 py-2 text-right">{{ __('Actions') }}</th>
                </tr>
            </x-slot:head>
            @forelse ($tags as $tag)
                <tr class="border-t border-gray-100 dark:border-gray-800">
                    <td class="px-3 py-2">{{ $tag->name }}</td>
                    <td class="px-3 py-2 text-xs text-gray-500">{{ $tag->slug }}</td>
                    <td class="px-3 py-2">{{ $tag->posts_count }}</td>
                    <td class="px-3 py-2 text-right space-x-2">
                        <a href="{{ route('admin.blog.tags.edit', $tag->id) }}" wire:navigate class="text-blue-600 hover:underline">{{ __('Edit') }}</a>
                        <button wire:click="delete('{{ $tag->id }}')" wire:confirm="{{ __('admin.confirm.delete') }}" class="text-red-600 hover:underline">{{ __('admin.actions.delete') }}</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-3 py-6 text-center text-gray-500">{{ __('No tags.') }}</td></tr>
            @endforelse
        </x-ui.table>
        <div class="p-3">{{ $tags->links() }}</div>
    </x-ui.card>
</div>
