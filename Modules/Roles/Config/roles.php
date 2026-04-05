<?php

declare(strict_types=1);

return [
    'route_prefix' => 'admin/roles',
    'middleware' => ['web', 'auth'],
    'cache_ttl' => 3600,
    'system_roles' => [
        'super-admin',
        'admin',
        'editor',
    ],
];