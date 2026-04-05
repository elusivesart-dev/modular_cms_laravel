<?php

declare(strict_types=1);

namespace Modules\Users\Infrastructure\Auth;

use App\Core\Auth\Contracts\AuthenticatableUserProviderInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use InvalidArgumentException;
use Modules\Users\Domain\Contracts\UserRepositoryInterface;
use Modules\Users\Infrastructure\Models\User;

final class UserAuthenticatableProvider implements AuthenticatableUserProviderInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
    ) {
    }

    public function findByEmail(string $email): ?Authenticatable
    {
        return $this->users->findByEmail($email);
    }

    public function isActive(Authenticatable $user): bool
    {
        return $this->toUser($user)->is_active;
    }

    public function hasVerifiedEmail(Authenticatable $user): bool
    {
        return $this->toUser($user)->hasVerifiedEmail();
    }

    public function getPasswordHash(Authenticatable $user): string
    {
        return (string) $this->toUser($user)->getAuthPassword();
    }

    private function toUser(Authenticatable $user): User
    {
        if (! $user instanceof User) {
            throw new InvalidArgumentException('Unsupported authenticatable instance.');
        }

        return $user;
    }
}
