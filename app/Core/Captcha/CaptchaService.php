<?php

namespace App\Core\Captcha;

use App\Core\Captcha\Drivers\HCaptchaDriver;
use App\Core\Captcha\Drivers\ReCaptchaV3Driver;
use App\Core\Captcha\Drivers\TurnstileDriver;
use App\Core\Settings\SettingsRepository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Client\Factory;
use InvalidArgumentException;

/**
 * Captcha facade. Reads driver/keys from SettingsRepository (DB-backed,
 * config fallback) so admins can swap providers without redeploying.
 *
 * Usage in views: <x-ui.captcha />
 * Usage in controllers/Livewire: app(CaptchaService::class)->verify($token)
 *
 * The `enabled()` check is layered: requires both the master toggle AND
 * a driver that has both a site key and secret key configured. This
 * keeps every public form working in dev with no captcha keys.
 */
class CaptchaService
{
    public function __construct(protected Container $container) {}

    public function enabled(): bool
    {
        if (! (bool) $this->setting('enabled', false)) {
            return false;
        }

        return $this->driver() !== null;
    }

    public function shouldProtect(string $route): bool
    {
        if (! $this->enabled()) {
            return false;
        }

        return in_array($route, (array) $this->setting('protect', config('hk.captcha.protect', [])), true);
    }

    public function render(string $action = 'submit'): string
    {
        return $this->driver()?->render($action) ?? '';
    }

    public function verify(string $token, ?string $ip = null): bool
    {
        return $this->driver()?->verify($token, $ip) ?? false;
    }

    public function driverName(): string
    {
        return (string) $this->setting('driver', 'turnstile');
    }

    /**
     * Name of the form field the active driver expects the token in.
     */
    public function tokenFieldName(): string
    {
        return match ($this->driverName()) {
            'turnstile' => 'cf-turnstile-response',
            'hcaptcha' => 'h-captcha-response',
            'recaptcha' => 'g-recaptcha-response',
            default => 'captcha',
        };
    }

    public function driver(): ?CaptchaDriver
    {
        $name = $this->driverName();

        return match ($name) {
            'turnstile' => $this->makeTurnstile(),
            'hcaptcha' => $this->makeHCaptcha(),
            'recaptcha' => $this->makeReCaptcha(),
            default => throw new InvalidArgumentException("Unknown captcha driver [$name]."),
        };
    }

    protected function makeTurnstile(): ?TurnstileDriver
    {
        $site = $this->setting('drivers.turnstile.site_key');
        $secret = $this->setting('drivers.turnstile.secret_key');

        if (! $site || ! $secret) {
            return null;
        }

        return new TurnstileDriver(
            $this->container->make(Factory::class),
            (string) $site,
            (string) $secret,
        );
    }

    protected function makeHCaptcha(): ?HCaptchaDriver
    {
        $site = $this->setting('drivers.hcaptcha.site_key');
        $secret = $this->setting('drivers.hcaptcha.secret_key');

        if (! $site || ! $secret) {
            return null;
        }

        return new HCaptchaDriver(
            $this->container->make(Factory::class),
            (string) $site,
            (string) $secret,
        );
    }

    protected function makeReCaptcha(): ?ReCaptchaV3Driver
    {
        $site = $this->setting('drivers.recaptcha.site_key');
        $secret = $this->setting('drivers.recaptcha.secret_key');

        if (! $site || ! $secret) {
            return null;
        }

        return new ReCaptchaV3Driver(
            $this->container->make(Factory::class),
            (string) $site,
            (string) $secret,
            (float) $this->setting('drivers.recaptcha.threshold', 0.5),
        );
    }

    protected function setting(string $key, mixed $default = null): mixed
    {
        $repo = $this->container->bound(SettingsRepository::class)
            ? $this->container->make(SettingsRepository::class)
            : null;

        return $repo
            ? $repo->get("captcha.{$key}", $default ?? config("hk.captcha.{$key}"))
            : config("hk.captcha.{$key}", $default);
    }
}
