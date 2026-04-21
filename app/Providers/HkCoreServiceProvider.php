<?php

namespace App\Providers;

use App\Core\Captcha\CaptchaService;
use App\Core\Installer\InstallationState;
use App\Core\Livewire\EnquiryForm;
use App\Core\Localization\LocaleManager;
use App\Core\Modules\ModuleManager;
use App\Core\PageBuilder\BlockRegistry;
use App\Core\PageBuilder\BlockRenderer;
use App\Core\Seo\BreadcrumbService;
use App\Core\Seo\JsonLd;
use App\Core\Seo\SeoManager;
use App\Core\Seo\SitemapGenerator;
use App\Core\Settings\SettingsRepository;
use App\Core\Storage\StorageManager;
use App\Core\Theme\ThemeManager;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\Translator;
use Livewire\Livewire;

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

        $this->app->singleton(ModuleManager::class, fn ($app) => new ModuleManager($app));

        $this->app->singleton(ThemeManager::class, fn ($app) => (new ThemeManager(
            $app, $app->make(Filesystem::class)
        ))->discover());

        $this->app->singleton(LocaleManager::class, fn ($app) => new LocaleManager(
            $app->make(Translator::class),
            $app->make(CacheRepository::class)
        ));

        $this->app->singleton(BlockRegistry::class);
        $this->app->singleton(BlockRenderer::class);

        $this->app->singleton(SeoManager::class);
        $this->app->singleton(JsonLd::class);
        $this->app->scoped(BreadcrumbService::class);
        $this->app->singleton(SitemapGenerator::class);
    }

    public function boot(): void
    {
        // Discover & register module manifests now that the DB is available.
        $this->app->make(ModuleManager::class)->discover();

        // Activate the configured public theme; safe to call even when no
        // theme is installed (it falls back to bare app views).
        $this->app->make(ThemeManager::class)->activate();

        // Page-builder views can be overridden by the active theme by
        // shipping a `page-builder/blocks/{name}.blade.php` file.
        View::addNamespace('page-builder', resource_path('views/page-builder'));

        // Shared core views (enquiry form, etc.) used by every module.
        View::addNamespace('hk-core', resource_path('views/hk-core'));

        // Reusable enquiry form Livewire component.
        if (class_exists(Livewire::class)) {
            Livewire::component('hk.enquiry-form', EnquiryForm::class);
        }

        // `@zone('footer-1')` renders every active widget bound to that zone.
        Blade::directive('zone', function (string $expression): string {
            return "<?php
                \$__zoneRenderer = app(\\App\\Core\\PageBuilder\\BlockRenderer::class);
                \$__zoneUser = auth()->user();
                foreach (\\App\\Models\\Widget::forZone({$expression}) as \$__zoneWidget) {
                    echo \$__zoneRenderer->renderWidget(\$__zoneWidget, \$__zoneUser);
                }
                unset(\$__zoneRenderer, \$__zoneUser, \$__zoneWidget);
            ?>";
        });

        // super-admin role bypasses every Gate::check().
        Gate::before(fn ($user, string $ability) => method_exists($user, 'hasRole') && $user->hasRole('super-admin') ? true : null);
    }
}
