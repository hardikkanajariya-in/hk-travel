@php
    $branding = app(\App\Core\Branding\BrandingService::class);
    $year = now()->year;
@endphp

<footer class="mt-16 border-t border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900">
    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-10 px-4 py-12 sm:px-6 md:grid-cols-3 lg:px-8">
        <div>@zone('footer-1')</div>
        <div>@zone('footer-2')</div>
        <div>@zone('footer-3')</div>
    </div>

    <div class="border-t border-zinc-200 dark:border-zinc-800">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-5 text-xs text-zinc-500 sm:px-6 lg:px-8">
            <span>© {{ $year }} {{ $branding->siteName() }}. {{ __('All rights reserved.') }}</span>
            <x-theme.menu location="footer" class="text-xs" />
        </div>
    </div>
</footer>
