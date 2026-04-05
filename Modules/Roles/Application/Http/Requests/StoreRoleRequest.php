<?php

declare(strict_types=1);

namespace Modules\Roles\Application\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->can('create', \Modules\Roles\Infrastructure\Models\Role::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:150', 'regex:/^[a-z0-9-]+$/', 'unique:roles,slug'],
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