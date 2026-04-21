<div class="space-y-4">
    <div class="flex items-center justify-between gap-3">
        <h1 class="text-2xl font-semibold">{{ __('Blog posts') }}</h1>
        @can('blog.create')
            <x-ui.button :href="route('admin.blog.posts.create')" wire:navigate>{{ __('New post') }}</x-ui.button>
        @endcan
    </div>

    <x-ui.card class="p-4">
        <div class="flex flex-col gap-3 md:flex-row">
            <x-ui.input wire:model.live.debounce.400ms="search" placeholder="{{ __('Search posts...') }}" class="md:max-w-sm" />
            <select wire:model.live="status" class="rounded border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                <option value="">{{ __('All statuses') }}</option>
                <option value="draft">{{ __('Draft') }}</option>
                <option value="scheduled">{{ __('Scheduled') }}</option>
                <option value="published">{{ __('Published') }}</option>
                <option value="archived">{{ __('Archived') }}</option>
            </select>
        </div>
    </x-ui.card>

    @if (session('status'))
        <x-ui.alert>{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.card>
        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th class="px-3 py-2 text-left">{{ __('Title') }}</th>
                    <th class="px-3 py-2 text-left">{{ __('Status') }}</th>
                    <th class="px-3 py-2 text-left">{{ __('Author') }}</th>
                    <th class="px-3 py-2 text-left">{{ __('Published') }}</th>
                    <th class="px-3 py-2 text-right">{{ __('Actions') }}</th>
                </tr>
            </x-slot:head>
            @forelse ($posts as $post)
                <tr class="border-t border-gray-100 dark:border-gray-800">
                    <td class="px-3 py-2">
                        <div class="font-medium">{{ $post->title }}</div>
                        <div class="text-xs text-gray-500">{{ $post->slug }}</div>
                    </td>
                    <td class="px-3 py-2"><x-ui.badge>{{ $post->status }}</x-ui.badge></td>
                    <td class="px-3 py-2">{{ $post->author?->name ?? '—' }}</td>
                    <td class="px-3 py-2">{{ $post->published_at?->format('Y-m-d H:i') ?? '—' }}</td>
                    <td class="px-3 py-2 text-right space-x-2">
                        @can('blog.update')
                            <a href="{{ route('admin.blog.posts.edit', $post->id) }}" wire:navigate class="text-blue-600 hover:underline">{{ __('Edit') }}</a>
                        @endcan
                        @can('blog.publish')
                            @if ($post->status !== 'published')
                                <button wire:click="publish('{{ $post->id }}')" class="text-emerald-600 hover:underline">{{ __('Publish') }}</button>
                            @else
                                <button wire:click="unpublish('{{ $post->id }}')" class="text-yellow-600 hover:underline">{{ __('Unpublish') }}</button>
                            @endif
                        @endcan
                        @can('blog.delete')
                            <button wire:click="delete('{{ $post->id }}')" wire:confirm="{{ __('admin.confirm.delete_post') }}" class="text-red-600 hover:underline">{{ __('admin.actions.delete') }}</button>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-3 py-6 text-center text-gray-500">{{ __('No posts yet.') }}</td></tr>
            @endforelse
        </x-ui.table>
        <div class="p-3">{{ $posts->links() }}</div>
    </x-ui.card>
</div>
