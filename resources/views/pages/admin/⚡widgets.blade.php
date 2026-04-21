<?php

use App\Core\PageBuilder\BlockRegistry;
use App\Core\Settings\SettingsRepository;
use App\Core\Theme\ThemeManager;
use App\Models\Widget;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Footer & sidebar blocks')] #[Layout('components.layouts.admin')] class extends Component {
    /** @var array<int, string> */
    public array $zones = [];

    /** @var array<int, string> */
    public array $allZones = [];

    public string $activeZone = '';

    public ?int $editingWidget = null;

    /** @var array<string, mixed> */
    public array $editingData = [];

    public string $newType = '';

    public int $footerColumns = 3;

    public ?string $flash = null;

    public function mount(): void
    {
        $themeKey = (string) app(SettingsRepository::class)->get('theme.active', config('hk.theme.active', 'default'));
        $theme = app(ThemeManager::class)->all()->get($themeKey);
        $this->allZones = (array) ($theme?->supports['widget_areas'] ?? ['footer-1', 'footer-2', 'footer-3', 'sidebar']);

        $this->footerColumns = (int) app(SettingsRepository::class)->get('theme.footer_columns', 3);
        $this->footerColumns = max(1, min(4, $this->footerColumns));

        $this->zones = $this->visibleZones();
        $this->activeZone = $this->zones[0] ?? ($this->allZones[0] ?? 'footer-1');
    }

    /**
     * Restrict the footer columns visible in the tabs to the number the user
     * has chosen, while keeping non-footer zones (e.g. "sidebar") intact.
     *
     * @return array<int, string>
     */
    protected function visibleZones(): array
    {
        return collect($this->allZones)
            ->filter(function (string $zone): bool {
                if (! str_starts_with($zone, 'footer-')) {
                    return true;
                }
                $n = (int) substr($zone, 7);

                return $n >= 1 && $n <= $this->footerColumns;
            })
            ->values()
            ->all();
    }

    public function updatedFooterColumns(): void
    {
        $this->footerColumns = max(1, min(4, (int) $this->footerColumns));
        app(SettingsRepository::class)->set('theme.footer_columns', $this->footerColumns);
        $this->zones = $this->visibleZones();
        if (! in_array($this->activeZone, $this->zones, true)) {
            $this->activeZone = $this->zones[0] ?? '';
            $this->editingWidget = null;
        }
        $this->flash = 'Footer layout updated.';
    }

    /**
     * Human-friendly label for a zone key. Keeps the technical key out of
     * the UI per our non-technical-user rules.
     */
    public function zoneLabel(string $zone): string
    {
        return match (true) {
            $zone === 'sidebar' => 'Sidebar',
            str_starts_with($zone, 'footer-') => 'Footer column '.substr($zone, 7),
            default => \Illuminate\Support\Str::headline($zone),
        };
    }

    public function selectZone(string $zone): void
    {
        $this->activeZone = $zone;
        $this->editingWidget = null;
    }

    public function availableTypes(): array
    {
        $registry = app(BlockRegistry::class);
        $user = auth()->user();
        $allow = ['heading', 'rich_text', 'image', 'button', 'menu', 'newsletter', 'cards', 'embed', 'custom_html'];
        $items = [];
        foreach ($allow as $key) {
            if (! $registry->has($key)) {
                continue;
            }
            $b = $registry->get($key);
            if ($b->permission() && ! $user?->can($b->permission())) {
                continue;
            }
            $items[$key] = $b->name();
        }

        return $items;
    }

    public function addWidget(): void
    {
        $registry = app(BlockRegistry::class);
        if (! $this->newType || ! $registry->has($this->newType)) {
            $this->flash = 'Pick a widget type first.';

            return;
        }

        $block = $registry->get($this->newType);
        if ($block->permission() && ! auth()->user()?->can($block->permission())) {
            $this->flash = 'You do not have permission to add this widget.';

            return;
        }

        $position = Widget::where('zone', $this->activeZone)->max('position') ?? -1;
        $w = Widget::create([
            'zone' => $this->activeZone,
            'type' => $this->newType,
            'data' => $block->defaultData(),
            'is_active' => true,
            'position' => $position + 1,
        ]);

        $this->editingWidget = $w->id;
        $this->newType = '';
        $this->flash = 'Widget added.';
    }

    public function deleteWidget(int $id): void
    {
        Widget::where('id', $id)->delete();
        $this->flash = 'Widget removed.';
    }

    public function toggleActive(int $id): void
    {
        $w = Widget::findOrFail($id);
        $w->is_active = ! $w->is_active;
        $w->save();
    }

    public function editWidget(int $id): void
    {
        $w = Widget::findOrFail($id);
        $this->editingWidget = $id;
        $this->editingData = (array) $w->data;
    }

    public function saveWidget(): void
    {
        if (! $this->editingWidget) {
            return;
        }
        $w = Widget::findOrFail($this->editingWidget);
        $registry = app(BlockRegistry::class);
        if (! $registry->has($w->type)) {
            return;
        }
        $w->data = $registry->get($w->type)->sanitize($this->editingData);
        $w->save();
        $this->flash = 'Widget saved.';
    }

    /** @param array<int, int|string> $order */
    public function reorder(array $order): void
    {
        foreach ($order as $position => $id) {
            Widget::where('id', (int) $id)
                ->where('zone', $this->activeZone)
                ->update(['position' => $position]);
        }
    }

    public function with(): array
    {
        return [
            'widgets' => Widget::where('zone', $this->activeZone)->orderBy('position')->get(),
            'registry' => app(BlockRegistry::class),
        ];
    }
};

