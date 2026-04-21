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
        'select_all' => 'Select all',
        'deselect_all' => 'Deselect all',
    ],

    'buttons' => [
        'back' => 'Back',
        'continue' => 'Continue',
        'install' => 'Install',
    ],

    'progress' => [
        'title' => 'Setting up your application',
        'subtitle' => 'This usually takes 10–30 seconds.',
        'hint' => 'Please don’t close this window — we’ll redirect you to the login page when it’s done.',
        'steps' => [
            'env' => 'Writing environment configuration…',
            'migrate' => 'Building database tables…',
            'seed' => 'Seeding default data…',
            'locale' => 'Configuring languages…',
            'admin' => 'Creating administrator account…',
            'modules' => 'Activating selected modules…',
            'cache' => 'Clearing caches…',
            'finalize' => 'Finalising installation…',
        ],
        'detail' => [
            'env' => '.env saved (driver: :driver)',
            'migrate' => ':count migrations applied',
            'seed' => 'Default records inserted',
            'locale' => 'Default language set to :code',
            'locale_missing' => 'Locale :code not present in languages table — left default unchanged',
            'admin' => 'Administrator :email created',
            'modules' => ':count module(s) enabled',
            'cache' => 'Config, view and application caches cleared',
            'finalize' => 'Installation lock written — redirecting to login',
        ],
        'failed_title' => 'Installation failed',
        'failed_hint' => 'Fix the issue below and click Retry to run the install again.',
        'retry' => 'Retry installation',
        'success_title' => 'Installation complete',
        'success_hint' => 'Redirecting you to the login page…',
    ],
];
