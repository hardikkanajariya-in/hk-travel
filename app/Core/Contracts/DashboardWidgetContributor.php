<?php

namespace App\Core\Contracts;

/**
 * Modules return customer-dashboard widgets through this contract.
 * Each widget renders a Blade view chunk on the customer dashboard.
 *
 * Item shape:
 *   ['key' => string, 'label' => string, 'view' => string,
 *    'data' => callable, 'order' => int]
 */
interface DashboardWidgetContributor
{
    /**
     * @return array<int, array{key:string, label:string, view:string, data?:callable, order?:int}>
     */
    public function dashboardWidgets(): array;
}
