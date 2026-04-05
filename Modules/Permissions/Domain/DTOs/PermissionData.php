<?php

declare(strict_types=1);

namespace Modules\Permissions\Domain\DTOs;

final readonly class PermissionData
{
    public function __construct(
        public string $name,
        public ?string $label,
        public ?string $description,
        public array $roleIds = [],
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) $data['name'],
            label: isset($data['label']) && $data['label'] !== '' ? (string) $data['label'] : null,
            description: isset($data['description']) && $data['description'] !== '' ? (string) $data['description'] : null,
            roleIds: array_values(array_unique(array_map('intval', $data['role_ids'] ?? []))),
        );
    }
}