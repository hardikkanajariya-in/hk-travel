<?php

use App\Core\Settings\SettingsRepository;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Settings')] #[Layout('components.layouts.admin')] class extends Component {
    public string $brandName = '';
    public string $brandTagline = '';
    public string $primaryColor = '#2563eb';
    public string $accentColor = '#f97316';
    public string $defaultLocale = 'en';
    public string $defaultCurrency = 'USD';

    public ?string $flash = null;

    public function mount(SettingsRepository $settings): void
    {
        $this->brandName = (string) $settings->get('brand.name', config('hk.brand.name'));
        $this->brandTagline = (string) $settings->get('brand.tagline', config('hk.brand.tagline'));
        $this->primaryColor = (string) $settings->get('brand.primary_color', '#2563eb');
        $this->accentColor = (string) $settings->get('brand.accent_color', '#f97316');
        $this->defaultLocale = (string) $settings->get('localization.default', 'en');
        $this->defaultCurrency = (string) $settings->get('payments.default_currency', 'USD');
    }

    public function save(SettingsRepository $settings): void
    {
        $this->validate([
            'brandName' => 'required|string|max:120',
            'brandTagline' => 'nullable|string|max:255',
            'primaryColor' => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
            'accentColor' => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
            'defaultLocale' => 'required|string|max:8',
            'defaultCurrency' => 'required|string|size:3',
        ]);

        $settings->setMany([
            'brand.name' => $this->brandName,
            'brand.tagline' => $this->brandTagline,
            'brand.primary_color' => $this->primaryColor,
            'brand.accent_color' => $this->accentColor,
            'localization.default' => $this->defaultLocale,
            'payments.default_currency' => $this->defaultCurrency,
        ]);

        $this->flash = 'Settings saved.';
    }
};

?>

<div class="space-y-6">
    @if ($flash)
        <x-ui.alert variant="success" :dismissible="true">{{ $flash }}</x-ui.alert>
    @endif

    <x-ui.card>
        <h2 class="text-base font-semibold mb-4">Branding</h2>
        <div class="space-y-4">
            <x-ui.input wire:model="brandName" label="Site name" required />
            <x-ui.input wire:model="brandTagline" label="Tagline" />
            <div class="grid grid-cols-2 gap-4">
                <x-ui.input wire:model="primaryColor" label="Primary color" type="color" />
                <x-ui.input wire:model="accentColor" label="Accent color" type="color" />
            </div>
        </div>
    </x-ui.card>

    <x-ui.card>
        <h2 class="text-base font-semibold mb-4">Localization & currency</h2>
        <div class="grid grid-cols-2 gap-4">
            <x-ui.input wire:model="defaultLocale" label="Default locale" required hint="e.g. en, fr, ar" />
            <x-ui.input wire:model="defaultCurrency" label="Default currency" required hint="ISO 4217 code, e.g. USD" />
        </div>
    </x-ui.card>

    <div class="flex justify-end">
        <x-ui.button wire:click="save">Save changes</x-ui.button>
    </div>
</div>
