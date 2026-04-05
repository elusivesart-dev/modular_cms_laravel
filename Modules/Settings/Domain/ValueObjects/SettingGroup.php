<?php

declare(strict_types=1);

namespace Modules\Settings\Domain\ValueObjects;

use Modules\Settings\Domain\Exceptions\SettingValidationException;

final readonly class SettingGroup
{
    public function __construct(
        public string $value,
    ) {
        $this->validate($value);
    }

    private function validate(string $value): void
    {
        if (!preg_match('/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/', $value)) {
            throw SettingValidationException::invalidGroup($value);
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}