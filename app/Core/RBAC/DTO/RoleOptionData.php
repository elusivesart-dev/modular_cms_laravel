<?php

declare(strict_types=1);

namespace App\Core\RBAC\DTO;

final readonly class RoleOptionData
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public ?string $description,
        public bool $isSystem,
    ) {
    }
}
