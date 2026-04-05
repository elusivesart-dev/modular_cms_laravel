<?php

declare(strict_types=1);

namespace Modules\Settings\Domain\Exceptions;

use RuntimeException;

final class SettingKeyAlreadyExistsException extends RuntimeException
{
    public static function forKey(string $key): self
    {
        return new self(__('settings::settings.exceptions.key_exists', ['key' => $key]));
    }
}