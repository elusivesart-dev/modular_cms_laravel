<?php

declare(strict_types=1);

namespace App\Core\Themes\Repositories;

use App\Core\Themes\Contracts\ThemeSettingsRepositoryInterface;
use InvalidArgumentException;
use Modules\Settings\Domain\Contracts\SettingRepositoryInterface;
use Modules\Settings\Domain\DTOs\SettingData;

final readonly class DatabaseThemeSettingsRepository implements ThemeSettingsRepositoryInterface
{
    public function __construct(
        private SettingRepositoryInterface $settings,
    ) {
    }

    public function getActiveThemeSlug(string $group): ?string
    {
        $setting = $this->settings->findByKey($this->settingsKey($group));

        if ($setting === null) {
            return null;
        }

        $value = $setting->value;

        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }

    public function setActiveThemeSlug(string $group, string $slug): void
    {
        $normalizedSlug = trim($slug);

        if ($normalizedSlug === '') {
            throw new InvalidArgumentException('Theme slug cannot be empty.');
        }

        $key = $this->settingsKey($group);

        $data = SettingData::fromArray([
            'group' => (string) config('themes.settings_group', 'system'),
            'key' => $key,
            'value' => $normalizedSlug,
            'type' => 'string',
            'label' => null,
            'description' => null,
            'is_public' => false,
            'is_system' => true,
        ]);

        $existingSetting = $this->settings->findByKey($key);

        if ($existingSetting === null) {
            $this->settings->create($data);

            return;
        }

        $this->settings->update($existingSetting, $data);
    }

    private function settingsKey(string $group): string
    {
        $normalizedGroup = strtolower(trim($group));

        $allowedGroups = config('themes.groups', ['public', 'admin']);

        if (!is_array($allowedGroups) || !in_array($normalizedGroup, $allowedGroups, true)) {
            throw new InvalidArgumentException(sprintf('Unsupported theme group [%s].', $group));
        }

        $key = config('themes.settings_keys.' . $normalizedGroup);

        if (!is_string($key) || trim($key) === '') {
            throw new InvalidArgumentException(sprintf(
                'Missing theme settings key mapping for group [%s].',
                $normalizedGroup
            ));
        }

        return trim($key);
    }
}