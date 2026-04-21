<?php

use Livewire\Component;

new class extends Component {
};

?>

<section class="space-y-4">
    <div>
        <h3 class="text-lg font-semibold text-hk-danger">{{ __('Delete account') }}</h3>
        <p class="mt-1 text-sm text-zinc-500">{{ __('Delete your account and all of its resources') }}</p>
    </div>

    <x-ui.button
        variant="danger"
        x-on:click="$dispatch('open-modal', { name: 'confirm-user-deletion' })"
        data-test="delete-user-button"
    >
        {{ __('Delete account') }}
    </x-ui.button>

    <livewire:pages::settings.delete-user-modal />
</section>
