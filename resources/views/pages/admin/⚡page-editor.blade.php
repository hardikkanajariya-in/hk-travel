<?php

use App\Core\PageBuilder\BlockRegistry;
use App\Models\Page;
use App\Models\PageBlock;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Title('Edit page')] #[Layout('components.layouts.admin')] class extends Component {
    public Page $page;

    #[Validate('required|string|max:200')]
    public string $title = '';

    #[Validate('required|string|max:200|alpha_dash')]
    public string $slug = '';

    #[Validate('in:draft,published')]
    public string $status = 'draft';

    public bool $is_homepage = false;

    /** @var array<string, mixed> */
    public array $seo = [
        'meta_title' => null,
        'meta_description' => null,
        'og_image' => null,
        'noindex' => false,
    ];

    /** @var array<int, array<string, mixed>> */
    public array $blocks = [];

    public ?int $editingBlock = null;

    public ?string $flash = null;

    public bool $picker = false;

    public function mount(Page $page): void
    {
        $this->page = $page;
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->status = $page->status;
        $this->is_homepage = (bool) $page->is_homepage;
        $this->seo = array_merge($this->seo, (array) $page->seo);

        $this->blocks = $page->blocks
            ->map(fn (PageBlock $b): array => [
                'id' => $b->id,
                'type' => $b->type,
                'data' => (array) $b->data,
                'visible_mobile' => (bool) $b->visible_mobile,
                'visible_tablet' => (bool) $b->visible_tablet,
                'visible_desktop' => (bool) $b->visible_desktop,
            ])
            ->values()
            ->all();
    }

    #[Computed]
    public function registry(): BlockRegistry
    {
        return app(BlockRegistry::class);
    }

    public function availableBlocks(): array
    {
        $user = auth()->user();
        $grouped = [];

        foreach ($this->registry->all() as $key => $block) {
            if ($block->permission() && ! $user?->can($block->permission())) {
                continue;
            }
            $grouped[$block->category()][] = ['key' => $key, 'name' => $block->name(), 'icon' => $block->icon()];
        }

        return $grouped;
    }

    public function addBlock(string $type): void
    {
        if (! $this->registry->has($type)) {
            return;
        }

        $block = $this->registry->get($type);

        if ($block->permission() && ! auth()->user()?->can($block->permission())) {
            $this->flash = 'You do not have permission to add this block.';

            return;
        }

        $this->blocks[] = [
            'id' => null,
            'type' => $type,
            'data' => $block->defaultData(),
            'visible_mobile' => true,
            'visible_tablet' => true,
            'visible_desktop' => true,
        ];

        $this->editingBlock = array_key_last($this->blocks);
        $this->picker = false;
    }

    public function removeBlock(int $index): void
    {
        unset($this->blocks[$index]);
        $this->blocks = array_values($this->blocks);
        $this->editingBlock = null;
    }

    public function duplicateBlock(int $index): void
    {
        if (! isset($this->blocks[$index])) {
            return;
        }

        $copy = $this->blocks[$index];
        $copy['id'] = null;
        array_splice($this->blocks, $index + 1, 0, [$copy]);
    }

    public function moveUp(int $index): void
    {
        if ($index <= 0 || ! isset($this->blocks[$index - 1])) {
            return;
        }
        [$this->blocks[$index - 1], $this->blocks[$index]] = [$this->blocks[$index], $this->blocks[$index - 1]];
    }

    public function moveDown(int $index): void
    {
        if (! isset($this->blocks[$index + 1])) {
            return;
        }
        [$this->blocks[$index + 1], $this->blocks[$index]] = [$this->blocks[$index], $this->blocks[$index + 1]];
    }

    /** @param array<int, int|string> $order */
    public function reorder(array $order): void
    {
        $reordered = [];
        foreach ($order as $index) {
            if (isset($this->blocks[(int) $index])) {
                $reordered[] = $this->blocks[(int) $index];
            }
        }
        $this->blocks = $reordered;
    }

    public function editBlock(int $index): void
    {
        $this->editingBlock = isset($this->blocks[$index]) ? $index : null;
    }

    public function addRepeaterItem(int $blockIndex, string $fieldKey): void
    {
        if (! isset($this->blocks[$blockIndex])) {
            return;
        }

        $type = $this->blocks[$blockIndex]['type'];
        if (! $this->registry->has($type)) {
            return;
        }

        $field = collect($this->registry->get($type)->fields())->firstWhere('key', $fieldKey);
        if (! $field || ($field['type'] ?? null) !== 'repeater') {
            return;
        }

        $template = [];
        foreach ($field['fields'] ?? [] as $sub) {
            $template[$sub['key']] = ($sub['type'] ?? null) === 'toggle' ? false : '';
        }

        $items = $this->blocks[$blockIndex]['data'][$fieldKey] ?? [];
        $items[] = $template;
        $this->blocks[$blockIndex]['data'][$fieldKey] = $items;
    }

    public function removeRepeaterItem(int $blockIndex, string $fieldKey, int $itemIndex): void
    {
        $items = $this->blocks[$blockIndex]['data'][$fieldKey] ?? [];
        unset($items[$itemIndex]);
        $this->blocks[$blockIndex]['data'][$fieldKey] = array_values($items);
    }

    public function save(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['required', 'string', 'max:200', 'alpha_dash', \Illuminate\Validation\Rule::unique('pages', 'slug')->ignore($this->page->id)],
            'status' => ['in:draft,published'],
        ]);

        $this->page->update([
            'title' => $this->title,
            'slug' => $this->slug,
            'status' => $this->status,
            'seo' => $this->seo,
            'published_at' => $this->status === 'published' && $this->page->published_at === null ? now() : $this->page->published_at,
            'updated_by' => auth()->id(),
        ]);

        if ($this->is_homepage) {
            Page::query()->where('id', '!=', $this->page->id)->where('is_homepage', true)->update(['is_homepage' => false]);
            $this->page->update(['is_homepage' => true]);
        } else {
            $this->page->update(['is_homepage' => false]);
        }

        // Persist blocks: wipe + recreate to keep it simple and atomic.
        $this->page->blocks()->delete();
        foreach ($this->blocks as $position => $row) {
            $type = $row['type'];
            if (! $this->registry->has($type)) {
                continue;
            }
            $block = $this->registry->get($type);
            $sanitized = $block->sanitize((array) $row['data']);

            PageBlock::create([
                'page_id' => $this->page->id,
                'type' => $type,
                'data' => $sanitized,
                'visible_mobile' => (bool) ($row['visible_mobile'] ?? true),
                'visible_tablet' => (bool) ($row['visible_tablet'] ?? true),
                'visible_desktop' => (bool) ($row['visible_desktop'] ?? true),
                'position' => $position,
            ]);
        }

        $this->page->refresh();
        $this->flash = 'Saved.';
    }

    public function publish(): void
    {
        $this->status = 'published';
        $this->save();
    }

    public function blockMeta(string $type): ?array
    {
        if (! $this->registry->has($type)) {
            return null;
        }
        $b = $this->registry->get($type);

        return ['name' => $b->name(), 'icon' => $b->icon(), 'fields' => $b->fields()];
    }
};

