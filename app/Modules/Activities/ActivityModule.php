<?php

namespace App\Modules\Activities;

use App\Core\Modules\Module;
use App\Modules\Activities\Models\Activity;
use Illuminate\Support\Facades\Schema;

class ActivityModule extends Module
{
    public function key(): string
    {
        return 'activities';
    }

    public function name(): string
    {
        return 'Activities & Experiences';
    }

    public function version(): string
    {
        return '0.1.0';
    }

    public function permissions(): array
    {
        return ['activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.publish'];
    }

    public function adminMenu(): array
    {
        return [[
            'label' => 'Activities',
            'route' => 'admin.activities.index',
            'icon' => 'sparkles',
            'permission' => 'activities.view',
            'group' => 'Catalogue',
        ]];
    }

    public function sitemapEntries(): iterable
    {
        if (! Schema::hasTable('activities')) {
            return [];
        }
        foreach (Activity::query()->where('is_published', true)->get(['slug', 'updated_at']) as $row) {
            yield [
                'loc' => route('activities.show', $row->slug),
                'lastmod' => $row->updated_at,
                'changefreq' => 'weekly',
                'priority' => 0.7,
            ];
        }
    }
}
