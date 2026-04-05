<?php

declare(strict_types=1);

namespace Modules\Settings\Application\Policies;

use App\Core\RBAC\Contracts\PermissionManagerInterface;
use Modules\Settings\Infrastructure\Models\Setting;
use Modules\Users\Infrastructure\Models\User;

final class SettingPolicy
{
    public function __construct(
        private readonly PermissionManagerInterface $permissions,
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->check($user, 'settings.view');
    }

    public function view(User $user, Setting $setting): bool
    {
        return $this->check($user, 'settings.view');
    }

    public function create(User $user): bool
    {
        return $this->check($user, 'settings.create');
    }

    public function update(User $user, Setting $setting): bool
    {
        return $this->check($user, 'settings.update');
    }

    public function delete(User $user, Setting $setting): bool
    {
        return !$setting->is_system && $this->check($user, 'settings.delete');
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