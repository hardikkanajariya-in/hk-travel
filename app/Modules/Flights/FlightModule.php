<?php

namespace App\Modules\Flights;

use App\Core\Modules\Module;
use App\Core\Settings\SettingsRepository;
use App\Modules\Flights\Contracts\FlightProvider;
use App\Modules\Flights\Providers\AmadeusProvider;
use App\Modules\Flights\Providers\DuffelProvider;
use App\Modules\Flights\Providers\FallbackFlightProvider;
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
            $settings = app(SettingsRepository::class);
            $driver = (string) $settings->get('modules.flights.provider', config('hk-modules.modules.flights.provider', 'stub'));
            $fallback = new StubProvider;

            $preferred = match ($driver) {
                'amadeus' => new AmadeusProvider((array) $settings->get('modules.flights.amadeus', config('hk-modules.modules.flights.amadeus', []))),
                'duffel' => new DuffelProvider((array) $settings->get('modules.flights.duffel', config('hk-modules.modules.flights.duffel', []))),
                default => $fallback,
            };

            return $preferred instanceof StubProvider
                ? $preferred
                : new FallbackFlightProvider($preferred, $fallback);
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
