<?php

declare(strict_types=1);

namespace Modules\Settings\Domain\Exceptions;

use RuntimeException;

final class SettingNotFoundException extends RuntimeException
{
    public static function forId(int $id): self
    {
        return new self(__('settings::settings.exceptions.not_found', ['id' => $id]));
    }

    public static function forKey(string $key): self
    {
        return new self(__('settings::settings.exceptions.not_found_by_key', ['key' => $key]));
    }
}