<?php

declare(strict_types=1);

namespace Modules\Roles\Domain\Exceptions;

use RuntimeException;

final class RoleAssignmentException extends RuntimeException
{
    public static function roleNotFound(string $roleSlug): self
    {
        return new self("Role with slug [{$roleSlug}] was not found.");
    }

    public static function systemRoleCannotBeRevoked(string $roleSlug): self
    {
        return new self("System role [{$roleSlug}] cannot be removed from the subject.");
    }
}