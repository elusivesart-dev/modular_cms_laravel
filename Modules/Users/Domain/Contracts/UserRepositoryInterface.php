<?php

declare(strict_types=1);

namespace Modules\Users\Domain\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): UserEntityInterface;

    /**
     * @param array<string, mixed> $data
     */
    public function update(UserEntityInterface $user, array $data): UserEntityInterface;

    public function delete(UserEntityInterface $user): bool;

    public function findById(int $id): ?UserEntityInterface;

    public function findByEmail(string $email): ?UserEntityInterface;

    public function paginate(int $perPage = 15): LengthAwarePaginator;
}