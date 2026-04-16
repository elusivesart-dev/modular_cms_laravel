<?php

declare(strict_types=1);

namespace Modules\Media\Application\Policies;

use App\Core\RBAC\Contracts\PermissionManagerInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Modules\Media\Infrastructure\Models\Media;

final class MediaPolicy
{
    public function __construct(
        private readonly PermissionManagerInterface $permissions,
    ) {
    }

    public function viewAny(Authenticatable $user): bool
    {
        return $this->check($user, 'media.view');
    }

    public function view(Authenticatable $user, Media $media): bool
    {
        return $this->check($user, 'media.view');
    }

    public function create(Authenticatable $user): bool
    {
        return $this->check($user, 'media.create');
    }

    public function delete(Authenticatable $user, Media $media): bool
    {
        return $this->check($user, 'media.delete');
    }

    private function check(Authenticatable $user, string $permission): bool
    {
        return $this->permissions->hasPermissionForSubject(
            $permission,
            $user::class,
            (int) $user->getAuthIdentifier(),
        );
    }
}