<?php

declare(strict_types=1);

namespace Modules\Roles\Application\Services;

use App\Core\Localization\Contracts\LanguageRegistryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;
use Modules\Roles\Domain\Contracts\RoleEntityInterface;

final class RoleTranslationService
{
    public function __construct(
        private readonly LanguageRegistryInterface $languages,
    ) {
    }

    public function decoratePaginator(LengthAwarePaginator $paginator): LengthAwarePaginator
    {
        $collection = $paginator->getCollection()->map(function (mixed $role) {
            if ($role instanceof RoleEntityInterface) {
                $this->decorateRole($role);
            }

            return $role;
        });

        $paginator->setCollection($collection);

        return $paginator;
    }

    public function decorateRole(RoleEntityInterface $role): RoleEntityInterface
    {
        if ($role instanceof Model) {
            $role->setAttribute('display_name', $this->displayName($role));
            $role->setAttribute('display_description', $this->displayDescription($role));
        }

        return $role;
    }

    /**
     * @param iterable<int, object> $languages
     * @return array<string, array{name:string,description:string}>
     */
    public function translationInputs(?RoleEntityInterface $role, iterable $languages): array
    {
        $preferredLocale = $this->preferredLocale();
        $inputs = [];

        foreach ($languages as $language) {
            $locale = (string) $language->code;
            $name = $role?->getTranslatedName($locale);
            $description = $role?->getTranslatedDescription($locale);

            if ($role !== null && $name === null && $description === null && $locale === $preferredLocale) {
                $name = $role->getName();
                $description = $role->getDescription();
            }

            $inputs[$locale] = [
                'name' => $name ?? '',
                'description' => $description ?? '',
            ];
        }

        return $inputs;
    }

    public function displayName(RoleEntityInterface $role): string
    {
        $systemKey = 'roles::roles.items.' . $role->getSlug() . '.name';

        if (Lang::has($systemKey)) {
            return (string) __($systemKey);
        }

        $preferredLocale = $this->preferredLocale();
        $fallbackLocale = $this->languages->getFallbackLocale();

        return $role->getTranslatedName($preferredLocale)
            ?? ($fallbackLocale !== $preferredLocale ? $role->getTranslatedName($fallbackLocale) : null)
            ?? $role->getName();
    }

    public function displayDescription(RoleEntityInterface $role): ?string
    {
        $systemKey = 'roles::roles.items.' . $role->getSlug() . '.description';

        if (Lang::has($systemKey)) {
            return (string) __($systemKey);
        }

        $preferredLocale = $this->preferredLocale();
        $fallbackLocale = $this->languages->getFallbackLocale();

        return $role->getTranslatedDescription($preferredLocale)
            ?? ($fallbackLocale !== $preferredLocale ? $role->getTranslatedDescription($fallbackLocale) : null)
            ?? $role->getDescription();
    }

    private function preferredLocale(): string
    {
        return $this->languages->normalize(app()->getLocale()) ?? $this->languages->getFallbackLocale();
    }
}