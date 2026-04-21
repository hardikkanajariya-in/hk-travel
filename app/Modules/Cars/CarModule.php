<?php

namespace App\Modules\Cars;

use App\Core\Modules\Module;
use App\Modules\Cars\Models\CarRental;
use Illuminate\Support\Facades\Schema;

class CarModule extends Module
{
    public function key(): string
    {
        return 'cars';
    }

    public function name(): string
    {
        return 'Car Rentals';
    }

    public function version(): string
    {
        return '0.1.0';
    }

    public function permissions(): array
    {
        return ['cars.view', 'cars.create', 'cars.update', 'cars.delete', 'cars.publish', 'cars.bookings.manage'];
    }

    public function adminMenu(): array
    {
        return [[
            'label' => 'Car Rentals',
            'route' => 'admin.cars.index',
            'icon' => 'truck',
            'permission' => 'cars.view',
            'group' => 'Catalogue',
        ]];
    }

    public function sitemapEntries(): iterable
    {
        if (! Schema::hasTable('car_rentals')) {
            return [];
        }
        foreach (CarRental::query()->where('is_published', true)->get(['slug', 'updated_at']) as $row) {
            yield [
                'loc' => route('cars.show', $row->slug),
                'lastmod' => $row->updated_at,
                'changefreq' => 'weekly',
                'priority' => 0.6,
            ];
        }
    }
}
