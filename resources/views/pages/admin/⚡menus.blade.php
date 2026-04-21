<?php

use App\Models\Language;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Title('Menus')] #[Layout('components.layouts.admin')] class extends Component {
    public ?int $menuId = null;

    public string $editingLocale = 'en';

    /** @var array<int, string> */
    public array $supportedLocales = ['en'];

    /** @var array<int, string> */
    public array $themeLocations = [];

    // Menu form
    #[Validate('required|string|max:120')]
    public string $menuName = '';

    #[Validate('required|string|max:120|alpha_dash')]
    public string $menuSlug = '';

    public ?string $menuLocation = null;

    public ?int $editingMenuId = null;

    // Item form
    public ?int $editingItemId = null;

    public ?int $itemParentId = null;

    public string $itemLabel = '';

    public string $itemUrl = '';

    public string $itemTarget = '_self';

    public string $itemLinkType = 'custom';

    public ?int $itemPageId = null;

    public ?string $itemRouteName = null;

    public ?string $itemCssClass = null;

    public ?string $flash = null;

    public function mount(): void
    {
        $this->supportedLocales = (array) (config('hk.localization.supported') ?? ['en']);
        $this->editingLocale = (string) (config('hk.localization.default') ?? 'en');

        $themeKey = (string) app(\App\Core\Settings\SettingsRepository::class)->get('theme.active', config('hk.theme.active', 'default'));
        $theme = app(\App\Core\Theme\ThemeManager::class)->all()->get($themeKey);
        $this->themeLocations = (array) ($theme?->supports['header_menus'] ?? ['primary', 'footer']);

        $first = Menu::orderBy('id')->first();
        $this->menuId = $first?->id;
    }

    public function selectMenu(int $id): void
    {
        $this->menuId = $id;
        $this->resetItemForm();
    }

    public function openCreateMenu(): void
    {
        $this->reset(['menuName', 'menuSlug', 'menuLocation', 'editingMenuId']);
    }

    public function editMenu(int $id): void
    {
        $m = Menu::findOrFail($id);
        $this->editingMenuId = $m->id;
        $this->menuName = $m->name;
        $this->menuSlug = $m->slug;
        $this->menuLocation = $m->location;
    }

    public function saveMenu(): void
    {
        $this->validate([
            'menuName' => ['required', 'string', 'max:120'],
            'menuSlug' => ['required', 'string', 'max:120', 'alpha_dash', \Illuminate\Validation\Rule::unique('menus', 'slug')->ignore($this->editingMenuId)],
            'menuLocation' => ['nullable', 'string', 'max:64'],
        ]);

        $payload = [
            'name' => $this->menuName,
            'slug' => $this->menuSlug,
            'location' => $this->menuLocation ?: null,
        ];

        if ($payload['location']) {
            // Only one menu may bind to a given location.
            Menu::query()->where('location', $payload['location'])
                ->when($this->editingMenuId, fn ($q) => $q->where('id', '!=', $this->editingMenuId))
                ->update(['location' => null]);
        }

        if ($this->editingMenuId) {
            Menu::where('id', $this->editingMenuId)->update($payload);
            $this->menuId = $this->editingMenuId;
        } else {
            $menu = Menu::create($payload);
            $this->menuId = $menu->id;
        }

        $this->reset(['menuName', 'menuSlug', 'menuLocation', 'editingMenuId']);
        $this->flash = 'Menu saved.';
    }

    public function deleteMenu(int $id): void
    {
        Menu::where('id', $id)->delete();
        if ($this->menuId === $id) {
            $this->menuId = Menu::orderBy('id')->value('id');
        }
        $this->flash = 'Menu deleted.';
    }

    public function updatedMenuName(): void
    {
        if ($this->menuSlug === '' && $this->editingMenuId === null) {
            $this->menuSlug = Str::slug($this->menuName);
        }
    }

    // Items
    public function newItem(?int $parentId = null): void
    {
        $this->resetItemForm();
        $this->itemParentId = $parentId;
    }

    public function editItem(int $id): void
    {
        $i = MenuItem::findOrFail($id);
        $this->editingItemId = $i->id;
        $this->itemParentId = $i->parent_id;
        $this->itemLabel = $i->translations[$this->editingLocale]['label'] ?? $i->label;
        $this->itemUrl = $i->translations[$this->editingLocale]['url'] ?? (string) $i->url;
        $this->itemTarget = $i->target;
        $this->itemLinkType = $i->link_type;
        $this->itemPageId = $i->link_target['page_id'] ?? null;
        $this->itemRouteName = $i->link_target['name'] ?? null;
        $this->itemCssClass = $i->css_class;
    }

    public function saveItem(): void
    {
        $this->validate([
            'itemLabel' => ['required', 'string', 'max:160'],
            'itemUrl' => ['nullable', 'string', 'max:500'],
            'itemTarget' => ['in:_self,_blank'],
            'itemLinkType' => ['in:custom,route,page,permalink'],
        ]);

        if (! $this->menuId) {
            $this->flash = 'Pick a menu first.';

            return;
        }

        $linkTarget = match ($this->itemLinkType) {
            'page' => ['page_id' => $this->itemPageId],
            'route' => ['name' => $this->itemRouteName, 'params' => []],
            default => null,
        };

        if ($this->editingItemId) {
            $item = MenuItem::findOrFail($this->editingItemId);
            $translations = (array) $item->translations;
            $translations[$this->editingLocale] = ['label' => $this->itemLabel, 'url' => $this->itemUrl ?: null];

            $item->update([
                'parent_id' => $this->itemParentId,
                'label' => $this->editingLocale === ($this->supportedLocales[0] ?? 'en') ? $this->itemLabel : $item->label,
                'url' => $this->itemLinkType === 'custom' ? ($this->itemUrl ?: null) : null,
                'target' => $this->itemTarget,
                'link_type' => $this->itemLinkType,
                'link_target' => $linkTarget,
                'translations' => $translations,
                'css_class' => $this->itemCssClass,
            ]);
        } else {
            $position = MenuItem::where('menu_id', $this->menuId)
                ->where('parent_id', $this->itemParentId)
                ->max('position') ?? -1;

            MenuItem::create([
                'menu_id' => $this->menuId,
                'parent_id' => $this->itemParentId,
                'label' => $this->itemLabel,
                'url' => $this->itemLinkType === 'custom' ? ($this->itemUrl ?: null) : null,
                'target' => $this->itemTarget,
                'link_type' => $this->itemLinkType,
                'link_target' => $linkTarget,
                'translations' => [$this->editingLocale => ['label' => $this->itemLabel, 'url' => $this->itemUrl ?: null]],
                'css_class' => $this->itemCssClass,
                'position' => $position + 1,
            ]);
        }

        $this->resetItemForm();
        $this->flash = 'Item saved.';
    }

    public function deleteItem(int $id): void
    {
        MenuItem::where('id', $id)->delete();
        $this->flash = 'Item deleted.';
    }

    /** @param array<int, int|string> $order */
    public function reorderItems(array $order, ?int $parentId = null): void
    {
        foreach ($order as $position => $id) {
            MenuItem::where('id', (int) $id)
                ->where('menu_id', $this->menuId)
                ->update(['parent_id' => $parentId, 'position' => $position]);
        }
    }

    protected function resetItemForm(): void
    {
        $this->reset(['editingItemId', 'itemParentId', 'itemLabel', 'itemUrl', 'itemPageId', 'itemRouteName', 'itemCssClass']);
        $this->itemTarget = '_self';
        $this->itemLinkType = 'custom';
    }

    public function with(): array
    {
        $menus = Menu::orderBy('name')->get();

        // Map of location-key => menu currently bound there. Used to surface
        // "already used by X" hints inside the location dropdown so users
        // never wonder why two menus seem to fight over the same slot.
        $usage = $menus->whereNotNull('location')->keyBy('location');

        $friendly = [
            'primary' => 'Header navigation (top of every page)',
            'header' => 'Header navigation (top of every page)',
            'footer' => 'Footer navigation (bottom of every page)',
            'mobile' => 'Mobile menu (small screens)',
            'sidebar' => 'Sidebar navigation',
        ];

        $locationOptions = ['' => 'Not shown anywhere yet (draft)'];
        foreach ($this->themeLocations as $loc) {
            $label = $friendly[$loc] ?? \Illuminate\Support\Str::headline((string) $loc);
            $taken = $usage->get($loc);
            if ($taken && $taken->id !== $this->editingMenuId) {
                $label .= ' — currently used by "'.$taken->name.'" (will be reassigned)';
            }
            $locationOptions[$loc] = $label;
        }

        return [
            'menus' => $menus,
            'currentMenu' => $this->menuId ? Menu::with('items.children.children')->find($this->menuId) : null,
            'pages' => Page::orderBy('title')->get(['id', 'title', 'slug']),
            'languages' => Language::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'locationOptions' => $locationOptions,
            'locationCount' => count($this->themeLocations),
            'locationsFilled' => $usage->only($this->themeLocations)->count(),
        ];
    }
};

