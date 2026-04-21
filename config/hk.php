<?php

/*
 * HK Travel core configuration.
 *
 * Defaults below ship with a clean install. Every value is overridable
 * at runtime through the SettingsRepository (DB-backed, file-cached) and
 * the admin Settings UI — never hand-edit this file in production.
 */

return [

    'brand' => [
        'name' => env('HK_BRAND_NAME', 'HK Travel'),
        'tagline' => env('HK_BRAND_TAGLINE', 'Discover the world, your way.'),
        'logo' => null,
        'favicon' => null,
        'primary_color' => '#2563eb',
        'accent_color' => '#f97316',
    ],

    'theme' => [
        'active' => env('HK_THEME', 'default'),
        'path' => resource_path('themes'),
        'dark_mode' => 'auto', // auto | light | dark
    ],

    'localization' => [
        'default' => env('HK_LOCALE', 'en'),
        'fallback' => env('HK_FALLBACK_LOCALE', 'en'),
        'supported' => ['en', 'hi', 'gu'],
        'rtl_locales' => ['ar', 'he', 'fa', 'ur'],
        'auto_detect' => true,
    ],

    'cache' => [
        'driver' => env('HK_CACHE_DRIVER', 'file'),
        'public_pages_ttl' => 3600,
        'tag_prefix' => 'hk:',
    ],

    'storage' => [
        'default_disk' => 'local',
        'public_disk' => 'public',
        'available_drivers' => ['local'],
        'scaffolded_drivers' => ['s3', 'spaces', 'gcs'],
    ],

    'security' => [
        'force_https' => env('HK_FORCE_HTTPS', false),
        'rate_limits' => [
            'auth' => '5,1',
            'api' => '60,1',
            'public_forms' => '10,1',
        ],
        'two_factor' => [
            'enabled' => true,
            'enforce_for_roles' => ['super-admin', 'admin'],
        ],
    ],

    'audit' => [
        'enabled' => true,
        'retention_days' => env('HK_AUDIT_RETENTION_DAYS', 180),
    ],

    'captcha' => [
        'enabled' => false,
        'driver' => 'turnstile',
        'protect' => ['login', 'register', 'password.reset', 'contact', 'inquiry', 'comment', 'review'],
        'drivers' => [
            'turnstile' => [
                'site_key' => env('TURNSTILE_SITE_KEY'),
                'secret_key' => env('TURNSTILE_SECRET_KEY'),
            ],
            'hcaptcha' => [
                'site_key' => env('HCAPTCHA_SITE_KEY'),
                'secret_key' => env('HCAPTCHA_SECRET_KEY'),
            ],
            'recaptcha' => [
                'site_key' => env('RECAPTCHA_SITE_KEY'),
                'secret_key' => env('RECAPTCHA_SECRET_KEY'),
            ],
        ],
    ],

    'payments' => [
        'default_currency' => env('HK_CURRENCY', 'USD'),
        'gateways' => [
            'stripe' => ['enabled' => false, 'test_mode' => true],
            'bank_transfer' => ['enabled' => true],
            'cash' => ['enabled' => true],
            'paypal' => ['enabled' => false, 'scaffolded' => true],
            'razorpay' => ['enabled' => false, 'scaffolded' => true],
            'paystack' => ['enabled' => false, 'scaffolded' => true],
        ],
    ],

    'notifications' => [
        'default_mailer' => env('MAIL_MAILER', 'log'),
        'channels' => [
            'mail' => ['enabled' => true],
            'database' => ['enabled' => true],
            'sms' => ['enabled' => false, 'driver' => 'twilio'],
            'webpush' => ['enabled' => false],
        ],
    ],

    'permalinks' => [
        'tour' => '/tours/{slug}',
        'hotel' => '/hotels/{slug}',
        'flight_search' => '/flights',
        'package' => '/packages/{slug}',
        'blog_post' => '/blog/{slug}',
        'destination' => '/destinations/{country}/{city}',
        'page' => '/{slug}',
    ],

    'seo' => [
        'site_title_separator' => '·',
        'meta_description' => null,
        'og_image' => null,
        'robots_default' => 'index, follow',
        'canonical_host' => null,
        'analytics' => [
            'ga4' => null,
            'gtm' => null,
            'meta_pixel' => null,
            'hotjar' => null,
            'custom_head' => null,
            'custom_body' => null,
        ],
    ],

    'general' => [
        'timezone' => env('APP_TIMEZONE', 'UTC'),
        'date_format' => 'Y-m-d',
        'time_format' => 'H:i',
    ],

    'contact' => [
        'email' => null,
        'phone' => null,
        'address' => null,
        'hours' => null,
        'map_embed' => null,
        'social' => [
            'facebook' => null,
            'instagram' => null,
            'twitter' => null,
            'youtube' => null,
            'linkedin' => null,
            'whatsapp' => null,
        ],
    ],

    'cookie' => [
        'enabled' => true,
        'message' => 'We use cookies to improve your experience. You can manage your preferences anytime.',
        'accept_label' => 'Accept all',
        'reject_label' => 'Reject',
        'settings_label' => 'Preferences',
        'policy_url' => '/privacy',
        'position' => 'bottom',
    ],

    'installer' => [
        'lock_file' => 'installed.lock',
    ],

];
