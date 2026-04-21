<?php

namespace App\Http\Middleware;

use App\Core\Settings\SettingsRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Forces HTTPS when admin enables `security.force_https` and the request
 * is not over a secure connection. Skips local environment so dev keeps
 * working over plain HTTP.
 */
class ForceHttps
{
    public function __construct(protected SettingsRepository $settings) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (
            ! $request->isSecure()
            && (bool) $this->settings->get('security.force_https', false)
            && ! app()->environment('local', 'testing')
        ) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
