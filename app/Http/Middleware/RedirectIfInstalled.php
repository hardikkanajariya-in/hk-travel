<?php

namespace App\Http\Middleware;

use App\Core\Installer\InstallationState;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Hides the installer wizard once the app is installed.
 * Visiting /install after install redirects to the public homepage.
 */
class RedirectIfInstalled
{
    public function __construct(protected InstallationState $state) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->state->isInstalled()) {
            return redirect('/');
        }

        return $next($request);
    }
}
