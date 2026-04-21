<?php

namespace App\Http\Middleware;

use App\Core\Localization\LocaleManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves and applies the request locale via LocaleManager, then
 * persists explicit ?lang= choices to the session so subsequent
 * navigations stick.
 */
class SetLocale
{
    public function __construct(protected LocaleManager $manager) {}

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->manager->detect($request);
        $this->manager->apply($locale);

        if ($request->query('lang') && $request->hasSession()) {
            $request->session()->put('locale', $locale);
        }

        return $next($request);
    }
}
