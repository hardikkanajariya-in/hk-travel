<?php

use App\Core\Localization\LocaleManager;
use App\Models\Language;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Title('Languages')] #[Layout('components.layouts.admin')] class extends Component {
    public ?int $editingId = null;

    #[Validate('required|string|max:8|regex:/^[a-z]{2}(-[A-Za-z]{2})?$/')]
    public string $code = '';

    #[Validate('required|string|max:120')]
    public string $name = '';

    #[Validate('nullable|string|max:120')]
    public string $native_name = '';

    #[Validate('nullable|string|max:8')]
    public string $flag = '';

    public bool $is_rtl = false;

    public bool $is_default = false;

    public bool $is_active = true;

    public int $sort_order = 0;

    public function with(): array
    {
        return [
            'languages' => Language::query()->orderBy('sort_order')->orderBy('name')->get(),
        ];
    }

    public function edit(int $id): void
    {
        $lang = Language::findOrFail($id);
        $this->editingId = $lang->id;
        $this->code = $lang->code;
        $this->name = $lang->name;
        $this->native_name = (string) $lang->native_name;
        $this->flag = (string) $lang->flag;
        $this->is_rtl = $lang->is_rtl;
        $this->is_default = $lang->is_default;
        $this->is_active = $lang->is_active;
        $this->sort_order = $lang->sort_order;
    }

    public function newLanguage(): void
    {
        $this->resetForm();
    }

    public function save(LocaleManager $manager): void
    {
        $this->validate();

        $data = [
            'code' => strtolower($this->code),
            'name' => $this->name,
            'native_name' => $this->native_name ?: null,
            'flag' => $this->flag ?: null,
            'is_rtl' => $this->is_rtl,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ];

        if ($this->editingId) {
            Language::where('id', $this->editingId)->update($data);
        } else {
            Language::create($data);
        }

        if ($this->is_default) {
            Language::where('id', '!=', $this->editingId ?? 0)->update(['is_default' => false]);
        }

        $manager->flush();
        session()->flash('settings.saved', __('Language saved.'));
        $this->resetForm();
    }

    public function toggleActive(int $id, LocaleManager $manager): void
    {
        $lang = Language::findOrFail($id);
        $lang->update(['is_active' => ! $lang->is_active]);
        $manager->flush();
    }

    public function delete(int $id, LocaleManager $manager): void
    {
        $lang = Language::findOrFail($id);
        if ($lang->is_default) {
            session()->flash('settings.error', __('Cannot delete the default language.'));

            return;
        }
        $lang->delete();
        $manager->flush();
    }

    protected function resetForm(): void
    {
        $this->reset(['editingId', 'code', 'name', 'native_name', 'flag', 'is_rtl', 'is_default', 'is_active', 'sort_order']);
        $this->is_active = true;
    }
};

?>

<div class="space-y-6">
    <x-admin.page-header title="Languages" subtitle="Manage available locales, their direction, sorting and default selection." />

    <x-admin.flash :message="session('settings.saved')" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <x-ui.card padding="none">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-sm">
                        <thead class="bg-zinc-50 dark:bg-zinc-900/40 text-xs uppercase tracking-wide text-zinc-500 text-left">
                            <tr>
                                <th class="px-4 py-2">Code</th>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Native</th>
                                <th class="px-4 py-2">Dir</th>
                                <th class="px-4 py-2">Default</th>
                                <th class="px-4 py-2">Active</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-900">
                            @foreach ($languages as $lang)
                                <tr>
                                    <td class="px-4 py-2 font-mono text-xs">{{ $lang->code }}</td>
                                    <td class="px-4 py-2">{{ $lang->name }}</td>
                                    <td class="px-4 py-2">{{ $lang->native_name }}</td>
                                    <td class="px-4 py-2">{{ $lang->is_rtl ? 'RTL' : 'LTR' }}</td>
                                    <td class="px-4 py-2">
                                        @if ($lang->is_default)
                                            <x-ui.badge variant="success" size="sm">Default</x-ui.badge>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2">
                                        <button type="button" wire:click="toggleActive({{ $lang->id }})" class="text-xs">
                                            @if ($lang->is_active)
                                                <x-ui.badge variant="success" size="sm">On</x-ui.badge>
                                            @else
                                                <x-ui.badge variant="neutral" size="sm">Off</x-ui.badge>
                                            @endif
                                        </button>
                                    </td>
                                    <td class="px-4 py-2 text-right">
                                        <button type="button" wire:click="edit({{ $lang->id }})" class="text-xs text-hk-primary-600 hover:underline">Edit</button>
                                        @if (! $lang->is_default)
                                            <button type="button" wire:click="delete({{ $lang->id }})" wire:confirm="{{ __('admin.confirm.delete') }}" class="text-xs text-red-600 hover:underline ml-2">{{ __('admin.actions.delete') }}</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        </div>

        <x-ui.card>
            <h2 class="text-base font-semibold mb-4">{{ $editingId ? 'Edit language' : 'Add language' }}</h2>
            <div class="space-y-3">
                <x-ui.input wire:model="code" label="Code" required hint="ISO 639-1 (en, fr) or with region (en-GB)" />
                <x-ui.input wire:model="name" label="Name" required />
                <x-ui.input wire:model="native_name" label="Native name" />
                <x-ui.input wire:model="flag" label="Flag (ISO 3166-1 alpha-2)" hint="e.g. gb, fr, sa" />
                <x-ui.input type="number" wire:model="sort_order" label="Sort order" />
                <label class="flex items-center gap-2"><input type="checkbox" wire:model="is_rtl" class="size-4 rounded"> <span class="text-sm">Right-to-left</span></label>
                <label class="flex items-center gap-2"><input type="checkbox" wire:model="is_default" class="size-4 rounded"> <span class="text-sm">Default language</span></label>
                <label class="flex items-center gap-2"><input type="checkbox" wire:model="is_active" class="size-4 rounded"> <span class="text-sm">Active</span></label>
            </div>
            <div class="mt-4 flex justify-end gap-2">
                @if ($editingId)
                    <button type="button" wire:click="newLanguage" class="text-sm text-zinc-500">Cancel</button>
                @endif
                <x-ui.button wire:click="save">{{ $editingId ? 'Update' : 'Add' }}</x-ui.button>
            </div>
        </x-ui.card>
    </div>
</div>
