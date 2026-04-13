<?php

declare(strict_types=1);

return [
    'errors' => [
        'invalid_manifest_name' => 'Invalid module manifest [:path]: missing or invalid [name].',
        'invalid_manifest_version' => 'Invalid module manifest [:path]: missing or invalid [version].',
        'invalid_manifest_provider' => 'Invalid module manifest [:path]: missing or invalid [provider].',
        'invalid_manifest_dependencies_array' => 'Invalid module manifest [:path]: [dependencies] must be an array.',
        'invalid_manifest_dependencies_items' => 'Invalid module manifest [:path]: [dependencies] must contain only non-empty strings.',
        'modules_directory_scan_failed' => 'Unable to scan modules directory [:path].',
        'module_manifest_read_failed' => 'Unable to read module manifest [:path].',
        'invalid_manifest_json' => 'Invalid JSON in module manifest [:path].',
        'invalid_manifest_root' => 'Invalid module manifest [:path]: root JSON value must be an object.',
        'module_provider_not_found' => 'Module provider [:provider] for module [:module] was not found.',
    ],
];