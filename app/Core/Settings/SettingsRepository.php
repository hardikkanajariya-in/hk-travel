<?php

namespace App\Core\Settings;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Arr;

/**
 * DB-overridable, file-cached settings store.
 *
 * Lookups fall back to config('hk.*') so a clean install is fully
 * functional before any DB row exists. Writes (later, from the admin UI)
 * go to a `settings` table and bust the cache key.
 *
 * The DB table is added in a later migration step; until then, only the
 * config defaults are returned.
 */
class SettingsRepository
{
    protected const CACHE_KEY = 'hk:settings';

    protected const CACHE_TTL = 86400;

    /** @var array<string, mixed>|null */
    protected ?array $overrides = null;

    public function __construct(protected Cache $cache) {}

    public function get(string $key, mixed $default = null): mixed
    {
        $overrides = $this->load();

        if (Arr::has($overrides, $key)) {
            return Arr::get($overrides, $key);
        }

        return config('hk.'.$key, $default);
    }

    public function set(string $key, mixed $value): void
    {
        $overrides = $this->load();
        Arr::set($overrides, $key, $value);
        $this->cache->put(self::CACHE_KEY, $overrides, self::CACHE_TTL);
        $this->overrides = $overrides;
    }

    public function flush(): void
    {
        $this->cache->forget(self::CACHE_KEY);
        $this->overrides = null;
    }

    /** @return array<string, mixed> */
    protected function load(): array
    {
        if ($this->overrides !== null) {
            return $this->overrides;
        }

        return $this->overrides = $this->cache->remember(self::CACHE_KEY, self::CACHE_TTL, function (): array {
            // DB-backed loader added in a later step; defaults to empty until then.
            return [];
        });
    }
}
