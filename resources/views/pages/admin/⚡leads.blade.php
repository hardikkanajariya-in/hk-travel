<?php

use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

new #[Title('Leads')] #[Layout('components.layouts.admin')] class extends Component {
    use WithFileUploads;
    use WithPagination;

    #[Url(as: 'pipeline')]
    public ?int $pipelineId = null;

    #[Url(as: 'stage')]
    public string $stage = '';

    #[Url(as: 'status')]
    public string $status = '';

    #[Url(as: 'assignee')]
    public ?int $assigneeId = null;

    #[Url(as: 'q')]
    public string $search = '';

    public string $newName = '';

    public ?string $newEmail = null;

    public ?string $newPhone = null;

    public ?int $newPipelineId = null;

    public $importFile;

    public function mount(): void
    {
        $this->pipelineId = $this->pipelineId ?: Pipeline::default()?->id;
        $this->newPipelineId = $this->pipelineId;
    }

    public function createLead(): void
    {
        $this->validate([
            'newName' => ['required', 'string', 'max:160'],
            'newEmail' => ['nullable', 'email:rfc'],
            'newPhone' => ['nullable', 'string', 'max:64'],
            'newPipelineId' => ['nullable', 'exists:pipelines,id'],
        ]);

        Lead::create([
            'name' => $this->newName,
            'email' => $this->newEmail,
            'phone' => $this->newPhone,
            'pipeline_id' => $this->newPipelineId,
            'stage' => 'new',
            'status' => 'open',
            'source' => 'manual',
        ]);

        $this->reset(['newName', 'newEmail', 'newPhone']);
        $this->dispatch('close-modal', name: 'lead-create');
    }

    public function delete(int $id): void
    {
        Lead::findOrFail($id)->delete();
    }

    public function export(): StreamedResponse
    {
        $rows = $this->baseQuery()->limit(10000)->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Name', 'Email', 'Phone', 'Company', 'Pipeline', 'Stage', 'Status', 'Source', 'Value', 'Assignee', 'Created']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->id, $r->name, $r->email, $r->phone, $r->company,
                    $r->pipeline?->name, $r->stage, $r->status, $r->source,
                    $r->value, $r->assignee?->name, $r->created_at?->toIso8601String(),
                ]);
            }
            fclose($out);
        }, 'leads-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function import(): void
    {
        $this->validate(['importFile' => ['required', 'file', 'mimes:csv,txt', 'max:5120']]);

        $handle = fopen($this->importFile->getRealPath(), 'r');
        $headers = array_map('strtolower', (array) fgetcsv($handle));
        $imported = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $assoc = array_combine($headers, $row) ?: [];
            if (empty($assoc['name']) && empty($assoc['email'])) {
                continue;
            }
            Lead::create([
                'name' => $assoc['name'] ?? ($assoc['email'] ?? 'Unknown'),
                'email' => $assoc['email'] ?? null,
                'phone' => $assoc['phone'] ?? null,
                'company' => $assoc['company'] ?? null,
                'pipeline_id' => $this->pipelineId,
                'stage' => $assoc['stage'] ?? 'new',
                'status' => 'open',
                'source' => $assoc['source'] ?? 'import',
            ]);
            $imported++;
        }
        fclose($handle);

        $this->reset('importFile');
        $this->dispatch('close-modal', name: 'lead-import');
        session()->flash('flash', "Imported {$imported} leads.");
    }

    protected function baseQuery()
    {
        return Lead::query()
            ->with(['pipeline', 'assignee'])
            ->when($this->pipelineId, fn ($q) => $q->where('pipeline_id', $this->pipelineId))
            ->when($this->stage !== '', fn ($q) => $q->where('stage', $this->stage))
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
            ->when($this->assigneeId, fn ($q) => $q->where('assigned_to', $this->assigneeId))
            ->when($this->search, fn ($q) => $q->where(fn ($s) => $s
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
                ->orWhere('company', 'like', "%{$this->search}%")
                ->orWhere('phone', 'like', "%{$this->search}%")))
            ->orderByDesc('id');
    }

    public function with(): array
    {
        $pipeline = $this->pipelineId ? Pipeline::find($this->pipelineId) : Pipeline::default();

        return [
            'leads' => $this->baseQuery()->paginate(25),
            'pipelines' => Pipeline::orderBy('sort_order')->orderBy('name')->get(),
            'pipeline' => $pipeline,
            'assignees' => User::orderBy('name')->limit(200)->get(['id', 'name']),
            'flash' => session('flash'),
        ];
    }
};

?>

