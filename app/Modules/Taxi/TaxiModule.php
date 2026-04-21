<?php

namespace App\Modules\Taxi;

use App\Core\Modules\Module;
use App\Modules\Taxi\Models\TaxiService;
use Illuminate\Support\Facades\Schema;

class TaxiModule extends Module
{
    public function key(): string
    {
        return 'taxi';
    }

    public function name(): string
    {
        return 'Taxi & Transfers';
    }

    public function version(): string
    {
        return '0.1.0';
    }

    public function permissions(): array
    {
        return ['taxi.view', 'taxi.create', 'taxi.update', 'taxi.delete', 'taxi.publish', 'taxi.bookings.manage'];
    }

    public function adminMenu(): array
    {
        return [[
            'label' => 'Taxi & Transfers',
            'route' => 'admin.taxi.index',
            'icon' => 'truck',
            'permission' => 'taxi.view',
            'group' => 'Catalogue',
        ]];
    }

    public function sitemapEntries(): iterable
    {
        if (! Schema::hasTable('taxi_services')) {
            return [];
        }
        foreach (TaxiService::query()->where('is_published', true)->get(['slug', 'updated_at']) as $row) {
            yield [
                'loc' => route('taxi.show', $row->slug),
                'lastmod' => $row->updated_at,
                'changefreq' => 'weekly',
                'priority' => 0.5,
            ];
        }
    }
}
