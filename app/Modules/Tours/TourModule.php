<?php

namespace App\Modules\Tours;

use App\Core\Modules\Module;
use App\Core\Routing\PublicUrlGenerator;
use App\Modules\Tours\Models\Tour;
use Illuminate\Support\Facades\Schema;

/**
 * Tours module manifest.
 *
 * Registered in config/hk-modules.php under key `tours`. When enabled,
 * ModuleServiceProvider boots its routes, migrations, views (namespace
 * `tours::`), translations, and any provider returned by provider().
 */
class TourModule extends Module
{
    public function key(): string
    {
        return 'tours';
    }

    public function name(): string
    {
        return 'Tours & Itineraries';
    }

    public function version(): string
    {
        return '0.1.0';
    }

    public function permissions(): array
    {
        return [
            'tours.view',
            'tours.create',
            'tours.update',
            'tours.delete',
            'tours.publish',
            'tours.bookings.manage',
        ];
    }

    public function adminMenu(): array
    {
        return [[
            'label' => 'Tours',
            'route' => 'admin.tours.index',
            'icon' => 'map',
            'permission' => 'tours.view',
            'group' => 'Catalogue',
        ]];
    }

    public function sitemapEntries(): iterable
    {
        if (! Schema::hasTable('tours')) {
            return [];
        }

        $urls = app(PublicUrlGenerator::class);

        foreach (Tour::query()->where('is_published', true)->get(['slug', 'updated_at']) as $row) {
            yield [
                'loc' => $urls->entity('tour', ['slug' => $row->slug]),
                'lastmod' => $row->updated_at,
                'changefreq' => 'weekly',
                'priority' => 0.8,
            ];
        }
    }
}
