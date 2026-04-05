<?php

declare(strict_types=1);

namespace Modules\Permissions\Application\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Permissions\Infrastructure\Models\Permission;

final class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Permission::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:120',
                'unique:permissions,name',
                'regex:/^[a-z0-9]+(?:\.[a-z0-9_]+)+$/',
            ],
            'label' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
        ];
    }
}