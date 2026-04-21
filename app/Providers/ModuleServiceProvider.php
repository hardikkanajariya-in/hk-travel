<?php

namespace App\Providers;

use App\Core\Modules\ModuleContract;
use App\Core\Modules\ModuleManager;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

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
        // Register sub-providers eagerly based on file config only.
        // DB-overridable enable flags are evaluated in boot() (DB-safe phase).
        foreach ((array) config('hk-modules.modules', []) as $key => $config) {
            if (! ($config['enabled'] ?? false)) {
                continue;
            }

            $class = $config['manifest'] ?? null;

            if (! $class || ! class_exists($class)) {
                continue;
            }

            $instance = $this->app->make($class);

            if ($instance instanceof ModuleContract && ($provider = $instance->provider())) {
                $this->app->register($provider);
            }
        }
    }

    public function boot(): void
    {
        // ModuleManager has been populated in HkCoreServiceProvider::boot().
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

        foreach ($module->livewireComponents() as $alias => $class) {
            if (class_exists($class)) {
                Livewire::component($alias, $class);
            }
        }
    }
}
