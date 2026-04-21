<?php

use App\Core\Permalink\PermalinkRouter;
use App\Models\PermalinkRedirect;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Title('Permalinks & redirects')] #[Layout('components.layouts.admin')] class extends Component {
    /** @var array<string, string> */
    public array $patterns = [];

    /** @var array<string, string> */
    public array $defaults = [];

    public ?string $flash = null;

    /** @var array<string, array<int, string>> */
    public array $collisions = [];

    // Redirect form
    #[Validate('required|string|max:255|starts_with:/')]
    public string $from_path = '';

    #[Validate('required|string|max:255')]
    public string $to_path = '';

    #[Validate('integer|in:301,302,307,308')]
    public int $status_code = 301;

    public ?int $editingRedirectId = null;

    public function mount(PermalinkRouter $router): void
    {
        $this->defaults = (array) config('hk.permalinks', []);
        $current = $router->all();

        foreach ($this->defaults as $entity => $pattern) {
            $this->patterns[$entity] = $current[$entity] ?? $pattern;
        }
    }

    public function savePatterns(PermalinkRouter $router): void
    {
        $this->collisions = [];
        $messages = [];

        foreach ($this->patterns as $entity => $pattern) {
            $pattern = trim($pattern);
            if ($pattern === '') {
                continue;
            }
            $normalized = $router->normalize($pattern);
            $clashes = $router->collisions($entity, $normalized);

            if ($clashes->isNotEmpty()) {
                $this->collisions[$entity] = $clashes->pluck('entity_type')->all();
                $messages[] = $entity.' conflicts with '.$clashes->pluck('entity_type')->implode(', ');

                continue;
            }

            $router->set($entity, $normalized);
        }

        $router->flush();

        if ($messages !== []) {
            $this->flash = 'Saved with collisions: '.implode('; ', $messages);
        } else {
            $this->flash = 'Permalink patterns saved.';
        }
    }

    public function saveRedirect(): void
    {
        $this->validate();

        $data = [
            'from_path' => '/'.ltrim($this->from_path, '/'),
            'to_path' => str_starts_with($this->to_path, 'http') ? $this->to_path : '/'.ltrim($this->to_path, '/'),
            'status_code' => $this->status_code,
            'is_active' => true,
        ];

        if ($this->editingRedirectId) {
            PermalinkRedirect::where('id', $this->editingRedirectId)->update($data);
        } else {
            PermalinkRedirect::updateOrCreate(['from_path' => $data['from_path']], $data);
        }

        $this->resetRedirectForm();
        $this->flash = 'Redirect saved.';
    }

    public function editRedirect(int $id): void
    {
        $r = PermalinkRedirect::findOrFail($id);
        $this->editingRedirectId = $r->id;
        $this->from_path = $r->from_path;
        $this->to_path = $r->to_path;
        $this->status_code = $r->status_code;
    }

    public function toggleRedirect(int $id): void
    {
        $r = PermalinkRedirect::findOrFail($id);
        $r->update(['is_active' => ! $r->is_active]);
    }

    public function deleteRedirect(int $id): void
    {
        PermalinkRedirect::where('id', $id)->delete();
    }

    public function with(): array
    {
        return [
            'redirects' => PermalinkRedirect::orderByDesc('id')->paginate(20),
        ];
    }

    protected function resetRedirectForm(): void
    {
        $this->reset(['from_path', 'to_path', 'editingRedirectId']);
        $this->status_code = 301;
    }
};

?>

