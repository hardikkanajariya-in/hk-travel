<?php

namespace App\Core\Concerns;

use App\Core\Routing\PublicUrlGenerator;
use App\Core\Seo\SeoManager;

trait EnsuresCanonicalPublicUrl
{
    protected function ensureCanonicalPublicUrl(
        string $entityType,
        string $slug,
        SeoManager $seo,
        PublicUrlGenerator $urls,
    ): void {
        $canonical = $urls->entity($entityType, ['slug' => $slug]);
        $seo->canonical($canonical);

        $currentPath = '/'.ltrim(request()->path(), '/');
        $canonicalPath = parse_url($canonical, PHP_URL_PATH) ?: '/';

        if ($currentPath !== $canonicalPath) {
            $this->redirect($canonical, navigate: true);
        }
    }
}
