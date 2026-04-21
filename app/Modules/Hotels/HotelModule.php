<?php

namespace App\Modules\Hotels;

use App\Core\Modules\Module;
use App\Modules\Hotels\Models\Hotel;
use Illuminate\Support\Facades\Schema;

class HotelModule extends Module
{
    public function key(): string
    {
        return 'hotels';
    }

    public function name(): string
    {
        return 'Hotels & Rooms';
    }

    public function version(): string
    {
        return '0.1.0';
    }

    public function permissions(): array
    {
        return [
            'hotels.view', 'hotels.create', 'hotels.update', 'hotels.delete', 'hotels.publish',
            'hotels.rooms.manage', 'hotels.bookings.manage',
        ];
    }

    public function adminMenu(): array
    {
        return [[
            'label' => 'Hotels',
            'route' => 'admin.hotels.index',
            'icon' => 'building-office',
            'permission' => 'hotels.view',
            'group' => 'Catalogue',
        ]];
    }

    public function sitemapEntries(): iterable
    {
        if (! Schema::hasTable('hotels')) {
            return [];
        }
        foreach (Hotel::query()->where('is_published', true)->get(['slug', 'updated_at']) as $row) {
            yield [
                'loc' => route('hotels.show', $row->slug),
                'lastmod' => $row->updated_at,
                'changefreq' => 'weekly',
                'priority' => 0.7,
            ];
        }
    }
}
