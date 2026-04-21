<?php

namespace App\Core\Captcha\Drivers;

use App\Core\Captcha\CaptchaDriver;
use Illuminate\Http\Client\Factory as Http;

/**
 * hCaptcha driver — privacy-focused alternative to reCAPTCHA.
 *
 * @see https://docs.hcaptcha.com/
 */
class HCaptchaDriver implements CaptchaDriver
{
    protected const VERIFY_URL = 'https://hcaptcha.com/siteverify';

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

        return <<<HTML
            <div class="h-captcha" data-sitekey="{$key}"></div>
            <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
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
