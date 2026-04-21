<?php

namespace App\Core\Captcha\Drivers;

use App\Core\Captcha\CaptchaDriver;
use Illuminate\Http\Client\Factory as Http;

/**
 * Cloudflare Turnstile driver — privacy-friendly, free, recommended default.
 *
 * @see https://developers.cloudflare.com/turnstile/
 */
class TurnstileDriver implements CaptchaDriver
{
    protected const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public function __construct(
        protected Http $http,
        protected ?string $siteKey,
        protected ?string $secretKey,
    ) {}

    public function render(string $action = 'submit'): string
    {
        if (! $this->siteKey) {
            return '';
        }

        $key = e($this->siteKey);
        $action = e($action);

        return <<<HTML
            <div class="cf-turnstile" data-sitekey="{$key}" data-action="{$action}"></div>
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
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

        return (bool) ($response->json('success') ?? false);
    }
}
