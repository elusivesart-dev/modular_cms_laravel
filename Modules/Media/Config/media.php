<?php

declare(strict_types=1);

return [
    'route_prefix' => 'admin/media',
    'middleware' => ['web', 'auth'],
    'disk' => 'public',
    'directory' => 'media',
    'max_file_size' => 10240,
    'allowed_mime_types' => [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
        'image/svg+xml',
        'application/pdf',
        'video/mp4',
        'video/webm',
        'audio/mpeg',
        'audio/wav',
    ],
    'allowed_extensions' => [
        'jpg',
        'jpeg',
        'png',
        'webp',
        'gif',
        'svg',
        'pdf',
        'mp4',
        'webm',
        'mp3',
        'wav',
    ],
    'visibility' => 'public',
    'per_page' => 24,
];