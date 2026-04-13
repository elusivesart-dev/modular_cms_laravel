<?php

declare(strict_types=1);

namespace Modules\Users\Application\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Modules\Users\Domain\Contracts\UserEntityInterface;

interface UserAdministrationWorkflowInterface
{
    /**
     * @return array<int, mixed>
     */
    public function availableRoles(): array;

    /**
     * @return Collection<int, mixed>
     */
    public function assignedRoles(UserEntityInterface $user): Collection;

    /**
     * @return array<int, string>
     */
    public function selectedRoleSlugs(UserEntityInterface $user): array;

    /**
     * @param array<string, mixed> $payload
     */
    public function store(array $payload): UserEntityInterface;

    /**
     * @param array<string, mixed> $payload
     */
    public function update(
        UserEntityInterface $user,
        array $payload,
        ?UploadedFile $avatar = null,
        ?int $uploadedBy = null
    ): UserEntityInterface;
}