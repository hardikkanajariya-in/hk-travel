<?php

namespace App\Providers;

use App\Core\Captcha\CaptchaService;
use App\Core\Installer\InstallationState;
use App\Core\Modules\ModuleManager;
use App\Core\Settings\SettingsRepository;
use App\Core\Storage\StorageManager;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\ServiceProvider;

/**
 * Boots HK Travel core services.
 *
 * Registers singletons for settings, storage, captcha, install state and
 * the module registry. ModuleServiceProvider then iterates registered
 * modules and bootstraps each one's routes/views/migrations.
 */
class HkCoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(config_path('hk.php'), 'hk');
        $this->mergeConfigFrom(config_path('hk-modules.php'), 'hk-modules');

        $this->app->singleton(SettingsRepository::class, fn ($app) => new SettingsRepository(
            $app->make(CacheRepository::class)
        ));

        $this->app->singleton(StorageManager::class, fn ($app) => new StorageManager(
            $app->make(FilesystemManager::class)
        ));

        $this->app->singleton(CaptchaService::class, fn ($app) => new CaptchaService($app));

        $this->app->singleton(InstallationState::class, fn ($app) => new InstallationState(
            $app->make(Filesystem::class)
        ));

        $this->app->singleton(ModuleManager::class, fn ($app) => (new ModuleManager($app))->discover());
    }

    public function boot(): void
    {
        // Module bootstrapping happens in ModuleServiceProvider, which is
        // registered after this provider in bootstrap/providers.php.
    }
}
