<?php

namespace App\Core\Captcha;

interface CaptchaDriver
{
    public function render(string $action = 'submit'): string;

    public function verify(string $token, ?string $ip = null): bool;
}
