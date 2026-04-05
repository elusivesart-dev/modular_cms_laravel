<?php

declare(strict_types=1);

namespace App\Core\Localization\Infrastructure\Manifests;

use App\Core\Localization\Contracts\LanguageManifestReaderInterface;
use App\Core\Localization\DTO\LanguageManifestData;
use App\Core\Localization\Exceptions\InvalidLanguageManifestException;
use App\Core\Localization\Support\LocaleCodeNormalizer;

final readonly class JsonLanguageManifestReader implements LanguageManifestReaderInterface
{
    public function __construct(
        private LocaleCodeNormalizer $normalizer,
    ) {
    }

    public function readFromDirectory(string $directory): LanguageManifestData
    {
        $path = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'language.json';

        if (!is_file($path)) {
            throw new InvalidLanguageManifestException('Language manifest not found: ' . $path);
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new InvalidLanguageManifestException('Unable to read language manifest: ' . $path);
        }

        $decoded = json_decode($contents, true);

        if (!is_array($decoded)) {
            throw new InvalidLanguageManifestException('Invalid JSON in language manifest: ' . $path);
        }

        $manifest = LanguageManifestData::fromArray($decoded);
        $code = $this->normalizer->normalize($manifest->code);

        if ($code === null || trim($manifest->name) === '' || trim($manifest->nativeName) === '') {
            throw new InvalidLanguageManifestException('Language manifest is missing required metadata: ' . $path);
        }

        $direction = strtolower(trim($manifest->direction));

        if (!in_array($direction, ['ltr', 'rtl'], true)) {
            throw new InvalidLanguageManifestException('Unsupported language direction in manifest: ' . $path);
        }

        return new LanguageManifestData(
            code: $code,
            name: trim($manifest->name),
            nativeName: trim($manifest->nativeName),
            direction: $direction,
            version: $manifest->version !== null ? trim($manifest->version) : null,
        );
    }
}