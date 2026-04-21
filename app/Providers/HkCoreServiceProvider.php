<?php

namespace App\Providers;

use App\Core\Captcha\CaptchaService;
use App\Core\Installer\InstallationState;
use App\Core\Localization\LocaleManager;
use App\Core\Modules\ModuleManager;
use App\Core\Settings\SettingsRepository;
use App\Core\Storage\StorageManager;
use App\Core\Theme\ThemeManager;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\Translator;

/**
 * Boots HK Travel core services.
 *
 * Registers singletons for settings, storage, captcha, install state,
 * the module registry, theme manager and locale manager. The companion
 * ModuleServiceProvider then iterates enabled modules and bootstraps
 * each one's routes/views/migrations.
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

        $this->app->singleton(ThemeManager::class, fn ($app) => (new ThemeManager(
            $app, $app->make(Filesystem::class)
        ))->discover());

        $this->app->singleton(LocaleManager::class, fn ($app) => new LocaleManager(
            $app->make(Translator::class)
        ));
    }

    public function boot(): void
    {
        // Activate the configured public theme; safe to call even when no
        // theme is installed (it falls back to bare app views).
        $this->app->make(ThemeManager::class)->activate();
    }
}
