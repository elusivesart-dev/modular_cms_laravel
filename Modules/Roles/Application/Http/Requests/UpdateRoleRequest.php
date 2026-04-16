<?php

declare(strict_types=1);

namespace Modules\Roles\Application\Http\Requests;

use App\Core\Localization\Contracts\LanguageRegistryInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Modules\Roles\Infrastructure\Models\Role;

final class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $role = $this->route('role');

        return $this->user() !== null
            && $role instanceof Role
            && $this->user()->can('update', $role);
    }

    public function rules(): array
    {
        /** @var Role $role */
        $role = $this->route('role');

        $rules = [
            'name' => ['nullable', 'string', 'max:150'],
            'slug' => [
                'required',
                'string',
                'max:150',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('roles', 'slug')->ignore($role->getKey()),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_system' => ['nullable', 'boolean'],
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
            'translations' => ['nullable', 'array'],
        ];

        foreach ($this->availableLocales() as $locale) {
            $rules["translations.$locale"] = ['nullable', 'array'];
            $rules["translations.$locale.name"] = ['nullable', 'string', 'max:150'];
            $rules["translations.$locale.description"] = ['nullable', 'string', 'max:1000'];
        }

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this->hasAnyRoleName()) {
                return;
            }

            $validator->errors()->add('translations', __('roles::roles.validation.role_name_required'));
        });
    }

    public function validatedPayload(): array
    {
        $data = $this->validated();
        $data['is_system'] = (bool) ($data['is_system'] ?? false);
        $data['permission_ids'] = array_values(array_unique(array_map('intval', $data['permission_ids'] ?? [])));
        $data['translations'] = $this->normalizeTranslations($data['translations'] ?? []);
        $data['name'] = $this->resolveLegacyName($data);
        $data['description'] = $this->resolveLegacyDescription($data);

        return $data;
    }

    /**
     * @return array<int, string>
     */
    private function availableLocales(): array
    {
        /** @var LanguageRegistryInterface $languages */
        $languages = app(LanguageRegistryInterface::class);

        return $languages->getAvailableLanguages()
            ->pluck('code')
            ->map(static fn (mixed $code): string => (string) $code)
            ->values()
            ->all();
    }

    /**
     * @param array<string, array{name?:string|null,description?:string|null}> $translations
     * @return array<string, array{name:?string,description:?string}>
     */
    private function normalizeTranslations(array $translations): array
    {
        $normalized = [];

        foreach ($this->availableLocales() as $locale) {
            $translation = $translations[$locale] ?? [];

            $normalized[$locale] = [
                'name' => $this->nullableString($translation['name'] ?? null),
                'description' => $this->nullableString($translation['description'] ?? null),
            ];
        }

        return $normalized;
    }

    private function hasAnyRoleName(): bool
    {
        if ($this->nullableString($this->input('name')) !== null) {
            return true;
        }

        foreach ($this->availableLocales() as $locale) {
            if ($this->nullableString($this->input("translations.$locale.name")) !== null) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function resolveLegacyName(array $data): string
    {
        $name = $this->nullableString($data['name'] ?? null);

        if ($name !== null) {
            return $name;
        }

        $preferredLocale = (string) app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', 'en');

        return $data['translations'][$preferredLocale]['name']
            ?? $data['translations'][$fallbackLocale]['name']
            ?? collect($data['translations'])->pluck('name')->filter()->first()
            ?? '';
    }

    /**
     * @param array<string, mixed> $data
     */
    private function resolveLegacyDescription(array $data): ?string
    {
        $description = $this->nullableString($data['description'] ?? null);

        if ($description !== null) {
            return $description;
        }

        $preferredLocale = (string) app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', 'en');

        return $data['translations'][$preferredLocale]['description']
            ?? $data['translations'][$fallbackLocale]['description']
            ?? collect($data['translations'])->pluck('description')->filter()->first()
            ?? null;
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value);

        return $normalized !== '' ? $normalized : null;
    }
}