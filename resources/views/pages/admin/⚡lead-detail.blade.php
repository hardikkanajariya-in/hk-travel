<?php

use App\Core\Crm\LeadAssigner;
use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Lead')] #[Layout('components.layouts.admin')] class extends Component {
    public Lead $lead;

    public string $name = '';

    public ?string $email = null;

    public ?string $phone = null;

    public ?string $company = null;

    public ?string $subject = null;

    public ?string $value = null;

    public ?string $currency = null;

    public ?int $assigned_to = null;

    public ?int $pipeline_id = null;

    public string $stage = '';

    public ?string $notes = null;

    // Activity composer
    public string $activityType = 'note';

    public string $activityBody = '';

    public ?string $activitySubject = null;

    // Reminder composer
    public string $reminderMessage = '';

    public ?string $reminderDueAt = null;

    // Merge
    public ?int $mergeIntoId = null;

    public ?string $flash = null;

    public function mount(Lead $lead): void
    {
        $this->lead = $lead;
        $this->fillFromLead();
    }

    protected function fillFromLead(): void
    {
        $this->name = $this->lead->name;
        $this->email = $this->lead->email;
        $this->phone = $this->lead->phone;
        $this->company = $this->lead->company;
        $this->subject = $this->lead->subject;
        $this->value = $this->lead->value !== null ? (string) $this->lead->value : null;
        $this->currency = $this->lead->currency;
        $this->assigned_to = $this->lead->assigned_to;
        $this->pipeline_id = $this->lead->pipeline_id;
        $this->stage = $this->lead->stage;
        $this->notes = $this->lead->notes;
    }

    public function saveDetails(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:160'],
            'email' => ['nullable', 'email:rfc'],
            'value' => ['nullable', 'numeric'],
            'currency' => ['nullable', 'string', 'size:3'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'pipeline_id' => ['nullable', 'exists:pipelines,id'],
            'stage' => ['required', 'string', 'max:64'],
        ]);

        $previousAssignee = $this->lead->assigned_to;
        $previousStage = $this->lead->stage;

        $this->lead->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'subject' => $this->subject,
            'value' => $this->value !== null && $this->value !== '' ? (float) $this->value : null,
            'currency' => $this->currency,
            'assigned_to' => $this->assigned_to,
            'pipeline_id' => $this->pipeline_id,
            'stage' => $this->stage,
            'notes' => $this->notes,
        ]);

        if ($previousStage !== $this->stage) {
            $this->lead->logActivity('status_change', "Moved {$previousStage} → {$this->stage}");
        }
        if ($previousAssignee !== $this->assigned_to && $this->assigned_to) {
            $this->lead->logActivity('assignment', 'Assignment changed.');
        }

        $this->flash = 'Saved.';
    }

    public function autoAssign(LeadAssigner $assigner): void
    {
        $user = $assigner->assign($this->lead);
        $this->lead->refresh();
        $this->fillFromLead();
        $this->flash = $user ? "Assigned to {$user->name}" : 'No assignee available.';
    }

    public function markStatus(string $status): void
    {
        $updates = ['status' => $status];
        if ($status === 'won') {
            $updates['won_at'] = now();
            $updates['lost_at'] = null;
        } elseif ($status === 'lost') {
            $updates['lost_at'] = now();
            $updates['won_at'] = null;
        } else {
            $updates['won_at'] = null;
            $updates['lost_at'] = null;
        }
        $this->lead->update($updates);
        $this->lead->logActivity('status_change', 'Status set to '.$status);
        $this->lead->refresh();
    }

    public function addActivity(): void
    {
        $this->validate([
            'activityType' => ['required', 'in:note,call,email,meeting,task'],
            'activityBody' => ['required_without:activitySubject', 'nullable', 'string', 'max:5000'],
            'activitySubject' => ['nullable', 'string', 'max:200'],
        ]);

        $this->lead->logActivity($this->activityType, $this->activitySubject, $this->activityBody);
        $this->lead->update(['last_contacted_at' => now()]);
        $this->reset(['activityBody', 'activitySubject']);
        $this->lead->refresh();
    }

    public function addReminder(): void
    {
        $this->validate([
            'reminderMessage' => ['required', 'string', 'max:300'],
            'reminderDueAt' => ['required', 'date'],
        ]);

        $this->lead->reminders()->create([
            'user_id' => auth()->id(),
            'message' => $this->reminderMessage,
            'due_at' => $this->reminderDueAt,
        ]);

        $this->reset(['reminderMessage', 'reminderDueAt']);
        $this->lead->refresh();
    }

    public function completeReminder(int $id): void
    {
        $this->lead->reminders()->where('id', $id)->update(['completed_at' => now()]);
        $this->lead->refresh();
    }

    public function merge(): void
    {
        $this->validate(['mergeIntoId' => ['required', 'exists:leads,id', 'different:lead.id']]);

        $target = Lead::findOrFail($this->mergeIntoId);

        // Move activities & reminders
        $this->lead->activities()->update(['lead_id' => $target->id]);
        $this->lead->reminders()->update(['lead_id' => $target->id]);

        // Merge fields if target is missing them
        $merged = false;
        foreach (['email', 'phone', 'company', 'subject', 'notes'] as $field) {
            if (empty($target->{$field}) && ! empty($this->lead->{$field})) {
                $target->{$field} = $this->lead->{$field};
                $merged = true;
            }
        }
        if ($merged) {
            $target->save();
        }
        $target->logActivity('note', 'Merged from lead #'.$this->lead->id, 'Original name: '.$this->lead->name);

        $this->lead->delete();
        $this->redirectRoute('admin.crm.leads.show', ['lead' => $target->id], navigate: true);
    }

    public function deleteActivity(int $id): void
    {
        $this->lead->activities()->where('id', $id)->delete();
        $this->lead->refresh();
    }

    public function with(): array
    {
        $pipeline = $this->lead->pipeline ?: Pipeline::default();

        return [
            'pipelines' => Pipeline::orderBy('name')->get(),
            'stages' => $pipeline?->stageList() ?? [],
            'assignees' => User::orderBy('name')->limit(200)->get(['id', 'name']),
            'activities' => $this->lead->activities()->with('user')->limit(100)->get(),
            'reminders' => $this->lead->reminders()->with('user')->get(),
            'mergeCandidates' => Lead::where('id', '!=', $this->lead->id)
                ->when($this->lead->email, fn ($q) => $q->where(fn ($w) => $w->where('email', $this->lead->email)->orWhere('phone', $this->lead->phone)))
                ->limit(20)->get(),
        ];
    }
};

