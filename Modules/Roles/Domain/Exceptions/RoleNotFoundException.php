<?php

declare(strict_types=1);

namespace Modules\Roles\Domain\Exceptions;

use RuntimeException;

final class RoleNotFoundException extends RuntimeException
{
    public static function forSlug(string $roleSlug): self
    {
        return new self(__('roles::roles.exceptions.role_not_found', ['role' => $roleSlug]));
    }
}