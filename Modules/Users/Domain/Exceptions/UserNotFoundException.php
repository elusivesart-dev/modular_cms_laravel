<?php

declare(strict_types=1);

namespace Modules\Users\Domain\Exceptions;

use RuntimeException;

final class UserNotFoundException extends RuntimeException
{
    public static function forId(int $id): self
    {
        return new self(__('users::users.exceptions.user_not_found', ['id' => $id]));
    }
}