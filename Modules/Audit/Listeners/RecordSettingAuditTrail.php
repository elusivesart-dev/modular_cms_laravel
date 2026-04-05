<?php

declare(strict_types=1);

namespace Modules\Audit\Listeners;

use App\Core\Audit\Services\AuditLogger;
use Modules\Settings\Domain\Events\SettingCreatedEvent;
use Modules\Settings\Domain\Events\SettingDeletedEvent;
use Modules\Settings\Domain\Events\SettingsGroupUpdatedEvent;
use Modules\Settings\Domain\Events\SettingUpdatedEvent;

final class RecordSettingAuditTrail
{
    public function __construct(
        private readonly AuditLogger $logger,
    ) {
    }

    public function handleSettingCreated(SettingCreatedEvent $event): void
    {
        $setting = $event->setting;

        $this->logger->log(
            'settings.created',
            $setting,
            [
                'group' => (string) $setting->group,
                'key' => (string) $setting->key,
                'value' => (string) $setting->value,
                'type' => (string) $setting->type,
                'label' => $setting->label,
                'description' => $setting->description,
                'is_public' => (bool) $setting->is_public,
                'is_system' => (bool) $setting->is_system,
            ],
            sprintf('Created setting [%s]', (string) $setting->key),
        );
    }

    public function handleSettingUpdated(SettingUpdatedEvent $event): void
    {
        $setting = $event->setting;

        $original = $setting->getOriginal();
        $current = $setting->getAttributes();

        $changes = [];
        $allowedKeys = [
            'group',
            'key',
            'value',
            'type',
            'label',
            'description',
            'is_public',
            'is_system',
        ];

        foreach ($allowedKeys as $key) {
            $oldValue = $original[$key] ?? null;
            $newValue = $current[$key] ?? null;

            if ($oldValue !== $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        $this->logger->log(
            'settings.updated',
            $setting,
            [
                'group' => (string) $setting->group,
                'key' => (string) $setting->key,
                'changes' => $changes,
            ],
            sprintf('Updated setting [%s]', (string) $setting->key),
        );
    }

    public function handleSettingDeleted(SettingDeletedEvent $event): void
    {
        $this->logger->log(
            'settings.deleted',
            null,
            [
                'group' => (string) $event->group,
                'key' => (string) $event->key,
                'setting_id' => (int) $event->settingId,
            ],
            sprintf('Deleted setting [%s]', (string) $event->key),
        );
    }

    public function handleSettingsGroupUpdated(SettingsGroupUpdatedEvent $event): void
    {
        $this->logger->log(
            'settings.group.updated',
            null,
            [
                'group' => (string) $event->group,
                'values' => is_array($event->values) ? $event->values : [],
            ],
            sprintf('Updated settings group [%s]', (string) $event->group),
        );
    }
}