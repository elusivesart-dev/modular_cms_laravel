<?php

declare(strict_types=1);

namespace Modules\Permissions\Application\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Permissions\Application\Http\Requests\StorePermissionRequest;
use Modules\Permissions\Application\Http\Requests\UpdatePermissionRequest;
use Modules\Permissions\Domain\Services\PermissionService;
use Modules\Permissions\Infrastructure\Models\Permission;

final class PermissionController extends Controller
{
    public function __construct(
        private readonly PermissionService $permissionService,
    ) {
        $this->authorizeResource(Permission::class, 'permission');
    }

    public function index(): View
    {
        return view('permissions::permissions.index', [
            'permissions' => $this->permissionService->paginate(),
        ]);
    }

    public function create(): View
    {
        return view('permissions::permissions.create', [
            'roles' => $this->permissionService->getAllRoleOptions(),
            'selectedRoleIds' => [],
        ]);
    }

    public function store(StorePermissionRequest $request): RedirectResponse
    {
        $this->permissionService->create($request->validated());

        return redirect()
            ->route('permissions.index')
            ->with('success', __('permissions::permissions.messages.created'));
    }

    public function edit(Permission $permission): View
    {
        return view('permissions::permissions.edit', [
            'permission' => $permission,
            'roles' => $this->permissionService->getAllRoleOptions(),
            'selectedRoleIds' => $this->permissionService->getAssignedRoleIds($permission),
        ]);
    }

    public function update(UpdatePermissionRequest $request, Permission $permission): RedirectResponse
    {
        $this->permissionService->update($permission, $request->validated());

        return redirect()
            ->route('permissions.index')
            ->with('success', __('permissions::permissions.messages.updated'));
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $this->permissionService->delete($permission);

        return redirect()
            ->route('permissions.index')
            ->with('success', __('permissions::permissions.messages.deleted'));
    }
}