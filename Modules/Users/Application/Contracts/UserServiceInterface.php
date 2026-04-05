<?php

declare(strict_types=1);

namespace Modules\Users\Application\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Users\Domain\DTOs\CreateUserData;
use Modules\Users\Infrastructure\Models\User;

interface UserServiceInterface
{
    public function create(CreateUserData $data): User;

    public function update(User $user, array $data): User;

    public function delete(User $user): bool;

    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function paginate(int $perPage = 15): LengthAwarePaginator;
}