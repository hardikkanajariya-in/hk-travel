<?php

return [
    'wizard' => [
        'badge' => 'સેટઅપ વિઝાર્ડ',
        'title' => 'HK Travel — સેટઅપ',
        'progress' => 'પગલું :current / :total — ચાલો તમારી સાઇટ ઓનલાઇન કરીએ.',
        'progress_aria' => 'પ્રગતિ',
    ],

    'steps' => [
        'server' => 'સર્વર',
        'app' => 'એપ',
        'database' => 'ડેટાબેઝ',
        'admin' => 'એડમિન',
        'modules' => 'મોડ્યુલ્સ',
    ],

    'server' => [
        'heading' => 'સર્વર આવશ્યકતાઓ',
        'subtitle' => 'આગળ વધતાં પહેલાં બધી ચકાસણીઓ પાસ થવી જોઈએ.',
        'ok' => 'ઓકે',
        'missing' => 'ગેરહાજર',
    ],

    'app' => [
        'heading' => 'એપ્લિકેશન સેટિંગ્સ',
        'fields' => [
            'site_name' => 'સાઇટનું નામ',
            'site_url' => 'સાઇટ URL',
            'site_url_hint' => 'જેમ કે https://example.com',
            'locale' => 'ડિફોલ્ટ ભાષા',
            'timezone' => 'સમય ઝોન',
        ],
        'locales' => [
            'en' => 'English',
            'hi' => 'हिन्दी (Hindi)',
            'gu' => 'ગુજરાતી (Gujarati)',
        ],
    ],

    'database' => [
        'heading' => 'ડેટાબેઝ કનેક્શન',
        'driver' => 'ડ્રાઇવર',
        'drivers' => [
            'sqlite' => 'SQLite (નાની ડિપ્લોયમેન્ટ્સ માટે ભલામણ)',
            'mysql' => 'MySQL',
            'mariadb' => 'MariaDB',
            'pgsql' => 'PostgreSQL',
        ],
        'host' => 'હોસ્ટ',
        'port' => 'પોર્ટ',
        'database' => 'ડેટાબેઝનું નામ',
        'username' => 'વપરાશકર્તા નામ',
        'password' => 'પાસવર્ડ',
        'sqlite_notice' => 'SQLite ડેટાબેઝ :path પર બનાવવામાં આવશે.',
    ],

    'admin_step' => [
        'heading' => 'એડમિનિસ્ટ્રેટર ખાતું',
        'fields' => [
            'name' => 'પૂરું નામ',
            'email' => 'ઈમેલ',
            'password' => 'પાસવર્ડ',
            'password_confirmation' => 'પાસવર્ડ ખાતરી કરો',
            'password_hint' => 'ઓછામાં ઓછા 8 અક્ષર',
        ],
    ],

    'modules' => [
        'heading' => 'ટ્રાવેલ મોડ્યુલ્સ સક્રિય કરો',
        'subtitle' => 'હમણાં સક્રિય કરવા માટે સુવિધાઓ પસંદ કરો. તમે કોઈપણ સમયે એડમિન પેનલમાંથી તેમને બદલી શકો છો.',
    ],

    'buttons' => [
        'back' => 'પાછળ',
        'continue' => 'ચાલુ રાખો',
        'install' => 'ઇન્સ્ટોલ કરો',
    ],
];
