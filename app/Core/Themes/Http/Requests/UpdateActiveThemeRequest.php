<?php

declare(strict_types=1);

namespace App\Core\Themes\Http\Requests;

use App\Core\RBAC\Contracts\RBACResolverInterface;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateActiveThemeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if ($user === null) {
            return false;
        }

        return app(RBACResolverInterface::class)->can($user, 'themes.manage');
    }

    public function rules(): array
    {
        return [
            'group' => ['required', 'string', 'in:public,admin'],
            'theme' => ['required', 'string', 'max:120'],
        ];
    }

    public function validatedPayload(): array
    {
        $data = $this->validated();

        return [
            'group' => trim((string) $data['group']),
            'theme' => trim((string) $data['theme']),
        ];
    }
}