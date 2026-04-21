<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div><h1 class="text-2xl font-semibold">Cruises</h1></div>
        <a href="{{ route('admin.cruises.create') }}" wire:navigate><x-ui.button variant="primary">New cruise</x-ui.button></a>
    </div>
    @if (session('status'))<x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>@endif

    <x-ui.card :padded="false">
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-800">
            <x-ui.input wire:model.live.debounce.500ms="search" placeholder="Search…" class="w-64" />
        </div>
        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th class="px-4 py-3 text-left">Title</th>
                    <th class="px-4 py-3 text-left">Line</th>
                    <th class="px-4 py-3 text-left">Route</th>
                    <th class="px-4 py-3 text-left">Nights</th>
                    <th class="px-4 py-3 text-left">From</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </x-slot:head>
            @forelse ($cruises as $c)
                <tr wire:key="cruise-{{ $c->id }}">
                    <td class="px-4 py-3 font-medium">{{ $c->title }}</td>
                    <td class="px-4 py-3 text-sm">{{ $c->cruise_line }}</td>
                    <td class="px-4 py-3 text-sm">{{ $c->departure_port }} → {{ $c->arrival_port }}</td>
                    <td class="px-4 py-3">{{ $c->duration_nights }}</td>
                    <td class="px-4 py-3">{{ $c->currency }} {{ number_format((float) $c->price_from, 2) }}</td>
                    <td class="px-4 py-3">
                        @if ($c->is_published)<x-ui.badge variant="success" size="sm">Published</x-ui.badge>
                        @else<x-ui.badge variant="neutral" size="sm">Draft</x-ui.badge>@endif
                    </td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <button wire:click="togglePublish('{{ $c->id }}')" class="text-xs text-hk-primary-600 hover:underline">{{ $c->is_published ? 'Unpublish' : 'Publish' }}</button>
                        <a href="{{ route('admin.cruises.edit', $c->id) }}" wire:navigate class="text-xs hover:underline">Edit</a>
                        <button wire:click="delete('{{ $c->id }}')" wire:confirm="{{ __('admin.confirm.delete') }}" class="text-xs text-hk-danger hover:underline">{{ __('admin.actions.delete') }}</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-zinc-500">No cruises yet.</td></tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
    <div>{{ $cruises->links() }}</div>
</div>
