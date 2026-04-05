<?php

declare(strict_types=1);

namespace App\Core\Themes\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class ThemeAssetController extends Controller
{
    public function show(string $group, string $theme, string $path): BinaryFileResponse
    {
        if (!in_array($group, ['public', 'admin'], true)) {
            abort(404);
        }

        $group = trim($group);
        $theme = trim($theme);
        $path = str_replace("\0", '', $path);
        $path = ltrim($path, '/\\');

        if ($theme === '' || $path === '') {
            abort(404);
        }

        if (str_starts_with($path, 'assets/')) {
            $path = substr($path, 7);
        } elseif (str_starts_with($path, 'assets\\')) {
            $path = substr($path, 7);
        }

        $assetsBasePath = base_path(
            'themes'
            . DIRECTORY_SEPARATOR . $group
            . DIRECTORY_SEPARATOR . $theme
            . DIRECTORY_SEPARATOR . 'assets'
        );

        $realAssetsBasePath = realpath($assetsBasePath);

        if ($realAssetsBasePath === false || !File::isDirectory($realAssetsBasePath)) {
            abort(404);
        }

        $normalizedPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $fullPath = $realAssetsBasePath . DIRECTORY_SEPARATOR . $normalizedPath;
        $realFullPath = realpath($fullPath);

        if (
            $realFullPath === false ||
            !File::exists($realFullPath) ||
            !File::isFile($realFullPath)
        ) {
            abort(404);
        }

        $allowedRoot = rtrim($realAssetsBasePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (!str_starts_with($realFullPath, $allowedRoot)) {
            abort(404);
        }

        $extension = strtolower(pathinfo($realFullPath, PATHINFO_EXTENSION));

        $mimeTypes = [
            'css' => 'text/css; charset=UTF-8',
            'js' => 'application/javascript; charset=UTF-8',
            'mjs' => 'application/javascript; charset=UTF-8',
            'json' => 'application/json; charset=UTF-8',
            'map' => 'application/json; charset=UTF-8',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'otf' => 'font/otf',
            'eot' => 'application/vnd.ms-fontobject',
        ];

        $contentType = $mimeTypes[$extension] ?? File::mimeType($realFullPath) ?? 'application/octet-stream';

        return Response::file($realFullPath, [
            'Content-Type' => $contentType,
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}