?>

<div class="space-y-6">
    <x-admin.page-header
        title="Footer & sidebar blocks"
        description="Choose how many columns your footer has and add reusable blocks (about text, links, contact info…) into each column."
    />

    @if ($flash)
        <x-admin.flash :message="$flash" />
    @endif

    {{-- Zone tabs + inline footer-columns chooser --}}
    <div class="flex flex-wrap items-end justify-between gap-3 border-b border-zinc-200 dark:border-zinc-800">
        <div class="flex flex-wrap gap-2">
            @foreach ($zones as $zone)
                <button type="button" wire:click="selectZone('{{ $zone }}')"
                        @class([
                            '-mb-px px-4 py-2 text-sm font-medium border-b-2 transition',
                            'border-hk-primary-500 text-hk-primary-700 dark:text-hk-primary-300' => $activeZone === $zone,
                            'border-transparent text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200' => $activeZone !== $zone,
                        ])>
                    <span>{{ $this->zoneLabel($zone) }}</span>
                    <span class="ml-1 text-xs text-zinc-400">({{ \App\Models\Widget::where('zone', $zone)->count() }})</span>
                </button>
            @endforeach
        </div>

        <div class="-mb-px flex items-center gap-2 pb-2">
            <label for="footerColumns" class="text-xs text-zinc-500">Footer columns</label>
            <select id="footerColumns" wire:model.live="footerColumns"
                    class="rounded-md border border-zinc-300 bg-white px-2 py-1 text-xs font-medium text-zinc-700 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        {{-- Widget list --}}
        <div class="lg:col-span-8">
            <x-ui.card>
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h3 class="text-sm font-semibold">Blocks in {{ $this->zoneLabel($activeZone) }}</h3>
                        <p class="mt-0.5 text-xs text-zinc-500">Drag to reorder. Click a block to edit its content.</p>
                    </div>
                    <div class="flex items-end gap-2">
                        <div class="min-w-[14rem]">
                            <x-ui.select
                                wire:model="newType"
                                label="Add a block"
                                placeholder="Choose a block type…"
                                :options="$this->availableTypes()"
                            />
                        </div>
                        <x-ui.button size="sm" wire:click="addWidget">+ Add</x-ui.button>
                    </div>
                </div>

                <ul x-sort="$wire.reorder($sortable.toArray())"
                    x-sort:config="{ animation: 150, ghostClass: 'opacity-50' }"
                    class="space-y-2">
                    @forelse ($widgets as $w)
                        @php $meta = $registry->has($w->type) ? $registry->get($w->type) : null; @endphp
                        <li x-sort:item="{{ $w->id }}" wire:key="w-{{ $w->id }}"
                            @class([
                                'rounded-lg border bg-white dark:bg-zinc-900 transition',
                                'border-hk-primary-500 ring-2 ring-hk-primary-500/20' => $editingWidget === $w->id,
                                'border-zinc-200 dark:border-zinc-800' => $editingWidget !== $w->id,
                                'opacity-50' => ! $w->is_active,
                            ])>
                            <div class="flex items-center gap-2 px-3 py-2">
                                <button type="button" x-sort:handle class="cursor-grab text-zinc-400">
                                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-4"><path d="M7 4a1 1 0 110 2 1 1 0 010-2zm6 0a1 1 0 110 2 1 1 0 010-2zM7 9a1 1 0 110 2 1 1 0 010-2zm6 0a1 1 0 110 2 1 1 0 010-2zM7 14a1 1 0 110 2 1 1 0 010-2zm6 0a1 1 0 110 2 1 1 0 010-2z" /></svg>
                                </button>
                                <button type="button" wire:click="editWidget({{ $w->id }})" class="flex-1 text-left text-sm font-medium">
                                    {{ $meta?->name() ?? $w->type }}
                                </button>
                                <button wire:click="toggleActive({{ $w->id }})" class="text-xs {{ $w->is_active ? 'text-green-600' : 'text-zinc-400' }} hover:underline">
                                    {{ $w->is_active ? 'Active' : 'Disabled' }}
                                </button>
                                <button wire:click="deleteWidget({{ $w->id }})" wire:confirm="{{ __('admin.confirm.remove_widget') }}" class="text-xs text-red-600 hover:underline">{{ __('admin.actions.remove') }}</button>
                            </div>
                        </li>
                    @empty
                        <li class="rounded-lg border-2 border-dashed border-zinc-200 bg-zinc-50 p-12 text-center text-sm text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900">
                            Nothing in this column yet. Pick a block type on the right and click <strong>+ Add</strong>.
                        </li>
                    @endforelse
                </ul>
            </x-ui.card>
        </div>

        {{-- Editor --}}
        <div class="lg:col-span-4">
            @php $w = $editingWidget ? $widgets->firstWhere('id', $editingWidget) : null; @endphp
            @if ($w && $registry->has($w->type))
                @php $meta = $registry->get($w->type); @endphp
                <x-ui.card>
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-sm font-semibold">{{ $meta->name() }}</h3>
                        <button wire:click="$set('editingWidget', null)" class="text-xs text-zinc-500 hover:underline">Done</button>
                    </div>
                    <div class="space-y-3">
                        @foreach ($meta->fields() as $field)
                            @php $key = 'editingData.'.$field['key']; @endphp
                            @switch($field['type'] ?? 'text')
                                @case('textarea')
                                    <x-ui.textarea wire:model="{{ $key }}" :label="$field['label']" :rows="$field['rows'] ?? 3" />
                                    @break
                                @case('richtext')
                                    <x-ui.textarea wire:model="{{ $key }}" :label="$field['label']" rows="6" hint="HTML allowed." />
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
                                @default
                                    <x-ui.input :type="$field['type'] ?? 'text'" wire:model="{{ $key }}" :label="$field['label']" />
                            @endswitch
                        @endforeach
                        <div class="flex justify-end pt-2">
                            <x-ui.button size="sm" wire:click="saveWidget">Save widget</x-ui.button>
                        </div>
                    </div>
                </x-ui.card>
            @else
                <div class="rounded-lg border-2 border-dashed border-zinc-200 bg-zinc-50 p-8 text-center text-sm text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900">
                    Click a block on the left to edit its content.
                </div>
            @endif
        </div>
    </div>
</div>
