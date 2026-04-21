<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">Car rentals</h1>
            <p class="text-sm text-zinc-500 mt-1">Vehicle inventory.</p>
        </div>
        <a href="{{ route('admin.cars.create') }}" wire:navigate>
            <x-ui.button variant="primary">New vehicle</x-ui.button>
        </a>
    </div>
    @if (session('status'))<x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>@endif

    <x-ui.card :padded="false">
        <div class="flex flex-wrap gap-3 p-4 border-b border-zinc-200 dark:border-zinc-800">
            <x-ui.input wire:model.live.debounce.500ms="search" placeholder="Search…" class="w-64" />
            <select wire:model.live="class" class="rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                <option value="">All classes</option>
                @foreach (['economy','compact','sedan','suv','luxury','van'] as $c)<option value="{{ $c }}">{{ ucfirst($c) }}</option>@endforeach
            </select>
        </div>
        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Class</th>
                    <th class="px-4 py-3 text-left">Seats</th>
                    <th class="px-4 py-3 text-left">Daily</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </x-slot:head>
            @forelse ($cars as $c)
                <tr wire:key="car-{{ $c->id }}">
                    <td class="px-4 py-3 font-medium">{{ $c->name }}</td>
                    <td class="px-4 py-3 text-sm">{{ ucfirst($c->vehicle_class) }}</td>
                    <td class="px-4 py-3">{{ $c->seats }}</td>
                    <td class="px-4 py-3">{{ $c->currency }} {{ number_format((float) $c->daily_rate, 2) }}</td>
                    <td class="px-4 py-3">
                        @if ($c->is_published)<x-ui.badge variant="success" size="sm">Published</x-ui.badge>
                        @else<x-ui.badge variant="neutral" size="sm">Draft</x-ui.badge>@endif
                    </td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <button wire:click="togglePublish('{{ $c->id }}')" class="text-xs text-hk-primary-600 hover:underline">{{ $c->is_published ? 'Unpublish' : 'Publish' }}</button>
                        <a href="{{ route('admin.cars.edit', $c->id) }}" wire:navigate class="text-xs hover:underline">Edit</a>
                        <button wire:click="delete('{{ $c->id }}')" wire:confirm="Delete?" class="text-xs text-hk-danger hover:underline">Delete</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-zinc-500">No vehicles yet.</td></tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
    <div>{{ $cars->links() }}</div>
</div>
