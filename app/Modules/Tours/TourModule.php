<?php

namespace App\Modules\Tours;

use App\Core\Modules\Module;

/**
 * Tours module manifest.
 *
 * Registered in config/hk-modules.php under key `tours`. When enabled,
 * ModuleServiceProvider boots its routes, migrations, views (namespace
 * `tours::`), translations, and any provider returned by provider().
 */
class TourModule extends Module
{
    public function key(): string
    {
        return 'tours';
    }

    public function name(): string
    {
        return 'Tours & Itineraries';
    }

    public function version(): string
    {
        return '0.1.0';
    }

    public function permissions(): array
    {
        return [
            'tours.view',
            'tours.create',
            'tours.update',
            'tours.delete',
            'tours.publish',
            'tours.bookings.manage',
        ];
    }
}
