<?php

declare(strict_types=1);

namespace Modules\Settings\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class SettingKey
{
    public function __construct(
        public string $value
    ) {
        if (!preg_match('/^[a-z0-9]+(?:\.[a-z0-9_]+)+$/', $value)) {
            throw new InvalidArgumentException("Invalid setting key format.");
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}