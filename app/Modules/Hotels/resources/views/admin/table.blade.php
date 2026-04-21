<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">Hotels</h1>
            <p class="text-sm text-zinc-500 mt-1">Properties and room inventory.</p>
        </div>
        <a href="{{ route('admin.hotels.create') }}" wire:navigate>
            <x-ui.button variant="primary">New hotel</x-ui.button>
        </a>
    </div>

    @if (session('status'))<x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>@endif

    <x-ui.card :padded="false">
        <div class="flex flex-wrap gap-3 p-4 border-b border-zinc-200 dark:border-zinc-800">
            <x-ui.input wire:model.live.debounce.500ms="search" placeholder="Search…" class="w-64" />
            <select wire:model.live="status" class="rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                <option value="">All statuses</option><option value="published">Published</option><option value="draft">Draft</option>
            </select>
            <select wire:model.live="stars" class="rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                <option value="">Any stars</option>
                @foreach ([5,4,3,2,1] as $s)<option value="{{ $s }}">{{ $s }} star</option>@endforeach
            </select>
        </div>
        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Stars</th>
                    <th class="px-4 py-3 text-left">Destination</th>
                    <th class="px-4 py-3 text-left">From</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </x-slot:head>
            @forelse ($hotels as $h)
                <tr wire:key="hotel-{{ $h->id }}">
                    <td class="px-4 py-3 font-medium">{{ $h->name }}</td>
                    <td class="px-4 py-3 text-amber-500">{{ str_repeat('★', $h->star_rating) }}</td>
                    <td class="px-4 py-3 text-sm text-zinc-500">{{ $h->destination?->name ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $h->currency }} {{ number_format((float) $h->price_from, 2) }}</td>
                    <td class="px-4 py-3">
                        @if ($h->is_published)<x-ui.badge variant="success" size="sm">Published</x-ui.badge>
                        @else<x-ui.badge variant="neutral" size="sm">Draft</x-ui.badge>@endif
                    </td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <button wire:click="togglePublish('{{ $h->id }}')" class="text-xs text-hk-primary-600 hover:underline">
                            {{ $h->is_published ? 'Unpublish' : 'Publish' }}
                        </button>
                        <a href="{{ route('admin.hotels.edit', $h->id) }}" wire:navigate class="text-xs hover:underline">Edit</a>
                        <button wire:click="delete('{{ $h->id }}')" wire:confirm="{{ __('admin.confirm.delete') }}" class="text-xs text-hk-danger hover:underline">{{ __('admin.actions.delete') }}</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-zinc-500">No hotels yet.</td></tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>

    <div>{{ $hotels->links() }}</div>
</div>
