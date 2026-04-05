<?php

declare(strict_types=1);

namespace Modules\Users\Application\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Users\Infrastructure\Models\User;

final class PublicUpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        /** @var User $user */
        $user = $this->user();

        return [
            'name' => ['required', 'string', 'max:120'],
            'slug' => [
                'required',
                'string',
                'max:180',
                'alpha_dash',
                Rule::unique('users', 'slug')->ignore($user->getKey()),
            ],
            'email' => [
                'required',
                'string',
                'email:rfc',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->getKey()),
            ],
            'bio' => ['nullable', 'string', 'max:5000'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048', 'dimensions:max_width=4096,max_height=4096'],
            'password' => ['nullable', 'string', 'min:' . (string) config('users.password_min_length', 8), 'max:255', 'confirmed'],
            'current_password' => ['required', 'string', 'current_password'],
        ];
    }

    /**
     * @return array{name:string,slug:string,email:string,bio:?string,password?:string}
     */
    public function validatedPayload(): array
    {
        $data = $this->validated();

        $payload = [
            'name' => trim(strip_tags((string) $data['name'])),
            'slug' => trim((string) $data['slug']),
            'email' => mb_strtolower(trim((string) $data['email'])),
            'bio' => isset($data['bio']) ? trim(strip_tags((string) $data['bio'])) : null,
        ];

        if (!empty($data['password'])) {
            $payload['password'] = (string) $data['password'];
        }

        return $payload;
    }
}