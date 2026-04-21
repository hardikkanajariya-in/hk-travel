<?php

namespace App\Core\Contracts;

/**
 * Modules return their admin sidebar entries through this contract.
 * Aggregated by ModuleManager and rendered by the admin sidebar.
 *
 * Item shape:
 *   ['label' => string, 'route' => string, 'icon' => string,
 *    'permission' => string|null, 'group' => string|null]
 */
interface AdminMenuContributor
{
    /**
     * @return array<int, array{label:string, route:string, icon?:?string, permission?:?string, group?:?string}>
     */
    public function adminMenu(): array;
}
