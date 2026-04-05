<?php

declare(strict_types=1);

namespace Modules\Settings\Domain\Exceptions;

use RuntimeException;

final class SettingValidationException extends RuntimeException
{
    public static function invalidType(string $type): self
    {
        return new self(__('settings::settings.exceptions.invalid_type', ['type' => $type]));
    }

    public static function invalidGroup(string $group): self
    {
        return new self(__('settings::settings.exceptions.invalid_group', ['group' => $group]));
    }

    public static function invalidKey(string $key): self
    {
        return new self(__('settings::settings.exceptions.invalid_key', ['key' => $key]));
    }
}