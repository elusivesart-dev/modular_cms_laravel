<?php

declare(strict_types=1);

namespace App\Core\Installer\Admin;

use App\Core\Installer\Contracts\AdminCreatorInterface;
use App\Core\Installer\DTO\InstallData;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Modules\Roles\Domain\Contracts\RoleRepositoryInterface;
use Modules\Users\Infrastructure\Models\User;

final class DatabaseAdminCreator implements AdminCreatorInterface
{
    public function __construct(
        private readonly RoleRepositoryInterface $roleRepository,
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

        $roleSlugs = [];

        if ($this->roleRepository->findBySlug('super-admin') !== null) {
            $roleSlugs[] = 'super-admin';
        }

        if ($this->roleRepository->findBySlug('admin') !== null) {
            $roleSlugs[] = 'admin';
        }

        if ($roleSlugs !== [] && method_exists($user, 'syncRoles')) {
            $user->syncRoles($roleSlugs);
        }
    }
}