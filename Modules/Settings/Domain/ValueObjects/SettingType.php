<?php

declare(strict_types=1);

namespace Modules\Settings\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class SettingType
{
    private const TYPES = [
        'string',
        'text',
        'integer',
        'boolean',
        'json',
    ];

    public function __construct(public string $value)
    {
        if (!in_array($value, self::TYPES, true)) {
            throw new InvalidArgumentException("Invalid setting type.");
        }
    }
}