<?php

namespace App\Core\Modules;

use App\Core\Settings\SettingsRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;

/**
 * Central registry & boot orchestrator for HK Travel modules.
 *
 * Reads the enabled-module map from `config/hk-modules.php`, resolves
 * each manifest class, and exposes them so HkCoreServiceProvider can
 * register routes/migrations/views/translations only for enabled modules.
 *
 * Disabled modules are completely invisible: no routes, no menus, no
 * search index, no migrations run.
 */
class ModuleManager
{
    /** @var Collection<string, ModuleContract> */
    protected Collection $modules;

    public function __construct(protected Application $app)
    {
        $this->modules = collect();
    }

    public function discover(): self
    {
        $modules = config('hk-modules.modules', []);
        $overrides = $this->safelyResolveOverrides();

        foreach ($modules as $key => $config) {
            $enabled = $overrides[$key] ?? ($config['enabled'] ?? false);

            if (! $enabled) {
                continue;
            }

            $class = $config['manifest'] ?? null;

            if (! $class || ! class_exists($class)) {
                continue;
            }

            $instance = $this->app->make($class);

            if (! $instance instanceof ModuleContract) {
                continue;
            }

            $this->modules->put($instance->key(), $instance);
        }

        return $this;
    }

    /**
     * Pull per-module enable flags from SettingsRepository.
     * Returns [] when settings table doesn't exist yet (pre-install).
     *
     * @return array<string, bool>
     */
    protected function safelyResolveOverrides(): array
    {
        try {
            $settings = $this->app->make(SettingsRepository::class);
            $value = $settings->get('modules');
        } catch (\Throwable) {
            return [];
        }

        if (! is_array($value)) {
            return [];
        }

        $out = [];
        foreach ($value as $key => $row) {
            // Flat shape: 'tours.enabled' => true
            if (is_string($key) && str_ends_with($key, '.enabled')) {
                $moduleKey = substr($key, 0, -strlen('.enabled'));
                $out[$moduleKey] = (bool) $row;

                continue;
            }
            // Nested shape: 'tours' => ['enabled' => true]
            if (is_array($row) && array_key_exists('enabled', $row)) {
                $out[$key] = (bool) $row['enabled'];
            }
        }

        return $out;
    }

    /** @return Collection<string, ModuleContract> */
    public function all(): Collection
    {
        return $this->modules;
    }

    public function enabled(string $key): bool
    {
        return $this->modules->has($key);
    }

    public function get(string $key): ?ModuleContract
    {
        return $this->modules->get($key);
    }

    /** @return array<int, string> */
    public function permissions(): array
    {
        return $this->modules
            ->flatMap(fn (ModuleContract $m) => $m->permissions())
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Aggregated admin sidebar items from every enabled module. Filters
     * out items whose `permission` the current user lacks (when one is
     * declared).
     *
     * @return array<int, array{label:string, route:string, icon?:?string, permission?:?string, group?:?string, module:string}>
     */
    public function adminMenuItems(): array
    {
        $user = $this->app->bound('auth') ? $this->app->make('auth')->user() : null;

        $items = [];
        foreach ($this->modules as $module) {
            if (! $module instanceof Module) {
                continue;
            }
            foreach ($module->adminMenu() as $item) {
                $perm = $item['permission'] ?? null;
                if ($perm && $user && method_exists($user, 'can') && ! $user->can($perm)) {
                    continue;
                }
                $items[] = $item + ['module' => $module->key()];
            }
        }

        return $items;
    }

    /**
     * Aggregated sitemap entries from every enabled module.
     *
     * @return iterable<int, array<string, mixed>>
     */
    public function sitemapEntries(): iterable
    {
        foreach ($this->modules as $module) {
            if (! $module instanceof Module) {
                continue;
            }
            foreach ($module->sitemapEntries() as $entry) {
                yield $entry;
            }
        }
    }
}