?>

<div class="space-y-6">
    <x-admin.page-header title="Menus" description="Build navigation per location, with nested items and per-locale labels." />

    @if ($flash)
        <x-admin.flash :message="$flash" />
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        {{-- Menu list & create --}}
        <div class="lg:col-span-3 space-y-4">
            <x-ui.card>
                <h3 class="text-sm font-semibold mb-3">Menus</h3>
                <ul class="space-y-1">
                    @forelse ($menus as $m)
                        <li>
                            <button type="button" wire:click="selectMenu({{ $m->id }})"
                                    @class([
                                        'flex w-full items-center justify-between gap-2 rounded-md px-3 py-2 text-sm transition',
                                        'bg-hk-primary-50 text-hk-primary-700 dark:bg-hk-primary-950 dark:text-hk-primary-300' => $menuId === $m->id,
                                        'hover:bg-zinc-100 dark:hover:bg-zinc-800' => $menuId !== $m->id,
                                    ])>
                                <span class="font-medium">{{ $m->name }}</span>
                                @if ($m->location)
                                    @php
                                        $shortLabel = match ($m->location) {
                                            'primary', 'header' => 'Header',
                                            'footer' => 'Footer',
                                            'mobile' => 'Mobile',
                                            'sidebar' => 'Sidebar',
                                            default => \Illuminate\Support\Str::headline($m->location),
                                        };
                                    @endphp
                                    <x-ui.badge variant="info" size="sm">{{ $shortLabel }}</x-ui.badge>
                                @endif
                            </button>
                        </li>
                    @empty
                        <li class="text-xs text-zinc-500">No menus yet.</li>
                    @endforelse
                </ul>
            </x-ui.card>

            <x-ui.card>
                <h3 class="text-sm font-semibold">{{ $editingMenuId ? 'Edit menu' : 'New menu' }}</h3>
                <p class="mb-3 mt-1 text-xs text-zinc-500">
                    Your active theme provides
                    <strong class="text-zinc-700 dark:text-zinc-300">{{ $locationCount }}</strong>
                    display {{ \Illuminate\Support\Str::plural('spot', $locationCount) }}
                    ({{ $locationsFilled }} filled). Pick where this menu should appear, or save it as a draft.
                </p>
                <div class="space-y-3">
                    <x-ui.input wire:model.live="menuName" label="Name" :error="$errors->first('menuName')" />
                    <x-ui.input wire:model="menuSlug" label="Slug" hint="Used internally — letters, numbers and dashes only." :error="$errors->first('menuSlug')" />
                    <x-ui.select
                        wire:model="menuLocation"
                        label="Where should it appear?"
                        hint="Display spots are defined by the active theme."
                        :options="$locationOptions"
                        :error="$errors->first('menuLocation')"
                    />
                    <div class="flex justify-between gap-2 pt-1">
                        @if ($editingMenuId)
                            <button type="button" wire:click="openCreateMenu" class="text-xs text-zinc-500 hover:underline">Cancel edit</button>
                        @else
                            <span></span>
                        @endif
                        <x-ui.button size="sm" wire:click="saveMenu">{{ $editingMenuId ? 'Update' : 'Create' }}</x-ui.button>
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- Menu items --}}
        <div class="lg:col-span-6">
            @if ($currentMenu)
                <x-ui.card>
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold">{{ $currentMenu->name }}</h3>
                            <p class="text-xs text-zinc-500">{{ $currentMenu->items->count() }} items</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button wire:click="editMenu({{ $currentMenu->id }})" class="text-xs text-hk-primary-600 hover:underline">Settings</button>
                            <button wire:click="deleteMenu({{ $currentMenu->id }})" wire:confirm="{{ __('admin.confirm.delete_menu') }}" class="text-xs text-red-600 hover:underline">{{ __('admin.actions.delete') }}</button>
                            <x-ui.button size="sm" wire:click="newItem">+ Add item</x-ui.button>
                        </div>
                    </div>

                    @php $rootItems = $currentMenu->items->whereNull('parent_id')->sortBy('position')->values(); @endphp

                    <ul x-sort="$wire.reorderItems($sortable.toArray(), null)"
                        x-sort:config="{ animation: 150, ghostClass: 'opacity-50' }"
                        class="space-y-1">
                        @forelse ($rootItems as $item)
                            <li x-sort:item="{{ $item->id }}" wire:key="mi-{{ $item->id }}" class="rounded-md border border-zinc-200 dark:border-zinc-800">
                                <div class="flex items-center gap-2 px-3 py-2">
                                    <button type="button" x-sort:handle class="cursor-grab text-zinc-400 hover:text-zinc-600">
                                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-4"><path d="M7 4a1 1 0 110 2 1 1 0 010-2zm6 0a1 1 0 110 2 1 1 0 010-2zM7 9a1 1 0 110 2 1 1 0 010-2zm6 0a1 1 0 110 2 1 1 0 010-2zM7 14a1 1 0 110 2 1 1 0 010-2zm6 0a1 1 0 110 2 1 1 0 010-2z" /></svg>
                                    </button>
                                    <span class="flex-1 text-sm font-medium">{{ $item->localizedLabel($editingLocale) }}</span>
                                    <span class="font-mono text-xs text-zinc-400">{{ $item->resolveUrl($editingLocale) }}</span>
                                    <button wire:click="newItem({{ $item->id }})" class="text-xs text-hk-primary-600 hover:underline">+ Child</button>
                                    <button wire:click="editItem({{ $item->id }})" class="text-xs text-zinc-500 hover:underline">Edit</button>
                                    <button wire:click="deleteItem({{ $item->id }})" wire:confirm="{{ __('admin.confirm.delete_item') }}" class="text-xs text-red-600 hover:underline">{{ __('admin.actions.delete') }}</button>
                                </div>

                                @php $children = $item->children->sortBy('position')->values(); @endphp
                                @if ($children->isNotEmpty())
                                    <ul x-sort="$wire.reorderItems($sortable.toArray(), {{ $item->id }})"
                                        class="ml-8 mb-2 space-y-1 border-l border-zinc-200 pl-3 dark:border-zinc-800">
                                        @foreach ($children as $child)
                                            <li x-sort:item="{{ $child->id }}" wire:key="mi-{{ $child->id }}" class="rounded border border-zinc-200 dark:border-zinc-800">
                                                <div class="flex items-center gap-2 px-3 py-2">
                                                    <button type="button" x-sort:handle class="cursor-grab text-zinc-400">
                                                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-4"><path d="M7 4a1 1 0 110 2 1 1 0 010-2zm6 0a1 1 0 110 2 1 1 0 010-2zM7 9a1 1 0 110 2 1 1 0 010-2zm6 0a1 1 0 110 2 1 1 0 010-2zM7 14a1 1 0 110 2 1 1 0 010-2zm6 0a1 1 0 110 2 1 1 0 010-2z" /></svg>
                                                    </button>
                                                    <span class="flex-1 text-sm">{{ $child->localizedLabel($editingLocale) }}</span>
                                                    <span class="font-mono text-xs text-zinc-400">{{ $child->resolveUrl($editingLocale) }}</span>
                                                    <button wire:click="editItem({{ $child->id }})" class="text-xs text-zinc-500 hover:underline">Edit</button>
                                                    <button wire:click="deleteItem({{ $child->id }})" wire:confirm="{{ __('admin.confirm.delete') }}" class="text-xs text-red-600 hover:underline">{{ __('admin.actions.delete') }}</button>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @empty
                            <li class="rounded-md border-2 border-dashed border-zinc-200 bg-zinc-50 p-8 text-center text-sm text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900">
                                No items yet. Click <strong>Add item</strong> to start.
                            </li>
                        @endforelse
                    </ul>
                </x-ui.card>
            @else
                <x-ui.card>
                    <p class="text-center text-sm text-zinc-500 py-12">Select or create a menu on the left.</p>
                </x-ui.card>
            @endif
        </div>

        {{-- Item form --}}
        <div class="lg:col-span-3">
            <x-ui.card>
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-sm font-semibold">{{ $editingItemId ? 'Edit item' : 'New item' }}</h3>
                    @if (count($supportedLocales) > 1)
                        <select wire:model.live="editingLocale" class="rounded-md border border-zinc-300 px-2 py-1 text-xs dark:border-zinc-700 dark:bg-zinc-900">
                            @foreach ($supportedLocales as $loc)
                                <option value="{{ $loc }}">{{ strtoupper($loc) }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div class="space-y-3">
                    <x-ui.input wire:model="itemLabel" :label="'Label ('.strtoupper($editingLocale).')'" :error="$errors->first('itemLabel')" />

                    <div>
                        <label class="block text-sm font-medium mb-1">Link type</label>
                        <select wire:model.live="itemLinkType" class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                            <option value="custom">Custom URL</option>
                            <option value="page">CMS page</option>
                            <option value="route">Named route</option>
                        </select>
                    </div>

                    @if ($itemLinkType === 'custom')
                        <x-ui.input wire:model="itemUrl" :label="'URL ('.strtoupper($editingLocale).')'" />
                    @elseif ($itemLinkType === 'page')
                        <div>
                            <label class="block text-sm font-medium mb-1">Page</label>
                            <select wire:model="itemPageId" class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                                <option value="">— pick a page —</option>
                                @foreach ($pages as $p)
                                    <option value="{{ $p->id }}">{{ $p->title }} (/{{ $p->slug }})</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif ($itemLinkType === 'route')
                        <x-ui.input wire:model="itemRouteName" label="Internal page reference (advanced)" placeholder="admin.dashboard" hint="Optional. Lets the menu link to a built-in page even if its address changes later." />
                    @endif

                    <div>
                        <label class="block text-sm font-medium mb-1">Open in</label>
                        <select wire:model="itemTarget" class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                            <option value="_self">Same tab</option>
                            <option value="_blank">New tab</option>
                        </select>
                    </div>

                    <x-ui.input wire:model="itemCssClass" label="CSS class" placeholder="optional" />

                    @if ($itemParentId)
                        <p class="text-xs text-zinc-500">Will be added under item #{{ $itemParentId }}.</p>
                    @endif

                    <div class="flex justify-end gap-2">
                        @if ($editingItemId)
                            <button type="button" wire:click="newItem" class="text-xs text-zinc-500 hover:underline">Cancel</button>
                        @endif
                        <x-ui.button size="sm" wire:click="saveItem">{{ $editingItemId ? 'Update' : 'Add' }}</x-ui.button>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>
</div>
