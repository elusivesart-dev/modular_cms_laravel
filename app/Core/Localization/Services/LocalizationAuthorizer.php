<?php

declare(strict_types=1);

namespace App\Core\Localization\Services;

use App\Core\RBAC\Contracts\PermissionManagerInterface;

final readonly class LocalizationAuthorizer
{
    public function __construct(
        private PermissionManagerInterface $permissions,
    ) {
    }

    public function canView(object $user): bool
    {
        return $this->canAny($user, [
            'localization.view',
            'localization.manage',
            'settings.view',
        ]);
    }

    public function canInstall(object $user): bool
    {
        return $this->canAny($user, [
            'localization.install',
            'localization.manage',
            'settings.update',
        ]);
    }

    public function canDelete(object $user): bool
    {
        return $this->canAny($user, [
            'localization.delete',
            'localization.manage',
            'settings.update',
        ]);
    }

    public function canUpdateDefaultLocale(object $user): bool
    {
        return $this->canAny($user, [
            'localization.update',
            'localization.manage',
            'settings.update',
        ]);
    }

    /**
     * @param array<int, string> $permissionSlugs
     */
    private function canAny(object $user, array $permissionSlugs): bool
    {
        if (!method_exists($user, 'getKey')) {
            return false;
        }

        $subjectId = (int) $user->getKey();

        foreach ($permissionSlugs as $permissionSlug) {
            if ($this->permissions->hasPermissionForSubject(
                $permissionSlug,
                $user::class,
                $subjectId,
            )) {
                return true;
            }
        }

        return false;
    }
}