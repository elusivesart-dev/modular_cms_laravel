<?php

declare(strict_types=1);

namespace App\Core\Installer\Admin;

use App\Core\Installer\Contracts\AdminCreatorInterface;
use App\Core\Installer\DTO\InstallData;
use App\Core\RBAC\Contracts\RoleCatalogInterface;
use App\Core\RBAC\Contracts\RoleManagerInterface;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

final class DatabaseAdminCreator implements AdminCreatorInterface
{
    public function __construct(
        private readonly RoleCatalogInterface $roles,
        private readonly RoleManagerInterface $roleManager,
    ) {
    }

    public function create(InstallData $data): void
    {
        $user = User::query()->firstOrNew([
            'email' => $data->adminEmail,
        ]);

        $user->name = $data->adminName;
        $user->email = $data->adminEmail;
        $user->password = Hash::make($data->adminPassword);
        $user->is_active = true;
        $user->email_verified_at = Carbon::now();

        $user->save();

        $availableRoleSlugs = array_map(
            static fn ($role): string => $role->slug,
            $this->roles->all(),
        );

        $roleSlugs = [];

        if (in_array('super-admin', $availableRoleSlugs, true)) {
            $roleSlugs[] = 'super-admin';
        }

        if (in_array('admin', $availableRoleSlugs, true)) {
            $roleSlugs[] = 'admin';
        }

        if ($roleSlugs === []) {
            return;
        }

        $this->roleManager->syncRolesToSubject(
            $roleSlugs,
            $user::class,
            (int) $user->getKey(),
        );
    }
}