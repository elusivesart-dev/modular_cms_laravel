<?php

declare(strict_types=1);

namespace App\Core\Localization\Infrastructure\Filesystem;

use RuntimeException;
use ZipArchive;

final class LanguagePackageExtractor
{
    public function extract(string $archivePath, string $targetDirectory): string
    {
        if (!is_file($archivePath)) {
            throw new RuntimeException('Language archive not found: ' . $archivePath);
        }

        if (!class_exists(ZipArchive::class)) {
            throw new RuntimeException('ZipArchive extension is not available.');
        }

        if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0755, true) && !is_dir($targetDirectory)) {
            throw new RuntimeException('Unable to create extraction directory: ' . $targetDirectory);
        }

        $zip = new ZipArchive();
        $result = $zip->open($archivePath);

        if ($result !== true) {
            throw new RuntimeException('Unable to open language archive: ' . $archivePath);
        }

        if (!$zip->extractTo($targetDirectory)) {
            $zip->close();

            throw new RuntimeException('Unable to extract language archive: ' . $archivePath);
        }

        $zip->close();

        return $targetDirectory;
    }
}