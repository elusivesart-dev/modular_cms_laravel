<?php

declare(strict_types=1);

namespace App\Core\Security\Policies;

use Illuminate\Support\Facades\Gate;

final class PolicyManager
{
    public function define(string $ability, callable $callback): void
    {
        Gate::define($ability, $callback);
    }

    public function allows(string $ability, mixed $arguments = null): bool
    {
        return Gate::allows($ability, $arguments);
    }
}