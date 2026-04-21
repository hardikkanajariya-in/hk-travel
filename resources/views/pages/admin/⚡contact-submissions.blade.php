<?php

use App\Models\ContactForm;
use App\Models\ContactSubmission;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

new #[Title('Form submissions')] #[Layout('components.layouts.admin')] class extends Component {
    use WithPagination;

    #[Url(as: 'form')]
    public ?int $formId = null;

    #[Url(as: 'status')]
    public string $status = '';

    #[Url(as: 'q')]
    public string $search = '';

    public ?ContactSubmission $viewing = null;

    public function open(int $id): void
    {
        $this->viewing = ContactSubmission::with('form')->find($id);
        if ($this->viewing && $this->viewing->status === 'new') {
            $this->viewing->update(['status' => 'read']);
        }
        $this->dispatch('open-modal', name: 'submission-view');
    }

    public function setStatus(int $id, string $status): void
    {
        ContactSubmission::where('id', $id)->update([
            'status' => $status,
            'handled_at' => in_array($status, ['handled', 'spam'], true) ? now() : null,
        ]);
        if ($this->viewing?->id === $id) {
            $this->viewing->refresh();
        }
    }

    public function delete(int $id): void
    {
        ContactSubmission::where('id', $id)->delete();
        if ($this->viewing?->id === $id) {
            $this->viewing = null;
            $this->dispatch('close-modal', name: 'submission-view');
        }
    }

    public function export(): StreamedResponse
    {
        $query = $this->baseQuery();
        $rows = $query->limit(5000)->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Form', 'Name', 'Email', 'Phone', 'Subject', 'Status', 'IP', 'Locale', 'Submitted at', 'Data']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->id,
                    $r->form?->name,
                    $r->name,
                    $r->email,
                    $r->phone,
                    $r->subject,
                    $r->status,
                    $r->ip,
                    $r->locale,
                    $r->created_at?->toIso8601String(),
                    json_encode($r->data, JSON_UNESCAPED_UNICODE),
                ]);
            }
            fclose($out);
        }, 'submissions-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    protected function baseQuery()
    {
        return ContactSubmission::query()
            ->with('form')
            ->when($this->formId, fn ($q) => $q->where('form_id', $this->formId))
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
            ->when($this->search, fn ($q) => $q->where(fn ($s) => $s
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
                ->orWhere('subject', 'like', "%{$this->search}%")))
            ->orderByDesc('id');
    }

    public function with(): array
    {
        return [
            'forms' => ContactForm::orderBy('name')->pluck('name', 'id'),
            'submissions' => $this->baseQuery()->paginate(20),
            'statusCounts' => ContactSubmission::query()->selectRaw('status, count(*) as c')->groupBy('status')->pluck('c', 'status'),
        ];
    }
};

?>

