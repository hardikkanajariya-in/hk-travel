<?php

namespace App\Modules\Destinations;

use App\Core\Modules\Module;
use App\Core\Routing\PublicUrlGenerator;
use App\Modules\Destinations\Models\Destination;
use Illuminate\Support\Facades\Schema;

/**
 * Destinations module manifest.
 *
 * Owns the global place taxonomy (countries, cities, regions) consumed
 * as a foreign key by Tours, Hotels, Activities, Cruises, etc. When
 * disabled, those modules fall back to free-text location fields and
 * the `/destinations` browse routes are removed.
 */
class DestinationModule extends Module
{
    public function key(): string
    {
        return 'destinations';
    }

    public function name(): string
    {
        return 'Destinations';
    }

    public function version(): string
    {
        return '0.1.0';
    }

    public function permissions(): array
    {
        return [
            'destinations.view',
            'destinations.create',
            'destinations.update',
            'destinations.delete',
            'destinations.publish',
        ];
    }

    public function adminMenu(): array
    {
        return [[
            'label' => 'Destinations',
            'route' => 'admin.destinations.index',
            'icon' => 'globe-alt',
            'permission' => 'destinations.view',
            'group' => 'Catalogue',
        ]];
    }

    public function sitemapEntries(): iterable
    {
        if (! Schema::hasTable('destinations')) {
            return [];
        }

        $urls = app(PublicUrlGenerator::class);

        foreach (Destination::query()->where('is_published', true)->get(['slug', 'updated_at']) as $row) {
            yield [
                'loc' => $urls->entity('destination', ['slug' => $row->slug]),
                'lastmod' => $row->updated_at,
                'changefreq' => 'weekly',
                'priority' => 0.6,
            ];
        }
    }
}
