<?php

namespace App\Http\Middleware;

use App\Models\PermalinkRedirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * If a request 404s — or arrives at a path matching `permalink_redirects.from_path`
 * — issue a 301/302 to the configured destination and bump the hit counter.
 *
 * Wired BEFORE route matching so legacy URLs short-circuit cheaply.
 */
class HandleLegacyPermalinks
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = '/'.ltrim($request->path(), '/');

        try {
            $redirect = PermalinkRedirect::query()
                ->where('is_active', true)
                ->where('from_path', $path)
                ->first();
        } catch (Throwable) {
            return $next($request);
        }

        if (! $redirect) {
            return $next($request);
        }

        $redirect->forceFill([
            'hit_count' => $redirect->hit_count + 1,
            'last_hit_at' => now(),
        ])->saveQuietly();

        $target = str_starts_with($redirect->to_path, 'http')
            ? $redirect->to_path
            : '/'.ltrim($redirect->to_path, '/');

        return redirect($target, $redirect->status_code ?: 301);
    }
}
