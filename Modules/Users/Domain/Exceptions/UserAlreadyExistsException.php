<?php

declare(strict_types=1);

namespace Modules\Users\Domain\Exceptions;

use RuntimeException;

final class UserAlreadyExistsException extends RuntimeException
{
    public static function forEmail(string $email): self
    {
        return new self(__('users::users.exceptions.user_already_exists', ['email' => $email]));
    }
}