<?php

declare(strict_types=1);

namespace Modules\Users\Application\Services;

use App\Core\Database\Contracts\TransactionManagerInterface;
use App\Core\RBAC\Contracts\RoleCatalogInterface;
use App\Core\RBAC\Contracts\RoleManagerInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Media\Application\Contracts\MediaServiceInterface;
use Modules\Users\Application\Contracts\UserAdministrationWorkflowInterface;
use Modules\Users\Application\Contracts\UserServiceInterface;
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

    public function assignedRoles(User $user): Collection
    {
        return $this->roles->rolesForSubject(User::class, (int) $user->getKey());
    }

    public function selectedRoleSlugs(User $user): array
    {
        return $this->assignedRoles($user)
            ->pluck('slug')
            ->map(static fn (mixed $slug): string => (string) $slug)
            ->values()
            ->all();
    }

    public function store(array $payload): User
    {
        return $this->transactions->transaction(function () use ($payload): User {
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

    public function update(User $user, array $payload, ?UploadedFile $avatar = null, ?int $uploadedBy = null): User
    {
        return $this->transactions->transaction(function () use ($user, $payload, $avatar, $uploadedBy): User {
            if ($avatar !== null) {
                $uploadedMedia = $this->media->upload(
                    file: $avatar,
                    uploadedBy: $uploadedBy,
                    title: (string) $user->name,
                    altText: (string) $user->name,
                );

                $payload['avatar_media_id'] = (int) $uploadedMedia->getKey();
                $payload['avatar_path'] = null;

                $this->cleanupLegacyAvatarPath($user);
            } elseif (! empty($payload['avatar_media_id'])) {
                $payload['avatar_path'] = null;

                $this->cleanupLegacyAvatarPath($user);
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
     *
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
}