<?php

declare(strict_types=1);

return [
    'path' => base_path('themes'),

    'default_public' => env('CMS_DEFAULT_PUBLIC_THEME', 'default'),
    'default_admin' => env('CMS_DEFAULT_ADMIN_THEME', 'default'),

    'manifest' => 'theme.json',

    'groups' => [
        'public',
        'admin',
    ],

    'settings_group' => 'system',

    'settings_keys' => [
        'public' => 'system.theme.public.active',
        'admin' => 'system.theme.admin.active',
    ],
];