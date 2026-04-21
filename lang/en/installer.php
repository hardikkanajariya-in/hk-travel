<?php

return [
    'wizard' => [
        'badge' => 'Setup wizard',
        'title' => 'HK Travel — Setup',
        'progress' => "Step :current of :total — let's get your site online.",
        'progress_aria' => 'Progress',
    ],

    'steps' => [
        'server' => 'Server',
        'app' => 'App',
        'database' => 'Database',
        'admin' => 'Admin',
        'modules' => 'Modules',
    ],

    'server' => [
        'heading' => 'Server requirements',
        'subtitle' => 'All checks must pass before continuing.',
        'ok' => 'OK',
        'missing' => 'Missing',
    ],

    'app' => [
        'heading' => 'Application settings',
        'fields' => [
            'site_name' => 'Site name',
            'site_url' => 'Site URL',
            'site_url_hint' => 'e.g. https://example.com',
            'locale' => 'Default locale',
            'timezone' => 'Timezone',
        ],
        'locales' => [
            'en' => 'English',
            'hi' => 'हिन्दी (Hindi)',
            'gu' => 'ગુજરાતી (Gujarati)',
        ],
    ],

    'database' => [
        'heading' => 'Database connection',
        'driver' => 'Driver',
        'drivers' => [
            'sqlite' => 'SQLite (recommended for small deployments)',
            'mysql' => 'MySQL',
            'mariadb' => 'MariaDB',
            'pgsql' => 'PostgreSQL',
        ],
        'host' => 'Host',
        'port' => 'Port',
        'database' => 'Database name',
        'username' => 'Username',
        'password' => 'Password',
        'sqlite_notice' => 'SQLite database will be created at :path.',
    ],

    'admin_step' => [
        'heading' => 'Administrator account',
        'fields' => [
            'name' => 'Full name',
            'email' => 'Email',
            'password' => 'Password',
            'password_confirmation' => 'Confirm password',
            'password_hint' => 'At least 8 characters',
        ],
    ],

    'modules' => [
        'heading' => 'Enable travel modules',
        'subtitle' => 'Pick the features to activate now. You can toggle these any time from the admin panel.',
    ],

    'buttons' => [
        'back' => 'Back',
        'continue' => 'Continue',
        'install' => 'Install',
    ],
];
