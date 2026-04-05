<?php

declare(strict_types=1);

return [
    'route_prefix' => 'admin/users',
    'middleware' => ['web', 'auth'],
    'default_guard' => 'web',
    'password_min_length' => 8,
];