<?php

use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component {
    #[Locked]
    public array $recoveryCodes = [];

    public function mount(): void
    {
        $this->loadRecoveryCodes();
    }

    public function regenerateRecoveryCodes(GenerateNewRecoveryCodes $generateNewRecoveryCodes): void
    {
        $generateNewRecoveryCodes(auth()->user());
        $this->loadRecoveryCodes();
    }

    private function loadRecoveryCodes(): void
    {
        $user = auth()->user();

        if ($user->hasEnabledTwoFactorAuthentication() && $user->two_factor_recovery_codes) {
            try {
                $this->recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
            } catch (Exception) {
                $this->addError('recoveryCodes', 'Failed to load recovery codes');
                $this->recoveryCodes = [];
            }
        }
    }
};

?>

<div
    class="py-6 space-y-5 border shadow-sm rounded-xl border-zinc-200 dark:border-zinc-800"
    wire:cloak
    x-data="{ showRecoveryCodes: false }"
>
    <div class="px-6 space-y-1">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('2FA recovery codes') }}</h3>
        <p class="text-sm text-zinc-500">{{ __('Recovery codes let you regain access if you lose your 2FA device. Store them in a secure password manager.') }}</p>
    </div>

    <div class="px-6 space-y-3">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <x-ui.button type="button" x-show="!showRecoveryCodes" @click="showRecoveryCodes = true">
                {{ __('View recovery codes') }}
            </x-ui.button>

            <x-ui.button type="button" variant="secondary" x-show="showRecoveryCodes" @click="showRecoveryCodes = false">
                {{ __('Hide recovery codes') }}
            </x-ui.button>

            @if (filled($recoveryCodes))
                <x-ui.button type="button" variant="secondary" x-show="showRecoveryCodes" wire:click="regenerateRecoveryCodes">
                    {{ __('Regenerate codes') }}
                </x-ui.button>
            @endif
        </div>

        <div x-show="showRecoveryCodes" x-transition x-cloak class="space-y-3">
            @error('recoveryCodes')
                <x-ui.alert variant="danger">{{ $message }}</x-ui.alert>
            @enderror

            @if (filled($recoveryCodes))
                <div class="grid gap-1 p-4 font-mono text-sm rounded-lg bg-zinc-100 dark:bg-zinc-800/60" role="list" aria-label="{{ __('Recovery codes') }}">
                    @foreach ($recoveryCodes as $code)
                        <div role="listitem" class="select-text" wire:loading.class="opacity-50 animate-pulse">{{ $code }}</div>
                    @endforeach
                </div>
                <p class="text-xs text-zinc-500">
                    {{ __('Each recovery code can be used once and will be removed after use.') }}
                </p>
            @endif
        </div>
    </div>
</div>
