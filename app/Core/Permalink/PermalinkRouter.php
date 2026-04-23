<?php

namespace App\Core\Permalink;

use App\Core\Settings\SettingsRepository;
use App\Models\Permalink;
use App\Models\PermalinkRedirect;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Collection;
use Throwable;

/**
 * Resolves runtime permalink patterns for entities (tour, hotel, page…).
 *
 * Patterns are stored in the `permalinks` table. If the table is missing
 * (clean install before migrate) we fall back to `config('hk.permalinks')`.
 *
 * Usage:
 *   $url = app(PermalinkRouter::class)->build('tour', ['slug' => $tour->slug]);
 *   $pattern = app(PermalinkRouter::class)->pattern('tour');
 *
 * Collision detection: when admin updates a pattern, call
 * `collisions(string $entity, string $pattern)` to list other entities
 * whose pattern would conflict.
 */
class PermalinkRouter
{
    protected const CACHE_KEY = 'hk:permalinks';

    protected const CACHE_TTL = 86400;

    public function __construct(
        protected SettingsRepository $settings,
        protected Cache $cache,
    ) {}

    /**
     * @param  array<string, mixed>  $tokens
     */
    public function build(string $entityType, array $tokens = []): string
    {
        $pattern = $this->pattern($entityType);

        return rtrim($this->replaceTokens($pattern, $tokens), '/') ?: '/';
    }

    public function pattern(string $entityType): string
    {
        $patterns = $this->all();

        return $patterns[$entityType]
            ?? (string) config("hk.permalinks.{$entityType}", '/'.$entityType.'/{slug}');
    }

    /**
     * @return array{entity_type:string, tokens:array<string, string>}|null
     */
    public function match(string $path): ?array
    {
        $normalizedPath = $this->normalize($path);

        return collect($this->all())
            ->sortBy(fn (string $pattern): int => substr_count($pattern, '{'))
            ->map(function (string $pattern, string $entityType) use ($normalizedPath): ?array {
                $tokens = $this->extractTokens($pattern, $normalizedPath);

                if ($tokens === null) {
                    return null;
                }

                return [
                    'entity_type' => $entityType,
                    'tokens' => $tokens,
                ];
            })
            ->first(fn (?array $match): bool => $match !== null);
    }

    /** @return array<string, string> */
    public function all(): array
    {
        return $this->cache->remember(self::CACHE_KEY, self::CACHE_TTL, function (): array {
            try {
                return Permalink::query()
                    ->where('is_active', true)
                    ->pluck('pattern', 'entity_type')
                    ->all();
            } catch (Throwable) {
                return [];
            }
        });
    }

    /**
     * @return Collection<int, Permalink>
     */
    public function collisions(string $entityType, string $pattern): Collection
    {
        return Permalink::query()
            ->where('entity_type', '!=', $entityType)
            ->where('pattern', $this->normalize($pattern))
            ->get();
    }

    public function set(string $entityType, string $pattern): Permalink
    {
        $normalized = $this->normalize($pattern);
        $previous = $this->pattern($entityType);

        $row = Permalink::updateOrCreate(
            ['entity_type' => $entityType, 'pattern' => $normalized],
            ['is_active' => true],
        );

        // Disable any other (now stale) patterns for this entity.
        Permalink::where('entity_type', $entityType)
            ->where('id', '!=', $row->id)
            ->update(['is_active' => false]);

        if ($previous !== $normalized) {
            PermalinkRedirect::updateOrCreate(
                ['from_path' => $previous],
                [
                    'to_path' => $normalized,
                    'status_code' => 301,
                    'is_active' => true,
                ],
            );
        }

        $this->flush();

        return $row;
    }

    public function flush(): void
    {
        $this->cache->forget(self::CACHE_KEY);
    }

    public function normalize(string $pattern): string
    {
        $pattern = '/'.ltrim(trim($pattern), '/');
        $pattern = preg_replace('#/+#', '/', $pattern) ?? $pattern;

        return rtrim($pattern, '/') ?: '/';
    }

    /**
     * @param  array<string, mixed>  $tokens
     */
    protected function replaceTokens(string $pattern, array $tokens): string
    {
        return preg_replace_callback('/\{(\w+)\}/', function (array $m) use ($tokens): string {
            $key = $m[1];

            return isset($tokens[$key]) ? rawurlencode((string) $tokens[$key]) : $m[0];
        }, $pattern) ?? $pattern;
    }

    /**
     * @return array<string, string>|null
     */
    public function extractTokens(string $pattern, string $path): ?array
    {
        $normalizedPattern = $this->normalize($pattern);
        $normalizedPath = $this->normalize($path);

        if (! str_contains($normalizedPattern, '{')) {
            return $normalizedPattern === $normalizedPath ? [] : null;
        }

        $segments = preg_split('/(\{\w+\})/', $normalizedPattern, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        if ($segments === false) {
            return null;
        }

        $regex = collect($segments)
            ->map(function (string $segment): string {
                if (preg_match('/^\{(\w+)\}$/', $segment, $matches) === 1) {
                    return '(?P<'.$matches[1].'>[^/]+)';
                }

                return preg_quote($segment, '#');
            })
            ->implode('');

        if (! preg_match('#^'.$regex.'$#', $normalizedPath, $matches)) {
            return null;
        }

        return collect($matches)
            ->filter(fn ($value, $key) => is_string($key) && is_string($value))
            ->all();
    }
}
