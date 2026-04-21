<?php

use App\Models\ContactForm;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Edit form')] #[Layout('components.layouts.admin')] class extends Component {
    public ContactForm $form;

    public string $name = '';

    public string $slug = '';

    public ?string $description = null;

    /** @var array<int, array<string, mixed>> */
    public array $fields = [];

    /** @var array<string, mixed> */
    public array $settings = [];

    /** @var array<int, string> */
    public array $notify_emails = [];

    public bool $create_lead = false;

    public bool $is_active = true;

    public ?int $editingField = null;

    public ?string $flash = null;

    public string $newEmail = '';

    public function mount(ContactForm $form): void
    {
        $this->form = $form;
        $this->name = $form->name;
        $this->slug = $form->slug;
        $this->description = $form->description;
        $this->fields = $form->fields ?? [];
        $this->settings = (array) ($form->settings ?? []);
        $this->notify_emails = (array) ($form->notify_emails ?? []);
        $this->create_lead = (bool) $form->create_lead;
        $this->is_active = (bool) $form->is_active;
    }

    public function fieldTypes(): array
    {
        return [
            'text' => 'Single line',
            'textarea' => 'Paragraph',
            'email' => 'Email',
            'tel' => 'Phone',
            'url' => 'URL',
            'number' => 'Number',
            'date' => 'Date',
            'select' => 'Dropdown',
            'checkbox' => 'Checkbox',
        ];
    }

    public function addField(string $type = 'text'): void
    {
        $this->fields[] = [
            'key' => 'field_'.(count($this->fields) + 1),
            'label' => 'Untitled',
            'type' => $type,
            'required' => false,
            'placeholder' => '',
            'options' => $type === 'select' ? ['Option 1', 'Option 2'] : [],
        ];
        $this->editingField = array_key_last($this->fields);
    }

    public function removeField(int $index): void
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields);
        $this->editingField = null;
    }

    public function duplicateField(int $index): void
    {
        if (! isset($this->fields[$index])) {
            return;
        }
        $copy = $this->fields[$index];
        $copy['key'] = $copy['key'].'_copy';
        array_splice($this->fields, $index + 1, 0, [$copy]);
    }

    /** @param array<int, int|string> $order */
    public function reorder(array $order): void
    {
        $reordered = [];
        foreach ($order as $i) {
            if (isset($this->fields[(int) $i])) {
                $reordered[] = $this->fields[(int) $i];
            }
        }
        $this->fields = $reordered;
    }

    public function addNotifyEmail(): void
    {
        $this->validate(['newEmail' => ['required', 'email:rfc']]);
        if (! in_array($this->newEmail, $this->notify_emails, true)) {
            $this->notify_emails[] = $this->newEmail;
        }
        $this->newEmail = '';
    }

    public function removeNotifyEmail(int $index): void
    {
        unset($this->notify_emails[$index]);
        $this->notify_emails = array_values($this->notify_emails);
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:120', 'alpha_dash', \Illuminate\Validation\Rule::unique('contact_forms', 'slug')->ignore($this->form->id)],
            'fields' => ['array', 'min:1'],
            'fields.*.key' => ['required', 'string', 'max:64', 'regex:/^[a-z0-9_]+$/i'],
            'fields.*.label' => ['required', 'string', 'max:160'],
            'fields.*.type' => ['required', 'in:text,textarea,email,tel,url,number,date,select,checkbox'],
        ]);

        $this->form->update([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'fields' => array_values($this->fields),
            'settings' => $this->settings,
            'notify_emails' => array_values($this->notify_emails),
            'create_lead' => $this->create_lead,
            'is_active' => $this->is_active,
        ]);

        $this->flash = 'Saved.';
    }
};

?>

