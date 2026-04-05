<?php

declare(strict_types=1);

namespace Modules\Permissions\Domain\Exceptions;

use InvalidArgumentException;

final class InvalidPermissionNameException extends InvalidArgumentException
{
    public static function forValue(string $value): self
    {
        return new self(sprintf(
            'Invalid permission name "%s". Expected format: module.action',
            $value
        ));
    }
}