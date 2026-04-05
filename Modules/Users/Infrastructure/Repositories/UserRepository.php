<?php

declare(strict_types=1);

namespace Modules\Users\Infrastructure\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Users\Domain\Contracts\UserRepositoryInterface;
use Modules\Users\Infrastructure\Models\User;

final class UserRepository implements UserRepositoryInterface
{
    public function create(array $data): User
    {
        /** @var User $user */
        $user = User::query()->create($data);

        return $user;
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->refresh();
    }

    public function delete(User $user): bool
    {
        return (bool) $user->delete();
    }

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

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return User::query()
            ->latest('id')
            ->paginate($perPage);
    }
}