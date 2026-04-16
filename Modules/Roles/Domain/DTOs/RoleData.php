<?php

declare(strict_types=1);

namespace Modules\Roles\Domain\DTOs;

final readonly class RoleData
{
    /**
     * @param array<string, array{name:?string,description:?string}> $translations
     */
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $description,
        public bool $is_system = false,
        public array $translations = [],
    ) {
    }

    /**
     * @param array{
     *     name:string,
     *     slug:string,
     *     description?:string|null,
     *     is_system?:bool,
     *     translations?:array<string, array{name?:string|null,description?:string|null}>
     * } $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            name: (string) $payload['name'],
            slug: (string) $payload['slug'],
            description: isset($payload['description']) ? self::nullableString($payload['description']) : null,
            is_system: (bool) ($payload['is_system'] ?? false),
            translations: self::normalizeTranslations($payload['translations'] ?? []),
        );
    }

    /**
     * @return array<string, array{name:?string,description:?string}>
     */
    public function getTranslations(): array
    {
        return $this->translations;
    }

    public function resolveLegacyName(string $preferredLocale, string $fallbackLocale): string
    {
        return $this->translationValue($preferredLocale, 'name')
            ?? $this->translationValue($fallbackLocale, 'name')
            ?? $this->firstTranslationValue('name')
            ?? $this->name;
    }

    public function resolveLegacyDescription(string $preferredLocale, string $fallbackLocale): ?string
    {
        return $this->translationValue($preferredLocale, 'description')
            ?? $this->translationValue($fallbackLocale, 'description')
            ?? $this->firstTranslationValue('description')
            ?? $this->description;
    }

    /**
     * @param array<string, array{name?:string|null,description?:string|null}> $translations
     * @return array<string, array{name:?string,description:?string}>
     */
    private static function normalizeTranslations(array $translations): array
    {
        $normalized = [];

        foreach ($translations as $locale => $translation) {
            if (! is_string($locale) || ! is_array($translation)) {
                continue;
            }

            $name = self::nullableString($translation['name'] ?? null);
            $description = self::nullableString($translation['description'] ?? null);

            $normalized[trim($locale)] = [
                'name' => $name,
                'description' => $description,
            ];
        }

        return $normalized;
    }

    private function translationValue(string $locale, string $field): ?string
    {
        return $this->translations[$locale][$field] ?? null;
    }

    private function firstTranslationValue(string $field): ?string
    {
        foreach ($this->translations as $translation) {
            $value = $translation[$field] ?? null;

            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    private static function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value);

        return $normalized !== '' ? $normalized : null;
    }
}