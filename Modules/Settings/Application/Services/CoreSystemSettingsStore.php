<?php

declare(strict_types=1);

namespace Modules\Settings\Application\Services;

use App\Core\Settings\Contracts\SystemSettingsStoreInterface;
use Modules\Settings\Domain\Contracts\SettingRepositoryInterface;
use Modules\Settings\Domain\DTOs\SettingData;

final readonly class CoreSystemSettingsStore implements SystemSettingsStoreInterface
{
    public function __construct(
        private SettingRepositoryInterface $settings,
    ) {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->settings->getValue($key, $default);
    }

    public function putString(
        string $group,
        string $key,
        string $value,
        ?string $label = null,
        ?string $description = null,
        bool $isPublic = false,
        bool $isSystem = true,
    ): void {
        $data = SettingData::fromArray([
            'group' => $group,
            'key' => $key,
            'value' => $value,
            'type' => 'string',
            'label' => $label,
            'description' => $description,
            'is_public' => $isPublic,
            'is_system' => $isSystem,
        ]);

        $existing = $this->settings->findByKey($key);

        if ($existing === null) {
            $this->settings->create($data);

            return;
        }

        $this->settings->update($existing, $data);
    }
}