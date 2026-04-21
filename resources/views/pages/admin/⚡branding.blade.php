<?php

use App\Concerns\SettingsForm;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Branding')] #[Layout('components.layouts.admin')] class extends Component {
    use SettingsForm;

    public function mount(): void
    {
        $this->state = [
            'name' => '',
            'tagline' => '',
            'logo' => null,
            'logo_dark' => null,
            'favicon' => null,
            'primary_color' => '#2563eb',
            'accent_color' => '#f97316',
            'font_family' => 'Inter',
            'show_header' => true,
            'show_footer' => true,
        ];

        $this->loadSettings();
    }

    public function save(): void
    {
        $this->saveSettings();
    }

    public function clearImage(string $key): void
    {
        if (! in_array($key, ['logo', 'logo_dark', 'favicon'], true)) {
            return;
        }

        $this->state[$key] = null;
        $this->saveSettings();
    }

    /** @return array<string, string> */
    protected function settingsKeys(): array
    {
        return [
            'name' => 'brand.name',
            'tagline' => 'brand.tagline',
            'logo' => 'brand.logo',
            'logo_dark' => 'brand.logo_dark',
            'favicon' => 'brand.favicon',
            'primary_color' => 'brand.primary_color',
            'accent_color' => 'brand.accent_color',
            'font_family' => 'brand.font_family',
            'show_header' => 'brand.show_header',
            'show_footer' => 'brand.show_footer',
        ];
    }

    /** @return array<string, mixed> */
    protected function settingsRules(): array
    {
        return [
            'state.name' => 'required|string|max:120',
            'state.tagline' => 'nullable|string|max:255',
            'state.logo' => 'nullable|string|max:500',
            'state.logo_dark' => 'nullable|string|max:500',
            'state.favicon' => 'nullable|string|max:500',
            'state.primary_color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'state.accent_color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'state.font_family' => 'required|string|max:64',
            'state.show_header' => 'boolean',
            'state.show_footer' => 'boolean',
        ];
    }
};

?>

<div class="space-y-6">
    <x-admin.page-header title="Branding" subtitle="Logo, colors, typography, and layout toggles applied site-wide." />

    <x-admin.flash :message="session('settings.saved')" />

    <x-ui.tabs :tabs="['identity' => 'Identity', 'images' => 'Images', 'colors' => 'Colors & fonts', 'layout' => 'Layout']">
        <x-ui.tab-panel name="identity">
            <x-ui.card>
                <h2 class="text-base font-semibold mb-4">Site identity</h2>
                <div class="space-y-4">
                    <x-ui.input wire:model="state.name" label="Site name" required />
                    <x-ui.input wire:model="state.tagline" label="Tagline" />
                </div>
            </x-ui.card>
        </x-ui.tab-panel>

        <x-ui.tab-panel name="images">
            <x-ui.card>
                <h2 class="text-base font-semibold mb-1">Logo (light)</h2>
                <p class="text-xs text-zinc-500 mb-3">Shown on the public site header in light mode. Use a transparent PNG or SVG for best results.</p>
                <x-ui.image-picker wire:model="state.logo" folder="branding" aspect="aspect-[3/1]" />
            </x-ui.card>

            <x-ui.card>
                <h2 class="text-base font-semibold mb-1">Logo (dark mode, optional)</h2>
                <p class="text-xs text-zinc-500 mb-3">Used when visitors switch to dark mode. Pick a version with light-coloured artwork so it stays readable.</p>
                <x-ui.image-picker wire:model="state.logo_dark" folder="branding" aspect="aspect-[3/1]" />
            </x-ui.card>

            <x-ui.card>
                <h2 class="text-base font-semibold mb-1">Favicon</h2>
                <p class="text-xs text-zinc-500 mb-3">The small icon shown in the browser tab. A square PNG or SVG of at least 64×64 pixels works best.</p>
                <x-ui.image-picker wire:model="state.favicon" folder="branding" aspect="aspect-square" />
            </x-ui.card>
        </x-ui.tab-panel>

        <x-ui.tab-panel name="colors">
            <x-ui.card>
                <h2 class="text-base font-semibold mb-4">Brand colors</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Primary</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model="state.primary_color" class="h-10 w-16 rounded border-zinc-300">
                            <x-ui.input wire:model="state.primary_color" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Accent</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model="state.accent_color" class="h-10 w-16 rounded border-zinc-300">
                            <x-ui.input wire:model="state.accent_color" />
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <h2 class="text-base font-semibold mb-4">Typography</h2>
                <label class="block text-sm font-medium mb-1">Font family</label>
                <select wire:model="state.font_family" class="block w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 text-sm">
                    <option value="system">System UI (no download)</option>
                    @foreach (['Inter', 'Roboto', 'Open Sans', 'Lato', 'Poppins', 'Montserrat', 'Nunito', 'Raleway', 'Source Sans 3', 'Work Sans', 'Plus Jakarta Sans', 'Manrope', 'DM Sans'] as $f)
                        <option value="{{ $f }}">{{ $f }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-zinc-500 mt-2">Loaded via Bunny Fonts (privacy-friendly Google Fonts mirror).</p>
            </x-ui.card>
        </x-ui.tab-panel>

        <x-ui.tab-panel name="layout">
            <x-ui.card>
                <h2 class="text-base font-semibold mb-4">Layout toggles</h2>
                <div class="space-y-3">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="state.show_header" class="size-4 rounded border-zinc-300 text-hk-primary-600">
                        <span class="text-sm">Show site header on public pages</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="state.show_footer" class="size-4 rounded border-zinc-300 text-hk-primary-600">
                        <span class="text-sm">Show site footer on public pages</span>
                    </label>
                </div>
            </x-ui.card>
        </x-ui.tab-panel>
    </x-ui.tabs>

    <div class="flex justify-end">
        <x-ui.button wire:click="save">Save changes</x-ui.button>
    </div>
</div>
