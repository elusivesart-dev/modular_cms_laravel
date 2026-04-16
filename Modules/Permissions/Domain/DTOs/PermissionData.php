<?php

declare(strict_types=1);

namespace Modules\Permissions\Domain\DTOs;

final readonly class PermissionData
{
    /**
     * @param array<string, array{label:?string,description:?string}> $translations
     */
    public function __construct(
        public string $name,
        public ?string $label,
        public ?string $description,
        public array $roleIds = [],
        public array $translations = [],
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) $data['name'],
            label: isset($data['label']) && $data['label'] !== '' ? (string) $data['label'] : null,
            description: isset($data['description']) && $data['description'] !== '' ? (string) $data['description'] : null,
            roleIds: array_values(array_unique(array_map('intval', $data['role_ids'] ?? []))),
            translations: self::normalizeTranslations($data['translations'] ?? []),
        );
    }

    /**
     * @return array<string, array{label:?string,description:?string}>
     */
    public function getTranslations(): array
    {
        return $this->translations;
    }

    /**
     * @param array<string, array{label?:string|null,description?:string|null}> $translations
     * @return array<string, array{label:?string,description:?string}>
     */
    private static function normalizeTranslations(array $translations): array
    {
        $normalized = [];

        foreach ($translations as $locale => $translation) {
            if (! is_string($locale) || ! is_array($translation)) {
                continue;
            }

            $normalized[trim($locale)] = [
                'label' => self::nullableString($translation['label'] ?? null),
                'description' => self::nullableString($translation['description'] ?? null),
            ];
        }

        return $normalized;
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