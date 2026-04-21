@php
    /** @var \App\Models\Page $page */
    $renderer = app(\App\Core\PageBuilder\PageRenderer::class);
    $crumbs = app(\App\Core\Seo\BreadcrumbService::class);
    if (! $page->is_homepage) {
        $crumbs->push($page->title, url('/'.$page->slug));
    }

    // If the first block is a hero we let it render full-bleed and skip
    // our own page header — otherwise we render a soft branded header band.
    $blocks = $page->blocks ?? collect();
    $firstBlockType = optional($blocks->first())->type;
    $showPageHeader = ! $page->is_homepage && $firstBlockType !== 'hero';
@endphp

<x-layouts.app :title="$page->localizedTitle(app()->getLocale())">
    <x-theme.seo :page="$page" />

    @if ($showPageHeader)
        <header class="relative isolate overflow-hidden bg-gradient-to-br from-hk-primary-50 via-white to-amber-50 dark:from-zinc-900 dark:via-zinc-950 dark:to-zinc-900">
            <x-theme.decoration variant="blob-a" class="absolute -left-20 -top-20 size-[28rem] text-hk-primary-100 dark:text-hk-primary-950/40" />
            <x-theme.decoration variant="dots" class="absolute right-10 top-10 size-32 text-hk-primary-200/60 dark:text-hk-primary-900/40" />

            <div class="relative mx-auto max-w-5xl px-4 pb-14 pt-16 sm:px-6 sm:pt-20 lg:px-8">
                <x-theme.breadcrumbs class="text-zinc-600 dark:text-zinc-400" />
                <h1 class="mt-4 text-balance text-4xl font-extrabold tracking-tight text-zinc-900 sm:text-5xl dark:text-white">
                    {{ $page->localizedTitle(app()->getLocale()) }}
                </h1>
                @if ($excerpt = $page->seo['description'] ?? null)
                    <p class="mt-3 max-w-2xl text-balance text-lg text-zinc-600 dark:text-zinc-400">{{ $excerpt }}</p>
                @endif
            </div>
        </header>
    @endif

    <article class="mx-auto max-w-5xl space-y-8 px-4 py-12 sm:px-6 lg:px-8">
        {!! $renderer->render($page, auth()->user()) !!}
    </article>
</x-layouts.app>
