<?php

declare(strict_types=1);

use App\Core\Themes\Contracts\ThemeManagerInterface;

if (! function_exists('admin_theme_asset')) {
    function admin_theme_asset(string $path): string
    {
        $theme = app(ThemeManagerInterface::class)->active('admin');

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'assets/')) {
            $path = substr($path, 7);
        }

        return route('theme.asset', [
            'group' => 'admin',
            'theme' => $theme->slug,
            'path' => $path,
        ]);
    }
}

if (! function_exists('public_theme_asset')) {
    function public_theme_asset(string $path): string
    {
        $theme = app(ThemeManagerInterface::class)->active('public');

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'assets/')) {
            $path = substr($path, 7);
        }

        return route('theme.asset', [
            'group' => 'public',
            'theme' => $theme->slug,
            'path' => $path,
        ]);
    }
}