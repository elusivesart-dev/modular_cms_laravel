<?php

declare(strict_types=1);

namespace Modules\Permissions\Domain\ValueObjects;

use Modules\Permissions\Domain\Exceptions\InvalidPermissionNameException;

final readonly class PermissionName
{
    public function __construct(
        public string $value,
    ) {
        $this->validate($value);
    }

    private function validate(string $value): void
    {
        if (!preg_match('/^[a-z0-9]+(?:\.[a-z0-9_]+)+$/', $value)) {
            throw InvalidPermissionNameException::forValue($value);
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}