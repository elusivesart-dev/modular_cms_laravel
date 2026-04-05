<?php

declare(strict_types=1);

namespace Modules\Roles\Domain\DTOs;

final readonly class RoleData
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $description,
        public bool $is_system = false,
    ) {
    }

    /**
     * @param array{name:string,slug:string,description?:string|null,is_system?:bool} $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            name: $payload['name'],
            slug: $payload['slug'],
            description: $payload['description'] ?? null,
            is_system: (bool) ($payload['is_system'] ?? false),
        );
    }
}