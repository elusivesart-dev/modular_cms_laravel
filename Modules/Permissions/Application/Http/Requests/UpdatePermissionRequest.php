<?php

declare(strict_types=1);

namespace Modules\Permissions\Application\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Permissions\Infrastructure\Models\Permission;

final class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $permission = $this->route('permission');

        return $permission instanceof Permission
            ? ($this->user()?->can('update', $permission) ?? false)
            : false;
    }

    public function rules(): array
    {
        $permission = $this->route('permission');
        $permissionId = $permission instanceof Permission ? (int) $permission->getKey() : 0;

        return [
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('permissions', 'name')->ignore($permissionId),
                'regex:/^[a-z0-9]+(?:\.[a-z0-9_]+)+$/',
            ],
            'label' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
        ];
    }
}