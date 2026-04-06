<?php

declare(strict_types=1);

namespace Modules\Settings\Infrastructure\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Modules\Settings\Domain\Contracts\SettingRepositoryInterface;
use Modules\Settings\Domain\DTOs\SettingData;
use Modules\Settings\Infrastructure\Models\Setting;

final class SettingRepository implements SettingRepositoryInterface
{
    private const RUNTIME_CACHE_KEY = 'settings.runtime';
    private const VALUE_CACHE_PREFIX = 'settings.value.';

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Setting::query()
            ->orderBy('group')
            ->orderBy('key')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Setting
    {
        return Setting::query()->find($id);
    }

    public function findByKey(string $key): ?Setting
    {
        return Setting::query()
            ->where('key', $key)
            ->first();
    }

    public function getByGroup(string $group): Collection
    {
        return Setting::query()
            ->where('group', $group)
            ->orderBy('id')
            ->get();
    }

    public function create(SettingData $data): Setting
    {
        $setting = Setting::query()->create([
            'group' => $data->group,
            'key' => $data->key,
            'value' => $this->normalize($data->value, $data->type),
            'type' => $data->type,
            'label' => $data->label,
            'description' => $data->description,
            'is_public' => $data->isPublic,
            'is_system' => $data->isSystem,
        ]);

        $this->forgetCaches((string) $setting->key);

        return $setting;
    }

    public function update(Setting $setting, SettingData $data): Setting
    {
        $oldKey = (string) $setting->key;

        $setting->update([
            'group' => $data->group,
            'key' => $data->key,
            'value' => $this->normalize($data->value, $data->type),
            'type' => $data->type,
            'label' => $data->label,
            'description' => $data->description,
            'is_public' => $data->isPublic,
            'is_system' => $data->isSystem,
        ]);

        $this->forgetCaches($oldKey);
        $this->forgetCaches((string) $setting->key);

        return $setting->refresh();
    }

    public function delete(Setting $setting): void
    {
        $key = (string) $setting->key;

        $setting->delete();

        $this->forgetCaches($key);
    }

    public function updateGroupValues(string $group, array $values): void
    {
        $settings = Setting::query()
            ->where('group', $group)
            ->get()
            ->keyBy('key');

        foreach ($values as $key => $value) {
            if (! $settings->has($key)) {
                continue;
            }

            /** @var Setting $setting */
            $setting = $settings->get($key);

            $setting->update([
                'value' => $this->normalize($value, (string) $setting->type),
            ]);

            $this->forgetValueCache((string) $setting->key);
        }

        $this->forgetRuntimeCache();
    }

    public function getDistinctGroups(): Collection
    {
        return Setting::query()
            ->select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');
    }

    public function getRuntimeSettings(): Collection
    {
        if (! $this->settingsTableExists()) {
            return collect();
        }

        return Cache::remember(
            self::RUNTIME_CACHE_KEY,
            now()->addMinutes(30),
            function (): Collection {
                if (! $this->settingsTableExists()) {
                    return collect();
                }

                return Setting::query()
                    ->whereIn('key', [
                        'general.site_name',
                        'system.default_locale',
                        'system.timezone',
                        'mail.from_name',
                        'mail.from_address',
                    ])
                    ->get(['key', 'value'])
                    ->pluck('value', 'key');
            }
        );
    }

    public function getValue(string $key, mixed $default = null): mixed
    {
        if (! $this->settingsTableExists()) {
            return $default;
        }

        return Cache::remember(
            self::VALUE_CACHE_PREFIX . md5($key),
            now()->addMinutes(30),
            function () use ($key, $default): mixed {
                if (! $this->settingsTableExists()) {
                    return $default;
                }

                $setting = Setting::query()
                    ->where('key', $key)
                    ->first(['value', 'type']);

                if ($setting === null) {
                    return $default;
                }

                return $this->castStoredValue(
                    value: $setting->value,
                    type: (string) $setting->type,
                    default: $default,
                );
            }
        );
    }

    private function castStoredValue(?string $value, string $type, mixed $default = null): mixed
    {
        if ($value === null) {
            return $default;
        }

        return match ($type) {
            'boolean' => $value === '1',
            'integer' => (int) $value,
            'json' => json_decode($value, true) ?? $default,
            default => $value,
        };
    }

    private function normalize(mixed $value, string $type): ?string
    {
        return match ($type) {
            'json' => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'boolean' => in_array($value, [1, '1', true, 'true', 'on'], true) ? '1' : '0',
            'integer' => (string) (int) $value,
            default => $value !== null ? trim((string) $value) : null,
        };
    }

    private function forgetCaches(string $key): void
    {
        $this->forgetRuntimeCache();
        $this->forgetValueCache($key);
    }

    private function forgetRuntimeCache(): void
    {
        Cache::forget(self::RUNTIME_CACHE_KEY);
    }

    private function forgetValueCache(string $key): void
    {
        Cache::forget(self::VALUE_CACHE_PREFIX . md5($key));
    }

    private function settingsTableExists(): bool
    {
        return Schema::hasTable('settings');
    }
}