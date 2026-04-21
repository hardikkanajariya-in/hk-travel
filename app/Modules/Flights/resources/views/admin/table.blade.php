<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">Flight offers</h1>
            <p class="text-sm text-zinc-500 mt-1">Featured and curated flight deals.</p>
        </div>
        <a href="{{ route('admin.flights.create') }}" wire:navigate><x-ui.button variant="primary">New offer</x-ui.button></a>
    </div>
    @if (session('status'))<x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>@endif

    <x-ui.card :padded="false">
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-800">
            <x-ui.input wire:model.live.debounce.500ms="search" placeholder="Search…" class="w-64" />
        </div>
        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th class="px-4 py-3 text-left">Airline</th>
                    <th class="px-4 py-3 text-left">Flight</th>
                    <th class="px-4 py-3 text-left">Route</th>
                    <th class="px-4 py-3 text-left">Cabin</th>
                    <th class="px-4 py-3 text-left">Price</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </x-slot:head>
            @forelse ($offers as $o)
                <tr wire:key="flight-{{ $o->id }}">
                    <td class="px-4 py-3 font-medium">{{ $o->airline }}</td>
                    <td class="px-4 py-3 text-sm">{{ $o->flight_number }}</td>
                    <td class="px-4 py-3 text-sm">{{ $o->origin }} → {{ $o->destination }}</td>
                    <td class="px-4 py-3 text-sm">{{ str_replace('_', ' ', $o->cabin) }}</td>
                    <td class="px-4 py-3">{{ $o->currency }} {{ number_format((float) $o->price, 2) }}</td>
                    <td class="px-4 py-3">
                        @if ($o->is_published)<x-ui.badge variant="success" size="sm">Published</x-ui.badge>
                        @else<x-ui.badge variant="neutral" size="sm">Draft</x-ui.badge>@endif
                    </td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <button wire:click="togglePublish('{{ $o->id }}')" class="text-xs text-hk-primary-600 hover:underline">{{ $o->is_published ? 'Unpublish' : 'Publish' }}</button>
                        <a href="{{ route('admin.flights.edit', $o->id) }}" wire:navigate class="text-xs hover:underline">Edit</a>
                        <button wire:click="delete('{{ $o->id }}')" wire:confirm="Delete?" class="text-xs text-hk-danger hover:underline">Delete</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-zinc-500">No offers yet.</td></tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
    <div>{{ $offers->links() }}</div>
</div>
