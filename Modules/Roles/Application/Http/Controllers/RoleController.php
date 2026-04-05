<?php

declare(strict_types=1);

namespace Modules\Roles\Application\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Permissions\Domain\Contracts\PermissionRepositoryInterface;
use Modules\Permissions\Domain\Services\PermissionAssignmentService;
use Modules\Roles\Application\Http\Requests\StoreRoleRequest;
use Modules\Roles\Application\Http\Requests\UpdateRoleRequest;
use Modules\Roles\Domain\Contracts\RoleRepositoryInterface;
use Modules\Roles\Domain\DTOs\RoleData;
use Modules\Roles\Domain\Events\RoleCreatedEvent;
use Modules\Roles\Domain\Events\RoleDeletedEvent;
use Modules\Roles\Domain\Events\RoleUpdatedEvent;
use Modules\Roles\Infrastructure\Models\Role;

final class RoleController extends Controller
{
    public function __construct(
        private readonly RoleRepositoryInterface $roles,
        private readonly PermissionRepositoryInterface $permissions,
        private readonly PermissionAssignmentService $permissionAssignments,
    ) {
        $this->authorizeResource(Role::class, 'role');
    }

    public function index(): View
    {
        return view('roles::roles.index', [
            'roles' => $this->roles->paginate(),
        ]);
    }

    public function create(): View
    {
        return view('roles::roles.create', [
            'permissions' => $this->permissions->paginate(1000)->items(),
            'selectedPermissionIds' => [],
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $payload = $request->validatedPayload();

        $role = $this->roles->create(RoleData::fromArray($payload));

        $this->permissionAssignments->syncPermissionsToRole(
            $role,
            $payload['permission_ids'] ?? [],
        );

        event(new RoleCreatedEvent($role->fresh('permissions')));

        return redirect()
            ->route('roles.index')
            ->with('success', __('roles::roles.created'));
    }

    public function show(Role $role): View
    {
        return view('roles::roles.show', [
            'role' => $role->load(['assignments', 'permissions']),
        ]);
    }

    public function edit(Role $role): View
    {
        return view('roles::roles.edit', [
            'role' => $role->load('permissions'),
            'permissions' => $this->permissions->paginate(1000)->items(),
            'selectedPermissionIds' => $role->permissions()
                ->pluck('permissions.id')
                ->map(static fn (mixed $id): int => (int) $id)
                ->all(),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $payload = $request->validatedPayload();

        $updated = $this->roles->update($role, RoleData::fromArray($payload));

        $this->permissionAssignments->syncPermissionsToRole(
            $updated,
            $payload['permission_ids'] ?? [],
        );

        event(new RoleUpdatedEvent($updated->fresh('permissions')));

        return redirect()
            ->route('roles.index')
            ->with('success', __('roles::roles.updated'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('delete', $role);

        $this->roles->delete($role);

        event(new RoleDeletedEvent($role));

        return redirect()
            ->route('roles.index')
            ->with('success', __('roles::roles.deleted'));
    }
}