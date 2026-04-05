<?php

declare(strict_types=1);

return [
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'cache' => [
        'enabled' => env('LOCALIZATION_CACHE_ENABLED', true),
        'ttl' => (int) env('LOCALIZATION_CACHE_TTL', 3600),
        'key' => env('LOCALIZATION_CACHE_KEY', 'core.localization.available_languages'),
    ],

    'paths' => [
        'core_lang' => app_path('Core/Localization/Resources/lang'),
        'packages_tmp' => storage_path('app/tmp/languages'),
        'packages_installed' => app_path('Core/Localization/Resources/lang'),
    ],

    'fallback_languages' => [
        'bg' => [
            'code' => 'bg',
            'name' => 'Bulgarian',
            'native_name' => 'Български',
            'direction' => 'ltr',
            'version' => '1.0.0',
            'installed_path' => app_path('Core/Localization/Resources/lang/bg'),
            'is_active' => true,
            'is_system' => true,
        ],
        'en' => [
            'code' => 'en',
            'name' => 'English',
            'native_name' => 'English',
            'direction' => 'ltr',
            'version' => '1.0.0',
            'installed_path' => app_path('Core/Localization/Resources/lang/en'),
            'is_active' => true,
            'is_system' => true,
        ],
    ],
];