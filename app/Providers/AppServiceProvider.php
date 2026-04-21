<?php

namespace App\Providers;

use App\Core\Settings\SettingsRepository;
use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureRateLimiters();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    /**
     * Register named rate limiters used across the app and admin.
     *
     * Limits are read from SettingsRepository (DB-backed) with config
     * fallback so admins can change them without redeploying.
     */
    protected function configureRateLimiters(): void
    {
        foreach (['auth', 'api', 'public_forms'] as $group) {
            $name = str_replace('_', '-', $group);
            RateLimiter::for("hk-{$name}", function (Request $request) use ($group) {
                [$max, $minutes] = $this->parseLimit($group);
                $key = ($request->user()?->id ?? $request->ip()).'|'.$group;

                return Limit::perMinutes($minutes, $max)->by($key);
            });
        }
    }

    /**
     * Parse a `max,minutes` setting string (e.g. "5,1") into a tuple.
     *
     * @return array{0:int,1:int}
     */
    protected function parseLimit(string $group): array
    {
        $value = (string) app(SettingsRepository::class)->get(
            "security.rate_limits.{$group}",
            config("hk.security.rate_limits.{$group}", '60,1'),
        );

        $parts = array_map('trim', explode(',', $value));
        $max = (int) ($parts[0] ?? 60);
        $minutes = (int) ($parts[1] ?? 1);

        return [max(1, $max), max(1, $minutes)];
    }
}
