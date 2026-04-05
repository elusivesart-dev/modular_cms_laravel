<?php

declare(strict_types=1);

namespace App\Core\Themes\Support;

final class AdminThemeView
{
    public static function make(string $view): string
    {
        return 'admin-theme::' . ltrim($view, '.');
    }
}