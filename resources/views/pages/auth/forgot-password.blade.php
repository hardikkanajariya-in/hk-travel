<x-layouts.auth :title="__('Forgot password?')" :description="__('Enter your email to receive a password reset link')">
    @if (session('status'))
        <x-ui.alert variant="success" class="mb-4">{{ session('status') }}</x-ui.alert>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <x-ui.input
            name="email"
            :label="__('Email address')"
            :value="old('email')"
            :error="$errors->first('email')"
            type="email"
            required
            autofocus
            autocomplete="email"
        />

        <x-ui.honeypot />
        <x-ui.captcha action="password-reset" />

        <x-ui.button type="submit" class="w-full">{{ __('Send reset link') }}</x-ui.button>
    </form>

    <p class="mt-6 text-center text-sm text-zinc-600 dark:text-zinc-400">
        <a href="{{ route('login') }}" wire:navigate class="text-hk-primary-600 hover:underline">{{ __('Back to log in') }}</a>
    </p>
</x-layouts.auth>
