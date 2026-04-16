<?php

declare(strict_types=1);

namespace Modules\Users\Infrastructure\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use InvalidArgumentException;
use Modules\Users\Domain\Contracts\UserEntityInterface;
use Modules\Users\Domain\Contracts\UserRepositoryInterface;
use Modules\Users\Infrastructure\Models\User;

final class UserRepository implements UserRepositoryInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): UserEntityInterface
    {
        /** @var User $user */
        $user = User::query()->create($data);

        return $user;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(UserEntityInterface $user, array $data): UserEntityInterface
    {
        $model = $this->toModel($user);

        $model->update($data);

        return $model->refresh();
    }

    public function delete(UserEntityInterface $user): bool
    {
        return (bool) $this->toModel($user)->delete();
    }

    public function findById(int $id): ?UserEntityInterface
    {
        return User::query()->find($id);
    }

    public function findByEmail(string $email): ?UserEntityInterface
    {
        return User::query()
            ->where('email', $email)
            ->first();
    }

    public function slugExists(string $slug, int $ignoreUserId = 0): bool
    {
        return User::query()
            ->where('slug', $slug)
            ->when(
                $ignoreUserId > 0,
                static fn ($query) => $query->whereKeyNot($ignoreUserId)
            )
            ->exists();
    }

    public function clearEmailVerification(UserEntityInterface $user): UserEntityInterface
    {
        $model = $this->toModel($user);

        $model->forceFill([
            'email_verified_at' => null,
        ])->save();

        return $model->refresh();
    }

    public function markEmailAsVerified(UserEntityInterface $user): UserEntityInterface
    {
        $model = $this->toModel($user);

        if (method_exists($model, 'markEmailAsVerified')) {
            $model->markEmailAsVerified();
        } else {
            $model->forceFill([
                'email_verified_at' => now(),
            ])->save();
        }

        return $model->refresh();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return User::query()
            ->latest('id')
            ->paginate($perPage);
    }

    private function toModel(UserEntityInterface $user): User
    {
        if (! $user instanceof User) {
            throw new InvalidArgumentException('Unsupported user entity implementation.');
        }

        return $user;
    }
}