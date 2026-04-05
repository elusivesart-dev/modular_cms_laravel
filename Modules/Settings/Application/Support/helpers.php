<?php

declare(strict_types=1);

use Modules\Settings\Domain\Contracts\SettingRepositoryInterface;

if (! function_exists('settings')) {
    function settings(string $key, mixed $default = null): mixed
    {
        return app(SettingRepositoryInterface::class)->getValue($key, $default);
    }
}