<div class="space-y-6">
    <x-admin.page-header title="Permalinks & redirects" subtitle="Customize URL patterns per entity and manage 301/302 redirects." />

    @if ($flash)
        <x-admin.flash :message="$flash" />
    @endif

    <x-ui.tabs :tabs="['patterns' => 'URL patterns', 'redirects' => '301 redirects']">
        <x-ui.tab-panel name="patterns">
            <x-ui.card>
                <h2 class="text-base font-semibold mb-2">Entity URL patterns</h2>
                <p class="text-sm text-zinc-500 mb-4">Use tokens like <code>{slug}</code>, <code>{country}</code>, <code>{city}</code>. Leading slash required.</p>

                <div class="space-y-3">
                    @foreach ($patterns as $entity => $pattern)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 items-center">
                            <div class="text-sm font-medium">{{ $entity }}</div>
                            <div class="md:col-span-2">
                                <x-ui.input wire:model="patterns.{{ $entity }}" :hint="'Default: '.($defaults[$entity] ?? '/')" />
                                @if (isset($collisions[$entity]))
                                    <p class="text-xs text-red-600 mt-1">Collides with: {{ implode(', ', $collisions[$entity]) }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 flex justify-end">
                    <x-ui.button wire:click="savePatterns">Save patterns</x-ui.button>
                </div>
            </x-ui.card>
        </x-ui.tab-panel>

        <x-ui.tab-panel name="redirects">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <x-ui.card padding="none">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-sm">
                                <thead class="bg-zinc-50 dark:bg-zinc-900/40 text-xs uppercase tracking-wide text-zinc-500 text-left">
                                    <tr>
                                        <th class="px-4 py-2">From</th>
                                        <th class="px-4 py-2">To</th>
                                        <th class="px-4 py-2">Code</th>
                                        <th class="px-4 py-2">Hits</th>
                                        <th class="px-4 py-2">Active</th>
                                        <th class="px-4 py-2"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-900">
                                    @forelse ($redirects as $r)
                                        <tr>
                                            <td class="px-4 py-2 font-mono text-xs">{{ $r->from_path }}</td>
                                            <td class="px-4 py-2 font-mono text-xs">{{ $r->to_path }}</td>
                                            <td class="px-4 py-2">{{ $r->status_code }}</td>
                                            <td class="px-4 py-2">{{ $r->hit_count }}</td>
                                            <td class="px-4 py-2">
                                                <button type="button" wire:click="toggleRedirect({{ $r->id }})">
                                                    @if ($r->is_active)<x-ui.badge variant="success" size="sm">On</x-ui.badge>@else<x-ui.badge variant="neutral" size="sm">Off</x-ui.badge>@endif
                                                </button>
                                            </td>
                                            <td class="px-4 py-2 text-right">
                                                <button wire:click="editRedirect({{ $r->id }})" class="text-xs text-hk-primary-600 hover:underline">Edit</button>
                                                <button wire:click="deleteRedirect({{ $r->id }})" wire:confirm="Delete?" class="text-xs text-red-600 hover:underline ml-2">Delete</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-zinc-500">No redirects configured.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="border-t border-zinc-200 dark:border-zinc-800 px-4 py-3">{{ $redirects->links() }}</div>
                    </x-ui.card>
                </div>

                <x-ui.card>
                    <h2 class="text-base font-semibold mb-4">{{ $editingRedirectId ? 'Edit redirect' : 'Add redirect' }}</h2>
                    <div class="space-y-3">
                        <x-ui.input wire:model="from_path" label="From path" required hint="e.g. /old-tour-name" />
                        <x-ui.input wire:model="to_path" label="To path / URL" required />
                        <div>
                            <label class="block text-sm font-medium mb-1">Status code</label>
                            <select wire:model="status_code" class="block w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 text-sm">
                                <option value="301">301 — Permanent</option>
                                <option value="302">302 — Found (temporary)</option>
                                <option value="307">307 — Temporary preserve method</option>
                                <option value="308">308 — Permanent preserve method</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        @if ($editingRedirectId)
                            <button type="button" wire:click="$set('editingRedirectId', null)" class="text-sm text-zinc-500">Cancel</button>
                        @endif
                        <x-ui.button wire:click="saveRedirect">{{ $editingRedirectId ? 'Update' : 'Add' }}</x-ui.button>
                    </div>
                </x-ui.card>
            </div>
        </x-ui.tab-panel>
    </x-ui.tabs>
</div>
