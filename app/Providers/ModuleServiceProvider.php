<?php

namespace App\Providers;

use App\Core\Modules\ModuleContract;
use App\Core\Modules\ModuleManager;
use Illuminate\Support\ServiceProvider;

/**
 * Boots every enabled HK Travel module.
 *
 * Each module's routes/migrations/views/lang/permissions are loaded only
 * when its enabled flag in config/hk-modules.php is true. This keeps the
 * application surface small for fresh installs and lets a deployment
 * disable entire feature areas without code changes.
 */
class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $manager = $this->app->make(ModuleManager::class);

        foreach ($manager->all() as $module) {
            if ($provider = $module->provider()) {
                $this->app->register($provider);
            }
        }
    }

    public function boot(): void
    {
        $manager = $this->app->make(ModuleManager::class);

        foreach ($manager->all() as $module) {
            $this->bootModule($module);
        }
    }

    protected function bootModule(ModuleContract $module): void
    {
        if ($path = $module->migrationsPath()) {
            $this->loadMigrationsFrom($path);
        }

        if ($path = $module->viewsPath()) {
            $this->loadViewsFrom($path, $module->viewNamespace() ?? $module->key());
        }

        if ($path = $module->langPath()) {
            $this->loadTranslationsFrom($path, $module->key());
        }

        if (($path = $module->publicRoutesPath()) && file_exists($path)) {
            $this->loadRoutesFrom($path);
        }

        if (($path = $module->adminRoutesPath()) && file_exists($path)) {
            $this->loadRoutesFrom($path);
        }
    }
}
