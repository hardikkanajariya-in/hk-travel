<?php

namespace App\Core\Routing;

use App\Core\Localization\LocaleManager;
use App\Core\Permalink\PermalinkRouter;

class PublicUrlGenerator
{
    public function __construct(
        protected LocaleManager $locales,
        protected PermalinkRouter $permalinks,
    ) {}

    public function entity(string $entityType, array $tokens = [], ?string $locale = null): string
    {
        return url($this->pathForLocale(
            $this->permalinks->build($entityType, $tokens),
            $locale
        ));
    }

    public function route(string $routeName, array $parameters = [], ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        if ($this->isDefaultLocale($locale)) {
            return route($routeName, $parameters);
        }

        return route('localized.'.$routeName, ['locale' => $locale, ...$parameters]);
    }

    public function pathForLocale(string $path, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $normalized = '/'.ltrim($path, '/');

        if ($this->isDefaultLocale($locale)) {
            return $normalized;
        }

        return '/'.trim($locale, '/').($normalized === '/' ? '' : $normalized);
    }

    protected function isDefaultLocale(?string $locale): bool
    {
        return blank($locale) || $locale === $this->locales->default();
    }
}
