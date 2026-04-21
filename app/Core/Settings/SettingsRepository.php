<?php

namespace App\Core\Settings;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * DB-overridable, file-cached settings store.
 *
 * Lookups fall back to config('hk.*') so a clean install is fully
 * functional before any DB row exists. Writes go to the `hk_settings`
 * table and bust the cache key.
 *
 * Keys use dotted paths (e.g. `brand.name`); the first segment is the
 * `group` column and the remainder is the `key` column.
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
        [$group, $subKey] = $this->split($key);

        DB::table('hk_settings')->updateOrInsert(
            ['group' => $group, 'key' => $subKey],
            [
                'value' => json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'type' => $this->detectType($value),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $this->flush();
    }

    /** @param array<string, mixed> $values */
    public function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function forget(string $key): void
    {
        [$group, $subKey] = $this->split($key);
        DB::table('hk_settings')->where(['group' => $group, 'key' => $subKey])->delete();
        $this->flush();
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
            try {
                $rows = DB::table('hk_settings')->get(['group', 'key', 'value']);
            } catch (Throwable) {
                return []; // Table may not exist yet (pre-install).
            }

            $out = [];
            foreach ($rows as $row) {
                $path = $row->group.($row->key !== '' ? '.'.$row->key : '');
                Arr::set($out, $path, json_decode((string) $row->value, true));
            }

            return $out;
        });
    }

    /** @return array{0: string, 1: string} */
    protected function split(string $key): array
    {
        if (! str_contains($key, '.')) {
            return [$key, ''];
        }

        [$group, $rest] = explode('.', $key, 2);

        return [$group, $rest];
    }

    protected function detectType(mixed $value): string
    {
        return match (true) {
            is_bool($value) => 'bool',
            is_int($value) => 'int',
            is_float($value) => 'float',
            is_array($value) => 'array',
            default => 'string',
        };
    }
}
