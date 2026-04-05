<?php

declare(strict_types=1);

namespace Modules\Roles\Application\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Roles\Infrastructure\Models\Role;

final class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Role|null $role */
        $role = $this->route('role');

        return $this->user() !== null && $role instanceof Role && $this->user()->can('update', $role);
    }

    public function rules(): array
    {
        /** @var Role $role */
        $role = $this->route('role');

        return [
            'name' => ['required', 'string', 'max:150'],
            'slug' => [
                'required',
                'string',
                'max:150',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('roles', 'slug')->ignore($role->getKey()),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_system' => ['nullable', 'boolean'],
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ];
    }

    public function validatedPayload(): array
    {
        $data = $this->validated();
        $data['is_system'] = (bool) ($data['is_system'] ?? false);
        $data['permission_ids'] = array_values(array_unique(array_map('intval', $data['permission_ids'] ?? [])));

        return $data;
    }
}