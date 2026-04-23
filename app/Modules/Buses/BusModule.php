<?php

namespace App\Modules\Buses;

use App\Core\Modules\Module;
use App\Core\Routing\PublicUrlGenerator;
use App\Modules\Buses\Models\BusRoute;
use Illuminate\Support\Facades\Schema;

class BusModule extends Module
{
    public function key(): string
    {
        return 'buses';
    }

    public function name(): string
    {
        return 'Bus Routes';
    }

    public function version(): string
    {
        return '0.1.0';
    }

    public function permissions(): array
    {
        return ['buses.view', 'buses.create', 'buses.update', 'buses.delete', 'buses.publish', 'buses.bookings.manage'];
    }

    public function adminMenu(): array
    {
        return [[
            'label' => 'Bus Routes',
            'route' => 'admin.buses.index',
            'icon' => 'truck',
            'permission' => 'buses.view',
            'group' => 'Catalogue',
        ]];
    }

    public function sitemapEntries(): iterable
    {
        if (! Schema::hasTable('bus_routes')) {
            return [];
        }

        $urls = app(PublicUrlGenerator::class);

        foreach (BusRoute::query()->where('is_published', true)->get(['slug', 'updated_at']) as $row) {
            yield [
                'loc' => $urls->entity('bus', ['slug' => $row->slug]),
                'lastmod' => $row->updated_at,
                'changefreq' => 'weekly',
                'priority' => 0.5,
            ];
        }
    }
}
