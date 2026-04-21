<?php

namespace App\Modules\Cruises;

use App\Core\Modules\Module;
use App\Modules\Cruises\Models\Cruise;
use Illuminate\Support\Facades\Schema;

class CruiseModule extends Module
{
    public function key(): string
    {
        return 'cruises';
    }

    public function name(): string
    {
        return 'Cruises';
    }

    public function version(): string
    {
        return '0.1.0';
    }

    public function permissions(): array
    {
        return ['cruises.view', 'cruises.create', 'cruises.update', 'cruises.delete', 'cruises.publish', 'cruises.bookings.manage'];
    }

    public function adminMenu(): array
    {
        return [[
            'label' => 'Cruises',
            'route' => 'admin.cruises.index',
            'icon' => 'sparkles',
            'permission' => 'cruises.view',
            'group' => 'Catalogue',
        ]];
    }

    public function sitemapEntries(): iterable
    {
        if (! Schema::hasTable('cruises')) {
            return [];
        }
        foreach (Cruise::query()->where('is_published', true)->get(['slug', 'updated_at']) as $row) {
            yield [
                'loc' => route('cruises.show', $row->slug),
                'lastmod' => $row->updated_at,
                'changefreq' => 'weekly',
                'priority' => 0.6,
            ];
        }
    }
}
