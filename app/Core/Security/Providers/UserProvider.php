<?php

declare(strict_types=1);

namespace App\Core\Security\Providers;

use App\Models\User;

final class UserProvider
{
    public function findById(int $id): ?User
    {
        return User::query()->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()
            ->where('email', $email)
            ->first();
    }
}