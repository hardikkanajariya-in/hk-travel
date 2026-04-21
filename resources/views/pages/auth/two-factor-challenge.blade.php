<x-layouts.auth :title="__('Two-factor authentication')" :description="__('Enter the code from your authenticator app')">
    <div x-data="{ recovery: false }">
        <form method="POST" action="{{ route('two-factor.login.store') }}" class="space-y-5">
            @csrf

            <div x-show="!recovery">
                <x-ui.input
                    name="code"
                    :label="__('Authentication code')"
                    :error="$errors->first('code')"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    autofocus
                />
            </div>

            <div x-show="recovery" x-cloak>
                <x-ui.input
                    name="recovery_code"
                    :label="__('Recovery code')"
                    :error="$errors->first('recovery_code')"
                    autocomplete="one-time-code"
                />
            </div>

            <x-ui.button type="submit" class="w-full">{{ __('Log in') }}</x-ui.button>

            <div class="text-center">
                <button type="button" @click="recovery = !recovery" class="text-sm text-hk-primary-600 hover:underline">
                    <span x-show="!recovery">{{ __('Use a recovery code') }}</span>
                    <span x-show="recovery" x-cloak>{{ __('Use an authentication code') }}</span>
                </button>
            </div>
        </form>
    </div>
</x-layouts.auth>
