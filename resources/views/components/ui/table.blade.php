@props([
    'striped' => false,
    'compact' => false,
])

<div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-800">
    <table {{ $attributes->class('w-full text-sm text-left text-zinc-700 dark:text-zinc-300') }}
           data-striped="{{ $striped ? 'true' : 'false' }}"
           data-compact="{{ $compact ? 'true' : 'false' }}">
        @isset ($head)
            <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                {{ $head }}
            </thead>
        @endisset
        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
            {{ $slot }}
        </tbody>
        @isset ($foot)
            <tfoot class="bg-zinc-50 dark:bg-zinc-900/50 border-t border-zinc-200 dark:border-zinc-800">
                {{ $foot }}
            </tfoot>
        @endisset
    </table>
</div>
