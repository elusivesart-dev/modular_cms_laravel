<?php

declare(strict_types=1);

namespace App\Core\Security\Authentication;

use Illuminate\Contracts\Auth\Factory as AuthFactory;

final class SessionGuard
{
    public function __construct(
        private readonly AuthFactory $auth
    ) {}

    public function attempt(array $credentials): bool
    {
        return $this->auth->guard('web')->attempt($credentials);
    }

    public function logout(): void
    {
        $this->auth->guard('web')->logout();
    }
}