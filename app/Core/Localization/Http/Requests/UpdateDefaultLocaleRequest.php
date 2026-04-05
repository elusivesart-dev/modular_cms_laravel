<?php

declare(strict_types=1);

namespace App\Core\Localization\Http\Requests;

use App\Core\Localization\Contracts\LanguageRegistryInterface;
use App\Core\Localization\Services\LocalizationAuthorizer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

final class UpdateDefaultLocaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && app(LocalizationAuthorizer::class)->canUpdateDefaultLocale($user);
    }

    public function rules(): array
    {
        return [
            'locale' => ['required', 'string', 'max:32'],
        ];
    }

    /**
     * @return array{locale:string}
     */
    public function validatedPayload(): array
    {
        $data = $this->validated();
        $locale = (string) ($data['locale'] ?? '');

        /** @var LanguageRegistryInterface $languages */
        $languages = app(LanguageRegistryInterface::class);

        if (!$languages->isSupported($locale)) {
            throw ValidationException::withMessages([
                'locale' => __('core-localization::ui.unsupported_locale'),
            ]);
        }

        return [
            'locale' => $languages->normalize($locale) ?? $languages->getFallbackLocale(),
        ];
    }
}