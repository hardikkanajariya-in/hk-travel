<?php

namespace App\Core\Support;

use Illuminate\Support\Facades\Route;

/**
 * Builds the breadcrumb trail shown above each admin page.
 *
 * Trails are derived from the current route name (`admin.crm.kanban`
 * becomes Dashboard › CRM › Kanban) using a small map of human labels.
 * Pages can override or append crumbs by passing them explicitly to the
 * `<x-admin.page-header :breadcrumbs="..." />` component.
 */
class Breadcrumbs
{
    /**
     * Friendly labels keyed by route name. Anything not listed falls back
     * to a humanised version of the last route segment.
     *
     * @var array<string, string>
     */
    protected static array $routeLabels = [
        'admin.dashboard' => 'Dashboard',
        'admin.pages' => 'Pages',
        'admin.page-editor' => 'Edit page',
        'admin.menus' => 'Menus',
        'admin.widgets' => 'Footer & sidebar',
        'admin.contact-forms' => 'Forms',
        'admin.contact-form-builder' => 'Form builder',
        'admin.contact-submissions' => 'Submissions',
        'admin.themes' => 'Themes',
        'admin.branding' => 'Branding',
        'admin.crm.leads' => 'Leads',
        'admin.crm.kanban' => 'Kanban',
        'admin.crm.pipelines' => 'Pipelines',
        'admin.crm.lead' => 'Lead',
        'admin.email-templates' => 'Email templates',
        'admin.notifications' => 'Notifications',
        'admin.security' => 'Security',
        'admin.captcha' => 'Captcha',
        'admin.audit' => 'Audit log',
        'admin.settings' => 'Settings',
        'admin.modules' => 'Modules',
        'admin.permalinks' => 'Permalinks',
        'admin.seo' => 'SEO &amp; sitemaps',
        'admin.users' => 'Users',
    ];

    /**
     * Group labels keyed by the second segment of the route name. These
     * appear as the parent crumb between Dashboard and the page label.
     *
     * @var array<string, string>
     */
    protected static array $groupLabels = [
        'pages' => 'Content',
        'page-editor' => 'Content',
        'menus' => 'Content',
        'widgets' => 'Content',
        'contact-forms' => 'Content',
        'contact-form-builder' => 'Content',
        'contact-submissions' => 'Content',
        'themes' => 'Appearance',
        'branding' => 'Appearance',
        'crm' => 'CRM',
        'email-templates' => 'Communication',
        'notifications' => 'Communication',
        'security' => 'Security',
        'captcha' => 'Security',
        'audit' => 'Security',
        'settings' => 'System',
        'modules' => 'System',
        'permalinks' => 'System',
        'seo' => 'System',
    ];

    /**
     * Build a default trail for the current request. Always starts with
     * Dashboard, then a non-clickable group crumb (when known), then the
     * current page.
     *
     * @return array<int, array{label:string, route?:string, url?:string}>
     */
    public static function forCurrentRoute(): array
    {
        $current = Route::currentRouteName();
        $trail = [['label' => 'Dashboard', 'route' => 'admin.dashboard']];

        if (! $current || ! str_starts_with($current, 'admin.')) {
            return $trail;
        }

        if ($current === 'admin.dashboard') {
            return $trail;
        }

        $segments = explode('.', $current);
        $groupKey = $segments[1] ?? null;
        if ($groupKey && isset(self::$groupLabels[$groupKey])) {
            $trail[] = ['label' => self::$groupLabels[$groupKey]];
        }

        $trail[] = [
            'label' => self::$routeLabels[$current] ?? self::humanise(end($segments) ?: $current),
        ];

        return $trail;
    }

    protected static function humanise(string $segment): string
    {
        return ucfirst(str_replace(['-', '_', '.'], ' ', $segment));
    }
}
