<?php

namespace App\Core\Contracts;

/**
 * Modules implement this contract to surface their public URLs into the
 * site-wide sitemap aggregated by the SEO core (Track A).
 *
 * Each entry is a flat array shaped:
 *   ['loc' => string, 'lastmod' => \DateTimeInterface|null,
 *    'changefreq' => string|null, 'priority' => float|null]
 */
interface SitemapContributor
{
    /**
     * @return iterable<int, array{loc:string, lastmod?:?\DateTimeInterface, changefreq?:?string, priority?:?float}>
     */
    public function sitemapEntries(): iterable;
}
