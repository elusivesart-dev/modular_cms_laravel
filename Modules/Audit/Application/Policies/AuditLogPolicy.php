<?php

declare(strict_types=1);

namespace Modules\Audit\Application\Policies;

use App\Core\Audit\Models\AuditLog;
use App\Core\RBAC\Contracts\RBACResolverInterface;
use App\Core\RBAC\Contracts\RoleManagerInterface;

final class AuditLogPolicy
{
    public function __construct(
        private readonly RBACResolverInterface $rbacResolver,
        private readonly RoleManagerInterface $roleManager,
    ) {
    }

    public function viewAny(object $user): bool
    {
        return $this->can($user, 'audit.view');
    }

    public function view(object $user, AuditLog $auditLog): bool
    {
        return $this->can($user, 'audit.view');
    }

    public function delete(object $user, AuditLog $auditLog): bool
    {
        return $this->can($user, 'audit.delete');
    }

    private function can(object $user, string $permissionSlug): bool
    {
        if (!method_exists($user, 'getAuthIdentifier')) {
            return false;
        }

        if ($this->rbacResolver->can($user, $permissionSlug)) {
            return true;
        }

        return $this->roleManager->hasRoleForSubject(
            'super-admin',
            $user::class,
            $user->getAuthIdentifier(),
        );
    }
}