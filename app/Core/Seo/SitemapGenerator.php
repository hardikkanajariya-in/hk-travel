<?php

namespace App\Core\Seo;

use App\Core\Modules\ModuleManager;
use App\Models\Page;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
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

        // Pages sitemap — always include the homepage, then every published
        // page. Routes resolve through PageController so the URL matches
        // what visitors actually see.
        $pages = Sitemap::create();
        $pages->add(
            Url::create(route('home'))
                ->setLastModificationDate(Carbon::now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(1.0)
        );

        if (Schema::hasTable('pages')) {
            Page::query()
                ->where('status', 'published')
                ->whereNotNull('slug')
                ->get()
                ->each(function (Page $p) use ($pages): void {
                    if ($p->is_homepage ?? false) {
                        return; // already added as `/`
                    }
                    $pages->add(
                        Url::create(url('/'.ltrim((string) $p->slug, '/')))
                            ->setLastModificationDate($p->updated_at ?? $p->published_at ?? Carbon::now())
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.7)
                    );
                });
        }

        $pages->writeToFile($base.DIRECTORY_SEPARATOR.'sitemap-pages.xml');
        $written[] = url('/sitemaps/sitemap-pages.xml');

        // Module-contributed sitemaps. Modules opt in by exposing a
        // `sitemapUrls(): iterable<Url|string>` method on their manifest class.
        foreach ($this->modules->all() as $key => $module) {
            if (! $this->modules->enabled((string) $key) || ! method_exists($module, 'sitemapUrls')) {
                continue;
            }
            $sm = Sitemap::create();
            $count = 0;
            foreach ((array) $module->{'sitemapUrls'}() as $u) {
                $sm->add($u instanceof Url ? $u : Url::create((string) $u));
                $count++;
            }
            if ($count === 0) {
                continue;
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
            'page_count' => count($pages->getTags()),
        ];
    }
}
