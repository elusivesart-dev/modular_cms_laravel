<?php

declare(strict_types=1);

namespace App\Core\RBAC\Exceptions;

use LogicException;

final class PermissionModuleNotImplementedException extends LogicException
{
    public static function create(): self
    {
        return new self(__('rbac.permissions_module_not_implemented'));
    }
}