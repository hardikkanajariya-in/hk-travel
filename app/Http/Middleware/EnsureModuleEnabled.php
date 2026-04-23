<?php

namespace App\Http\Middleware;

use App\Core\Settings\SettingsRepository;
use Closure;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleEnabled
{
    public function __construct(protected SettingsRepository $settings) {}

    public function handle($request, Closure $next, string $module): Response
    {
        $default = (bool) config("hk-modules.modules.{$module}.enabled", false);
        $enabled = (bool) $this->settings->get("modules.{$module}.enabled", $default);

        abort_unless($enabled, 404);

        return $next($request);
    }
}
