<?php

namespace App\Modules\Visa;

use App\Core\Modules\Module;
use App\Core\Routing\PublicUrlGenerator;
use App\Modules\Visa\Models\VisaService;
use Illuminate\Support\Facades\Schema;

class VisaModule extends Module
{
    public function key(): string
    {
        return 'visa';
    }

    public function name(): string
    {
        return 'Visa Services';
    }

    public function version(): string
    {
        return '0.1.0';
    }

    public function permissions(): array
    {
        return ['visa.view', 'visa.create', 'visa.update', 'visa.delete', 'visa.publish', 'visa.applications.manage'];
    }

    public function adminMenu(): array
    {
        return [[
            'label' => 'Visa Services',
            'route' => 'admin.visa.index',
            'icon' => 'identification',
            'permission' => 'visa.view',
            'group' => 'Catalogue',
        ]];
    }

    public function sitemapEntries(): iterable
    {
        if (! Schema::hasTable('visa_services')) {
            return [];
        }

        $urls = app(PublicUrlGenerator::class);

        foreach (VisaService::query()->where('is_published', true)->get(['slug', 'updated_at']) as $row) {
            yield [
                'loc' => $urls->entity('visa', ['slug' => $row->slug]),
                'lastmod' => $row->updated_at,
                'changefreq' => 'weekly',
                'priority' => 0.6,
            ];
        }
    }
}