?>

<div class="space-y-6">
    <x-admin.page-header :title="'Edit: '.$title" description="Drag blocks to reorder, click to edit, toggle device visibility per block.">
        <x-slot:actions>
            <a href="{{ route('admin.pages') }}" wire:navigate class="text-sm text-zinc-500 hover:underline">← All pages</a>
            <a href="{{ url('/'.$slug) }}" target="_blank" rel="noopener" class="text-sm text-hk-primary-600 hover:underline">Preview</a>
            <x-ui.button variant="outline" wire:click="save">Save draft</x-ui.button>
            <x-ui.button wire:click="publish">{{ $status === 'published' ? 'Update' : 'Publish' }}</x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    @if ($flash)
        <x-admin.flash :message="$flash" />
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        {{-- LEFT: block list --}}
        <div class="lg:col-span-3">
            <x-ui.card>
                <h3 class="text-sm font-semibold mb-3">Page settings</h3>
                <div class="space-y-3">
                    <x-ui.input wire:model="title" label="Title" :error="$errors->first('title')" />
                    <x-ui.input wire:model="slug" label="Slug" :error="$errors->first('slug')" />
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" wire:model="is_homepage" class="size-4 rounded border-zinc-300 text-hk-primary-600 focus:ring-hk-primary-500">
                        Use as homepage
                    </label>
                    <div>
                        <label class="block text-sm font-medium mb-1">Status</label>
                        <select wire:model="status" class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                </div>
            </x-ui.card>

            <div class="mt-4">
                <x-ui.card>
                    <h3 class="text-sm font-semibold mb-3">SEO</h3>
                    <div class="space-y-3">
                        <x-ui.input wire:model="seo.meta_title" label="Meta title" />
                        <x-ui.textarea wire:model="seo.meta_description" label="Meta description" rows="3" />
                        <x-ui.input wire:model="seo.og_image" label="OG image URL" />
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" wire:model="seo.noindex" class="size-4 rounded border-zinc-300 text-hk-primary-600 focus:ring-hk-primary-500">
                            Hide from search engines (noindex)
                        </label>
                    </div>
                </x-ui.card>
            </div>
        </div>

        {{-- CENTER: blocks --}}
        <div class="lg:col-span-6">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">Blocks ({{ count($blocks) }})</h3>
                <x-ui.button size="sm" x-on:click="$dispatch('open-modal', { name: 'block-picker' })">+ Add block</x-ui.button>
            </div>

            <ul x-sort="$wire.reorder($sortable.toArray())"
                x-sort:config="{ animation: 150, ghostClass: 'opacity-50' }"
                class="space-y-2">
                @forelse ($blocks as $i => $row)
                    @php $meta = $this->blockMeta($row['type']); @endphp
                    <li x-sort:item="{{ $i }}"
                        wire:key="block-{{ $i }}-{{ $row['type'] }}"
                        @class([
                            'group rounded-lg border bg-white dark:bg-zinc-900 transition',
                            'border-hk-primary-500 ring-2 ring-hk-primary-500/20' => $editingBlock === $i,
                            'border-zinc-200 dark:border-zinc-800 hover:border-zinc-300' => $editingBlock !== $i,
                        ])>
                        <div class="flex items-center gap-2 px-3 py-2">
                            <button type="button" x-sort:handle class="cursor-grab text-zinc-400 hover:text-zinc-600" aria-label="Drag">
                                <svg viewBox="0 0 20 20" fill="currentColor" class="size-4"><path d="M7 4a1 1 0 110 2 1 1 0 010-2zm6 0a1 1 0 110 2 1 1 0 010-2zM7 9a1 1 0 110 2 1 1 0 010-2zm6 0a1 1 0 110 2 1 1 0 010-2zM7 14a1 1 0 110 2 1 1 0 010-2zm6 0a1 1 0 110 2 1 1 0 010-2z" /></svg>
                            </button>
                            <button type="button" wire:click="editBlock({{ $i }})" class="flex flex-1 items-center gap-2 text-left">
                                <span class="text-sm font-medium">{{ $meta['name'] ?? $row['type'] }}</span>
                                <span class="text-xs text-zinc-400">#{{ $i + 1 }}</span>
                            </button>

                            <div class="flex items-center gap-1 text-zinc-400">
                                <button type="button" wire:click="duplicateBlock({{ $i }})" class="rounded p-1 hover:bg-zinc-100 dark:hover:bg-zinc-800" title="Duplicate">
                                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-4"><path d="M7 3a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V5a2 2 0 00-2-2H7zm2 4a1 1 0 011-1h2a1 1 0 010 2h-2a1 1 0 01-1-1zm0 3a1 1 0 011-1h2a1 1 0 010 2h-2a1 1 0 01-1-1z" /><path d="M3 7a2 2 0 012-2v9a3 3 0 003 3h6a2 2 0 01-2 2H7a4 4 0 01-4-4V7z" /></svg>
                                </button>
                                <button type="button" wire:click="removeBlock({{ $i }})" wire:confirm="{{ __('admin.confirm.delete') }}" class="rounded p-1 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950" title="{{ __('admin.actions.delete') }}">
                                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M8.75 1A1.75 1.75 0 007 2.75V3H4.5a.75.75 0 000 1.5h11a.75.75 0 000-1.5H13v-.25A1.75 1.75 0 0011.25 1h-2.5zM5.5 6.25a.75.75 0 011.5 0v9.25a.75.75 0 01-1.5 0V6.25zm4 0a.75.75 0 011.5 0v9.25a.75.75 0 01-1.5 0V6.25zm4 0a.75.75 0 011.5 0v9.25a.75.75 0 01-1.5 0V6.25z" clip-rule="evenodd" /></svg>
                                </button>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="rounded-lg border-2 border-dashed border-zinc-200 bg-zinc-50 p-12 text-center text-sm text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900">
                        No blocks yet. Click <strong>Add block</strong> to start building.
                    </li>
                @endforelse
            </ul>
        </div>

        {{-- RIGHT: block editor --}}
        <div class="lg:col-span-3">
            @if ($editingBlock !== null && isset($blocks[$editingBlock]))
                @php
                    $row = $blocks[$editingBlock];
                    $meta = $this->blockMeta($row['type']);
                @endphp

                <x-ui.card>
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-sm font-semibold">{{ $meta['name'] ?? $row['type'] }}</h3>
                        <button type="button" wire:click="$set('editingBlock', null)" class="text-xs text-zinc-500 hover:underline">Done</button>
                    </div>

                    <div class="space-y-3">
                        @foreach ($meta['fields'] ?? [] as $field)
                            @php $key = "blocks.$editingBlock.data.".$field['key']; @endphp
                            @switch($field['type'] ?? 'text')
                                @case('textarea')
                                    <x-ui.textarea wire:model="{{ $key }}" :label="$field['label']" :rows="$field['rows'] ?? 3" />
                                    @break
                                @case('richtext')
                                    <x-ui.textarea wire:model="{{ $key }}" :label="$field['label']" rows="6" hint="HTML allowed (sanitised on save)." />
                                    @break
                                @case('toggle')
                                    <label class="flex items-center gap-2 text-sm">
                                        <input type="checkbox" wire:model="{{ $key }}" class="size-4 rounded border-zinc-300 text-hk-primary-600">
                                        {{ $field['label'] }}
                                    </label>
                                    @break
                                @case('select')
                                    <div>
                                        <label class="block text-sm font-medium mb-1">{{ $field['label'] }}</label>
                                        <select wire:model="{{ $key }}" class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                                            @foreach ($field['options'] ?? [] as $optVal => $optLabel)
                                                <option value="{{ $optVal }}">{{ $optLabel }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @break
                                @case('repeater')
                                    <div class="rounded-md border border-zinc-200 p-3 dark:border-zinc-800">
                                        <div class="mb-2 flex items-center justify-between">
                                            <span class="block text-sm font-medium">{{ $field['label'] }}</span>
                                            <button type="button"
                                                    wire:click="addRepeaterItem({{ $editingBlock }}, '{{ $field['key'] }}')"
                                                    class="text-xs font-medium text-hk-primary-600 hover:underline">+ Add item</button>
                                        </div>

                                        @php $items = $row['data'][$field['key']] ?? []; @endphp
                                        @if (empty($items))
                                            <p class="text-xs text-zinc-500">No items yet.</p>
                                        @else
                                            <ul class="space-y-3">
                                                @foreach ($items as $itemIdx => $item)
                                                    <li class="rounded border border-zinc-200 p-2 dark:border-zinc-800" wire:key="rep-{{ $editingBlock }}-{{ $field['key'] }}-{{ $itemIdx }}">
                                                        <div class="mb-2 flex items-center justify-between">
                                                            <span class="text-xs text-zinc-500">Item {{ $itemIdx + 1 }}</span>
                                                            <button type="button"
                                                                    wire:click="removeRepeaterItem({{ $editingBlock }}, '{{ $field['key'] }}', {{ $itemIdx }})"
                                                                    class="text-xs text-red-600 hover:underline">Remove</button>
                                                        </div>
                                                        <div class="space-y-2">
                                                            @foreach ($field['fields'] ?? [] as $sub)
                                                                @php $subKey = "blocks.$editingBlock.data.".$field['key'].".$itemIdx.".$sub['key']; @endphp
                                                                @switch($sub['type'] ?? 'text')
                                                                    @case('textarea')
                                                                    @case('richtext')
                                                                        <x-ui.textarea wire:model="{{ $subKey }}" :label="$sub['label']" rows="3" />
                                                                        @break
                                                                    @case('toggle')
                                                                        <label class="flex items-center gap-2 text-sm">
                                                                            <input type="checkbox" wire:model="{{ $subKey }}" class="size-4 rounded border-zinc-300 text-hk-primary-600">
                                                                            {{ $sub['label'] }}
                                                                        </label>
                                                                        @break
                                                                    @default
                                                                        <x-ui.input :type="$sub['type'] ?? 'text'" wire:model="{{ $subKey }}" :label="$sub['label']" />
                                                                @endswitch
                                                            @endforeach
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                    @break
                                @default
                                    <x-ui.input :type="$field['type'] ?? 'text'" wire:model="{{ $key }}" :label="$field['label']" />
                            @endswitch
                        @endforeach
                    </div>

                    <div class="mt-5 border-t border-zinc-200 pt-4 dark:border-zinc-800">
                        <h4 class="text-xs font-semibold uppercase tracking-wide text-zinc-500 mb-2">Visibility</h4>
                        <div class="space-y-1.5 text-sm">
                            <label class="flex items-center gap-2"><input type="checkbox" wire:model="blocks.{{ $editingBlock }}.visible_mobile" class="size-4 rounded border-zinc-300 text-hk-primary-600"> Mobile</label>
                            <label class="flex items-center gap-2"><input type="checkbox" wire:model="blocks.{{ $editingBlock }}.visible_tablet" class="size-4 rounded border-zinc-300 text-hk-primary-600"> Tablet</label>
                            <label class="flex items-center gap-2"><input type="checkbox" wire:model="blocks.{{ $editingBlock }}.visible_desktop" class="size-4 rounded border-zinc-300 text-hk-primary-600"> Desktop</label>
                        </div>
                    </div>
                </x-ui.card>
            @else
                <div class="rounded-lg border-2 border-dashed border-zinc-200 bg-zinc-50 p-8 text-center text-sm text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900">
                    Select a block to edit its content.
                </div>
            @endif
        </div>
    </div>

    {{-- Block picker modal --}}
    <x-ui.modal name="block-picker" title="Add a block" maxWidth="3xl">
        <div class="space-y-6">
            @foreach ($this->availableBlocks() as $category => $items)
                <div>
                    <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-zinc-500">{{ $category }}</h4>
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                        @foreach ($items as $b)
                            <button type="button"
                                    wire:click="addBlock('{{ $b['key'] }}')"
                                    x-on:click="$dispatch('close-modal', { name: 'block-picker' })"
                                    class="flex flex-col items-start gap-1 rounded-md border border-zinc-200 p-3 text-left transition hover:border-hk-primary-500 hover:bg-hk-primary-50 dark:border-zinc-800 dark:hover:bg-hk-primary-950">
                                <span class="text-sm font-medium">{{ $b['name'] }}</span>
                                <span class="font-mono text-[10px] text-zinc-400">{{ $b['key'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </x-ui.modal>
</div>
