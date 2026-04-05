<?php

declare(strict_types=1);

namespace App\Core\Themes\Support;

final class ThemeView
{
    public static function make(string $view): string
    {
        return 'theme::' . ltrim($view, '.');
    }
}