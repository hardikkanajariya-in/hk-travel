<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

new #[Title('Audit log')] #[Layout('components.layouts.admin')] class extends Component {
    use WithPagination;

    #[Url(as: 'log')]
    public string $logName = '';

    #[Url(as: 'event')]
    public string $event = '';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $from = '';

    #[Url]
    public string $to = '';

    public ?string $flash = null;

    public function updating(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['logName', 'event', 'search', 'from', 'to']);
        $this->resetPage();
    }

    public function purge(): void
    {
        $this->authorize('admin.audit.purge');

        Activity::query()->delete();
        $this->flash = 'All audit log entries deleted.';
        $this->resetPage();
    }

    public function with(): array
    {
        $query = Activity::query()
            ->latest()
            ->when($this->logName !== '', fn ($q) => $q->where('log_name', $this->logName))
            ->when($this->event !== '', fn ($q) => $q->where('event', $this->event))
            ->when($this->from !== '', fn ($q) => $q->where('created_at', '>=', $this->from))
            ->when($this->to !== '', fn ($q) => $q->where('created_at', '<=', $this->to.' 23:59:59'))
            ->when($this->search !== '', function ($q) {
                $term = '%'.$this->search.'%';
                $q->where(fn ($w) => $w->where('description', 'like', $term)
                    ->orWhere('subject_type', 'like', $term)
                    ->orWhere('causer_type', 'like', $term));
            });

        return [
            'activities' => $query->paginate(25),
            'logNames' => Activity::query()->select('log_name')->distinct()->pluck('log_name')->filter()->values(),
            'events' => Activity::query()->select('event')->distinct()->pluck('event')->filter()->values(),
        ];
    }
};

?>

<div class="space-y-6">
    <x-admin.page-header title="Audit log" subtitle="Authentication events, model changes, and admin actions." />

    <x-admin.flash :message="$flash" />

    <x-ui.card>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <x-ui.input label="Search" wire:model.live.debounce.400ms="search" placeholder="description, type…" />
            <x-ui.select label="Log" wire:model.live="logName">
                <option value="">All</option>
                @foreach ($logNames as $name)
                    <option value="{{ $name }}">{{ $name }}</option>
                @endforeach
            </x-ui.select>
            <x-ui.select label="Event" wire:model.live="event">
                <option value="">All</option>
                @foreach ($events as $name)
                    <option value="{{ $name }}">{{ $name }}</option>
                @endforeach
            </x-ui.select>
            <x-ui.input type="date" label="From" wire:model.live="from" />
            <x-ui.input type="date" label="To" wire:model.live="to" />
        </div>
        <div class="mt-3 flex items-center justify-between gap-2">
            <button type="button" wire:click="clearFilters" class="text-sm text-zinc-600 hover:underline">Clear filters</button>
            @can('admin.audit.purge')
                <x-ui.button variant="danger" size="sm" wire:click="purge" wire:confirm="{{ __('admin.confirm.purge_audit') }}">Purge all</x-ui.button>
            @endcan
        </div>
    </x-ui.card>

    <x-ui.card padding="none">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-900/40 text-left text-xs uppercase tracking-wide text-zinc-500">
                    <tr>
                        <th class="px-4 py-2">When</th>
                        <th class="px-4 py-2">Log</th>
                        <th class="px-4 py-2">Event</th>
                        <th class="px-4 py-2">Description</th>
                        <th class="px-4 py-2">Causer</th>
                        <th class="px-4 py-2">Subject</th>
                        <th class="px-4 py-2">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-900">
                    @forelse ($activities as $activity)
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap text-zinc-600 dark:text-zinc-400">{{ $activity->created_at?->format('Y-m-d H:i:s') }}</td>
                            <td class="px-4 py-2"><x-ui.badge variant="neutral" size="sm">{{ $activity->log_name ?? '—' }}</x-ui.badge></td>
                            <td class="px-4 py-2 font-mono text-xs">{{ $activity->event ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $activity->description }}</td>
                            <td class="px-4 py-2 text-xs text-zinc-500">
                                @if ($activity->causer_id)
                                    {{ class_basename((string) $activity->causer_type) }}#{{ $activity->causer_id }}
                                @else
                                    system
                                @endif
                            </td>
                            <td class="px-4 py-2 text-xs text-zinc-500">
                                @if ($activity->subject_id)
                                    {{ class_basename((string) $activity->subject_type) }}#{{ $activity->subject_id }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-2 text-xs font-mono text-zinc-500">{{ $activity->properties['ip'] ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-sm text-zinc-500">No activity recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-zinc-200 dark:border-zinc-800 px-4 py-3">
            {{ $activities->links() }}
        </div>
    </x-ui.card>
</div>
