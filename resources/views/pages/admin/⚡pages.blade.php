<?php

use App\Models\Page;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Pages')] #[Layout('components.layouts.admin')] class extends Component {
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    #[Validate('required|string|max:200')]
    public string $newTitle = '';

    #[Validate('required|string|max:200|alpha_dash|unique:pages,slug')]
    public string $newSlug = '';

    public ?string $flash = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedNewTitle(): void
    {
        if ($this->newSlug === '') {
            $this->newSlug = \Illuminate\Support\Str::slug($this->newTitle);
        }
    }

    public function create(): void
    {
        $this->validate();

        $page = Page::create([
            'title' => $this->newTitle,
            'slug' => $this->newSlug,
            'layout' => 'default',
            'status' => 'draft',
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $this->reset(['newTitle', 'newSlug']);
        $this->redirectRoute('admin.pages.edit', ['page' => $page->id], navigate: true);
    }

    public function delete(int $id): void
    {
        $page = Page::findOrFail($id);
        $page->delete();
        $this->flash = "Deleted “{$page->title}”.";
    }

    public function setHomepage(int $id): void
    {
        Page::query()->where('is_homepage', true)->update(['is_homepage' => false]);
        Page::where('id', $id)->update(['is_homepage' => true]);
        $this->flash = 'Homepage updated.';
    }

    public function with(): array
    {
        $query = Page::query()->orderByDesc('updated_at');

        if ($this->search !== '') {
            $query->where(function ($q): void {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('slug', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        return [
            'pages' => $query->paginate(20),
        ];
    }
};

?>

<div class="space-y-6">
    <x-admin.page-header title="Pages" description="Drag-and-drop landing pages built from blocks.">
        <x-slot:actions>
            <x-ui.button x-on:click="$dispatch('open-modal', { name: 'page-create' })">New page</x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    @if ($flash)
        <x-admin.flash :message="$flash" />
    @endif

    <x-ui.card padding="none">
        <div class="flex flex-wrap gap-3 border-b border-zinc-200 px-4 py-3 dark:border-zinc-800">
            <div class="flex-1 min-w-64">
                <x-ui.input wire:model.live.debounce.300ms="search" placeholder="Search title or slug…" />
            </div>
            <select wire:model.live="statusFilter"
                    class="rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-hk-primary-500 focus:outline-none focus:ring-2 focus:ring-hk-primary-500/30 dark:border-zinc-700 dark:bg-zinc-900">
                <option value="">All statuses</option>
                <option value="draft">Draft</option>
                <option value="published">Published</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-800">
                <thead class="bg-zinc-50 text-left text-xs uppercase tracking-wide text-zinc-500 dark:bg-zinc-900/40">
                    <tr>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Slug</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Homepage</th>
                        <th class="px-4 py-3">Updated</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-900">
                    @forelse ($pages as $page)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $page->title }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-zinc-500">/{{ $page->slug }}</td>
                            <td class="px-4 py-3">
                                @if ($page->status === 'published')
                                    <x-ui.badge variant="success" size="sm">Published</x-ui.badge>
                                @else
                                    <x-ui.badge variant="neutral" size="sm">Draft</x-ui.badge>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if ($page->is_homepage)
                                    <x-ui.badge variant="info" size="sm">Yes</x-ui.badge>
                                @else
                                    <button wire:click="setHomepage({{ $page->id }})" class="text-xs text-hk-primary-600 hover:underline">
                                        Set as homepage
                                    </button>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-zinc-500">{{ $page->updated_at?->diffForHumans() }}</td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('admin.pages.edit', $page) }}" wire:navigate class="text-xs text-hk-primary-600 hover:underline">Edit</a>
                                <a href="{{ url('/'.$page->slug) }}" target="_blank" rel="noopener" class="text-xs text-zinc-500 hover:underline">View</a>
                                <button wire:click="delete({{ $page->id }})" wire:confirm="{{ __('admin.confirm.delete_page') }}" class="text-xs text-red-600 hover:underline">{{ __('admin.actions.delete') }}</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-zinc-500">No pages yet — create your first one.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-zinc-200 px-4 py-3 dark:border-zinc-800">
            {{ $pages->links() }}
        </div>
    </x-ui.card>

    <x-ui.modal name="page-create" title="New page">
        <div class="space-y-4">
            <x-ui.input wire:model.live="newTitle" label="Title" required :error="$errors->first('newTitle')" />
            <x-ui.input wire:model="newSlug" label="Slug" required hint="URL: /{{ $newSlug ?: 'slug' }}" :error="$errors->first('newSlug')" />
        </div>
        <x-slot:footer>
            <x-ui.button variant="outline" x-on:click="$dispatch('close-modal', { name: 'page-create' })">Cancel</x-ui.button>
            <x-ui.button wire:click="create">Create &amp; edit</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>
</div>
