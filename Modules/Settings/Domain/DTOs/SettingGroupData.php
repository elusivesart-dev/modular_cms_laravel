<?php

declare(strict_types=1);

namespace Modules\Settings\Domain\DTOs;

final readonly class SettingGroupData
{
    /**
     * @param array<string, mixed> $values
     */
    public function __construct(
        public string $group,
        public array $values,
    ) {
    }

    /**
     * @param array{group:string,values?:array<string,mixed>} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            group: (string) $data['group'],
            values: is_array($data['values'] ?? null) ? $data['values'] : [],
        );
    }
}