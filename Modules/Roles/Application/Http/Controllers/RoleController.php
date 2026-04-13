<?php

declare(strict_types=1);

namespace Modules\Roles\Application\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Roles\Application\Contracts\RoleAdministrationWorkflowInterface;
use Modules\Roles\Application\Http\Requests\StoreRoleRequest;
use Modules\Roles\Application\Http\Requests\UpdateRoleRequest;
use Modules\Roles\Domain\Contracts\RoleRepositoryInterface;
use Modules\Roles\Domain\DTOs\RoleData;
use Modules\Roles\Infrastructure\Models\Role;

final class RoleController extends Controller
{
    public function __construct(
        private readonly RoleRepositoryInterface $roles,
        private readonly RoleAdministrationWorkflowInterface $workflow,
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
            'permissions' => $this->workflow->availablePermissions(),
            'selectedPermissionIds' => [],
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $payload = $request->validatedPayload();

        $this->workflow->store(
            RoleData::fromArray($payload),
            $payload['permission_ids'] ?? [],
        );

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
            'permissions' => $this->workflow->availablePermissions(),
            'selectedPermissionIds' => $this->workflow->selectedPermissionIds($role),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $payload = $request->validatedPayload();

        $this->workflow->update(
            $role,
            RoleData::fromArray($payload),
            $payload['permission_ids'] ?? [],
        );

        return redirect()
            ->route('roles.index')
            ->with('success', __('roles::roles.updated'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('delete', $role);

        $this->workflow->delete($role);

        return redirect()
            ->route('roles.index')
            ->with('success', __('roles::roles.deleted'));
    }
}