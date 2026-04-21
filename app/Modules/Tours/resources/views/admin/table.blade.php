<div class="space-y-6">
    <div class="flex items-end justify-between">
        <h1 class="text-2xl font-semibold">Tours</h1>
        <x-ui.input wire:model.live.debounce.500ms="search" placeholder="Search…" class="w-64" />
    </div>

    <x-ui.card :padded="false">
        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Price</th>
                    <th class="px-4 py-3">Duration</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </x-slot:head>

            @forelse ($tours as $tour)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $tour->name }}</td>
                    <td class="px-4 py-3">{{ number_format($tour->price, 2) }}</td>
                    <td class="px-4 py-3">{{ $tour->duration_days }}d</td>
                    <td class="px-4 py-3">
                        @if ($tour->is_published)
                            <x-ui.badge variant="success" size="sm">Published</x-ui.badge>
                        @else
                            <x-ui.badge variant="neutral" size="sm">Draft</x-ui.badge>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-zinc-500">No tours yet.</td></tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>

    <div>{{ $tours->links() }}</div>
</div>
