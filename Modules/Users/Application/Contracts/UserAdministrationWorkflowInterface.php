<?php

declare(strict_types=1);

namespace Modules\Users\Application\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Modules\Users\Infrastructure\Models\User;

interface UserAdministrationWorkflowInterface
{
    /**
     * @return array<int, mixed>
     */
    public function availableRoles(): array;

    /**
     * @return Collection<int, mixed>
     */
    public function assignedRoles(User $user): Collection;

    /**
     * @return array<int, string>
     */
    public function selectedRoleSlugs(User $user): array;

    /**
     * @param array<string, mixed> $payload
     */
    public function store(array $payload): User;

    /**
     * @param array<string, mixed> $payload
     */
    public function update(User $user, array $payload, ?UploadedFile $avatar = null, ?int $uploadedBy = null): User;
}