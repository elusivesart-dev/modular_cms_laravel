<?php

declare(strict_types=1);

namespace App\Core\Themes\Support;

final class PublicThemeView
{
    public static function make(string $view): string
    {
        return 'public-theme::' . ltrim($view, '.');
    }
}