<div class="space-y-6">
    <x-admin.page-header title="Leads" description="All inbound and manually-added leads.">
        <x-slot:actions>
            @if ($pipeline)
                <a href="{{ route('admin.crm.kanban', ['pipeline' => $pipeline->id]) }}" wire:navigate class="text-sm text-zinc-500 hover:underline">Kanban view →</a>
            @endif
            <a href="{{ route('admin.crm.pipelines') }}" wire:navigate class="text-sm text-zinc-500 hover:underline">Pipelines</a>
            <x-ui.button variant="outline" wire:click="export">Export CSV</x-ui.button>
            <x-ui.button variant="outline" x-on:click="$dispatch('open-modal', { name: 'lead-import' })">Import</x-ui.button>
            <x-ui.button x-on:click="$dispatch('open-modal', { name: 'lead-create' })">+ New lead</x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    @if ($flash)
        <x-admin.flash :message="$flash" />
    @endif

    <x-ui.card>
        <div class="mb-4 flex flex-wrap items-center gap-3">
            <select wire:model.live="pipelineId" class="rounded-md border border-zinc-300 px-3 py-1.5 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                <option value="">All pipelines</option>
                @foreach ($pipelines as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
            @if ($pipeline)
                <select wire:model.live="stage" class="rounded-md border border-zinc-300 px-3 py-1.5 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <option value="">All stages</option>
                    @foreach ($pipeline->stageList() as $s)
                        <option value="{{ $s['key'] }}">{{ $s['label'] }}</option>
                    @endforeach
                </select>
            @endif
            <select wire:model.live="status" class="rounded-md border border-zinc-300 px-3 py-1.5 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                <option value="">Any status</option>
                @foreach (['open', 'won', 'lost'] as $s)
                    <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <select wire:model.live="assigneeId" class="rounded-md border border-zinc-300 px-3 py-1.5 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                <option value="">Any assignee</option>
                @foreach ($assignees as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </select>
            <x-ui.input wire:model.live.debounce.300ms="search" placeholder="Search…" class="max-w-xs" />
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-zinc-200 text-left text-xs uppercase tracking-wide text-zinc-500 dark:border-zinc-800">
                    <tr>
                        <th class="px-3 py-2">Lead</th>
                        <th class="px-3 py-2">Pipeline / Stage</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">Value</th>
                        <th class="px-3 py-2">Assignee</th>
                        <th class="px-3 py-2">Source</th>
                        <th class="px-3 py-2">Created</th>
                        <th class="px-3 py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($leads as $l)
                        <tr wire:key="lead-{{ $l->id }}">
                            <td class="px-3 py-2">
                                <div class="font-medium">
                                    <a href="{{ route('admin.crm.leads.show', $l) }}" wire:navigate class="hover:underline">{{ $l->name }}</a>
                                </div>
                                <div class="text-xs text-zinc-500">{{ $l->email ?? '—' }}</div>
                            </td>
                            <td class="px-3 py-2">
                                <div>{{ $l->pipeline?->name ?? '—' }}</div>
                                <div class="text-xs text-zinc-500">{{ $l->stage }}</div>
                            </td>
                            <td class="px-3 py-2">
                                <x-ui.badge :variant="$l->status === 'won' ? 'success' : ($l->status === 'lost' ? 'danger' : 'neutral')">{{ ucfirst($l->status) }}</x-ui.badge>
                            </td>
                            <td class="px-3 py-2">{{ $l->value ? number_format((float) $l->value, 2).' '.$l->currency : '—' }}</td>
                            <td class="px-3 py-2">{{ $l->assignee?->name ?? '—' }}</td>
                            <td class="px-3 py-2 text-xs text-zinc-500">{{ $l->source ?: '—' }}</td>
                            <td class="px-3 py-2 text-xs text-zinc-500 whitespace-nowrap">{{ $l->created_at?->diffForHumans() }}</td>
                            <td class="px-3 py-2 text-right">
                                <a href="{{ route('admin.crm.leads.show', $l) }}" wire:navigate class="text-xs text-hk-primary-600 hover:underline">Open</a>
                                <button wire:click="delete({{ $l->id }})" wire:confirm="{{ __('admin.confirm.delete_lead') }}" class="ml-3 text-xs text-red-600 hover:underline">{{ __('admin.actions.delete') }}</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-3 py-12 text-center text-sm text-zinc-500">No leads.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $leads->links() }}</div>
    </x-ui.card>

    <x-ui.modal name="lead-create" title="New lead">
        <div class="space-y-3">
            <x-ui.input wire:model="newName" label="Name" :error="$errors->first('newName')" />
            <x-ui.input wire:model="newEmail" type="email" label="Email" :error="$errors->first('newEmail')" />
            <x-ui.input wire:model="newPhone" label="Phone" />
            <div>
                <label class="block text-sm font-medium mb-1">Pipeline</label>
                <select wire:model="newPipelineId" class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                    @foreach ($pipelines as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <x-slot:footer>
            <x-ui.button variant="outline" x-on:click="$dispatch('close-modal', { name: 'lead-create' })">Cancel</x-ui.button>
            <x-ui.button wire:click="createLead">Create</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>

    <x-ui.modal name="lead-import" title="Import leads from CSV">
        <p class="mb-3 text-sm text-zinc-500">CSV with headers: <code>name, email, phone, company, stage, source</code>.</p>
        <input type="file" wire:model="importFile" accept=".csv,text/csv" class="block w-full text-sm">
        @error('importFile')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
        <x-slot:footer>
            <x-ui.button variant="outline" x-on:click="$dispatch('close-modal', { name: 'lead-import' })">Cancel</x-ui.button>
            <x-ui.button wire:click="import">Import</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>
</div>
