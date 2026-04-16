<?php

declare(strict_types=1);

namespace Modules\Users\Application\Http\Requests;

use App\Core\Media\Contracts\MediaAssetManagerInterface;
use App\Core\RBAC\Contracts\RoleManagerInterface;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Modules\Users\Infrastructure\Models\User;

final class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $actor = $this->user();
        /** @var User|null $subject */
        $subject = $this->route('user');

        if ($actor === null || $subject === null || ! method_exists($actor, 'getAuthIdentifier')) {
            return false;
        }

        if ((int) $actor->getAuthIdentifier() === (int) $subject->getKey()) {
            return true;
        }

        return app(RoleManagerInterface::class)->hasAnyRoleForSubject(
            ['super-admin', 'admin'],
            $actor::class,
            $actor->getAuthIdentifier(),
        );
    }

    public function rules(): array
    {
        /** @var User $user */
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email:rfc',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->getKey()),
            ],
            'password' => ['nullable', 'string', 'min:' . (string) config('users.password_min_length', 8), 'max:255'],
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
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048', 'dimensions:max_width=4096,max_height=4096'],
            'avatar_media_id' => ['nullable', 'integer', 'exists:media,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $avatarMediaId = $this->input('avatar_media_id');

            if ($avatarMediaId === null || $avatarMediaId === '') {
                return;
            }

            $media = app(MediaAssetManagerInterface::class)->findById((int) $avatarMediaId);

            if ($media === null || ! str_starts_with((string) $media->mimeType, 'image/')) {
                $validator->errors()->add(
                    'avatar_media_id',
                    __('users::users.media.invalid_avatar_selection')
                );
            }
        });
    }

    public function validatedPayload(): array
    {
        $data = $this->validated();

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['role_slugs'] = array_values(array_unique($data['role_slugs'] ?? []));
        $data['avatar_media_id'] = isset($data['avatar_media_id']) && $data['avatar_media_id'] !== ''
            ? (int) $data['avatar_media_id']
            : null;

        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }
}