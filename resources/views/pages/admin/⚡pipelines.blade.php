<?php

use App\Models\Pipeline;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Pipelines')] #[Layout('components.layouts.admin')] class extends Component {
    public ?int $editingId = null;

    public string $name = '';

    public string $slug = '';

    public ?string $description = null;

    /** @var array<int, array<string, mixed>> */
    public array $stages = [];

    public bool $is_default = false;

    public bool $is_active = true;

    public function open(?int $id = null): void
    {
        if ($id) {
            $p = Pipeline::findOrFail($id);
            $this->editingId = $p->id;
            $this->name = $p->name;
            $this->slug = $p->slug;
            $this->description = $p->description;
            $this->stages = $p->stageList();
            $this->is_default = (bool) $p->is_default;
            $this->is_active = (bool) $p->is_active;
        } else {
            $this->reset(['editingId', 'name', 'slug', 'description', 'is_default']);
            $this->stages = [
                ['key' => 'new', 'label' => 'New', 'color' => 'info', 'is_won' => false, 'is_lost' => false],
                ['key' => 'won', 'label' => 'Won', 'color' => 'success', 'is_won' => true, 'is_lost' => false],
                ['key' => 'lost', 'label' => 'Lost', 'color' => 'danger', 'is_won' => false, 'is_lost' => true],
            ];
            $this->is_active = true;
        }
        $this->dispatch('open-modal', name: 'pipeline-edit');
    }

    public function addStage(): void
    {
        $this->stages[] = ['key' => 'stage_'.(count($this->stages) + 1), 'label' => 'Untitled', 'color' => 'neutral', 'is_won' => false, 'is_lost' => false];
    }

    public function removeStage(int $i): void
    {
        unset($this->stages[$i]);
        $this->stages = array_values($this->stages);
    }

    public function updatedName(): void
    {
        if (! $this->editingId && $this->slug === '') {
            $this->slug = Str::slug($this->name);
        }
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:160'],
            'slug' => ['required', 'string', 'max:160', 'alpha_dash', \Illuminate\Validation\Rule::unique('pipelines', 'slug')->ignore($this->editingId)],
            'stages' => ['array', 'min:1'],
            'stages.*.key' => ['required', 'string', 'max:64', 'regex:/^[a-z0-9_]+$/i'],
            'stages.*.label' => ['required', 'string', 'max:120'],
        ]);

        if ($this->is_default) {
            Pipeline::query()->where('id', '!=', $this->editingId ?? 0)->update(['is_default' => false]);
        }

        Pipeline::updateOrCreate(
            ['id' => $this->editingId],
            [
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
                'stages' => array_values($this->stages),
                'is_default' => $this->is_default,
                'is_active' => $this->is_active,
            ]
        );

        $this->dispatch('close-modal', name: 'pipeline-edit');
        $this->reset(['editingId']);
    }

    public function delete(int $id): void
    {
        Pipeline::where('id', $id)->where('is_default', false)->delete();
    }

    public function with(): array
    {
        return ['pipelines' => Pipeline::orderBy('sort_order')->orderBy('name')->withCount('leads')->get()];
    }
};

?>

<div class="space-y-6">
    <x-admin.page-header title="Pipelines" description="Define stages for your sales/CRM workflow.">
        <x-slot:actions>
            <a href="{{ route('admin.crm.leads') }}" wire:navigate class="text-sm text-zinc-500 hover:underline">← Leads</a>
            <x-ui.button wire:click="open">+ New pipeline</x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-ui.card>
        <table class="w-full text-sm">
            <thead class="border-b border-zinc-200 text-left text-xs uppercase tracking-wide text-zinc-500 dark:border-zinc-800">
                <tr>
                    <th class="px-3 py-2">Name</th>
                    <th class="px-3 py-2">Stages</th>
                    <th class="px-3 py-2">Leads</th>
                    <th class="px-3 py-2">Default</th>
                    <th class="px-3 py-2">Status</th>
                    <th class="px-3 py-2 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse ($pipelines as $p)
                    <tr wire:key="p-{{ $p->id }}">
                        <td class="px-3 py-2 font-medium">{{ $p->name }}<div class="text-xs text-zinc-500 font-mono">{{ $p->slug }}</div></td>
                        <td class="px-3 py-2">{{ count($p->stages ?? []) }}</td>
                        <td class="px-3 py-2">{{ $p->leads_count ?? 0 }}</td>
                        <td class="px-3 py-2">@if ($p->is_default)<x-ui.badge variant="primary">Default</x-ui.badge>@endif</td>
                        <td class="px-3 py-2"><x-ui.badge :variant="$p->is_active ? 'success' : 'neutral'">{{ $p->is_active ? 'Active' : 'Disabled' }}</x-ui.badge></td>
                        <td class="px-3 py-2 text-right">
                            <button wire:click="open({{ $p->id }})" class="text-xs text-hk-primary-600 hover:underline">Edit</button>
                            @unless ($p->is_default)
                                <button wire:click="delete({{ $p->id }})" wire:confirm="Delete pipeline?" class="ml-3 text-xs text-red-600 hover:underline">Delete</button>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-3 py-12 text-center text-sm text-zinc-500">No pipelines.</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-ui.card>

    <x-ui.modal name="pipeline-edit" :title="$editingId ? 'Edit pipeline' : 'New pipeline'" size="lg">
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <x-ui.input wire:model.live="name" label="Name" :error="$errors->first('name')" />
                <x-ui.input wire:model="slug" label="Slug" :error="$errors->first('slug')" />
            </div>
            <x-ui.textarea wire:model="description" label="Description" rows="2" />

            <div>
                <div class="mb-2 flex items-center justify-between">
                    <h4 class="text-sm font-semibold">Stages</h4>
                    <x-ui.button size="sm" variant="outline" wire:click="addStage">+ Add stage</x-ui.button>
                </div>
                <div class="space-y-2">
                    @foreach ($stages as $i => $s)
                        <div wire:key="stage-{{ $i }}" class="grid grid-cols-12 gap-2 rounded-md border border-zinc-200 p-2 dark:border-zinc-800">
                            <input wire:model="stages.{{ $i }}.key" placeholder="key" class="col-span-3 rounded border-zinc-300 px-2 py-1 text-xs font-mono dark:border-zinc-700 dark:bg-zinc-900">
                            <input wire:model="stages.{{ $i }}.label" placeholder="Label" class="col-span-4 rounded border-zinc-300 px-2 py-1 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                            <select wire:model="stages.{{ $i }}.color" class="col-span-2 rounded border-zinc-300 px-2 py-1 text-xs dark:border-zinc-700 dark:bg-zinc-900">
                                @foreach (['neutral', 'primary', 'info', 'success', 'warning', 'danger'] as $c)
                                    <option value="{{ $c }}">{{ $c }}</option>
                                @endforeach
                            </select>
                            <label class="col-span-1 flex items-center gap-1 text-xs"><input type="checkbox" wire:model="stages.{{ $i }}.is_won">Won</label>
                            <label class="col-span-1 flex items-center gap-1 text-xs"><input type="checkbox" wire:model="stages.{{ $i }}.is_lost">Lost</label>
                            <button wire:click="removeStage({{ $i }})" class="col-span-1 text-xs text-red-600 hover:underline">×</button>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="is_default" class="size-4 rounded">Default pipeline</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="is_active" class="size-4 rounded">Active</label>
            </div>
        </div>
        <x-slot:footer>
            <x-ui.button variant="outline" x-on:click="$dispatch('close-modal', { name: 'pipeline-edit' })">Cancel</x-ui.button>
            <x-ui.button wire:click="save">Save</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>
</div>
