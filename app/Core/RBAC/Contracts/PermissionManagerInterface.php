<?php

declare(strict_types=1);

namespace App\Core\RBAC\Contracts;

interface PermissionManagerInterface
{
    public function create(string $permissionSlug): void;

    public function delete(string $permissionSlug): bool;

    public function exists(string $permissionSlug): bool;

    public function hasPermissionForSubject(string $permissionSlug, string $subjectType, int|string $subjectId): bool;

    /**
     * @param array<int, string> $permissionSlugs
     */
    public function hasAnyPermissionForSubject(array $permissionSlugs, string $subjectType, int|string $subjectId): bool;
}