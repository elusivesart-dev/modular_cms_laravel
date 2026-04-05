<?php

declare(strict_types=1);

namespace App\Core\Localization\Http\Requests;

use App\Core\Localization\Services\LocalizationAuthorizer;
use Illuminate\Foundation\Http\FormRequest;

final class UploadLanguagePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && app(LocalizationAuthorizer::class)->canInstall($user);
    }

    public function rules(): array
    {
        return [
            'language_package' => [
                'required',
                'file',
                'mimes:zip',
                'max:51200',
            ],
        ];
    }
}