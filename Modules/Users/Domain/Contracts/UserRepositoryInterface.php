<?php

declare(strict_types=1);

namespace Modules\Users\Domain\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Users\Infrastructure\Models\User;

interface UserRepositoryInterface
{
    public function create(array $data): User;

    public function update(User $user, array $data): User;

    public function delete(User $user): bool;

    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function slugExists(string $slug, int $ignoreUserId = 0): bool;

    public function clearEmailVerification(User $user): User;

    public function markEmailAsVerified(User $user): User;

    public function paginate(int $perPage = 15): LengthAwarePaginator;
}