?>

<div class="space-y-6">
    <x-admin.page-header :title="$lead->name" :description="$lead->subject">
        <x-slot:actions>
            <a href="{{ route('admin.crm.leads') }}" wire:navigate class="text-sm text-zinc-500 hover:underline">← Leads</a>
            @if ($lead->status === 'open')
                <x-ui.button variant="success" wire:click="markStatus('won')">Mark won</x-ui.button>
                <x-ui.button variant="danger" wire:click="markStatus('lost')">Mark lost</x-ui.button>
            @else
                <x-ui.button variant="outline" wire:click="markStatus('open')">Reopen</x-ui.button>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    @if ($flash)<x-admin.flash :message="$flash" />@endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        {{-- Details --}}
        <div class="lg:col-span-4 space-y-4">
            <x-ui.card>
                <h3 class="text-sm font-semibold mb-3">Contact</h3>
                <div class="space-y-3">
                    <x-ui.input wire:model="name" label="Name" :error="$errors->first('name')" />
                    <x-ui.input wire:model="email" type="email" label="Email" />
                    <x-ui.input wire:model="phone" label="Phone" />
                    <x-ui.input wire:model="company" label="Company" />
                    <x-ui.input wire:model="subject" label="Subject" />
                </div>
            </x-ui.card>

            <x-ui.card>
                <h3 class="text-sm font-semibold mb-3">Pipeline & assignment</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Pipeline</label>
                        <select wire:model.live="pipeline_id" class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                            @foreach ($pipelines as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Stage</label>
                        <select wire:model="stage" class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                            @foreach ($stages as $s)
                                <option value="{{ $s['key'] }}">{{ $s['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Assignee</label>
                        <select wire:model="assigned_to" class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                            <option value="">Unassigned</option>
                            @foreach ($assignees as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                        <button wire:click="autoAssign" class="mt-1 text-xs text-hk-primary-600 hover:underline">Auto-assign</button>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="col-span-2"><x-ui.input wire:model="value" label="Value" /></div>
                        <x-ui.input wire:model="currency" label="Cur" placeholder="USD" />
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <h3 class="text-sm font-semibold mb-3">Notes</h3>
                <x-ui.textarea wire:model="notes" rows="6" />
            </x-ui.card>

            <x-ui.button class="w-full" wire:click="saveDetails">Save changes</x-ui.button>

            @if ($mergeCandidates->isNotEmpty())
                <x-ui.card>
                    <h3 class="text-sm font-semibold mb-2">Possible duplicates</h3>
                    <select wire:model="mergeIntoId" class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                        <option value="">Select a lead to merge into…</option>
                        @foreach ($mergeCandidates as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} — {{ $c->email }}</option>
                        @endforeach
                    </select>
                    <button wire:click="merge" wire:confirm="Merge this lead into the selected one? Activities will be moved and this lead deleted." class="mt-2 text-xs text-amber-600 hover:underline">Merge into selected</button>
                </x-ui.card>
            @endif
        </div>

        {{-- Activity timeline --}}
        <div class="lg:col-span-5 space-y-4">
            <x-ui.card>
                <h3 class="text-sm font-semibold mb-3">Add activity</h3>
                <div class="space-y-2">
                    <div class="flex gap-2">
                        <select wire:model="activityType" class="rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                            <option value="note">Note</option>
                            <option value="call">Call</option>
                            <option value="email">Email</option>
                            <option value="meeting">Meeting</option>
                            <option value="task">Task</option>
                        </select>
                        <x-ui.input wire:model="activitySubject" placeholder="Subject (optional)" class="flex-1" />
                    </div>
                    <x-ui.textarea wire:model="activityBody" placeholder="What happened?" rows="3" />
                    <x-ui.button size="sm" wire:click="addActivity">Log activity</x-ui.button>
                </div>
            </x-ui.card>

            <x-ui.card>
                <h3 class="text-sm font-semibold mb-3">Activity timeline</h3>
                <ul class="space-y-3">
                    @forelse ($activities as $a)
                        <li wire:key="act-{{ $a->id }}" class="border-l-2 border-zinc-200 pl-3 dark:border-zinc-800">
                            <div class="flex items-center justify-between text-xs text-zinc-500">
                                <span>
                                    <x-ui.badge size="sm" variant="neutral">{{ $a->type }}</x-ui.badge>
                                    {{ $a->user?->name ?? 'System' }} · {{ $a->created_at?->diffForHumans() }}
                                </span>
                                <button wire:click="deleteActivity({{ $a->id }})" wire:confirm="Delete?" class="text-red-500 hover:underline">×</button>
                            </div>
                            @if ($a->subject)<div class="text-sm font-medium mt-1">{{ $a->subject }}</div>@endif
                            @if ($a->body)<div class="text-sm text-zinc-700 dark:text-zinc-300 mt-1 whitespace-pre-wrap">{{ $a->body }}</div>@endif
                        </li>
                    @empty
                        <li class="text-sm text-zinc-500">No activity yet.</li>
                    @endforelse
                </ul>
            </x-ui.card>
        </div>

        {{-- Reminders --}}
        <div class="lg:col-span-3 space-y-4">
            <x-ui.card>
                <h3 class="text-sm font-semibold mb-3">Add reminder</h3>
                <div class="space-y-2">
                    <x-ui.input wire:model="reminderMessage" placeholder="Follow up with client" />
                    <x-ui.input wire:model="reminderDueAt" type="datetime-local" label="When" />
                    <x-ui.button size="sm" wire:click="addReminder">Add</x-ui.button>
                </div>
            </x-ui.card>

            <x-ui.card>
                <h3 class="text-sm font-semibold mb-3">Reminders</h3>
                <ul class="space-y-2">
                    @forelse ($reminders as $r)
                        <li wire:key="rem-{{ $r->id }}" @class(['text-sm', 'opacity-60 line-through' => $r->completed_at])>
                            <div>{{ $r->message }}</div>
                            <div class="text-xs text-zinc-500">{{ $r->due_at?->format('M j, Y g:ia') }}</div>
                            @unless ($r->completed_at)
                                <button wire:click="completeReminder({{ $r->id }})" class="text-xs text-green-600 hover:underline">Mark done</button>
                            @endunless
                        </li>
                    @empty
                        <li class="text-sm text-zinc-500">No reminders.</li>
                    @endforelse
                </ul>
            </x-ui.card>
        </div>
    </div>
</div>
