<?php

namespace App\Http\Middleware;

use App\Core\Settings\SettingsRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds security headers (HSTS, X-Frame-Options, X-Content-Type-Options,
 * Referrer-Policy, Permissions-Policy, Cross-Origin-Opener-Policy) to
 * every response. CSP itself is delivered by Spatie\Csp\AddCspHeaders
 * which is registered alongside this middleware in bootstrap/app.php.
 *
 * All toggles read from SettingsRepository so admins can tune behaviour
 * without redeploying.
 */
class SecurityHeaders
{
    public function __construct(protected SettingsRepository $settings) {}

    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if ((bool) $this->settings->get('security.headers.frame_deny', true)) {
            $response->headers->set('X-Frame-Options', 'DENY');
        }

        if ((bool) $this->settings->get('security.headers.nosniff', true)) {
            $response->headers->set('X-Content-Type-Options', 'nosniff');
        }

        $response->headers->set(
            'Referrer-Policy',
            (string) $this->settings->get('security.headers.referrer_policy', 'strict-origin-when-cross-origin'),
        );

        $response->headers->set(
            'Permissions-Policy',
            (string) $this->settings->get(
                'security.headers.permissions_policy',
                'camera=(), microphone=(), geolocation=(self), payment=(self)',
            ),
        );

        if ((bool) $this->settings->get('security.headers.coop', true)) {
            $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        }

        if ($request->isSecure() && (bool) $this->settings->get('security.hsts.enabled', false)) {
            $maxAge = (int) $this->settings->get('security.hsts.max_age', 31536000);
            $value = "max-age={$maxAge}";
            if ($this->settings->get('security.hsts.include_subdomains', true)) {
                $value .= '; includeSubDomains';
            }
            if ($this->settings->get('security.hsts.preload', false)) {
                $value .= '; preload';
            }
            $response->headers->set('Strict-Transport-Security', $value);
        }

        return $response;
    }
}
