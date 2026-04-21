@props(['page' => null, 'jsonld' => true])

@php
    $seo = app(\App\Core\Seo\SeoManager::class);
    $jsonLd = app(\App\Core\Seo\JsonLd::class);
    $breadcrumbs = app(\App\Core\Seo\BreadcrumbService::class);

    if ($page instanceof \App\Models\Page) {
        $seo->fromPage($page);
    }

    $snap = $seo->snapshot();
@endphp

@push('head')
    <meta name="description" content="{{ $snap['description'] }}">
    @if ($snap['canonical'])
        <link rel="canonical" href="{{ $snap['canonical'] }}">
    @endif
    @if ($snap['noindex'])
        <meta name="robots" content="noindex, nofollow">
    @else
        <meta name="robots" content="index, follow">
    @endif

    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $snap['title'] }}">
    <meta property="og:description" content="{{ $snap['description'] }}">
    <meta property="og:url" content="{{ $snap['canonical'] ?? url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ app(\App\Core\Branding\BrandingService::class)->siteName() }}">
    @if ($snap['image'])
        <meta property="og:image" content="{{ $snap['image'] }}">
    @endif

    {{-- Twitter --}}
    <meta name="twitter:card" content="{{ $snap['image'] ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $snap['title'] }}">
    <meta name="twitter:description" content="{{ $snap['description'] }}">
    @if ($snap['image'])
        <meta name="twitter:image" content="{{ $snap['image'] }}">
    @endif

    @foreach (($snap['extra'] ?? []) as $name => $content)
        <meta name="{{ $name }}" content="{{ $content }}">
    @endforeach

    @if ($jsonld)
        @php
            $jsonLd->organization()->website();
            if (! $breadcrumbs->isEmpty()) {
                $jsonLd->breadcrumbs($breadcrumbs->all());
            }
        @endphp
        {!! $jsonLd->render() !!}
    @endif
@endpush
