<?php

namespace Database\Seeders;

use App\Core\Modules\ModuleManager;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeds the four built-in roles and the union of every enabled module's
 * declared permissions plus the always-present admin permissions.
 *
 * Re-runnable: uses firstOrCreate so it's safe to invoke after enabling
 * a new module to refresh the permission catalogue.
 */
class RolesAndPermissionsSeeder extends Seeder
{
    /** @var array<int, string> */
    protected array $corePermissions = [
        'admin.access',
        'admin.settings.manage',
        'admin.modules.manage',
        'admin.themes.manage',
        'admin.users.manage',
        'admin.roles.manage',
        'admin.localization.manage',
        'admin.media.manage',
        'admin.security.manage',
        'admin.captcha.manage',
        'admin.audit.view',
        'admin.audit.purge',
        'admin.branding.manage',
        'admin.languages.manage',
        'admin.permalinks.manage',
        'admin.redirects.manage',
        'admin.email-templates.manage',
        'admin.notifications.manage',
        'admin.analytics.manage',
        'admin.pages.manage',
        'admin.menus.manage',
        'admin.widgets.manage',
        'admin.seo.manage',
        'admin.forms.manage',
        'admin.forms.submissions.view',
        'admin.crm.leads.view',
        'admin.crm.leads.manage',
        'admin.crm.leads.assign',
        'admin.crm.pipelines.manage',
        'pages.developer-blocks',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $modulePermissions = app(ModuleManager::class)->permissions();
        $all = array_values(array_unique(array_merge($this->corePermissions, $modulePermissions)));

        foreach ($all as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $customer = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        // super-admin gets everything via Gate::before in HkCoreServiceProvider;
        // we still attach the permission rows so admin UIs reflect the truth.
        $superAdmin->syncPermissions(Permission::all());

        $admin->syncPermissions(array_values(array_filter($all, fn (string $p): bool => $p !== 'admin.roles.manage'
            && $p !== 'admin.audit.purge'
            && $p !== 'pages.developer-blocks')));

        $editor->syncPermissions(array_values(array_filter($all, fn (string $p): bool => str_contains($p, '.view')
            || str_contains($p, '.create')
            || str_contains($p, '.update'))));

        $customer->syncPermissions([]);
    }
}
