<x-layouts.auth :title="__('Verify your email')">
    @if (session('status') === 'verification-link-sent')
        <x-ui.alert variant="success" class="mb-4">
            {{ __('A new verification link has been sent to your email address.') }}
        </x-ui.alert>
    @endif

    <p class="text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just emailed to you. If you didn\'t receive the email, we will gladly send you another.') }}
    </p>

    <div class="mt-6 flex flex-col gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-ui.button type="submit" class="w-full">{{ __('Resend verification email') }}</x-ui.button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 underline">
                {{ __('Log out') }}
            </button>
        </form>
    </div>
</x-layouts.auth>
