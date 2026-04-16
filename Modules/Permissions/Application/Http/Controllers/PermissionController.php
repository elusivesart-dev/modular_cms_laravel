<?php

declare(strict_types=1);

namespace Modules\Permissions\Application\Http\Controllers;

use App\Core\Localization\Contracts\LanguageRegistryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Modules\Permissions\Application\Http\Requests\StorePermissionRequest;
use Modules\Permissions\Application\Http\Requests\UpdatePermissionRequest;
use Modules\Permissions\Application\Services\PermissionTranslationService;
use Modules\Permissions\Domain\Services\PermissionService;
use Modules\Permissions\Infrastructure\Models\Permission;

final class PermissionController extends Controller
{
    public function __construct(
        private readonly PermissionService $permissionService,
        private readonly PermissionTranslationService $translations,
        private readonly LanguageRegistryInterface $languages,
    ) {
        $this->authorizeResource(Permission::class, 'permission');
    }

    public function index(): View
    {
        return view('permissions::permissions.index', [
            'permissions' => $this->translations->decoratePaginator($this->permissionService->paginate()),
        ]);
    }

    public function create(): View
    {
        $languages = $this->languages->getAvailableLanguages();

        return view('permissions::permissions.create', [
            'roles' => $this->permissionService->getAllRoleOptions(),
            'selectedRoleIds' => [],
            'languages' => $languages,
            'translationInputs' => $this->translations->translationInputs(null, $languages),
        ]);
    }

    public function store(StorePermissionRequest $request): RedirectResponse
    {
        $this->permissionService->create($request->validatedPayload());

        return redirect()
            ->route('permissions.index')
            ->with('success', __('permissions::permissions.messages.created'));
    }

    public function edit(Permission $permission): View
    {
        if (Schema::hasTable('permission_translations')) {
            $permission->load('translations');
        }

        $languages = $this->languages->getAvailableLanguages();

        return view('permissions::permissions.edit', [
            'permission' => $permission,
            'roles' => $this->permissionService->getAllRoleOptions(),
            'selectedRoleIds' => $this->permissionService->getAssignedRoleIds($permission),
            'languages' => $languages,
            'translationInputs' => $this->translations->translationInputs($permission, $languages),
        ]);
    }

    public function update(UpdatePermissionRequest $request, Permission $permission): RedirectResponse
    {
        $this->permissionService->update($permission, $request->validatedPayload());

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