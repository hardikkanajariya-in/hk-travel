<x-layouts.auth :title="__('Reset your password')">
    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ request()->route('token') }}">

        <x-ui.input
            name="email"
            :label="__('Email address')"
            :value="old('email', request('email'))"
            :error="$errors->first('email')"
            type="email"
            required
            autofocus
            autocomplete="email"
        />

        <x-ui.input
            name="password"
            :label="__('New password')"
            :error="$errors->first('password')"
            type="password"
            required
            autocomplete="new-password"
        />

        <x-ui.input
            name="password_confirmation"
            :label="__('Confirm password')"
            :error="$errors->first('password_confirmation')"
            type="password"
            required
            autocomplete="new-password"
        />

        <x-ui.button type="submit" class="w-full">{{ __('Reset password') }}</x-ui.button>
    </form>
</x-layouts.auth>
