<?php

declare(strict_types=1);

namespace Modules\Permissions\Application\Services;

use App\Core\Localization\Contracts\LanguageRegistryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Modules\Permissions\Infrastructure\Models\Permission;

final class PermissionTranslationService
{
    public function __construct(
        private readonly LanguageRegistryInterface $languages,
    ) {
    }

    public function decoratePaginator(LengthAwarePaginator $paginator): LengthAwarePaginator
    {
        $collection = $paginator->getCollection()->map(function (mixed $permission) {
            if ($permission instanceof Permission) {
                $this->decoratePermission($permission);
            }

            return $permission;
        });

        $paginator->setCollection($collection);

        return $paginator;
    }

    public function decoratePermission(Permission $permission): Permission
    {
        $permission->setAttribute('display_label', $this->displayLabel($permission));
        $permission->setAttribute('display_description', $this->displayDescription($permission));

        return $permission;
    }

    /**
     * @param iterable<int, object> $languages
     * @return array<string, array{label:string,description:string}>
     */
    public function translationInputs(?Permission $permission, iterable $languages): array
    {
        $preferredLocale = $this->preferredLocale();
        $inputs = [];

        foreach ($languages as $language) {
            $locale = (string) $language->code;
            $label = $permission?->getTranslatedLabel($locale);
            $description = $permission?->getTranslatedDescription($locale);

            if ($permission !== null && $label === null && $description === null && $locale === $preferredLocale) {
                $label = $this->legacyInputLabel($permission);
                $description = $permission->description !== null ? (string) $permission->description : '';
            }

            $inputs[$locale] = [
                'label' => $label ?? '',
                'description' => $description ?? '',
            ];
        }

        return $inputs;
    }

    public function displayLabel(Permission $permission): string
    {
        $preferredLocale = $this->preferredLocale();
        $fallbackLocale = $this->languages->getFallbackLocale();

        return $permission->getTranslatedLabel($preferredLocale)
            ?? ($fallbackLocale !== $preferredLocale ? $permission->getTranslatedLabel($fallbackLocale) : null)
            ?? $this->resolveSystemLabel($permission)
            ?? $this->legacyInputLabel($permission)
            ?? $this->humanizeName((string) $permission->name);
    }

    public function displayDescription(Permission $permission): ?string
    {
        $preferredLocale = $this->preferredLocale();
        $fallbackLocale = $this->languages->getFallbackLocale();

        return $permission->getTranslatedDescription($preferredLocale)
            ?? ($fallbackLocale !== $preferredLocale ? $permission->getTranslatedDescription($fallbackLocale) : null)
            ?? ($permission->description !== null ? (string) $permission->description : null);
    }

    private function resolveSystemLabel(Permission $permission): ?string
    {
        $label = $permission->label !== null ? trim((string) $permission->label) : null;

        if ($label === null || $label === '' || ! str_contains($label, '::')) {
            return null;
        }

        if (! Lang::has($label)) {
            return null;
        }

        return (string) __($label);
    }

    private function legacyInputLabel(Permission $permission): ?string
    {
        $label = $permission->label !== null ? trim((string) $permission->label) : null;

        if ($label === null || $label === '' || str_contains($label, '::')) {
            return null;
        }

        return $label;
    }

    private function preferredLocale(): string
    {
        return $this->languages->normalize(app()->getLocale()) ?? $this->languages->getFallbackLocale();
    }

    private function humanizeName(string $name): string
    {
        return (string) Str::of($name)
            ->replace(['.', '_', '-'], ' ')
            ->title();
    }
}