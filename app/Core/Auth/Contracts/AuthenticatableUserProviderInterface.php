<?php

declare(strict_types=1);

namespace App\Core\Auth\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface AuthenticatableUserProviderInterface
{
    public function findByEmail(string $email): ?Authenticatable;

    public function isActive(Authenticatable $user): bool;

    public function hasVerifiedEmail(Authenticatable $user): bool;

    public function getPasswordHash(Authenticatable $user): string;
}
