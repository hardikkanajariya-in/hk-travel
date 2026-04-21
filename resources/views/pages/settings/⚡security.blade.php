<?php

use App\Concerns\PasswordValidationRules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Security settings')] #[Layout('components.layouts.admin')] class extends Component {
    use PasswordValidationRules;

    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';
    public ?string $flash = null;

    public bool $canManageTwoFactor = false;
    public bool $twoFactorEnabled = false;
    public bool $requiresConfirmation = false;

    public function mount(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void
    {
        $this->canManageTwoFactor = Features::canManageTwoFactorAuthentication();

        if ($this->canManageTwoFactor) {
            if (Fortify::confirmsTwoFactorAuthentication() && is_null(auth()->user()->two_factor_confirmed_at)) {
                $disableTwoFactorAuthentication(auth()->user());
            }

            $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
            $this->requiresConfirmation = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm');
        }
    }

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => $this->currentPasswordRules(),
                'password' => $this->passwordRules(),
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update(['password' => $validated['password']]);
        $this->reset('current_password', 'password', 'password_confirmation');
        $this->flash = __('Password updated.');
    }

    #[On('two-factor-enabled')]
    public function onTwoFactorEnabled(): void
    {
        $this->twoFactorEnabled = true;
    }

    public function disable(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void
    {
        $disableTwoFactorAuthentication(auth()->user());
        $this->twoFactorEnabled = false;
    }
};

?>

<div>
    <x-settings.shell :heading="__('Update password')" :subheading="__('Ensure your account is using a long, random password to stay secure')">
        @if ($flash)
            <x-ui.alert variant="success" :dismissible="true" class="mb-4">{{ $flash }}</x-ui.alert>
        @endif

        <form wire:submit="updatePassword" class="space-y-5">
            <x-ui.input wire:model="current_password" type="password" :label="__('Current password')" :error="$errors->first('current_password')" required autocomplete="current-password" />
            <x-ui.input wire:model="password" type="password" :label="__('New password')" :error="$errors->first('password')" required autocomplete="new-password" />
            <x-ui.input wire:model="password_confirmation" type="password" :label="__('Confirm password')" :error="$errors->first('password_confirmation')" required autocomplete="new-password" />

            <div>
                <x-ui.button type="submit" data-test="update-password-button">{{ __('Save') }}</x-ui.button>
            </div>
        </form>

        @if ($canManageTwoFactor)
            <section class="mt-12 pt-6 border-t border-zinc-200 dark:border-zinc-800">
                <h3 class="text-lg font-semibold">{{ __('Two-factor authentication') }}</h3>
                <p class="mt-1 text-sm text-zinc-500">{{ __('Manage your two-factor authentication settings') }}</p>

                <div class="mt-6 space-y-4 text-sm" wire:cloak>
                    @if ($twoFactorEnabled)
                        <p class="text-zinc-600 dark:text-zinc-400">
                            {{ __('You will be prompted for a secure, random pin during login, which you can retrieve from the TOTP-supported application on your phone.') }}
                        </p>

                        <div>
                            <x-ui.button variant="danger" wire:click="disable">{{ __('Disable 2FA') }}</x-ui.button>
                        </div>

                        <livewire:pages::settings.two-factor.recovery-codes :$requiresConfirmation />
                    @else
                        <p class="text-zinc-600 dark:text-zinc-400">
                            {{ __('When you enable two-factor authentication, you will be prompted for a secure pin during login. This pin can be retrieved from a TOTP-supported application on your phone.') }}
                        </p>

                        <div>
                            <x-ui.button
                                wire:click="$dispatch('start-two-factor-setup')"
                                x-on:click="$dispatch('open-modal', { name: 'two-factor-setup' })"
                            >
                                {{ __('Enable 2FA') }}
                            </x-ui.button>
                        </div>

                        <livewire:pages::settings.two-factor-setup-modal :requires-confirmation="$requiresConfirmation" />
                    @endif
                </div>
            </section>
        @endif
    </x-settings.shell>
</div>
