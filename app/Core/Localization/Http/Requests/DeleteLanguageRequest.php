<?php

declare(strict_types=1);

namespace App\Core\Localization\Http\Requests;

use App\Core\Localization\Services\LocalizationAuthorizer;
use Illuminate\Foundation\Http\FormRequest;

final class DeleteLanguageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && app(LocalizationAuthorizer::class)->canDelete($user);
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:32'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => (string) $this->route('code'),
        ]);
    }
}