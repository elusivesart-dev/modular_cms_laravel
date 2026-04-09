<?php

declare(strict_types=1);

namespace Modules\Users\Application\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Users\Domain\Contracts\UserEntityInterface;
use Modules\Users\Domain\DTOs\CreateUserData;

interface UserServiceInterface
{
    public function create(CreateUserData $data): UserEntityInterface;

    /**
     * @param array<string, mixed> $data
     */
    public function update(UserEntityInterface $user, array $data): UserEntityInterface;

    public function delete(UserEntityInterface $user): bool;

    public function findById(int $id): ?UserEntityInterface;

    public function findByEmail(string $email): ?UserEntityInterface;

    public function paginate(int $perPage = 15): LengthAwarePaginator;
}