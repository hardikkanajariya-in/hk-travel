<?php

namespace App\Modules\Crm;

use App\Core\Modules\Module;

class CrmModule extends Module
{
    public function key(): string
    {
        return 'crm';
    }

    public function name(): string
    {
        return 'CRM & Leads';
    }

    public function version(): string
    {
        return '0.1.0';
    }

    public function permissions(): array
    {
        return [
            'admin.crm.leads.view',
            'admin.crm.leads.manage',
            'admin.crm.leads.assign',
            'admin.crm.pipelines.manage',
        ];
    }

    public function adminMenu(): array
    {
        return [
            [
                'label' => 'Leads',
                'route' => 'admin.crm.leads',
                'icon' => 'user-plus',
                'permission' => 'admin.crm.leads.view',
                'group' => 'CRM',
            ],
            [
                'label' => 'Kanban',
                'route' => 'admin.crm.kanban',
                'icon' => 'view-columns',
                'permission' => 'admin.crm.leads.view',
                'group' => 'CRM',
            ],
            [
                'label' => 'Pipelines',
                'route' => 'admin.crm.pipelines',
                'icon' => 'queue-list',
                'permission' => 'admin.crm.pipelines.manage',
                'group' => 'CRM',
            ],
        ];
    }
}
