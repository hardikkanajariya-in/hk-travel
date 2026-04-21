<?php

namespace App\Core\Seo;

use App\Core\Modules\ModuleManager;
use App\Models\Page;
use Illuminate\Filesystem\Filesystem;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;

/**
 * Generates a split sitemap: a sitemap-index plus one file per content
 * source (pages, plus one per enabled module that opts in by exposing
 * a `sitemapUrls(): iterable<Url|string>` method on its module class).
 *
 * Files are written to `public/sitemaps/` so they can be served by the
 * web server directly, with `/sitemap.xml` acting as the entry index.
 */
class SitemapGenerator
{
    public function __construct(
        protected Filesystem $files,
        protected ModuleManager $modules,
    ) {}

    public function generate(): array
    {
        $base = public_path('sitemaps');
        if (! $this->files->isDirectory($base)) {
            $this->files->makeDirectory($base, 0755, true);
        }

        $written = [];

        // Pages sitemap.
        $pages = Sitemap::create();
        Page::query()->where('status', 'published')->get()->each(function (Page $p) use ($pages): void {
            $pages->add(
                Url::create('/'.$p->slug)
                    ->setLastModificationDate($p->updated_at ?? $p->published_at ?? now())
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority($p->is_homepage ? 1.0 : 0.7)
            );
        });
        $pages->writeToFile($base.DIRECTORY_SEPARATOR.'sitemap-pages.xml');
        $written[] = url('/sitemaps/sitemap-pages.xml');

        // Module-contributed sitemaps. Modules opt in by exposing a
        // `sitemapUrls(): iterable` method on their manifest class.
        foreach ($this->modules->all() as $key => $module) {
            if (! $this->modules->enabled((string) $key) || ! method_exists($module, 'sitemapUrls')) {
                continue;
            }
            $sm = Sitemap::create();
            $urls = $module->{'sitemapUrls'}();
            foreach ((array) $urls as $u) {
                $sm->add($u);
            }
            $slug = strtolower((string) $key);
            $sm->writeToFile($base.DIRECTORY_SEPARATOR."sitemap-{$slug}.xml");
            $written[] = url("/sitemaps/sitemap-{$slug}.xml");
        }

        // Index referencing every child sitemap.
        $index = SitemapIndex::create();
        foreach ($written as $u) {
            $index->add($u);
        }
        $index->writeToFile($base.DIRECTORY_SEPARATOR.'sitemap.xml');

        return [
            'index' => $base.DIRECTORY_SEPARATOR.'sitemap.xml',
            'children' => $written,
        ];
    }
}
