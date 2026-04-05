<?php

declare(strict_types=1);

namespace Modules\Media\Domain\DTOs;

final readonly class StoreMediaData
{
    public function __construct(
        public string $disk,
        public string $directory,
        public string $path,
        public string $filename,
        public string $originalName,
        public string $mimeType,
        public string $extension,
        public int $size,
        public string $visibility,
        public ?int $uploadedBy,
        public ?string $title = null,
        public ?string $altText = null,
    ) {
    }
}