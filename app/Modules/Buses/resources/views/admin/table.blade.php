<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">Bus routes</h1>
            <p class="text-sm text-zinc-500 mt-1">Long-distance and city coach routes.</p>
        </div>
        <a href="{{ route('admin.buses.create') }}" wire:navigate>
            <x-ui.button variant="primary">New route</x-ui.button>
        </a>
    </div>
    @if (session('status'))<x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>@endif

    <x-ui.card :padded="false">
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-800">
            <x-ui.input wire:model.live.debounce.500ms="search" placeholder="Search…" class="w-64" />
        </div>
        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th class="px-4 py-3 text-left">Route</th>
                    <th class="px-4 py-3 text-left">Operator</th>
                    <th class="px-4 py-3 text-left">Type</th>
                    <th class="px-4 py-3 text-left">Dep</th>
                    <th class="px-4 py-3 text-left">Fare</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </x-slot:head>
            @forelse ($routes as $r)
                <tr wire:key="bus-{{ $r->id }}">
                    <td class="px-4 py-3 font-medium">{{ $r->origin }} → {{ $r->destination }}</td>
                    <td class="px-4 py-3 text-sm">{{ $r->operator }}</td>
                    <td class="px-4 py-3 text-sm">{{ ucfirst($r->bus_type) }}</td>
                    <td class="px-4 py-3 text-sm">{{ $r->departure_time }}</td>
                    <td class="px-4 py-3">{{ $r->currency }} {{ number_format((float) $r->fare, 2) }}</td>
                    <td class="px-4 py-3">
                        @if ($r->is_published)<x-ui.badge variant="success" size="sm">Published</x-ui.badge>
                        @else<x-ui.badge variant="neutral" size="sm">Draft</x-ui.badge>@endif
                    </td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <button wire:click="togglePublish('{{ $r->id }}')" class="text-xs text-hk-primary-600 hover:underline">{{ $r->is_published ? 'Unpublish' : 'Publish' }}</button>
                        <a href="{{ route('admin.buses.edit', $r->id) }}" wire:navigate class="text-xs hover:underline">Edit</a>
                        <button wire:click="delete('{{ $r->id }}')" wire:confirm="Delete?" class="text-xs text-hk-danger hover:underline">Delete</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-zinc-500">No routes yet.</td></tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
    <div>{{ $routes->links() }}</div>
</div>
