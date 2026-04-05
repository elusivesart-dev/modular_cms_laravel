<?php

declare(strict_types=1);

namespace Modules\Auth\Domain\Exceptions;

use RuntimeException;

final class AuthenticationFailedException extends RuntimeException
{
    public static function invalidCredentials(): self
    {
        return new self(__('auth-module::auth.invalid_credentials'));
    }

    public static function emailNotVerified(): self
    {
        return new self(__('auth-module::auth.email_not_verified'));
    }
}