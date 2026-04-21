<?php

namespace App\Console\Commands;

use App\Core\Seo\SitemapGenerator;
use Illuminate\Console\Command;

class SitemapGenerateCommand extends Command
{
    protected $signature = 'hk:sitemap';

    protected $description = 'Regenerate the public sitemap.xml index and per-source children.';

    public function handle(SitemapGenerator $generator): int
    {
        $result = $generator->generate();

        $this->info('Sitemap index written: '.$result['index']);
        foreach ($result['children'] as $child) {
            $this->line(' • '.$child);
        }

        return self::SUCCESS;
    }
}
