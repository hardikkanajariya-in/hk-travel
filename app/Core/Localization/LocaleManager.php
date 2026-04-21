<?php

namespace App\Core\Localization;

use App\Models\Language;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Translation\Translator;
use Throwable;

/**
 * Centralised locale switcher.
 *
 * Resolution order: URL prefix segment → explicit ?lang= → session →
 * cookie → user preference → browser Accept-Language → default.
 *
 * The active list is read from the `languages` table when present,
 * with a config fallback so a clean install (pre-migrate) still works.
 */
class LocaleManager
{
    protected const CACHE_KEY = 'hk:languages';

    protected const CACHE_TTL = 86400;

    public function __construct(
        protected Translator $translator,
        protected Cache $cache,
    ) {}

    public function detect(Request $request): string
    {
        $supported = $this->supported();
        $default = $this->default();

        $candidates = array_filter([
            $this->fromUrlPrefix($request, $supported),
            $request->query('lang'),
            $request->session()?->get('locale'),
            $request->cookie('locale'),
            $request->user()?->locale ?? null,
            $request->getPreferredLanguage($supported),
            $default,
        ]);

        foreach ($candidates as $candidate) {
            $candidate = strtolower(substr((string) $candidate, 0, 5));
            $short = substr($candidate, 0, 2);

            if (in_array($candidate, $supported, true)) {
                return $candidate;
            }

            if (in_array($short, $supported, true)) {
                return $short;
            }
        }

        return $default;
    }

    public function apply(string $locale): void
    {
        app()->setLocale($locale);
        $this->translator->setLocale($locale);
    }

    public function isRtl(?string $locale = null): bool
    {
        $locale = $locale ?? app()->getLocale();

        $lang = $this->languages()->firstWhere('code', $locale);
        if ($lang) {
            return (bool) $lang['is_rtl'];
        }

        $rtl = (array) config('hk.localization.rtl_locales', []);

        return in_array($locale, $rtl, true);
    }

    /** @return array<int, string> */
    public function supported(): array
    {
        $langs = $this->languages();
        if ($langs->isNotEmpty()) {
            return $langs->pluck('code')->all();
        }

        $supported = (array) config('hk.localization.supported', ['en']);

        return array_values(array_unique(array_filter($supported)));
    }

    public function default(): string
    {
        $default = $this->languages()->firstWhere('is_default', true);

        return $default['code'] ?? (string) config('hk.localization.default', 'en');
    }

    /** @return Collection<int, array<string, mixed>> */
    public function languages(): Collection
    {
        return $this->cache->remember(self::CACHE_KEY, self::CACHE_TTL, function (): Collection {
            try {
                /** @var Collection<int, Language> $models */
                $models = Language::query()->active()->get(['code', 'name', 'native_name', 'flag', 'is_rtl', 'is_default']);

                return $models->map(fn (Language $l): array => $l->toArray())->values();
            } catch (Throwable) {
                return collect();
            }
        });
    }

    public function flush(): void
    {
        $this->cache->forget(self::CACHE_KEY);
    }

    /**
     * @param  array<int, string>  $supported
     */
    protected function fromUrlPrefix(Request $request, array $supported): ?string
    {
        $segment = strtolower((string) $request->segment(1));
        if ($segment === '') {
            return null;
        }

        if (in_array($segment, $supported, true)) {
            return $segment;
        }

        $short = substr($segment, 0, 2);

        return in_array($short, $supported, true) ? $short : null;
    }
}
