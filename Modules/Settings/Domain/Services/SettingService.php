<?php

declare(strict_types=1);

namespace Modules\Settings\Domain\Services;

use Illuminate\Support\Collection;
use Modules\Settings\Domain\Contracts\SettingRepositoryInterface;
use Modules\Settings\Domain\DTOs\SettingGroupData;
use Modules\Settings\Domain\Events\SettingsGroupUpdatedEvent;
use Modules\Settings\Domain\ValueObjects\SettingGroup;

final readonly class SettingService
{
    public function __construct(
        private SettingRepositoryInterface $settings,
    ) {
    }

    public function getGroups(): Collection
    {
        return $this->settings->getDistinctGroups();
    }

    public function getGroupsWithSettings(): Collection
    {
        return $this->settings->getDistinctGroups()
            ->map(function (string $group): array {
                $items = $this->settings->getByGroup($group);

                return [
                    'group' => $group,
                    'title' => $this->groupTitle($group),
                    'description' => $this->groupDescription($group),
                    'count' => $items->count(),
                ];
            })
            ->values();
    }

    public function getEditableGroup(string $group): Collection
    {
        new SettingGroup($group);

        return $this->settings->getByGroup($group);
    }

    public function updateGroup(array $payload): void
    {
        $data = SettingGroupData::fromArray($payload);

        new SettingGroup($data->group);

        $currentSettings = $this->settings->getByGroup($data->group)->keyBy('key');

        $normalizedValues = [];

        foreach ($data->values as $key => $value) {
            if (! $currentSettings->has($key)) {
                continue;
            }

            $setting = $currentSettings->get($key);

            $normalizedValues[$key] = $this->normalizeGroupValue(
                type: (string) $setting->type,
                value: $value,
            );
        }

        $this->settings->updateGroupValues($data->group, $normalizedValues);

        event(new SettingsGroupUpdatedEvent($data->group, $normalizedValues));
    }

    private function normalizeGroupValue(string $type, mixed $value): string
    {
        return match ($type) {
            'boolean' => in_array($value, [1, '1', true, 'true', 'on'], true) ? '1' : '0',
            'integer' => (string) (int) $value,
            'json' => json_encode(is_array($value) ? $value : [$value], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]',
            default => trim((string) $value),
        };
    }

    private function groupTitle(string $group): string
    {
        return __('settings::settings.groups.' . $group . '.title');
    }

    private function groupDescription(string $group): string
    {
        return __('settings::settings.groups.' . $group . '.description');
    }
}