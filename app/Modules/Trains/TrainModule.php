<?php

namespace App\Modules\Trains;

use App\Core\Modules\Module;
use App\Core\Settings\SettingsRepository;
use App\Modules\Trains\Contracts\TrainProvider;
use App\Modules\Trains\Providers\FallbackTrainProvider;
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
            $settings = app(SettingsRepository::class);
            $driver = (string) $settings->get('modules.trains.provider', config('hk-modules.modules.trains.provider', 'stub'));
            $fallback = new StubProvider;

            $preferred = match ($driver) {
                'sabre' => new SabreRailProvider((array) $settings->get('modules.trains.sabre', config('hk-modules.modules.trains.sabre', []))),
                'trainline' => new TrainlineProvider((array) $settings->get('modules.trains.trainline', config('hk-modules.modules.trains.trainline', []))),
                default => $fallback,
            };

            return $preferred instanceof StubProvider
                ? $preferred
                : new FallbackTrainProvider($preferred, $fallback);
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
