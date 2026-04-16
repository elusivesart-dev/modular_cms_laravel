<?php

declare(strict_types=1);

namespace Modules\Settings\Application\Policies;

use App\Core\RBAC\Contracts\PermissionManagerInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Modules\Settings\Infrastructure\Models\Setting;

final class SettingPolicy
{
    public function __construct(
        private readonly PermissionManagerInterface $permissions,
    ) {
    }

    public function viewAny(Authenticatable $user): bool
    {
        return $this->check($user, 'settings.view');
    }

    public function view(Authenticatable $user, Setting $setting): bool
    {
        return $this->check($user, 'settings.view');
    }

    public function create(Authenticatable $user): bool
    {
        return $this->check($user, 'settings.create');
    }

    public function update(Authenticatable $user, Setting $setting): bool
    {
        return $this->check($user, 'settings.update');
    }

    public function delete(Authenticatable $user, Setting $setting): bool
    {
        return ! $setting->is_system && $this->check($user, 'settings.delete');
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