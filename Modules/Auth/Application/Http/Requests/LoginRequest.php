<?php

declare(strict_types=1);

namespace Modules\Auth\Application\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Auth\Domain\DTOs\LoginData;

final class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email:rfc', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public function loginData(): LoginData
    {
        $data = $this->validated();
        $data['remember'] = (bool) ($data['remember'] ?? false);

        return LoginData::fromArray($data);
    }
}