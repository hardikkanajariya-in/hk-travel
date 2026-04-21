@props(['title' => null])

<header class="flex h-16 shrink-0 items-center justify-between gap-3 border-b border-zinc-200 bg-white px-4 sm:px-6 dark:border-zinc-800 dark:bg-zinc-900">
    <div class="flex min-w-0 items-center gap-2">
        {{-- Mobile hamburger --}}
        <button
            type="button"
            class="-ml-1 rounded-md p-2 text-zinc-600 hover:bg-zinc-100 md:hidden dark:text-zinc-300 dark:hover:bg-zinc-800"
            @click="sidebarOpen = true"
            aria-label="{{ __('Open menu') }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
            </svg>
        </button>

        <h1 class="truncate text-lg font-semibold">{{ $title ?? __('admin.topbar.dashboard') }}</h1>
    </div>

    <div class="flex items-center gap-3">
        <x-ui.locale-switcher />
        <livewire:pages::admin.notification-bell />

        @auth
            <x-ui.dropdown align="right" width="56">
                <x-slot:trigger>
                    <button type="button" class="flex items-center gap-2 rounded-full bg-zinc-100 dark:bg-zinc-800 px-3 py-1.5 text-sm" aria-label="{{ __('admin.topbar.open_menu') }}">
                        <span class="size-7 rounded-full bg-hk-primary-600 text-white flex items-center justify-center text-xs font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                    </button>
                </x-slot:trigger>

                <x-ui.dropdown-item :href="route('profile.edit')">{{ __('admin.topbar.profile') }}</x-ui.dropdown-item>
                <x-ui.dropdown-item :href="route('security.edit')">{{ __('admin.topbar.security') }}</x-ui.dropdown-item>
                <div class="my-1 border-t border-zinc-200 dark:border-zinc-800"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-hk-danger hover:bg-zinc-100 dark:hover:bg-zinc-800">
                        {{ __('admin.topbar.logout') }}
                    </button>
                </form>
            </x-ui.dropdown>
        @endauth
    </div>
</header>
