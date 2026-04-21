<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">Visa services</h1>
            <p class="text-sm text-zinc-500 mt-1">Visa types, fees, and processing.</p>
        </div>
        <a href="{{ route('admin.visa.create') }}" wire:navigate>
            <x-ui.button variant="primary">New service</x-ui.button>
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
                    <th class="px-4 py-3 text-left">Country</th>
                    <th class="px-4 py-3 text-left">Type</th>
                    <th class="px-4 py-3 text-left">Processing</th>
                    <th class="px-4 py-3 text-left">Fee</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </x-slot:head>
            @forelse ($services as $s)
                <tr wire:key="visa-{{ $s->id }}">
                    <td class="px-4 py-3 font-medium">{{ $s->country }} <span class="text-xs text-zinc-500">{{ $s->country_code }}</span></td>
                    <td class="px-4 py-3">{{ $s->visa_type }}</td>
                    <td class="px-4 py-3 text-sm text-zinc-500">{{ $s->processing_days_min }}–{{ $s->processing_days_max }} days</td>
                    <td class="px-4 py-3">{{ $s->currency }} {{ number_format((float) $s->fee + (float) $s->service_fee, 2) }}</td>
                    <td class="px-4 py-3">
                        @if ($s->is_published)<x-ui.badge variant="success" size="sm">Published</x-ui.badge>
                        @else<x-ui.badge variant="neutral" size="sm">Draft</x-ui.badge>@endif
                    </td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <button wire:click="togglePublish('{{ $s->id }}')" class="text-xs text-hk-primary-600 hover:underline">{{ $s->is_published ? 'Unpublish' : 'Publish' }}</button>
                        <a href="{{ route('admin.visa.edit', $s->id) }}" wire:navigate class="text-xs hover:underline">Edit</a>
                        <button wire:click="delete('{{ $s->id }}')" wire:confirm="{{ __('admin.confirm.delete') }}" class="text-xs text-hk-danger hover:underline">{{ __('admin.actions.delete') }}</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-zinc-500">No visa services yet.</td></tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
    <div>{{ $services->links() }}</div>
</div>
