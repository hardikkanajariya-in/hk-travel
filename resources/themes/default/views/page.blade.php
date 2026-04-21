@php
    /** @var \App\Models\Page $page */
    $renderer = app(\App\Core\PageBuilder\PageRenderer::class);
@endphp

<x-layouts.app :title="$page->localizedTitle(app()->getLocale())">
    <article class="mx-auto max-w-5xl space-y-10 px-4 py-12 sm:px-6 lg:px-8">
        {!! $renderer->render($page, auth()->user()) !!}
    </article>
</x-layouts.app>
