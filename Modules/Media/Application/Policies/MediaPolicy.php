<?php

declare(strict_types=1);

namespace Modules\Media\Application\Policies;

use App\Core\RBAC\Contracts\PermissionManagerInterface;
use Modules\Media\Infrastructure\Models\Media;
use Modules\Users\Infrastructure\Models\User;

final class MediaPolicy
{
    public function __construct(
        private readonly PermissionManagerInterface $permissions,
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->check($user, 'media.view');
    }

    public function view(User $user, Media $media): bool
    {
        return $this->check($user, 'media.view');
    }

    public function create(User $user): bool
    {
        return $this->check($user, 'media.create');
    }

    public function delete(User $user, Media $media): bool
    {
        return $this->check($user, 'media.delete');
    }

    private function check(User $user, string $permission): bool
    {
        return $this->permissions->hasPermissionForSubject(
            $permission,
            $user::class,
            (int) $user->getKey(),
        );
    }
}