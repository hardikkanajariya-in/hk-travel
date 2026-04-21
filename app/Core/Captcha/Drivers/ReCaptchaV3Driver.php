<?php

namespace App\Core\Captcha\Drivers;

use App\Core\Captcha\CaptchaDriver;
use Illuminate\Http\Client\Factory as Http;

/**
 * Google reCAPTCHA v3 driver — invisible, score-based.
 *
 * Returns success when Google reports `success=true` and the returned
 * score meets or exceeds the configured threshold (default 0.5).
 *
 * @see https://developers.google.com/recaptcha/docs/v3
 */
class ReCaptchaV3Driver implements CaptchaDriver
{
    protected const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    public function __construct(
        protected Http $http,
        protected ?string $siteKey,
        protected ?string $secretKey,
        protected float $threshold = 0.5,
    ) {}

    public function render(string $action = 'submit'): string
    {
        if (! $this->siteKey) {
            return '';
        }

        $key = e($this->siteKey);
        $action = e($action);

        return <<<HTML
            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response" />
            <script src="https://www.google.com/recaptcha/api.js?render={$key}" async defer></script>
            <script>
                window.addEventListener('load', () => {
                    if (typeof grecaptcha === 'undefined') return;
                    grecaptcha.ready(() => {
                        grecaptcha.execute('{$key}', { action: '{$action}' }).then(token => {
                            document.getElementById('g-recaptcha-response').value = token;
                        });
                    });
                });
            </script>
            HTML;
    }

    public function verify(string $token, ?string $ip = null): bool
    {
        if (! $this->secretKey) {
            return false;
        }

        $payload = [
            'secret' => $this->secretKey,
            'response' => $token,
        ];

        if ($ip) {
            $payload['remoteip'] = $ip;
        }

        $response = $this->http->asForm()->post(self::VERIFY_URL, $payload);

        if (! ($response->json('success') ?? false)) {
            return false;
        }

        return (float) ($response->json('score') ?? 0.0) >= $this->threshold;
    }
}
