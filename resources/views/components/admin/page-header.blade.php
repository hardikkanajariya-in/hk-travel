@props(['title', 'description' => null])

<header class="mb-6 flex flex-wrap items-end justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-4">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight">{{ $title }}</h1>
        @if ($description)
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $description }}</p>
        @endif
    </div>

    @isset ($actions)
        <div class="flex items-center gap-2">{{ $actions }}</div>
    @endisset
</header>
