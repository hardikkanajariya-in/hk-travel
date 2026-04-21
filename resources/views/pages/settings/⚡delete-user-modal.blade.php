<?php

use App\Concerns\PasswordValidationRules;
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component {
    use PasswordValidationRules;

    public string $password = '';

    public function deleteUser(Logout $logout): void
    {
        $this->validate(['password' => $this->currentPasswordRules()]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
};

?>

<div>
    <x-ui.modal name="confirm-user-deletion" :title="__('Delete account')" maxWidth="lg">
        <form wire:submit="deleteUser" class="space-y-5">
            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm.') }}
            </p>

            <x-ui.input wire:model="password" type="password" :label="__('Password')" :error="$errors->first('password')" required />

            <div class="flex justify-end gap-2">
                <x-ui.button variant="secondary" type="button" x-on:click="$dispatch('close-modal', { name: 'confirm-user-deletion' })">
                    {{ __('Cancel') }}
                </x-ui.button>
                <x-ui.button variant="danger" type="submit" data-test="confirm-delete-user-button">
                    {{ __('Delete account') }}
                </x-ui.button>
            </div>
        </form>
    </x-ui.modal>
</div>
