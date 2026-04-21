<?php

namespace App\Modules\Trains;

use App\Core\Modules\Module;
use App\Modules\Trains\Contracts\TrainProvider;
use App\Modules\Trains\Providers\SabreRailProvider;
use App\Modules\Trains\Providers\StubProvider;
use App\Modules\Trains\Providers\TrainlineProvider;

/**
 * Trains module manifest.
 *
 * Ships a pluggable provider abstraction (Stub | Sabre Rail (GDS) |
 * Trainline) so search results can come from a real rail GDS or from
 * curated offers stored locally. Disable the module to remove the
 * /trains search page and admin section entirely.
 */
class TrainModule extends Module
{
    public function key(): string
    {
        return 'trains';
    }

    public function name(): string
    {
        return 'Trains';
    }

    public function version(): string
    {
        return '0.1.0';
    }

    public function permissions(): array
    {
        return [
            'trains.view',
            'trains.create',
            'trains.update',
            'trains.delete',
            'trains.publish',
            'trains.bookings.manage',
        ];
    }

    public function register(): void
    {
        app()->singleton(TrainProvider::class, function (): TrainProvider {
            $driver = (string) config('hk-modules.modules.trains.provider', 'stub');

            return match ($driver) {
                'sabre' => new SabreRailProvider((array) config('hk-modules.modules.trains.sabre', [])),
                'trainline' => new TrainlineProvider((array) config('hk-modules.modules.trains.trainline', [])),
                default => new StubProvider,
            };
        });
    }

    public function adminMenu(): array
    {
        return [[
            'label' => 'Trains',
            'route' => 'admin.trains.index',
            'icon' => 'rail',
            'permission' => 'trains.view',
            'group' => 'Catalogue',
        ]];
    }

    public function sitemapEntries(): iterable
    {
        return [];
    }
}