<div class="space-y-6">
    <x-admin.page-header title="Form submissions" description="Inbox of all contact-form submissions across the site.">
        <x-slot:actions>
            <a href="{{ route('admin.contact-forms') }}" wire:navigate class="text-sm text-zinc-500 hover:underline">Manage forms →</a>
            <x-ui.button variant="outline" wire:click="export">Export CSV</x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-ui.card>
        <div class="mb-4 flex flex-wrap items-center gap-3">
            <select wire:model.live="formId" class="rounded-md border border-zinc-300 px-3 py-1.5 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                <option value="">All forms</option>
                @foreach ($forms as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
            <select wire:model.live="status" class="rounded-md border border-zinc-300 px-3 py-1.5 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                <option value="">All statuses</option>
                @foreach (['new', 'read', 'handled', 'spam', 'archived'] as $s)
                    <option value="{{ $s }}">{{ ucfirst($s) }} ({{ $statusCounts[$s] ?? 0 }})</option>
                @endforeach
            </select>
            <x-ui.input wire:model.live.debounce.300ms="search" placeholder="Search name, email, subject…" class="max-w-xs" />
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-zinc-200 text-left text-xs uppercase tracking-wide text-zinc-500 dark:border-zinc-800">
                    <tr>
                        <th class="px-3 py-2">When</th>
                        <th class="px-3 py-2">Form</th>
                        <th class="px-3 py-2">From</th>
                        <th class="px-3 py-2">Subject</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($submissions as $s)
                        <tr wire:key="sub-{{ $s->id }}" @class(['bg-amber-50/50 dark:bg-amber-950/20' => $s->status === 'new'])>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-zinc-500">{{ $s->created_at?->diffForHumans() }}</td>
                            <td class="px-3 py-2">{{ $s->form?->name ?? '—' }}</td>
                            <td class="px-3 py-2">
                                <div class="font-medium">{{ $s->name ?: '—' }}</div>
                                <div class="text-xs text-zinc-500">{{ $s->email }}</div>
                            </td>
                            <td class="px-3 py-2 max-w-xs truncate">{{ $s->subject }}</td>
                            <td class="px-3 py-2">
                                <x-ui.badge :variant="match($s->status) {
                                    'new' => 'warning',
                                    'read' => 'info',
                                    'handled' => 'success',
                                    'spam' => 'danger',
                                    default => 'neutral',
                                }">{{ ucfirst($s->status) }}</x-ui.badge>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <button wire:click="open({{ $s->id }})" class="text-xs text-hk-primary-600 hover:underline">View</button>
                                <button wire:click="delete({{ $s->id }})" wire:confirm="Delete this submission?" class="ml-3 text-xs text-red-600 hover:underline">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-3 py-12 text-center text-sm text-zinc-500">No submissions.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $submissions->links() }}</div>
    </x-ui.card>

    <x-ui.modal name="submission-view" :title="$viewing ? ('Submission #'.$viewing->id) : 'Submission'" size="lg">
        @if ($viewing)
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><dt class="text-xs text-zinc-500">Form</dt><dd>{{ $viewing->form?->name }}</dd></div>
                    <div><dt class="text-xs text-zinc-500">When</dt><dd>{{ $viewing->created_at?->toDayDateTimeString() }}</dd></div>
                    <div><dt class="text-xs text-zinc-500">Name</dt><dd>{{ $viewing->name ?: '—' }}</dd></div>
                    <div><dt class="text-xs text-zinc-500">Email</dt><dd>{{ $viewing->email ?: '—' }}</dd></div>
                    <div><dt class="text-xs text-zinc-500">Phone</dt><dd>{{ $viewing->phone ?: '—' }}</dd></div>
                    <div><dt class="text-xs text-zinc-500">IP / Locale</dt><dd class="text-xs font-mono">{{ $viewing->ip }} · {{ $viewing->locale }}</dd></div>
                </div>
                <div>
                    <dt class="mb-1 text-xs text-zinc-500">Data</dt>
                    <dl class="divide-y divide-zinc-100 rounded-md border border-zinc-200 dark:divide-zinc-800 dark:border-zinc-800">
                        @foreach ((array) $viewing->data as $k => $v)
                            <div class="grid grid-cols-3 gap-2 px-3 py-2 text-sm">
                                <dt class="font-mono text-xs text-zinc-500">{{ $k }}</dt>
                                <dd class="col-span-2 break-words">{{ is_scalar($v) ? (string) $v : json_encode($v) }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </div>
        @endif
        <x-slot:footer>
            @if ($viewing)
                <x-ui.button variant="outline" wire:click="setStatus({{ $viewing->id }}, 'spam')">Mark spam</x-ui.button>
                <x-ui.button variant="outline" wire:click="setStatus({{ $viewing->id }}, 'archived')">Archive</x-ui.button>
                <x-ui.button wire:click="setStatus({{ $viewing->id }}, 'handled')">Mark handled</x-ui.button>
            @endif
        </x-slot:footer>
    </x-ui.modal>
</div>
