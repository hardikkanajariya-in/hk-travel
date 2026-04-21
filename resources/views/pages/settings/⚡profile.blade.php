<?php

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Profile settings')] #[Layout('components.layouts.admin')] class extends Component {
    use ProfileValidationRules;

    public string $name = '';
    public string $email = '';
    public ?string $flash = null;

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();
        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->flash = __('Profile updated.');
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();
        $this->flash = __('A new verification link has been sent to your email address.');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
};

?>

<div>
    <x-settings.shell :heading="__('Profile')" :subheading="__('Update your name and email address')">
        @if ($flash)
            <x-ui.alert variant="success" :dismissible="true" class="mb-4">{{ $flash }}</x-ui.alert>
        @endif

        <form wire:submit="updateProfileInformation" class="space-y-5">
            <x-ui.input wire:model="name" :label="__('Name')" :error="$errors->first('name')" required autofocus autocomplete="name" />

            <div>
                <x-ui.input wire:model="email" type="email" :label="__('Email')" :error="$errors->first('email')" required autocomplete="email" />

                @if ($this->hasUnverifiedEmail)
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                        {{ __('Your email address is unverified.') }}
                        <button type="button" wire:click.prevent="resendVerificationNotification" class="text-hk-primary-600 hover:underline">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>
                @endif
            </div>

            <div>
                <x-ui.button type="submit" data-test="update-profile-button">{{ __('Save') }}</x-ui.button>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <div class="mt-12 pt-6 border-t border-zinc-200 dark:border-zinc-800">
                <livewire:pages::settings.delete-user-form />
            </div>
        @endif
    </x-settings.shell>
</div>
