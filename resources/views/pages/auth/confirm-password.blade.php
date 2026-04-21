<x-layouts.auth :title="__('Confirm your password')" :description="__('Please confirm your password before continuing')">
    <form method="POST" action="{{ route('password.confirm.store') }}" class="space-y-5">
        @csrf

        <x-ui.input
            name="password"
            :label="__('Password')"
            :error="$errors->first('password')"
            type="password"
            required
            autofocus
            autocomplete="current-password"
        />

        <x-ui.button type="submit" class="w-full">{{ __('Confirm') }}</x-ui.button>
    </form>
</x-layouts.auth>
