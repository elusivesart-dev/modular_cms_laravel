<?php

declare(strict_types=1);

namespace App\Core\Settings\Contracts;

interface SystemSettingsStoreInterface
{
    public function get(string $key, mixed $default = null): mixed;

    public function putString(
        string $group,
        string $key,
        string $value,
        ?string $label = null,
        ?string $description = null,
        bool $isPublic = false,
        bool $isSystem = true,
    ): void;
}