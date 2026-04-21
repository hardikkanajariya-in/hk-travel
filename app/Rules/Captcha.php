<?php

namespace App\Rules;

use App\Core\Captcha\CaptchaService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Request;

/**
 * Captcha validation rule.
 *
 * Verifies the request's captcha token via the active driver. Skips
 * verification entirely when the service is disabled or no driver is
 * configured, which keeps every public form working in dev without
 * keys.
 *
 * Usage:
 *
 *     $request->validate([
 *         'captcha' => [new Captcha()],
 *     ]);
 *
 * The actual token is read from the request field that the active
 * driver expects (e.g. `cf-turnstile-response` for Turnstile) — the
 * `$value` passed to the rule is ignored and may be empty/null.
 */
class Captcha implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $service = app(CaptchaService::class);

        if (! $service->enabled()) {
            return;
        }

        $token = (string) Request::input($service->tokenFieldName(), $value ?? '');

        if ($token === '') {
            $fail((string) __('validation.captcha.missing'));

            return;
        }

        if (! $service->verify($token, Request::ip())) {
            $fail((string) __('validation.captcha.failed'));
        }
    }
}