<div class="space-y-6">
    <x-admin.page-header :title="'Form: '.$name" description="Drag fields to reorder, click to edit. Embed via the Contact form block on any page.">
        <x-slot:actions>
            <a href="{{ route('admin.contact-forms') }}" wire:navigate class="text-sm text-zinc-500 hover:underline">← All forms</a>
            <x-ui.button wire:click="save">Save</x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    @if ($flash)
        <x-admin.flash :message="$flash" />
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        {{-- Settings --}}
        <div class="lg:col-span-3 space-y-4">
            <x-ui.card>
                <h3 class="text-sm font-semibold mb-3">General</h3>
                <div class="space-y-3">
                    <x-ui.input wire:model="name" label="Name" :error="$errors->first('name')" />
                    <x-ui.input wire:model="slug" label="Slug" :error="$errors->first('slug')" />
                    <x-ui.textarea wire:model="description" label="Description" rows="3" />
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" wire:model="is_active" class="size-4 rounded border-zinc-300 text-hk-primary-600">
                        Active
                    </label>
                </div>
            </x-ui.card>

            <x-ui.card>
                <h3 class="text-sm font-semibold mb-3">Behaviour</h3>
                <div class="space-y-3">
                    <x-ui.input wire:model="settings.submit_label" label="Submit label" />
                    <x-ui.textarea wire:model="settings.success_message" label="Success message" rows="3" />
                    <x-ui.input wire:model="settings.redirect_url" label="Redirect URL (optional)" />
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" wire:model="settings.captcha" class="size-4 rounded border-zinc-300 text-hk-primary-600">
                        Require captcha
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" wire:model="settings.store_submissions" class="size-4 rounded border-zinc-300 text-hk-primary-600">
                        Store submissions in inbox
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" wire:model="create_lead" class="size-4 rounded border-zinc-300 text-hk-primary-600">
                        Also create a CRM Lead
                    </label>
                </div>
            </x-ui.card>

            <x-ui.card>
                <h3 class="text-sm font-semibold mb-3">Notification emails</h3>
                <div class="space-y-2">
                    @foreach ($notify_emails as $i => $email)
                        <div class="flex items-center gap-2 text-sm" wire:key="ne-{{ $i }}">
                            <span class="flex-1 truncate">{{ $email }}</span>
                            <button wire:click="removeNotifyEmail({{ $i }})" class="text-xs text-red-600 hover:underline">Remove</button>
                        </div>
                    @endforeach
                    <div class="flex gap-2">
                        <x-ui.input wire:model="newEmail" placeholder="name@example.com" />
                        <x-ui.button size="sm" wire:click="addNotifyEmail">Add</x-ui.button>
                    </div>
                    @error('newEmail')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </x-ui.card>
        </div>

        {{-- Field list --}}
        <div class="lg:col-span-6">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">Fields ({{ count($fields) }})</h3>
                <div class="flex items-center gap-2">
                    <select x-data x-on:change="$wire.addField($event.target.value); $event.target.value = ''"
                            class="rounded-md border border-zinc-300 px-3 py-1.5 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                        <option value="">+ Add field…</option>
                        @foreach ($this->fieldTypes() as $type => $label)
                            <option value="{{ $type }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <ul x-sort="$wire.reorder($sortable.toArray())"
                x-sort:config="{ animation: 150, ghostClass: 'opacity-50' }"
                class="space-y-2">
                @forelse ($fields as $i => $field)
                    <li x-sort:item="{{ $i }}" wire:key="field-{{ $i }}-{{ $field['key'] }}"
                        @class([
                            'group rounded-lg border bg-white dark:bg-zinc-900 transition',
                            'border-hk-primary-500 ring-2 ring-hk-primary-500/20' => $editingField === $i,
                            'border-zinc-200 dark:border-zinc-800 hover:border-zinc-300' => $editingField !== $i,
                        ])>
                        <div class="flex items-center gap-2 px-3 py-2">
                            <button type="button" x-sort:handle class="cursor-grab text-zinc-400 hover:text-zinc-600">
                                <svg viewBox="0 0 20 20" fill="currentColor" class="size-4"><path d="M7 4a1 1 0 110 2 1 1 0 010-2zm6 0a1 1 0 110 2 1 1 0 010-2zM7 9a1 1 0 110 2 1 1 0 010-2zm6 0a1 1 0 110 2 1 1 0 010-2zM7 14a1 1 0 110 2 1 1 0 010-2zm6 0a1 1 0 110 2 1 1 0 010-2z" /></svg>
                            </button>
                            <button type="button" wire:click="$set('editingField', {{ $i }})" class="flex flex-1 items-center gap-2 text-left">
                                <span class="text-sm font-medium">{{ $field['label'] ?: '(untitled)' }}</span>
                                <span class="font-mono text-xs text-zinc-400">{{ $field['key'] }}</span>
                                <x-ui.badge size="sm" variant="neutral">{{ $field['type'] }}</x-ui.badge>
                                @if ($field['required'] ?? false)<x-ui.badge size="sm" variant="warning">required</x-ui.badge>@endif
                            </button>
                            <button wire:click="duplicateField({{ $i }})" class="text-xs text-zinc-500 hover:underline">Duplicate</button>
                            <button wire:click="removeField({{ $i }})" wire:confirm="Remove field?" class="text-xs text-red-600 hover:underline">Remove</button>
                        </div>
                    </li>
                @empty
                    <li class="rounded-lg border-2 border-dashed border-zinc-200 bg-zinc-50 p-12 text-center text-sm text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900">
                        No fields yet. Pick a type above to add one.
                    </li>
                @endforelse
            </ul>
        </div>

        {{-- Field editor --}}
        <div class="lg:col-span-3">
            @if ($editingField !== null && isset($fields[$editingField]))
                @php $f = $fields[$editingField]; @endphp
                <x-ui.card>
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-sm font-semibold">Field properties</h3>
                        <button wire:click="$set('editingField', null)" class="text-xs text-zinc-500 hover:underline">Done</button>
                    </div>
                    <div class="space-y-3">
                        <x-ui.input wire:model="fields.{{ $editingField }}.label" label="Label" />
                        <x-ui.input wire:model="fields.{{ $editingField }}.key" label="Key" hint="snake_case identifier (used in submissions)." />
                        <x-ui.input wire:model="fields.{{ $editingField }}.placeholder" label="Placeholder" />
                        <div>
                            <label class="block text-sm font-medium mb-1">Type</label>
                            <select wire:model="fields.{{ $editingField }}.type" class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                                @foreach ($this->fieldTypes() as $type => $label)
                                    <option value="{{ $type }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($f['type'] === 'select')
                            <div>
                                <label class="block text-sm font-medium mb-1">Options (one per line)</label>
                                <textarea
                                    rows="5"
                                    class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900"
                                    wire:model="fields.{{ $editingField }}.options_text"
                                    x-data
                                    x-init="$el.value = (@js($f['options'] ?? [])).join('\n'); $el.addEventListener('input', e => $wire.set('fields.{{ $editingField }}.options', e.target.value.split('\n').map(s => s.trim()).filter(Boolean)))"></textarea>
                            </div>
                        @endif
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" wire:model="fields.{{ $editingField }}.required" class="size-4 rounded border-zinc-300 text-hk-primary-600">
                            Required
                        </label>
                    </div>
                </x-ui.card>
            @else
                <div class="rounded-lg border-2 border-dashed border-zinc-200 bg-zinc-50 p-8 text-center text-sm text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900">
                    Select a field to edit.
                </div>
            @endif
        </div>
    </div>
</div>
