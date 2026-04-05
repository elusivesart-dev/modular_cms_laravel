<?php

declare(strict_types=1);

namespace Modules\Users\Application\Http\Requests;

use App\Core\RBAC\Contracts\RoleManagerInterface;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

final class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if ($user === null || ! method_exists($user, 'getAuthIdentifier')) {
            return false;
        }

        return app(RoleManagerInterface::class)->hasAnyRoleForSubject(
            ['super-admin', 'admin'],
            $user::class,
            $user->getAuthIdentifier(),
        );
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:' . (string) config('users.password_min_length', 8), 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'role_slugs' => ['nullable', 'array'],
            'role_slugs.*' => [
                'string',
                'exists:roles,slug',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $actor = $this->user();

                    if (
                        $value === 'super-admin'
                        && $actor !== null
                        && method_exists($actor, 'getAuthIdentifier')
                        && ! app(RoleManagerInterface::class)->hasRoleForSubject(
                            'super-admin',
                            $actor::class,
                            $actor->getAuthIdentifier(),
                        )
                    ) {
                        $fail(__('users::users.exceptions.cannot_assign_super_admin'));
                    }
                },
            ],
        ];
    }

    public function validatedPayload(): array
    {
        $data = $this->validated();

        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        $data['role_slugs'] = array_values(array_unique($data['role_slugs'] ?? []));

        return $data;
    }
}