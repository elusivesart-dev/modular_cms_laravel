<?php

declare(strict_types=1);

namespace Modules\Users\Application\Services;

use App\Core\Database\Contracts\TransactionManagerInterface;
use App\Core\RBAC\Contracts\RoleCatalogInterface;
use App\Core\RBAC\Contracts\RoleManagerInterface;
use DomainException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Media\Application\Contracts\MediaServiceInterface;
use Modules\Users\Application\Contracts\UserAdministrationWorkflowInterface;
use Modules\Users\Application\Contracts\UserServiceInterface;
use Modules\Users\Domain\Contracts\UserEntityInterface;
use Modules\Users\Domain\DTOs\CreateUserData;
use Modules\Users\Infrastructure\Models\User;

final class UserAdministrationWorkflowService implements UserAdministrationWorkflowInterface
{
    public function __construct(
        private readonly UserServiceInterface $users,
        private readonly RoleManagerInterface $roles,
        private readonly RoleCatalogInterface $roleCatalog,
        private readonly MediaServiceInterface $media,
        private readonly TransactionManagerInterface $transactions,
    ) {
    }

    public function availableRoles(): array
    {
        return $this->roleCatalog->all();
    }

    public function assignedRoles(UserEntityInterface $user): Collection
    {
        return $this->roles->rolesForSubject(User::class, (int) $user->getKey());
    }

    public function selectedRoleSlugs(UserEntityInterface $user): array
    {
        return $this->assignedRoles($user)
            ->pluck('slug')
            ->map(static fn (mixed $slug): string => (string) $slug)
            ->values()
            ->all();
    }

    public function store(array $payload): UserEntityInterface
    {
        return $this->transactions->transaction(function () use ($payload): UserEntityInterface {
            $user = $this->users->create(new CreateUserData(
                name: (string) $payload['name'],
                email: (string) $payload['email'],
                password: (string) $payload['password'],
                isActive: (bool) $payload['is_active'],
            ));

            $this->roles->syncRolesToSubject(
                $this->normalizeRoleSlugs($payload),
                User::class,
                (int) $user->getKey(),
            );

            return $user;
        });
    }

    public function update(
        UserEntityInterface $user,
        array $payload,
        ?UploadedFile $avatar = null,
        ?int $uploadedBy = null
    ): UserEntityInterface {
        return $this->transactions->transaction(function () use ($user, $payload, $avatar, $uploadedBy): UserEntityInterface {
            $model = $this->toModel($user);

            if ($avatar !== null) {
                $uploadedMedia = $this->media->upload(
                    file: $avatar,
                    uploadedBy: $uploadedBy,
                    title: (string) $model->name,
                    altText: (string) $model->name,
                );

                $payload['avatar_media_id'] = (int) $uploadedMedia->getKey();
                $payload['avatar_path'] = null;

                $this->cleanupLegacyAvatarPath($model);
            } elseif (! empty($payload['avatar_media_id'])) {
                $payload['avatar_path'] = null;
                $this->cleanupLegacyAvatarPath($model);
            }

            $updated = $this->users->update($user, $payload);

            $this->roles->syncRolesToSubject(
                $this->normalizeRoleSlugs($payload),
                User::class,
                (int) $updated->getKey(),
            );

            return $updated;
        });
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<int, string>
     */
    private function normalizeRoleSlugs(array $payload): array
    {
        $roleSlugs = $payload['role_slugs'] ?? [];

        if (! is_array($roleSlugs)) {
            return [];
        }

        return array_values(array_unique(array_map(
            static fn (mixed $slug): string => (string) $slug,
            $roleSlugs,
        )));
    }

    private function cleanupLegacyAvatarPath(User $user): void
    {
        if (! empty($user->avatar_path) && Storage::disk('public')->exists((string) $user->avatar_path)) {
            Storage::disk('public')->delete((string) $user->avatar_path);
        }
    }

    private function toModel(UserEntityInterface $user): User
    {
        if (! $user instanceof User) {
            throw new DomainException('Unsupported user entity implementation.');
        }

        return $user;
    }
}