<x-layouts.auth :title="__('Create your account')" :description="__('Sign up with your details below')">
    <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
        @csrf

        <x-ui.input
            name="name"
            :label="__('Name')"
            :value="old('name')"
            :error="$errors->first('name')"
            required
            autofocus
            autocomplete="name"
        />

        <x-ui.input
            name="email"
            :label="__('Email address')"
            :value="old('email')"
            :error="$errors->first('email')"
            type="email"
            required
            autocomplete="email"
            placeholder="email@example.com"
        />

        <x-ui.input
            name="password"
            :label="__('Password')"
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

        <x-ui.honeypot />
        <x-ui.captcha action="register" />

        <x-ui.button type="submit" class="w-full">{{ __('Create account') }}</x-ui.button>
    </form>

    <p class="mt-6 text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}" wire:navigate class="text-hk-primary-600 hover:underline">{{ __('Log in') }}</a>
    </p>
</x-layouts.auth>
