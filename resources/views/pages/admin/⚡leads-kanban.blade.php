<?php

use App\Models\Lead;
use App\Models\Pipeline;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Title('CRM kanban')] #[Layout('components.layouts.admin')] class extends Component {
    #[Url(as: 'pipeline')]
    public ?int $pipelineId = null;

    #[Url(as: 'q')]
    public string $search = '';

    public function mount(): void
    {
        $this->pipelineId = $this->pipelineId ?: Pipeline::default()?->id;
    }

    /** @param array<int, int|string> $order */
    public function moveCard(string $stage, array $order): void
    {
        if (! $this->pipelineId) {
            return;
        }

        foreach ($order as $i => $leadId) {
            Lead::query()
                ->where('id', (int) $leadId)
                ->where('pipeline_id', $this->pipelineId)
                ->update(['sort_order' => $i]);
        }

        // For each lead now in this column, ensure its stage matches.
        Lead::query()
            ->whereIn('id', array_map('intval', $order))
            ->where('pipeline_id', $this->pipelineId)
            ->where('stage', '!=', $stage)
            ->get()
            ->each(fn (Lead $l) => $l->moveToStage($stage));
    }

    public function with(): array
    {
        $pipeline = $this->pipelineId ? Pipeline::find($this->pipelineId) : Pipeline::default();
        $stages = $pipeline ? $pipeline->stageList() : [];

        $leadsByStage = collect($stages)->mapWithKeys(function ($s) use ($pipeline) {
            $q = Lead::query()
                ->with('assignee')
                ->where('pipeline_id', $pipeline?->id)
                ->where('stage', $s['key'])
                ->when($this->search, fn ($qq) => $qq->where(fn ($w) => $w
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")))
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->limit(100);

            return [$s['key'] => $q->get()];
        });

        return [
            'pipeline' => $pipeline,
            'pipelines' => Pipeline::orderBy('sort_order')->orderBy('name')->get(),
            'stages' => $stages,
            'leadsByStage' => $leadsByStage,
        ];
    }
};

?>

<div class="space-y-4">
    <x-admin.page-header title="CRM Kanban" description="Drag leads between stages.">
        <x-slot:actions>
            <a href="{{ route('admin.crm.leads') }}" wire:navigate class="text-sm text-zinc-500 hover:underline">← Table view</a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="flex flex-wrap items-center gap-3">
        <select wire:model.live="pipelineId" class="rounded-md border border-zinc-300 px-3 py-1.5 text-sm dark:border-zinc-700 dark:bg-zinc-900">
            @foreach ($pipelines as $p)
                <option value="{{ $p->id }}">{{ $p->name }}</option>
            @endforeach
        </select>
        <x-ui.input wire:model.live.debounce.300ms="search" placeholder="Search…" class="max-w-xs" />
    </div>

    @if (! $pipeline)
        <div class="rounded-md border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-700 dark:bg-amber-950">
            No pipeline configured. <a href="{{ route('admin.crm.pipelines') }}" wire:navigate class="underline">Create one</a>.
        </div>
    @else
        <div class="flex gap-4 overflow-x-auto pb-4">
            @foreach ($stages as $stage)
                @php $cards = $leadsByStage[$stage['key']] ?? collect(); @endphp
                <div class="flex w-72 shrink-0 flex-col rounded-lg bg-zinc-100 p-3 dark:bg-zinc-900">
                    <div class="mb-2 flex items-center justify-between">
                        <h3 class="text-sm font-semibold">{{ $stage['label'] }}</h3>
                        <span class="text-xs text-zinc-500">{{ $cards->count() }}</span>
                    </div>

                    <ul x-sort:group="kanban"
                        x-sort="$wire.moveCard('{{ $stage['key'] }}', $sortable.toArray())"
                        x-sort:config="{ animation: 150, ghostClass: 'opacity-40' }"
                        class="flex min-h-32 flex-1 flex-col gap-2">
                        @foreach ($cards as $card)
                            <li x-sort:item="{{ $card->id }}" wire:key="card-{{ $card->id }}"
                                class="cursor-grab rounded-md border border-zinc-200 bg-white p-3 text-sm shadow-sm hover:shadow dark:border-zinc-800 dark:bg-zinc-950">
                                <a href="{{ route('admin.crm.leads.show', $card) }}" wire:navigate class="block">
                                    <div class="font-medium">{{ $card->name }}</div>
                                    @if ($card->email)<div class="text-xs text-zinc-500">{{ $card->email }}</div>@endif
                                    <div class="mt-2 flex items-center justify-between text-xs text-zinc-500">
                                        <span>{{ $card->value ? number_format((float) $card->value, 0) : '' }}</span>
                                        <span>{{ $card->assignee?->name ?? '—' }}</span>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    @endif
</div>
