<?php

namespace App\Core\Security;

use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;
use Spatie\Csp\Policy;
use Spatie\Csp\Preset;
use Spatie\Csp\Scheme;

/**
 * HK Travel Content Security Policy preset.
 *
 * Sensible defaults: own origin only, inline-style allowed for Tailwind
 * and runtime branding CSS, Cloudflare Turnstile + hCaptcha + reCAPTCHA
 * + Google/Bunny Fonts allowed. Toggleable from admin via
 * SettingsRepository (`security.csp.enabled`).
 */
class HkContentSecurityPolicy implements Preset
{
    public function configure(Policy $policy): void
    {
        $policy
            ->add(Directive::BASE, Keyword::SELF)
            ->add(Directive::DEFAULT, Keyword::SELF)
            ->add(Directive::OBJECT, Keyword::NONE)
            ->add(Directive::FORM_ACTION, Keyword::SELF)
            ->add(Directive::FRAME_ANCESTORS, Keyword::NONE)
            ->add(Directive::CONNECT, [
                Keyword::SELF,
                'https://challenges.cloudflare.com',
                'https://hcaptcha.com',
                'https://*.hcaptcha.com',
                'https://www.google.com',
                'https://www.recaptcha.net',
            ])
            ->add(Directive::FRAME, [
                'https://challenges.cloudflare.com',
                'https://hcaptcha.com',
                'https://*.hcaptcha.com',
                'https://www.google.com',
            ])
            ->add(Directive::SCRIPT, [
                Keyword::SELF,
                'https://challenges.cloudflare.com',
                'https://js.hcaptcha.com',
                'https://www.google.com/recaptcha/',
                'https://www.gstatic.com/recaptcha/',
            ])
            ->addNonce(Directive::SCRIPT)
            // Allows Livewire (and other trusted scripts) to inject further
            // scripts during runtime morphs/updates without each one needing
            // its own nonce. Required for `wire:click` round-trips.
            ->add(Directive::SCRIPT, Keyword::STRICT_DYNAMIC)
            ->add(Directive::STYLE, [
                Keyword::SELF,
                Keyword::UNSAFE_INLINE,
                'https://fonts.bunny.net',
                'https://fonts.googleapis.com',
            ])
            ->add(Directive::FONT, [
                Keyword::SELF,
                Scheme::DATA,
                'https://fonts.bunny.net',
                'https://fonts.gstatic.com',
            ])
            ->add(Directive::IMG, [
                Keyword::SELF,
                Scheme::DATA,
                Scheme::HTTPS,
                Scheme::BLOB,
            ])
            ->add(Directive::MEDIA, Keyword::SELF);

        if (app()->environment('local')) {
            $policy
                ->add(Directive::CONNECT, [Scheme::WS, Scheme::WSS, 'http://localhost:*', 'http://127.0.0.1:*'])
                ->add(Directive::SCRIPT, [Keyword::UNSAFE_EVAL, 'http://localhost:*', 'http://127.0.0.1:*']);
        }
    }
}
