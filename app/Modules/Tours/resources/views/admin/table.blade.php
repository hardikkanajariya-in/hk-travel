<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">Tours</h1>
            <p class="text-sm text-zinc-500 mt-1">Curated multi-day itineraries.</p>
        </div>
        <a href="{{ route('admin.tours.create') }}" wire:navigate>
            <x-ui.button variant="primary">New tour</x-ui.button>
        </a>
    </div>

    @if (session('status'))
        <x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.card :padded="false">
        <div class="flex flex-wrap gap-3 p-4 border-b border-zinc-200 dark:border-zinc-800">
            <x-ui.input wire:model.live.debounce.500ms="search" placeholder="Search…" class="w-64" />
            <select wire:model.live="status" class="rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                <option value="">All statuses</option>
                <option value="published">Published</option>
                <option value="draft">Draft</option>
            </select>
            <select wire:model.live="difficulty" class="rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                <option value="">Any difficulty</option>
                <option value="easy">Easy</option>
                <option value="moderate">Moderate</option>
                <option value="challenging">Challenging</option>
                <option value="extreme">Extreme</option>
            </select>
        </div>

        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Destination</th>
                    <th class="px-4 py-3 text-left">Price</th>
                    <th class="px-4 py-3 text-left">Duration</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </x-slot:head>

            @forelse ($tours as $tour)
                <tr wire:key="tour-{{ $tour->id }}">
                    <td class="px-4 py-3 font-medium">
                        {{ $tour->name }}
                        @if ($tour->is_featured)
                            <x-ui.badge variant="warning" size="sm" class="ml-2">Featured</x-ui.badge>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-zinc-500">{{ $tour->destination?->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        {{ $tour->currency }} {{ number_format($tour->effectivePrice(), 2) }}
                        @if ($tour->discount_price)
                            <span class="ml-1 text-xs text-zinc-400 line-through">{{ number_format((float) $tour->price, 2) }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">{{ $tour->duration_days }}d</td>
                    <td class="px-4 py-3">
                        @if ($tour->is_published)
                            <x-ui.badge variant="success" size="sm">Published</x-ui.badge>
                        @else
                            <x-ui.badge variant="neutral" size="sm">Draft</x-ui.badge>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <button wire:click="togglePublish('{{ $tour->id }}')" class="text-xs text-hk-primary-600 hover:underline">
                            {{ $tour->is_published ? 'Unpublish' : 'Publish' }}
                        </button>
                        <a href="{{ route('admin.tours.edit', $tour->id) }}" wire:navigate class="text-xs hover:underline">Edit</a>
                        <button wire:click="delete('{{ $tour->id }}')" wire:confirm="{{ __('admin.confirm.delete') }}" class="text-xs text-hk-danger hover:underline">{{ __('admin.actions.delete') }}</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-zinc-500">No tours yet.</td></tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>

    <div>{{ $tours->links() }}</div>
</div>
