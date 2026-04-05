<?php

declare(strict_types=1);

return [
    'route_prefix' => 'admin/settings',
    'middleware' => ['web', 'auth'],
    'per_page' => 15,
];