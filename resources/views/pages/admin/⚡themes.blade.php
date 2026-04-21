<?php

use App\Core\Settings\SettingsRepository;
use App\Core\Theme\ThemeManager;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Themes')] #[Layout('components.layouts.admin')] class extends Component {
    public string $active = 'default';

    public ?string $flash = null;

    public function mount(SettingsRepository $settings): void
    {
        $this->active = (string) $settings->get('theme.active', config('hk.theme.active', 'default'));
    }

    public function activate(string $key, ThemeManager $themes, SettingsRepository $settings): void
    {
        if (! $themes->all()->has($key)) {
            $this->flash = "Theme [$key] is not installed.";

            return;
        }

        $settings->set('theme.active', $key);
        $this->active = $key;
        $this->flash = "Activated theme: $key. The next public request will use it.";
    }

    public function with(): array
    {
        return [
            'themes' => app(ThemeManager::class)->all(),
        ];
    }
};

?>

<div class="space-y-6">
    <x-admin.page-header title="Themes" description="Pick a public-facing theme. The active theme is loaded on every front-end request." />

    @if ($flash)
        <x-ui.alert variant="success" :dismissible="true">{{ $flash }}</x-ui.alert>
    @endif

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($themes as $key => $theme)
            <x-ui.card padding="none">
                <div class="aspect-video w-full overflow-hidden rounded-t-md bg-gradient-to-br from-hk-primary-500 to-hk-primary-800">
                    @if ($theme->screenshot)
                        <img src="{{ $theme->screenshot }}" alt="{{ $theme->name }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full items-center justify-center text-2xl font-bold text-white opacity-80">
                            {{ $theme->name }}
                        </div>
                    @endif
                </div>

                <div class="space-y-3 p-5">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h3 class="text-base font-semibold">{{ $theme->name }}</h3>
                            <p class="text-xs text-zinc-500">v{{ $theme->version }} · {{ $theme->author }}</p>
                        </div>
                        @if ($active === $key)
                            <x-ui.badge variant="success" size="sm">Active</x-ui.badge>
                        @endif
                    </div>

                    @if ($theme->description)
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $theme->description }}</p>
                    @endif

                    <div class="flex flex-wrap gap-1">
                        @foreach ($theme->supports as $feature => $value)
                            @if ($value)
                                <x-ui.badge variant="neutral" size="sm">
                                    {{ str_replace('_', ' ', $feature) }}
                                </x-ui.badge>
                            @endif
                        @endforeach
                    </div>

                    <div class="pt-2">
                        @if ($active === $key)
                            <x-ui.button variant="outline" size="sm" disabled>Currently active</x-ui.button>
                        @else
                            <x-ui.button wire:click="activate('{{ $key }}')" size="sm">Activate</x-ui.button>
                        @endif
                    </div>
                </div>
            </x-ui.card>
        @endforeach
    </div>
</div>
