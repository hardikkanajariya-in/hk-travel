<?php

namespace App\Core\Modules;

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

        foreach ($modules as $key => $config) {
            if (! ($config['enabled'] ?? false)) {
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
}
