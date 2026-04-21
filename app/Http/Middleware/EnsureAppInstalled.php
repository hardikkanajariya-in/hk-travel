<?php

namespace App\Http\Middleware;

use App\Core\Installer\InstallationState;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirects every public/admin request to the installer wizard until
 * the app is installed. The wizard routes themselves use
 * RedirectIfInstalled, so the two middlewares form a complete gate.
 */
class EnsureAppInstalled
{
    public function __construct(protected InstallationState $state) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (app()->runningUnitTests()) {
            return $next($request);
        }

        if (! $this->state->isInstalled() && ! $request->is('install*', 'livewire*', '_debugbar*', 'up')) {
            return redirect()->route('install.welcome');
        }

        return $next($request);
    }
}
