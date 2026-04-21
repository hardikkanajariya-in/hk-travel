<?php

namespace App\Core\Security;

use Stevebauman\Purify\Facades\Purify;

/**
 * Thin Sanitizer wrapper around HTMLPurifier (stevebauman/purify).
 *
 * Centralises the three sanitisation profiles defined in config/purify.php
 * so call sites never need to know which library is doing the work — if
 * we ever swap to another sanitizer this is the only file that changes.
 *
 * Profiles:
 *  - `default`     plain text + minimal inline tags (safe for anonymous input)
 *  - `rich-text`   admin/editor authored content (lists, tables, images)
 *  - `developer`   privileged users (custom HTML blocks, full markup minus scripts)
 */
class Sanitizer
{
    public static function clean(?string $html, string $profile = 'default'): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        return Purify::config($profile)->clean($html);
    }

    public static function rich(?string $html): string
    {
        return self::clean($html, 'rich-text');
    }

    public static function developer(?string $html): string
    {
        return self::clean($html, 'developer');
    }
}
