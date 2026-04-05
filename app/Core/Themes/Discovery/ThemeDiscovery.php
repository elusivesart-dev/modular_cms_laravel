<?php

declare(strict_types=1);

namespace App\Core\Themes\Discovery;

use App\Core\Themes\DTO\ThemeData;
use Illuminate\Filesystem\Filesystem;

final class ThemeDiscovery
{
    public function __construct(
        private readonly Filesystem $files,
    ) {
    }

    /**
     * @return array<int, ThemeData>
     */
    public function discover(string $group): array
    {
        $basePath = rtrim((string) config('themes.path'), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . trim($group, DIRECTORY_SEPARATOR);

        $manifestFile = (string) config('themes.manifest', 'theme.json');

        if (! $this->files->isDirectory($basePath)) {
            return [];
        }

        $themes = [];

        foreach ($this->files->directories($basePath) as $directory) {
            $slug = basename($directory);
            $manifestPath = $directory . DIRECTORY_SEPARATOR . $manifestFile;

            $meta = [];
            if ($this->files->exists($manifestPath)) {
                $decoded = json_decode((string) $this->files->get($manifestPath), true);

                if (is_array($decoded)) {
                    $meta = $decoded;
                }
            }

            $themes[] = new ThemeData(
                name: (string) ($meta['name'] ?? ucfirst($slug)),
                slug: (string) ($meta['slug'] ?? $slug),
                group: $group,
                path: $directory,
                viewsPath: $directory . DIRECTORY_SEPARATOR . 'views',
                assetsPath: $directory . DIRECTORY_SEPARATOR . 'assets',
                meta: $meta,
            );
        }

        usort(
            $themes,
            static fn (ThemeData $left, ThemeData $right): int => strcmp($left->slug, $right->slug)
        );

        return $themes;
    }
}