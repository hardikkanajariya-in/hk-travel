@props(['title' => null])

<header class="flex h-16 items-center justify-between border-b border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-6">
    <div>
        <h1 class="text-lg font-semibold">{{ $title ?? 'Dashboard' }}</h1>
    </div>

    <div class="flex items-center gap-3">
        <x-ui.locale-switcher />
        <livewire:pages::admin.notification-bell />

        @auth
            <x-ui.dropdown align="right" width="56">
                <x-slot:trigger>
                    <button type="button" class="flex items-center gap-2 rounded-full bg-zinc-100 dark:bg-zinc-800 px-3 py-1.5 text-sm">
                        <span class="size-7 rounded-full bg-hk-primary-600 text-white flex items-center justify-center text-xs font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                    </button>
                </x-slot:trigger>

                <x-ui.dropdown-item :href="route('profile.edit')">Profile</x-ui.dropdown-item>
                <x-ui.dropdown-item :href="route('security.edit')">Security</x-ui.dropdown-item>
                <div class="my-1 border-t border-zinc-200 dark:border-zinc-800"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-hk-danger hover:bg-zinc-100 dark:hover:bg-zinc-800">
                        Log out
                    </button>
                </form>
            </x-ui.dropdown>
        @endauth
    </div>
</header>
