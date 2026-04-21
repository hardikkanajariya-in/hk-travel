<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">Activities</h1>
            <p class="text-sm text-zinc-500 mt-1">Curated experiences and day trips.</p>
        </div>
        <a href="{{ route('admin.activities.create') }}" wire:navigate>
            <x-ui.button variant="primary">New activity</x-ui.button>
        </a>
    </div>
    @if (session('status'))<x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>@endif

    <x-ui.card :padded="false">
        <div class="flex flex-wrap gap-3 p-4 border-b border-zinc-200 dark:border-zinc-800">
            <x-ui.input wire:model.live.debounce.500ms="search" placeholder="Search…" class="w-64" />
            <select wire:model.live="status" class="rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                <option value="">All</option><option value="published">Published</option><option value="draft">Draft</option>
            </select>
        </div>
        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Category</th>
                    <th class="px-4 py-3 text-left">Duration</th>
                    <th class="px-4 py-3 text-left">Price</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </x-slot:head>
            @forelse ($activities as $a)
                <tr wire:key="ac-{{ $a->id }}">
                    <td class="px-4 py-3 font-medium">{{ $a->name }}</td>
                    <td class="px-4 py-3 text-sm text-zinc-500">{{ $a->category ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $a->duration_hours }} h</td>
                    <td class="px-4 py-3">{{ $a->currency }} {{ number_format((float) $a->price, 2) }}</td>
                    <td class="px-4 py-3">
                        @if ($a->is_published)<x-ui.badge variant="success" size="sm">Published</x-ui.badge>
                        @else<x-ui.badge variant="neutral" size="sm">Draft</x-ui.badge>@endif
                    </td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <button wire:click="togglePublish('{{ $a->id }}')" class="text-xs text-hk-primary-600 hover:underline">{{ $a->is_published ? 'Unpublish' : 'Publish' }}</button>
                        <a href="{{ route('admin.activities.edit', $a->id) }}" wire:navigate class="text-xs hover:underline">Edit</a>
                        <button wire:click="delete('{{ $a->id }}')" wire:confirm="{{ __('admin.confirm.delete') }}" class="text-xs text-hk-danger hover:underline">{{ __('admin.actions.delete') }}</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-zinc-500">No activities yet.</td></tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
    <div>{{ $activities->links() }}</div>
</div>
