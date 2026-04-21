<?php

use App\Core\Settings\SettingsRepository;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Modules')] #[Layout('components.layouts.admin')] class extends Component {
    /** @var array<string, bool> */
    public array $enabled = [];

    public ?string $flash = null;

    public function mount(SettingsRepository $settings): void
    {
        foreach (config('hk-modules.modules', []) as $key => $module) {
            $this->enabled[$key] = (bool) $settings->get("modules.$key.enabled", $module['enabled'] ?? false);
        }
    }

    #[Computed]
    public function modules(): array
    {
        return config('hk-modules.modules', []);
    }

    public function save(SettingsRepository $settings): void
    {
        foreach ($this->enabled as $key => $value) {
            $settings->set("modules.$key.enabled", (bool) $value);
        }

        $settings->flush();
        $this->flash = 'Module preferences saved. Reload the page for changes to take effect.';
    }
};

?>

<div class="space-y-6">
    @if ($flash)
        <x-ui.alert variant="success" :dismissible="true">{{ $flash }}</x-ui.alert>
    @endif

    <x-ui.card>
        <div class="mb-4">
            <h2 class="text-base font-semibold">Travel modules</h2>
            <p class="text-sm text-zinc-500">Toggle entire feature areas on or off. Disabled modules add no routes, menus, or database load.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach ($this->modules as $key => $module)
                <label class="flex items-start gap-3 rounded-md border border-zinc-200 dark:border-zinc-800 p-3 cursor-pointer hover:border-hk-primary-400 transition">
                    <input type="checkbox"
                           wire:model.live="enabled.{{ $key }}"
                           class="mt-0.5 size-4 rounded border-zinc-300 text-hk-primary-600 focus:ring-hk-primary-500">
                    <span class="flex-1">
                        <span class="block text-sm font-medium">{{ $module['label'] ?? $key }}</span>
                        <span class="block text-xs text-zinc-500 font-mono">{{ $key }}</span>
                    </span>
                    @if ($enabled[$key] ?? false)
                        <x-ui.badge variant="success" size="sm">On</x-ui.badge>
                    @else
                        <x-ui.badge variant="neutral" size="sm">Off</x-ui.badge>
                    @endif
                </label>
            @endforeach
        </div>

        <div class="mt-6 flex justify-end">
            <x-ui.button wire:click="save">Save changes</x-ui.button>
        </div>
    </x-ui.card>
</div>
