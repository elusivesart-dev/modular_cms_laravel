<?php

declare(strict_types=1);

namespace App\Core\Security\Authentication;

use Illuminate\Contracts\Auth\Factory as AuthFactory;

final class AuthenticationManager
{
    public function __construct(
        private readonly AuthFactory $auth
    ) {}

    public function guard(string $name = null)
    {
        return $this->auth->guard($name);
    }

    public function user()
    {
        return $this->auth->guard()->user();
    }

    public function check(): bool
    {
        return $this->auth->guard()->check();
    }
}