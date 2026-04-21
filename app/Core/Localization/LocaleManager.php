<?php

namespace App\Core\Localization;

use Illuminate\Http\Request;
use Illuminate\Translation\Translator;

/**
 * Centralised locale switcher.
 *
 * Resolution order: explicit ?lang= → session → cookie → user preference
 * (later, when User has a `locale` column) → browser Accept-Language →
 * config('hk.localization.default').
 *
 * RTL detection is exposed for layout templates so they can flip
 * direction without duplicating the locale list.
 */
class LocaleManager
{
    public function __construct(protected Translator $translator) {}

    public function detect(Request $request): string
    {
        $supported = $this->supported();

        $candidates = array_filter([
            $request->query('lang'),
            $request->session()?->get('locale'),
            $request->cookie('locale'),
            $request->user()?->locale ?? null,
            $request->getPreferredLanguage($supported),
            (string) config('hk.localization.default', 'en'),
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

        return (string) config('hk.localization.default', 'en');
    }

    public function apply(string $locale): void
    {
        app()->setLocale($locale);
        $this->translator->setLocale($locale);
    }

    public function isRtl(?string $locale = null): bool
    {
        $locale = $locale ?? app()->getLocale();
        $rtl = (array) config('hk.localization.rtl_locales', []);

        return in_array($locale, $rtl, true);
    }

    /** @return array<int, string> */
    public function supported(): array
    {
        $supported = (array) config('hk.localization.supported', ['en']);

        return array_values(array_unique(array_filter($supported)));
    }
}
