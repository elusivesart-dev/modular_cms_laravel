<?php

declare(strict_types=1);

namespace Modules\Permissions\Application\Http\Requests;

use App\Core\Localization\Contracts\LanguageRegistryInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Modules\Permissions\Infrastructure\Models\Permission;

final class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $permission = $this->route('permission');

        return $permission instanceof Permission
            ? ($this->user()?->can('update', $permission) ?? false)
            : false;
    }

    public function rules(): array
    {
        $permission = $this->route('permission');
        $permissionId = $permission instanceof Permission ? (int) $permission->getKey() : 0;

        $rules = [
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('permissions', 'name')->ignore($permissionId),
                'regex:/^[a-z0-9]+(?:\.[a-z0-9_]+)+$/',
            ],
            'label' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
            'translations' => ['nullable', 'array'],
        ];

        foreach ($this->availableLocales() as $locale) {
            $rules["translations.$locale"] = ['nullable', 'array'];
            $rules["translations.$locale.label"] = ['nullable', 'string', 'max:150'];
            $rules["translations.$locale.description"] = ['nullable', 'string', 'max:1000'];
        }

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this->hasAnyDisplayLabel()) {
                return;
            }

            $validator->errors()->add('translations', __('permissions::permissions.validation.label_required'));
        });
    }

    public function validatedPayload(): array
    {
        $data = $this->validated();
        $data['role_ids'] = array_values(array_unique(array_map('intval', $data['role_ids'] ?? [])));
        $data['translations'] = $this->normalizeTranslations($data['translations'] ?? []);
        $data['label'] = $this->resolveLegacyLabel($data);
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
     * @param array<string, array{label?:string|null,description?:string|null}> $translations
     * @return array<string, array{label:?string,description:?string}>
     */
    private function normalizeTranslations(array $translations): array
    {
        $normalized = [];

        foreach ($this->availableLocales() as $locale) {
            $translation = $translations[$locale] ?? [];

            $normalized[$locale] = [
                'label' => $this->nullableString($translation['label'] ?? null),
                'description' => $this->nullableString($translation['description'] ?? null),
            ];
        }

        return $normalized;
    }

    private function hasAnyDisplayLabel(): bool
    {
        if ($this->nullableString($this->input('label')) !== null) {
            return true;
        }

        foreach ($this->availableLocales() as $locale) {
            if ($this->nullableString($this->input("translations.$locale.label")) !== null) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function resolveLegacyLabel(array $data): string
    {
        $label = $this->nullableString($data['label'] ?? null);

        if ($label !== null) {
            return $label;
        }

        $preferredLocale = (string) app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', 'en');

        return $data['translations'][$preferredLocale]['label']
            ?? $data['translations'][$fallbackLocale]['label']
            ?? collect($data['translations'])->pluck('label')->filter()->first()
            ?? $this->humanizeName((string) $data['name']);
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

    private function humanizeName(string $name): string
    {
        return (string) Str::of($name)
            ->replace(['.', '_', '-'], ' ')
            ->title();
    }
}