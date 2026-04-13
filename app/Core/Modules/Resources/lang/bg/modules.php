<?php

declare(strict_types=1);

return [
    'errors' => [
        'invalid_manifest_name' => 'Невалиден module manifest [:path]: липсва или е невалидно полето [name].',
        'invalid_manifest_version' => 'Невалиден module manifest [:path]: липсва или е невалидно полето [version].',
        'invalid_manifest_provider' => 'Невалиден module manifest [:path]: липсва или е невалидно полето [provider].',
        'invalid_manifest_dependencies_array' => 'Невалиден module manifest [:path]: полето [dependencies] трябва да бъде масив.',
        'invalid_manifest_dependencies_items' => 'Невалиден module manifest [:path]: [dependencies] трябва да съдържа само непразни низове.',
        'modules_directory_scan_failed' => 'Неуспешно сканиране на директорията за модули [:path].',
        'module_manifest_read_failed' => 'Неуспешно прочитане на module manifest файла [:path].',
        'invalid_manifest_json' => 'Невалиден JSON в module manifest файла [:path].',
        'invalid_manifest_root' => 'Невалиден module manifest [:path]: root JSON стойността трябва да бъде обект.',
        'module_provider_not_found' => 'Module provider [:provider] за модул [:module] не беше намерен.',
    ],
];