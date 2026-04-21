<?php

use App\Models\ContactForm;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Contact forms')] #[Layout('components.layouts.admin')] class extends Component {
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Validate('required|string|max:120')]
    public string $newName = '';

    #[Validate('required|string|max:120|alpha_dash|unique:contact_forms,slug')]
    public string $newSlug = '';

    public function updatedNewName(): void
    {
        if ($this->newSlug === '') {
            $this->newSlug = Str::slug($this->newName);
        }
    }

    public function create(): void
    {
        $this->validate();

        $form = ContactForm::create([
            'name' => $this->newName,
            'slug' => $this->newSlug,
            'fields' => [
                ['key' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
                ['key' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                ['key' => 'message', 'label' => 'Message', 'type' => 'textarea', 'required' => true, 'rows' => 5],
            ],
            'settings' => [
                'submit_label' => 'Send message',
                'success_message' => 'Thanks — we\'ll get back to you shortly.',
                'captcha' => true,
                'store_submissions' => true,
            ],
            'is_active' => true,
        ]);

        $this->reset(['newName', 'newSlug']);
        $this->dispatch('close-modal', name: 'form-create');
        $this->redirectRoute('admin.contact-forms.edit', ['form' => $form->id], navigate: true);
    }

    public function delete(int $id): void
    {
        ContactForm::where('id', $id)->delete();
    }

    public function toggleActive(int $id): void
    {
        $f = ContactForm::findOrFail($id);
        $f->is_active = ! $f->is_active;
        $f->save();
    }

    public function with(): array
    {
        return [
            'forms' => ContactForm::query()
                ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('slug', 'like', "%{$this->search}%"))
                ->withCount('submissions')
                ->orderByDesc('id')
                ->paginate(15),
        ];
    }
};

?>

<div class="space-y-6">
    <x-admin.page-header title="Contact forms" description="Build forms with drag/drop, then embed them on any page via the page builder.">
        <x-slot:actions>
            <a href="{{ route('admin.contact-submissions') }}" wire:navigate class="text-sm text-hk-primary-600 hover:underline">View submissions →</a>
            <x-ui.button x-on:click="$dispatch('open-modal', { name: 'form-create' })">+ New form</x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-ui.card>
        <div class="mb-4 flex items-center gap-3">
            <x-ui.input wire:model.live.debounce.300ms="search" placeholder="Search forms…" class="max-w-xs" />
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-zinc-200 text-left text-xs uppercase tracking-wide text-zinc-500 dark:border-zinc-800">
                    <tr>
                        <th class="px-3 py-2">Name</th>
                        <th class="px-3 py-2">Slug</th>
                        <th class="px-3 py-2">Fields</th>
                        <th class="px-3 py-2">Submissions</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($forms as $form)
                        <tr wire:key="cf-{{ $form->id }}">
                            <td class="px-3 py-2 font-medium">{{ $form->name }}</td>
                            <td class="px-3 py-2 font-mono text-xs text-zinc-500">{{ $form->slug }}</td>
                            <td class="px-3 py-2">{{ count($form->fields ?? []) }}</td>
                            <td class="px-3 py-2">{{ $form->submissions_count }}</td>
                            <td class="px-3 py-2">
                                <button wire:click="toggleActive({{ $form->id }})">
                                    <x-ui.badge :variant="$form->is_active ? 'success' : 'neutral'">{{ $form->is_active ? 'Active' : 'Disabled' }}</x-ui.badge>
                                </button>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <a href="{{ route('admin.contact-forms.edit', $form) }}" wire:navigate class="text-xs text-hk-primary-600 hover:underline">Edit</a>
                                <button wire:click="delete({{ $form->id }})" wire:confirm="{{ __('admin.confirm.delete_form') }}" class="ml-3 text-xs text-red-600 hover:underline">{{ __('admin.actions.delete') }}</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-3 py-12 text-center text-sm text-zinc-500">No forms yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $forms->links() }}</div>
    </x-ui.card>

    <x-ui.modal name="form-create" title="New contact form">
        <div class="space-y-3">
            <x-ui.input wire:model.live="newName" label="Name" :error="$errors->first('newName')" />
            <x-ui.input wire:model="newSlug" label="Slug" hint="Used as the form key in shortcodes and pages." :error="$errors->first('newSlug')" />
        </div>
        <x-slot:footer>
            <x-ui.button variant="outline" x-on:click="$dispatch('close-modal', { name: 'form-create' })">Cancel</x-ui.button>
            <x-ui.button wire:click="create">Create</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>
</div>
