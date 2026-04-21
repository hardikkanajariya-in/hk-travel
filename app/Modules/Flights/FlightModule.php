<?php

namespace App\Modules\Flights;

use App\Core\Modules\Module;
use App\Modules\Flights\Contracts\FlightProvider;
use App\Modules\Flights\Providers\AmadeusProvider;
use App\Modules\Flights\Providers\DuffelProvider;
use App\Modules\Flights\Providers\StubProvider;

class FlightModule extends Module
{
    public function key(): string
    {
        return 'flights';
    }

    public function name(): string
    {
        return 'Flights';
    }

    public function version(): string
    {
        return '0.1.0';
    }

    public function permissions(): array
    {
        return ['flights.view', 'flights.create', 'flights.update', 'flights.delete', 'flights.publish', 'flights.bookings.manage'];
    }

    public function register(): void
    {
        app()->singleton(FlightProvider::class, function (): FlightProvider {
            $driver = (string) config('hk-modules.modules.flights.provider', 'stub');

            return match ($driver) {
                'amadeus' => new AmadeusProvider((array) config('hk-modules.modules.flights.amadeus', [])),
                'duffel' => new DuffelProvider((array) config('hk-modules.modules.flights.duffel', [])),
                default => new StubProvider,
            };
        });
    }

    public function adminMenu(): array
    {
        return [[
            'label' => 'Flights',
            'route' => 'admin.flights.index',
            'icon' => 'paper-airplane',
            'permission' => 'flights.view',
            'group' => 'Catalogue',
        ]];
    }

    public function sitemapEntries(): iterable
    {
        return [];
    }
}
