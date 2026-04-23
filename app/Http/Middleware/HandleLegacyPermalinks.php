<?php

namespace App\Http\Middleware;

use App\Core\Permalink\PermalinkRouter;
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

            if (! $redirect) {
                $redirect = PermalinkRedirect::query()
                    ->where('is_active', true)
                    ->get()
                    ->first(function (PermalinkRedirect $candidate) use ($path): bool {
                        return $this->templateMatches($candidate->from_path, $path);
                    });
            }
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

        $target = $this->targetPath($redirect, $path);

        return redirect($target, $redirect->status_code ?: 301);
    }

    protected function targetPath(PermalinkRedirect $redirect, string $path): string
    {
        if (str_starts_with($redirect->to_path, 'http')) {
            return $redirect->to_path;
        }

        $params = $this->extractTemplateParameters($redirect->from_path, $path);
        $toPath = $redirect->to_path;

        foreach ($params as $key => $value) {
            $toPath = str_replace('{'.$key.'}', $value, $toPath);
        }

        return '/'.ltrim($toPath, '/');
    }

    protected function templateMatches(string $template, string $path): bool
    {
        return $this->extractTemplateParameters($template, $path) !== null;
    }

    /**
     * @return array<string, string>|null
     */
    protected function extractTemplateParameters(string $template, string $path): ?array
    {
        return app(PermalinkRouter::class)->extractTokens($template, $path);
    }
}
