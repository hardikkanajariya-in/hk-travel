<?php

use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    #[Locked]
    public bool $requiresConfirmation;

    #[Locked]
    public string $qrCodeSvg = '';

    #[Locked]
    public string $manualSetupKey = '';

    public bool $showVerificationStep = false;

    public bool $setupComplete = false;

    #[Validate('required|string|size:6', onUpdate: false)]
    public string $code = '';

    public function mount(bool $requiresConfirmation): void
    {
        $this->requiresConfirmation = $requiresConfirmation;
    }

    #[On('start-two-factor-setup')]
    public function startTwoFactorSetup(): void
    {
        $enableTwoFactorAuthentication = app(EnableTwoFactorAuthentication::class);
        $enableTwoFactorAuthentication(auth()->user());

        $this->loadSetupData();
    }

    private function loadSetupData(): void
    {
        $user = auth()->user()?->fresh();

        try {
            if (! $user || ! $user->two_factor_secret) {
                throw new Exception('Two-factor setup secret is not available.');
            }

            $this->qrCodeSvg = $user->twoFactorQrCodeSvg();
            $this->manualSetupKey = decrypt($user->two_factor_secret);
        } catch (Exception) {
            $this->addError('setupData', 'Failed to fetch setup data.');
            $this->reset('qrCodeSvg', 'manualSetupKey');
        }
    }

    public function showVerificationIfNecessary(): void
    {
        if ($this->requiresConfirmation) {
            $this->showVerificationStep = true;
            $this->resetErrorBag();

            return;
        }

        $this->closeModal();
        $this->dispatch('two-factor-enabled');
    }

    public function confirmTwoFactor(ConfirmTwoFactorAuthentication $confirmTwoFactorAuthentication): void
    {
        $this->validate();

        $confirmTwoFactorAuthentication(auth()->user(), $this->code);

        $this->setupComplete = true;
        $this->closeModal();
        $this->dispatch('two-factor-enabled');
    }

    public function resetVerification(): void
    {
        $this->reset('code', 'showVerificationStep');
        $this->resetErrorBag();
    }

    public function closeModal(): void
    {
        $this->reset('code', 'manualSetupKey', 'qrCodeSvg', 'showVerificationStep', 'setupComplete');
        $this->resetErrorBag();

        $this->dispatch('close-modal', name: 'two-factor-setup');
    }

    #[Computed]
    public function modalConfig(): array
    {
        if ($this->setupComplete) {
            return [
                'title' => __('Two-factor authentication enabled'),
                'description' => __('Two-factor authentication is now enabled.'),
                'buttonText' => __('Close'),
            ];
        }

        if ($this->showVerificationStep) {
            return [
                'title' => __('Verify authentication code'),
                'description' => __('Enter the 6-digit code from your authenticator app.'),
                'buttonText' => __('Continue'),
            ];
        }

        return [
            'title' => __('Enable two-factor authentication'),
            'description' => __('Scan the QR code or enter the setup key in your authenticator app.'),
            'buttonText' => __('Continue'),
        ];
    }
};

?>

<div>
    <x-ui.modal name="two-factor-setup" :title="$this->modalConfig['title']" maxWidth="md">
        <div class="space-y-5" wire:cloak>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $this->modalConfig['description'] }}</p>

            @if ($showVerificationStep)
                <div class="space-y-4">
                    <x-ui.input wire:model="code" :label="__('Authentication code')" :error="$errors->first('code')" inputmode="numeric" maxlength="6" autocomplete="one-time-code" />

                    <div class="flex gap-2">
                        <x-ui.button variant="secondary" type="button" class="flex-1" wire:click="resetVerification">
                            {{ __('Back') }}
                        </x-ui.button>
                        <x-ui.button type="button" class="flex-1" wire:click="confirmTwoFactor">
                            {{ __('Confirm') }}
                        </x-ui.button>
                    </div>
                </div>
            @else
                @error('setupData')
                    <x-ui.alert variant="danger">{{ $message }}</x-ui.alert>
                @enderror

                <div class="flex justify-center">
                    <div class="relative w-56 aspect-square overflow-hidden border rounded-lg border-zinc-200 dark:border-zinc-700 bg-white">
                        @empty($qrCodeSvg)
                            <div class="absolute inset-0 flex items-center justify-center animate-pulse text-zinc-400">
                                <x-ui.spinner class="size-6" />
                            </div>
                        @else
                            <div class="flex items-center justify-center h-full p-3">
                                {!! $qrCodeSvg !!}
                            </div>
                        @endempty
                    </div>
                </div>

                <x-ui.button class="w-full" type="button" :disabled="$errors->has('setupData')" wire:click="showVerificationIfNecessary">
                    {{ $this->modalConfig['buttonText'] }}
                </x-ui.button>

                <div class="space-y-2">
                    <div class="text-center text-xs text-zinc-500">{{ __('or, enter the setup key manually') }}</div>

                    <div
                        class="flex items-stretch w-full border rounded-lg border-zinc-200 dark:border-zinc-700"
                        x-data="{
                            copied: false,
                            async copy() {
                                try {
                                    await navigator.clipboard.writeText('{{ $manualSetupKey }}');
                                    this.copied = true;
                                    setTimeout(() => this.copied = false, 1500);
                                } catch (e) {}
                            }
                        }"
                    >
                        @empty($manualSetupKey)
                            <div class="w-full p-3 bg-zinc-100 dark:bg-zinc-800 text-zinc-400 text-sm">{{ __('Loading…') }}</div>
                        @else
                            <input type="text" readonly value="{{ $manualSetupKey }}" class="w-full p-3 bg-transparent outline-none text-sm font-mono text-zinc-900 dark:text-zinc-100" />
                            <button type="button" @click="copy()" class="px-3 border-l border-zinc-200 dark:border-zinc-700 text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                <span x-show="!copied">{{ __('Copy') }}</span>
                                <span x-show="copied" class="text-hk-success">{{ __('Copied') }}</span>
                            </button>
                        @endempty
                    </div>
                </div>
            @endif
        </div>
    </x-ui.modal>
</div>
