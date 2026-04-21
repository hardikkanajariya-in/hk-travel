<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">Destinations</h1>
            <p class="text-sm text-zinc-500 mt-1">Geographic taxonomy used across travel modules.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.destinations.create') }}" wire:navigate>
                <x-ui.button variant="primary">New destination</x-ui.button>
            </a>
        </div>
    </div>

    @if (session('status'))
        <x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.card :padded="false">
        <div class="flex flex-wrap gap-3 p-4 border-b border-zinc-200 dark:border-zinc-800">
            <x-ui.input wire:model.live.debounce.500ms="search" placeholder="Search…" class="w-64" />
            <select wire:model.live="type" class="rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                <option value="">All types</option>
                <option value="country">Country</option>
                <option value="region">Region</option>
                <option value="city">City</option>
                <option value="area">Area</option>
                <option value="poi">Point of interest</option>
            </select>
            <select wire:model.live="status" class="rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                <option value="">All statuses</option>
                <option value="published">Published</option>
                <option value="draft">Draft</option>
            </select>
        </div>

        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Type</th>
                    <th class="px-4 py-3 text-left">Country</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </x-slot:head>

            @forelse ($destinations as $row)
                <tr wire:key="dest-{{ $row->id }}">
                    <td class="px-4 py-3 font-medium">
                        {{ $row->name }}
                        @if ($row->is_featured)
                            <x-ui.badge variant="warning" size="sm" class="ml-2">Featured</x-ui.badge>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-zinc-500 capitalize">{{ $row->type }}</td>
                    <td class="px-4 py-3 text-sm text-zinc-500">{{ $row->country_code }}</td>
                    <td class="px-4 py-3">
                        @if ($row->is_published)
                            <x-ui.badge variant="success" size="sm">Published</x-ui.badge>
                        @else
                            <x-ui.badge variant="neutral" size="sm">Draft</x-ui.badge>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <button wire:click="togglePublish('{{ $row->id }}')" class="text-xs text-hk-primary-600 hover:underline">
                            {{ $row->is_published ? 'Unpublish' : 'Publish' }}
                        </button>
                        <a href="{{ route('admin.destinations.edit', $row->id) }}" wire:navigate class="text-xs text-zinc-700 hover:underline">Edit</a>
                        <button wire:click="delete('{{ $row->id }}')" wire:confirm="{{ __('admin.confirm.delete') }}" class="text-xs text-hk-danger hover:underline">{{ __('admin.actions.delete') }}</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-zinc-500">No destinations yet.</td></tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>

    <div>{{ $destinations->links() }}</div>
</div>
