<?php

use App\Core\Modules\ModuleManager;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Dashboard')] #[Layout('components.layouts.admin')] class extends Component {
    #[Computed]
    public function stats(): array
    {
        $modules = app(ModuleManager::class);

        return [
            ['label' => 'Active modules', 'value' => $modules->all()->count()],
            ['label' => 'Available modules', 'value' => count(config('hk-modules.modules', []))],
            ['label' => 'Theme', 'value' => config('hk.theme.active', 'default')],
            ['label' => 'Locale', 'value' => app()->getLocale()],
        ];
    }
};

?>

<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ($this->stats as $stat)
            <x-ui.card>
                <div class="text-xs uppercase tracking-wide text-zinc-500">{{ $stat['label'] }}</div>
                <div class="mt-1 text-2xl font-semibold">{{ $stat['value'] }}</div>
            </x-ui.card>
        @endforeach
    </div>

    <x-ui.card>
        <h2 class="text-base font-semibold mb-2">Welcome to {{ config('hk.brand.name') }}</h2>
        <p class="text-sm text-zinc-600 dark:text-zinc-400">
            Your travel platform is ready. Use the sidebar to enable modules, adjust settings, and start configuring your site.
        </p>
    </x-ui.card>
</div>
