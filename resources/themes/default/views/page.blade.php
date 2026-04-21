@php
    /** @var \App\Models\Page $page */
    $renderer = app(\App\Core\PageBuilder\PageRenderer::class);
    $crumbs = app(\App\Core\Seo\BreadcrumbService::class);
    if (! $page->is_homepage) {
        $crumbs->push($page->title, url('/'.$page->slug));
    }
@endphp

<x-layouts.app :title="$page->localizedTitle(app()->getLocale())">
    <x-theme.seo :page="$page" />

    <article class="mx-auto max-w-5xl space-y-6 px-4 py-12 sm:px-6 lg:px-8">
        @unless ($page->is_homepage)
            <x-theme.breadcrumbs />
        @endunless

        {!! $renderer->render($page, auth()->user()) !!}
    </article>
</x-layouts.app>
