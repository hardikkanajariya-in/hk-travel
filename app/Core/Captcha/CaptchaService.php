<?php

namespace App\Core\Captcha;

use App\Core\Captcha\Drivers\TurnstileDriver;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Client\Factory;
use InvalidArgumentException;

/**
 * Captcha facade. Reads driver/keys from config/hk.php (and DB overrides
 * via SettingsRepository at a later step), constructs the driver lazily.
 *
 * Usage in views: <x-ui.captcha />
 * Usage in controllers/Livewire: app(CaptchaService::class)->verify($token)
 */
class CaptchaService
{
    public function __construct(protected Container $container) {}

    public function enabled(): bool
    {
        if (! config('hk.captcha.enabled', false)) {
            return false;
        }

        $driver = $this->driver();

        return $driver !== null;
    }

    public function shouldProtect(string $route): bool
    {
        if (! $this->enabled()) {
            return false;
        }

        return in_array($route, (array) config('hk.captcha.protect', []), true);
    }

    public function render(string $action = 'submit'): string
    {
        return $this->driver()?->render($action) ?? '';
    }

    public function verify(string $token, ?string $ip = null): bool
    {
        return $this->driver()?->verify($token, $ip) ?? false;
    }

    protected function driver(): ?CaptchaDriver
    {
        $name = config('hk.captcha.driver', 'turnstile');

        return match ($name) {
            'turnstile' => $this->makeTurnstile(),
            'hcaptcha', 'recaptcha' => null, // scaffolded — wired in a later release
            default => throw new InvalidArgumentException("Unknown captcha driver [$name]."),
        };
    }

    protected function makeTurnstile(): ?TurnstileDriver
    {
        $site = config('hk.captcha.drivers.turnstile.site_key');
        $secret = config('hk.captcha.drivers.turnstile.secret_key');

        if (! $site || ! $secret) {
            return null;
        }

        return new TurnstileDriver(
            $this->container->make(Factory::class),
            $site,
            $secret,
        );
    }
}
