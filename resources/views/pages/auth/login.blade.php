<x-layouts.auth :title="__('Log in to your account')" :description="__('Enter your email and password below')">
    @if (session('status'))
        <x-ui.alert variant="success" class="mb-4">{{ session('status') }}</x-ui.alert>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
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
            placeholder="email@example.com"
        />

        <div>
            <x-ui.input
                name="password"
                :label="__('Password')"
                :error="$errors->first('password')"
                type="password"
                required
                autocomplete="current-password"
            />
            @if (Route::has('password.request'))
                <div class="mt-1.5 text-end">
                    <a href="{{ route('password.request') }}" wire:navigate class="text-xs text-hk-primary-600 hover:underline">
                        {{ __('Forgot your password?') }}
                    </a>
                </div>
            @endif
        </div>

        <x-ui.checkbox name="remember" :label="__('Remember me')" :checked="(bool) old('remember')" />

        <x-ui.button type="submit" class="w-full" data-test="login-button">
            {{ __('Log in') }}
        </x-ui.button>
    </form>

    @if (Route::has('register'))
        <p class="mt-6 text-center text-sm text-zinc-600 dark:text-zinc-400">
            {{ __('Don\'t have an account?') }}
            <a href="{{ route('register') }}" wire:navigate class="text-hk-primary-600 hover:underline">{{ __('Sign up') }}</a>
        </p>
    @endif
</x-layouts.auth>
<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('Log in') }}
                </flux:button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
                <span>{{ __('Don\'t have an account?') }}</span>
                <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
            </div>
        @endif
    </div>
</x-layouts::auth>
