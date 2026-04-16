<?php

declare(strict_types=1);

namespace App\Core\Themes\Repositories;

use App\Core\Settings\Contracts\SystemSettingsStoreInterface;
use App\Core\Themes\Contracts\ThemeSettingsRepositoryInterface;
use InvalidArgumentException;

final readonly class DatabaseThemeSettingsRepository implements ThemeSettingsRepositoryInterface
{
    public function __construct(
        private SystemSettingsStoreInterface $settings,
    ) {
    }

    public function getActiveThemeSlug(string $group): ?string
    {
        $value = $this->settings->get($this->settingsKey($group));

        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }

    public function setActiveThemeSlug(string $group, string $slug): void
    {
        $normalizedSlug = trim($slug);

        if ($normalizedSlug === '') {
            throw new InvalidArgumentException('Theme slug cannot be empty.');
        }

        $this->settings->putString(
            group: (string) config('themes.settings_group', 'system'),
            key: $this->settingsKey($group),
            value: $normalizedSlug,
            label: null,
            description: null,
            isPublic: false,
            isSystem: true,
        );
    }

    private function settingsKey(string $group): string
    {
        $normalizedGroup = strtolower(trim($group));

        $allowedGroups = config('themes.groups', ['public', 'admin']);

        if (! is_array($allowedGroups) || ! in_array($normalizedGroup, $allowedGroups, true)) {
            throw new InvalidArgumentException(sprintf('Unsupported theme group [%s].', $group));
        }

        $key = config('themes.settings_keys.' . $normalizedGroup);

        if (! is_string($key) || trim($key) === '') {
            throw new InvalidArgumentException(sprintf(
                'Missing theme settings key mapping for group [%s].',
                $normalizedGroup
            ));
        }

        return trim($key);
    }
}