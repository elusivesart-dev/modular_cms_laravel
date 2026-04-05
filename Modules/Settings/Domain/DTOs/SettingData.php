<?php

declare(strict_types=1);

namespace Modules\Settings\Domain\DTOs;

final readonly class SettingData
{
    public function __construct(
        public string $group,
        public string $key,
        public mixed $value,
        public string $type,
        public ?string $label,
        public ?string $description,
        public bool $isPublic,
        public bool $isSystem,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            group: (string) $data['group'],
            key: (string) $data['key'],
            value: $data['value'] ?? null,
            type: (string) ($data['type'] ?? 'string'),
            label: $data['label'] ?? null,
            description: $data['description'] ?? null,
            isPublic: (bool) ($data['is_public'] ?? false),
            isSystem: (bool) ($data['is_system'] ?? false),
        );
    }
}