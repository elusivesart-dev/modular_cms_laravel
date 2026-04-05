<?php

declare(strict_types=1);

namespace Modules\Users\Application\Services;

use App\Core\Installer\Contracts\AdminCreatorInterface;
use App\Core\Installer\DTO\InstallData;
use App\Core\RBAC\Contracts\RoleManagerInterface;
use Illuminate\Support\Facades\DB;
use Modules\Users\Domain\DTOs\CreateUserData;
use Modules\Users\Domain\Contracts\UserRepositoryInterface;
use Modules\Users\Infrastructure\Models\User;

final class InstallerAdminCreator implements AdminCreatorInterface
{
    public function __construct(
        private readonly UserService $userService,
        private readonly UserRepositoryInterface $users,
        private readonly RoleManagerInterface $roles,
    ) {
    }

    public function create(InstallData $data): void
    {
        if ($this->users->findByEmail($data->adminEmail) !== null) {
            return;
        }

        DB::transaction(function () use ($data): void {
            $user = $this->userService->create(new CreateUserData(
                name: $data->adminName,
                email: $data->adminEmail,
                password: $data->adminPassword,
                isActive: true,
            ));

            $this->roles->assignRoleToSubject(
                'super-admin',
                User::class,
                (int) $user->getKey(),
            );
        });
    }
}