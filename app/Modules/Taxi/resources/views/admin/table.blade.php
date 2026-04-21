<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">Taxi & transfers</h1>
        </div>
        <a href="{{ route('admin.taxi.create') }}" wire:navigate>
            <x-ui.button variant="primary">New service</x-ui.button>
        </a>
    </div>
    @if (session('status'))<x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>@endif

    <x-ui.card :padded="false">
        <div class="flex flex-wrap gap-3 p-4 border-b border-zinc-200 dark:border-zinc-800">
            <x-ui.input wire:model.live.debounce.500ms="search" placeholder="Search…" class="w-64" />
            <select wire:model.live="type" class="rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                <option value="">All types</option>
                <option value="airport_transfer">Airport transfer</option>
                <option value="hourly">Hourly hire</option>
                <option value="point_to_point">Point to point</option>
            </select>
        </div>
        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th class="px-4 py-3 text-left">Title</th>
                    <th class="px-4 py-3 text-left">Type</th>
                    <th class="px-4 py-3 text-left">Vehicle</th>
                    <th class="px-4 py-3 text-left">From</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </x-slot:head>
            @forelse ($services as $s)
                <tr wire:key="taxi-{{ $s->id }}">
                    <td class="px-4 py-3 font-medium">{{ $s->title }}</td>
                    <td class="px-4 py-3 text-sm">{{ str_replace('_', ' ', $s->service_type) }}</td>
                    <td class="px-4 py-3">{{ $s->vehicle_type }}</td>
                    <td class="px-4 py-3">{{ $s->currency }} {{ number_format((float) max($s->flat_rate, $s->base_fare), 2) }}</td>
                    <td class="px-4 py-3">
                        @if ($s->is_published)<x-ui.badge variant="success" size="sm">Published</x-ui.badge>
                        @else<x-ui.badge variant="neutral" size="sm">Draft</x-ui.badge>@endif
                    </td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <button wire:click="togglePublish('{{ $s->id }}')" class="text-xs text-hk-primary-600 hover:underline">{{ $s->is_published ? 'Unpublish' : 'Publish' }}</button>
                        <a href="{{ route('admin.taxi.edit', $s->id) }}" wire:navigate class="text-xs hover:underline">Edit</a>
                        <button wire:click="delete('{{ $s->id }}')" wire:confirm="{{ __('admin.confirm.delete') }}" class="text-xs text-hk-danger hover:underline">{{ __('admin.actions.delete') }}</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-zinc-500">No services yet.</td></tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
    <div>{{ $services->links() }}</div>
</div>
