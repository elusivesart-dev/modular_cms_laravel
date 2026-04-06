<?php

declare(strict_types=1);

namespace Modules\Roles\Domain\Exceptions;

use App\Core\RBAC\Exceptions\RoleOperationException;

final class RoleAssignmentException extends RoleOperationException
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